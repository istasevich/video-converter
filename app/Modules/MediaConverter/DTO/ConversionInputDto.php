<?php

namespace App\Modules\MediaConverter\DTO;

final readonly class ConversionInputDto
{
    public function __construct(
        public string $inputDisk,
        public string $inputPath,
        public string $outputDisk,
        public string $outputPath,
        public VideoConversionOptionsDto $options,
    ) {
    }
}
