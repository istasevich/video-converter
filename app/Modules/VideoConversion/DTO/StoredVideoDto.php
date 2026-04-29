<?php

namespace App\Modules\VideoConversion\DTO;

final readonly class StoredVideoDto
{
    public function __construct(
        public string $disk,
        public string $path,
        public string $originalFilename,
        public ?string $mimeType,
        public ?int $size,
    ) {}
}
