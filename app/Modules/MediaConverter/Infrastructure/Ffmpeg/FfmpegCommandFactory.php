<?php

namespace App\Modules\MediaConverter\Infrastructure\Ffmpeg;

use App\Modules\MediaConverter\DTO\ConversionInputDto;
use App\Modules\MediaConverter\Settings\FfmpegSettings;
use Illuminate\Support\Facades\Storage;

final readonly class FfmpegCommandFactory
{
    public function __construct(
        protected FfmpegSettings $settings,
    ) {
    }

    public function makeForBrowserMp4(ConversionInputDto $input): array
    {
        $inputPath = Storage::disk($input->inputDisk)->path($input->inputPath);
        $outputPath = Storage::disk($input->outputDisk)->path($input->outputPath);

        $command = [
            $this->settings->binary,
            '-y',
            '-i',
            $inputPath,
            '-vf',
            sprintf('scale=-2:min(%d\\,ih)', $input->options->maxResolution->maxHeight()),
            '-c:v',
            'libx264',
            '-preset',
            'fast',
            '-crf',
            '23',
            '-c:a',
            'aac',
            '-b:a',
            '128k',
        ];

        if ($input->options->fastStart) {
            $command[] = '-movflags';
            $command[] = '+faststart';
        }

        $command[] = $outputPath;

        return $command;
    }
}
