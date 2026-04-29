<?php

namespace App\Modules\MediaConverter\Providers;

use App\Modules\MediaConverter\Contracts\MediaConverterInterface;
use App\Modules\MediaConverter\Infrastructure\Ffmpeg\FfmpegMediaConverter;
use App\Modules\MediaConverter\Settings\FfmpegSettings;
use Illuminate\Support\ServiceProvider;

final class MediaConverterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../../../config/media-converter.php', 'media-converter');

        $this->app->singleton(FfmpegSettings::class, function (): FfmpegSettings {
            /** @var array{binary: string, ffprobe_binary: string, timeout: int} $config */
            $config = config('media-converter.ffmpeg');

            return FfmpegSettings::fromConfig($config);
        });

        $this->app->bind(MediaConverterInterface::class, FfmpegMediaConverter::class);
    }
}
