<?php

declare(strict_types=1);

namespace App\Modules\VideoConversion\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

final class CleanupSourceVideoJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        protected string $conversionUuid,
    ) {}

    public function handle(): void
    {
        Log::info('Source video cleanup job executed', [
            'conversion_uuid' => $this->conversionUuid,
        ]);
    }
}
