<?php

namespace App\Modules\VideoConversion\Tasks;

use App\Modules\VideoConversion\Jobs\ConvertVideoJob;
use App\Modules\VideoConversion\Models\VideoConversion;
use App\Modules\VideoConversion\Settings\VideoConversionQueueSettings;

final readonly class DispatchVideoConversionJobTask
{
    public function __construct(
        protected VideoConversionQueueSettings $queueSettings,
    ) {
        // Nothing
    }

    public function run(VideoConversion $conversion): void
    {
        ConvertVideoJob::dispatch($conversion->id)
            ->onQueue($this->queueSettings->conversionQueue);
    }
}
