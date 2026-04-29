<?php

namespace App\Modules\VideoConversion\Tasks;

use App\Modules\MediaConverter\DTO\ConversionInputDto;
use App\Modules\VideoConversion\Models\VideoConversion;
use App\Modules\VideoConversion\Settings\VideoConversionOptionsSettings;
use App\Modules\VideoConversion\Settings\VideoConversionStorageSettings;

final readonly class BuildConversionInputTask
{
    public function __construct(
        protected VideoConversionStorageSettings $storageSettings,
        protected VideoConversionOptionsSettings $optionsSettings,
    ) {
        // Nothing
    }

    public function run(VideoConversion $conversion): ConversionInputDto
    {
        $options = $this->optionsSettings->toDto();

        return new ConversionInputDto(
            inputDisk: $conversion->input_disk,
            inputPath: $conversion->input_path,
            outputDisk: $this->storageSettings->outputDisk,
            outputPath: $this->storageSettings->outputPath($conversion->uuid, $options->format),
            options: $options,
        );
    }
}
