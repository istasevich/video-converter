<?php

declare(strict_types=1);

namespace App\Modules\VideoConversion\Listeners;

use App\Modules\VideoConversion\Events\VideoConversionCompleted;
use App\Modules\VideoConversion\Jobs\GenerateVideoThumbnailJob;
use App\Modules\VideoConversion\Settings\VideoConversionQueueSettings;
use Illuminate\Support\Facades\Log;

final readonly class GenerateVideoThumbnailListener
{
    public function __construct(
        protected VideoConversionQueueSettings $queueSettings,
    ) {
        // Nothing
    }

    public function handle(VideoConversionCompleted $event): void
    {
        Log::info('Thumbnail generation scheduled', [
            'conversion_uuid' => $event->conversionUuid,
        ]);

        GenerateVideoThumbnailJob::dispatch($event->conversionUuid)
            ->onQueue($this->queueSettings->postProcessingQueue);
    }
}
