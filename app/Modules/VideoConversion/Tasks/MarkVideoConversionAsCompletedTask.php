<?php

namespace App\Modules\VideoConversion\Tasks;

use App\Modules\MediaConverter\DTO\ConversionOutputDto;
use App\Modules\VideoConversion\Enums\VideoConversionStatusEnum;
use App\Modules\VideoConversion\Events\VideoConversionCompleted;
use App\Modules\VideoConversion\Models\VideoConversion;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

final readonly class MarkVideoConversionAsCompletedTask
{
    public function __construct()
    {
        // Nothing
    }

    public function run(VideoConversion $conversion, ConversionOutputDto $output): VideoConversion
    {
        /** @var FilesystemAdapter $outputDisk */
        $outputDisk = Storage::disk($output->disk);

        $conversion->forceFill([
            'status' => VideoConversionStatusEnum::Completed,
            'progress' => 100,
            'output_disk' => $output->disk,
            'output_path' => $output->path,
            'download_url' => $outputDisk->url($output->path),
            'completed_at' => now(),
        ])->save();

        DB::afterCommit(static fn () => event(new VideoConversionCompleted($conversion->uuid)));

        return $conversion;
    }
}
