<?php

namespace App\Modules\VideoConversion\Settings;

use App\Modules\MediaConverter\DTO\VideoConversionOptionsDto;
use App\Modules\MediaConverter\Enums\OutputVideoFormatEnum;
use App\Modules\MediaConverter\Enums\VideoResolutionEnum;

final readonly class VideoConversionOptionsSettings
{
    public function __construct(
        public OutputVideoFormatEnum $format,
        public VideoResolutionEnum $maxResolution,
        public bool $fastStart,
    ) {
        // Nothing
    }

    /**
     * @param array{format: string, max_resolution: string, fast_start: bool} $config
     */
    public static function fromConfig(array $config): self
    {
        return new self(
            format: OutputVideoFormatEnum::from($config['format']),
            maxResolution: VideoResolutionEnum::from($config['max_resolution']),
            fastStart: $config['fast_start'],
        );
    }

    public function toDto(): VideoConversionOptionsDto
    {
        return new VideoConversionOptionsDto(
            format: $this->format,
            maxResolution: $this->maxResolution,
            fastStart: $this->fastStart,
        );
    }
}
