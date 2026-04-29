<?php

namespace App\Modules\VideoConversion\Providers;

use App\Modules\VideoConversion\Events\VideoConversionCompleted;
use App\Modules\VideoConversion\Events\VideoConversionFailed;
use App\Modules\VideoConversion\Listeners\CleanupSourceVideoListener;
use App\Modules\VideoConversion\Listeners\GenerateVideoThumbnailListener;
use App\Modules\VideoConversion\Listeners\LogVideoConversionFailureListener;
use App\Modules\VideoConversion\Listeners\PublishVideoConversionCompletedListener;
use App\Modules\VideoConversion\Settings\VideoConversionOptionsSettings;
use App\Modules\VideoConversion\Settings\VideoConversionQueueSettings;
use App\Modules\VideoConversion\Settings\VideoConversionStorageSettings;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

final class VideoConversionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../../../config/video-conversion.php', 'video-conversion');

        $this->app->singleton(VideoConversionStorageSettings::class, function (): VideoConversionStorageSettings {
            /** @var array{input_disk: string, output_disk: string, input_directory: string, output_directory: string} $config */
            $config = config('video-conversion.storage');

            return VideoConversionStorageSettings::fromConfig($config);
        });

        $this->app->singleton(VideoConversionQueueSettings::class, function (): VideoConversionQueueSettings {
            /** @var array{queues: array{conversion: string, post_processing: string}, job: array{tries: int, timeout: int, backoff: list<int>}} $config */
            $config = config('video-conversion');

            return VideoConversionQueueSettings::fromConfig($config);
        });

        $this->app->singleton(VideoConversionOptionsSettings::class, function (): VideoConversionOptionsSettings {
            /** @var array{format: string, max_resolution: string, fast_start: bool} $config */
            $config = config('video-conversion.conversion');

            return VideoConversionOptionsSettings::fromConfig($config);
        });
    }

    public function boot(): void
    {
        Event::listen(VideoConversionCompleted::class, GenerateVideoThumbnailListener::class);
        Event::listen(VideoConversionCompleted::class, CleanupSourceVideoListener::class);
        Event::listen(VideoConversionCompleted::class, PublishVideoConversionCompletedListener::class);
        Event::listen(VideoConversionFailed::class, LogVideoConversionFailureListener::class);
    }
}
