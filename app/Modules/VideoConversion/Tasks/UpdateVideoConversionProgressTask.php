<?php

declare(strict_types=1);

namespace App\Modules\VideoConversion\Tasks;

use App\Modules\VideoConversion\Models\VideoConversion;

final readonly class UpdateVideoConversionProgressTask
{
    public function run(VideoConversion $conversion, int $progress): VideoConversion
    {
        $progress = max(0, min(100, $progress));

        if ($progress <= $conversion->progress) {
            return $conversion;
        }

        $conversion->forceFill([
            'progress' => $progress,
        ])->save();

        return $conversion;
    }
}
