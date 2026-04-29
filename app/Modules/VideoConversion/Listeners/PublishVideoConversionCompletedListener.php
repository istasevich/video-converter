<?php

declare(strict_types=1);

namespace App\Modules\VideoConversion\Listeners;

use App\Modules\VideoConversion\Events\VideoConversionCompleted;
use App\Modules\VideoConversion\Jobs\PublishVideoConversionCompletedJob;
use App\Modules\VideoConversion\Settings\VideoConversionQueueSettings;
use Illuminate\Support\Facades\Log;

final readonly class PublishVideoConversionCompletedListener
{
    public function __construct(
        protected VideoConversionQueueSettings $queueSettings,
    ) {}

    public function handle(VideoConversionCompleted $event): void
    {
        Log::info('Video conversion completed event publishing scheduled', [
            'conversion_uuid' => $event->conversionUuid,
        ]);

        PublishVideoConversionCompletedJob::dispatch($event->conversionUuid)
            ->onQueue($this->queueSettings->postProcessingQueue);
    }
}
