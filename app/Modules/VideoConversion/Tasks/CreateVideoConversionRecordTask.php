<?php

namespace App\Modules\VideoConversion\Tasks;

use App\Modules\VideoConversion\DTO\StoredVideoDto;
use App\Modules\VideoConversion\Enums\VideoConversionStatusEnum;
use App\Modules\VideoConversion\Models\VideoConversion;

final readonly class CreateVideoConversionRecordTask
{
    public function run(StoredVideoDto $storedVideo): VideoConversion
    {
        return VideoConversion::query()->create([
            'status' => VideoConversionStatusEnum::Queued,
            'progress' => 0,
            'original_filename' => $storedVideo->originalFilename,
            'input_disk' => $storedVideo->disk,
            'input_path' => $storedVideo->path,
            'mime_type' => $storedVideo->mimeType,
            'size' => $storedVideo->size,
        ]);
    }
}
