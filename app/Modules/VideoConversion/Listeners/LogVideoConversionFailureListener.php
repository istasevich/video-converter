<?php

namespace App\Modules\VideoConversion\Listeners;

use App\Modules\VideoConversion\Events\VideoConversionFailed;
use Illuminate\Support\Facades\Log;

final readonly class LogVideoConversionFailureListener
{
    public function handle(VideoConversionFailed $event): void
    {
        Log::warning('Video conversion failed', [
            'conversion_uuid' => $event->conversionUuid,
            'exception_class' => $event->exceptionClass,
        ]);
    }
}
