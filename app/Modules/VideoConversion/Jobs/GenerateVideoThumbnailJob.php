<?php

declare(strict_types=1);

namespace App\Modules\VideoConversion\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

final class GenerateVideoThumbnailJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        protected string $conversionUuid,
    ) {
        // Nothing
    }

    public function handle(): void
    {
        Log::info('Thumbnail generation job executed', [
            'conversion_uuid' => $this->conversionUuid,
        ]);
    }
}