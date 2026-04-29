<?php

declare(strict_types=1);

namespace App\Modules\MediaConverter\Infrastructure\Ffmpeg;

use App\Modules\MediaConverter\Contracts\MediaConverterInterface;
use App\Modules\MediaConverter\DTO\ConversionInputDto;
use App\Modules\MediaConverter\DTO\ConversionOutputDto;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

final readonly class FfmpegMediaConverter implements MediaConverterInterface
{
    public function __construct(
        protected FfmpegCommandFactory $commandFactory,
        protected FfmpegProcessRunner $processRunner,
        protected FfmpegMetadataReader $metadataReader,
    ) {
        // Nothing
    }

    public function convert(ConversionInputDto $input, ?callable $onProgress = null): ConversionOutputDto
    {
        Storage::disk($input->outputDisk)->makeDirectory(dirname($input->outputPath));

        $inputPath = Storage::disk($input->inputDisk)->path($input->inputPath);
        $durationSeconds = $this->metadataReader->durationSeconds($inputPath);
        $command = $this->commandFactory->makeForBrowserMp4($input);
        $progressBuffer = '';
        $lastProgress = 0;

        $this->processRunner->run($command, function (string $type, string $buffer) use ($durationSeconds, $onProgress, &$progressBuffer, &$lastProgress): void {
            if ($type !== Process::OUT || $onProgress === null) {
                return;
            }

            $progressBuffer .= $buffer;

            while (($lineEndPosition = strpos($progressBuffer, "\n")) !== false) {
                $line = trim(substr($progressBuffer, 0, $lineEndPosition));
                $progressBuffer = substr($progressBuffer, $lineEndPosition + 1);

                $progress = $this->progressFromLine($line, $durationSeconds);

                if ($progress !== null && $progress > $lastProgress) {
                    $lastProgress = $progress;
                    $onProgress($progress);
                }
            }
        });

        if ($onProgress !== null) {
            $onProgress(100);
        }

        return new ConversionOutputDto(
            disk: $input->outputDisk,
            path: $input->outputPath,
            mimeType: 'video/mp4',
            format: $input->options->format->value,
            size: Storage::disk($input->outputDisk)->size($input->outputPath) ?: null,
        );
    }

    protected function progressFromLine(string $line, float $durationSeconds): ?int
    {
        if ($durationSeconds <= 0.0 || ! str_contains($line, '=')) {
            return null;
        }

        [$key, $value] = explode('=', $line, 2);

        if (! in_array($key, ['out_time_ms', 'out_time_us'], true) || ! is_numeric($value)) {
            return null;
        }

        $processedSeconds = (float) $value / 1_000_000;

        return min(99, max(0, (int) floor(($processedSeconds / $durationSeconds) * 100)));
    }
}
