<?php

namespace App\Modules\MediaConverter\Infrastructure\Ffmpeg;

use App\Modules\MediaConverter\Contracts\MediaConverterInterface;
use App\Modules\MediaConverter\DTO\ConversionInputDto;
use App\Modules\MediaConverter\DTO\ConversionOutputDto;
use Illuminate\Support\Facades\Storage;

final readonly class FfmpegMediaConverter implements MediaConverterInterface
{
    public function __construct(
        protected FfmpegCommandFactory $commandFactory,
        protected FfmpegProcessRunner $processRunner,
    ) {}

    public function convert(ConversionInputDto $input): ConversionOutputDto
    {
        Storage::disk($input->outputDisk)->makeDirectory(dirname($input->outputPath));

        $command = $this->commandFactory->makeForBrowserMp4($input);

        $this->processRunner->run($command);

        return new ConversionOutputDto(
            disk: $input->outputDisk,
            path: $input->outputPath,
            mimeType: 'video/mp4',
            format: $input->options->format->value,
            size: Storage::disk($input->outputDisk)->size($input->outputPath) ?: null,
        );
    }
}
