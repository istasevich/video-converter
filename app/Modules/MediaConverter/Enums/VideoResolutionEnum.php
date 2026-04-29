<?php

namespace App\Modules\MediaConverter\Enums;

enum VideoResolutionEnum: string
{
    case P720 = '720p';

    public function maxHeight(): int
    {
        return match ($this) {
            self::P720 => 720,
        };
    }
}
