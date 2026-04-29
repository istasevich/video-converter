<?php

namespace App\Modules\VideoConversion\Tasks;

use App\Modules\VideoConversion\Enums\VideoConversionStatusEnum;
use App\Modules\VideoConversion\Events\VideoConversionStarted;
use App\Modules\VideoConversion\Models\VideoConversion;
use Illuminate\Support\Facades\DB;

final readonly class MarkVideoConversionAsProcessingTask
{
    public function __construct()
    {
        // Nothing
    }

    public function run(VideoConversion $conversion): VideoConversion
    {
        $conversion->forceFill([
            'status' => VideoConversionStatusEnum::Processing,
            'progress' => max($conversion->progress, 10),
            'started_at' => $conversion->started_at ?? now(),
        ])->save();

        DB::afterCommit(static fn () => event(new VideoConversionStarted($conversion->uuid)));

        return $conversion;
    }
}
