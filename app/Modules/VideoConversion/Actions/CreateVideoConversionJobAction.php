<?php

namespace App\Modules\VideoConversion\Actions;

use App\Modules\VideoConversion\Events\VideoConversionCreated;
use App\Modules\VideoConversion\Models\VideoConversion;
use App\Modules\VideoConversion\Tasks\CreateVideoConversionRecordTask;
use App\Modules\VideoConversion\Tasks\DispatchVideoConversionJobTask;
use App\Modules\VideoConversion\Tasks\StoreUploadedVideoTask;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

final readonly class CreateVideoConversionJobAction
{
    public function __construct(
        protected StoreUploadedVideoTask $storeUploadedVideoTask,
        protected CreateVideoConversionRecordTask $createVideoConversionRecordTask,
        protected DispatchVideoConversionJobTask $dispatchVideoConversionJobTask,
    ) {
        // Nothing
    }

    public function run(UploadedFile $file): VideoConversion
    {
        return DB::transaction(function () use ($file): VideoConversion {
            $storedVideo = $this->storeUploadedVideoTask->run($file);
            $conversion = $this->createVideoConversionRecordTask->run($storedVideo);

            $this->dispatchVideoConversionJobTask->run($conversion);

            DB::afterCommit(static fn () => event(new VideoConversionCreated($conversion->uuid)));

            return $conversion;
        });
    }
}
