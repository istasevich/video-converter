<?php

declare(strict_types=1);

namespace App\Modules\MediaConverter\Infrastructure\Ffmpeg;

use App\Modules\MediaConverter\Exceptions\InvalidMediaInputException;
use App\Modules\MediaConverter\Exceptions\TemporaryMediaConverterUnavailableException;
use App\Modules\MediaConverter\Settings\FfmpegSettings;
use Symfony\Component\Process\Process;

final readonly class FfmpegMetadataReader
{
    public function __construct(
        protected FfmpegSettings $settings,
    ) {
        // Nothing
    }

    public function durationSeconds(string $path): float
    {
        $process = new Process([
            $this->settings->ffprobeBinary,
            '-v',
            'error',
            '-show_entries',
            'format=duration',
            '-of',
            'default=noprint_wrappers=1:nokey=1',
            $path,
        ]);
        $process->setTimeout($this->settings->timeoutSeconds);
        $process->run();

        if (! $process->isSuccessful()) {
            $errorOutput = $process->getErrorOutput() ?: $process->getOutput();

            if ($this->looksTemporary($errorOutput)) {
                throw new TemporaryMediaConverterUnavailableException($errorOutput);
            }

            throw new InvalidMediaInputException($errorOutput);
        }

        $duration = trim($process->getOutput());

        if (! is_numeric($duration) || (float) $duration <= 0.0) {
            throw new InvalidMediaInputException('Unable to read input duration.');
        }

        return (float) $duration;
    }

    protected function looksTemporary(string $output): bool
    {
        $temporaryMarkers = [
            'Resource temporarily unavailable',
            'Connection refused',
            'Cannot allocate memory',
            'No space left on device',
            'Device or resource busy',
        ];

        foreach ($temporaryMarkers as $marker) {
            if (str_contains($output, $marker)) {
                return true;
            }
        }

        return false;
    }
}
