<?php

namespace App\Modules\MediaConverter\DTO;

use App\Modules\MediaConverter\Enums\OutputVideoFormatEnum;
use App\Modules\MediaConverter\Enums\VideoResolutionEnum;

final readonly class VideoConversionOptionsDto
{
    public function __construct(
        public OutputVideoFormatEnum $format,
        public VideoResolutionEnum $maxResolution,
        public bool $fastStart,
    ) {
    }
}
