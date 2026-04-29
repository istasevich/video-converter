<?php

namespace App\Modules\VideoConversion\Tasks;

use App\Modules\VideoConversion\Enums\VideoConversionStatusEnum;
use App\Modules\VideoConversion\Events\VideoConversionFailed;
use App\Modules\VideoConversion\Models\VideoConversion;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class MarkVideoConversionAsFailedTask
{
    public function run(VideoConversion $conversion, Throwable $exception): VideoConversion
    {
        $conversion->forceFill([
            'status' => VideoConversionStatusEnum::Failed,
            'failure_reason' => $exception->getMessage(),
            'failed_at' => now(),
        ])->save();

        DB::afterCommit(static fn () => event(new VideoConversionFailed($conversion->uuid, get_class($exception))));

        return $conversion;
    }
}
