<?php

namespace App\Modules\MediaConverter\Settings;

final readonly class FfmpegSettings
{
    public function __construct(
        public string $binary,
        public string $ffprobeBinary,
        public int $timeoutSeconds,
    ) {
        // Nothing
    }

    /**
     * @param array{binary: string, ffprobe_binary: string, timeout: int} $config
     */
    public static function fromConfig(array $config): self
    {
        return new self(
            binary: $config['binary'],
            ffprobeBinary: $config['ffprobe_binary'],
            timeoutSeconds: $config['timeout'],
        );
    }
}
