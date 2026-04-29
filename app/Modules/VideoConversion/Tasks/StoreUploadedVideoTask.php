<?php

namespace App\Modules\VideoConversion\Tasks;

use App\Modules\VideoConversion\DTO\StoredVideoDto;
use App\Modules\VideoConversion\Settings\VideoConversionStorageSettings;
use Illuminate\Http\UploadedFile;

final readonly class StoreUploadedVideoTask
{
    public function __construct(
        protected VideoConversionStorageSettings $storageSettings,
    ) {
    }

    public function run(UploadedFile $file): StoredVideoDto
    {
        $path = $file->store($this->storageSettings->inputDirectory, $this->storageSettings->inputDisk);

        return new StoredVideoDto(
            disk: $this->storageSettings->inputDisk,
            path: $path,
            originalFilename: $file->getClientOriginalName(),
            mimeType: $file->getMimeType(),
            size: $file->getSize(),
        );
    }
}
