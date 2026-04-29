<?php

namespace App\Modules\VideoConversion\Jobs;

use App\Modules\VideoConversion\Actions\ProcessVideoConversionAction;
use App\Modules\VideoConversion\Models\VideoConversion;
use App\Modules\VideoConversion\Settings\VideoConversionQueueSettings;
use App\Modules\VideoConversion\Tasks\MarkVideoConversionAsFailedTask;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

final class ConvertVideoJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries;
    public int $timeout;

    /** @var list<int> */
    protected array $backoffSeconds;

    public function __construct(
        protected int $videoConversionId,
    ) {
        /** @var VideoConversionQueueSettings $queueSettings */
        $queueSettings = app(VideoConversionQueueSettings::class);

        $this->tries = $queueSettings->tries;
        $this->timeout = $queueSettings->timeoutSeconds;
        $this->backoffSeconds = $queueSettings->backoffSeconds;
    }

    public function backoff(): array
    {
        return $this->backoffSeconds;
    }

    public function handle(ProcessVideoConversionAction $processVideoConversionAction): void
    {
        $conversion = VideoConversion::query()->findOrFail($this->videoConversionId);

        $processVideoConversionAction->run($conversion);
    }

    public function failed(Throwable $exception): void
    {
        $conversion = VideoConversion::query()->find($this->videoConversionId);

        if (! $conversion) {
            return;
        }

        app(MarkVideoConversionAsFailedTask::class)->run($conversion, $exception);
    }
}
