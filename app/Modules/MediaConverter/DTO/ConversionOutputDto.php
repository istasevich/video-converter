<?php

namespace App\Modules\MediaConverter\DTO;

final readonly class ConversionOutputDto
{
    public function __construct(
        public string $disk,
        public string $path,
        public string $mimeType,
        public string $format,
        public ?int $durationSeconds = null,
        public ?int $width = null,
        public ?int $height = null,
        public ?int $size = null,
    ) {}
}
