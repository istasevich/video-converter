<?php

namespace App\Modules\VideoConversion\Settings;

use App\Modules\MediaConverter\Enums\OutputVideoFormatEnum;

final readonly class VideoConversionStorageSettings
{
    public function __construct(
        public string $inputDisk,
        public string $outputDisk,
        public string $inputDirectory,
        public string $outputDirectory,
    ) {
    }

    /**
     * @param array{input_disk: string, output_disk: string, input_directory: string, output_directory: string} $config
     */
    public static function fromConfig(array $config): self
    {
        return new self(
            inputDisk: $config['input_disk'],
            outputDisk: $config['output_disk'],
            inputDirectory: trim($config['input_directory'], '/'),
            outputDirectory: trim($config['output_directory'], '/'),
        );
    }

    public function outputPath(string $conversionUuid, OutputVideoFormatEnum $format): string
    {
        return sprintf('%s/%s.%s', $this->outputDirectory, $conversionUuid, $format->value);
    }
}
