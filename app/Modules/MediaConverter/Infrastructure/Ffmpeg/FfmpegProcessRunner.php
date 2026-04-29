<?php

namespace App\Modules\MediaConverter\Infrastructure\Ffmpeg;

use App\Modules\MediaConverter\Settings\FfmpegSettings;
use App\Modules\MediaConverter\Exceptions\InvalidMediaInputException;
use App\Modules\MediaConverter\Exceptions\TemporaryMediaConverterUnavailableException;
use Symfony\Component\Process\Process;

final readonly class FfmpegProcessRunner
{
    public function __construct(
        protected FfmpegSettings $settings,
    ) {
        // Nothing
    }

    public function run(array $command): void
    {
        $process = new Process($command);
        $process->setTimeout($this->settings->timeoutSeconds);
        $process->run();

        if ($process->isSuccessful()) {
            return;
        }

        $errorOutput = $process->getErrorOutput() ?: $process->getOutput();

        if ($this->looksTemporary($errorOutput)) {
            throw new TemporaryMediaConverterUnavailableException($errorOutput);
        }

        throw new InvalidMediaInputException($errorOutput);
    }

    protected function looksTemporary(string $output): bool
    {
        $temporaryMarkers = [
            'Resource temporarily unavailable',
            'Connection refused',
            'Cannot allocate memory',
            'No space left on device',
            'Device or resource busy',
        ];

        foreach ($temporaryMarkers as $marker) {
            if (str_contains($output, $marker)) {
                return true;
            }
        }

        return false;
    }
}
