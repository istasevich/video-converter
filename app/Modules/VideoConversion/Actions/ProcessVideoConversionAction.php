<?php

namespace App\Modules\VideoConversion\Actions;

use App\Modules\MediaConverter\Contracts\MediaConverterInterface;
use App\Modules\MediaConverter\Exceptions\InvalidMediaInputException;
use App\Modules\VideoConversion\Models\VideoConversion;
use App\Modules\VideoConversion\Tasks\BuildConversionInputTask;
use App\Modules\VideoConversion\Tasks\MarkVideoConversionAsCompletedTask;
use App\Modules\VideoConversion\Tasks\MarkVideoConversionAsFailedTask;
use App\Modules\VideoConversion\Tasks\MarkVideoConversionAsProcessingTask;

final readonly class ProcessVideoConversionAction
{
    public function __construct(
        protected MarkVideoConversionAsProcessingTask $markAsProcessingTask,
        protected BuildConversionInputTask $buildConversionInputTask,
        protected MediaConverterInterface $mediaConverter,
        protected MarkVideoConversionAsCompletedTask $markAsCompletedTask,
        protected MarkVideoConversionAsFailedTask $markAsFailedTask,
    ) {
        // Nothing
    }

    public function run(VideoConversion $conversion): void
    {
        $this->markAsProcessingTask->run($conversion);

        try {
            $input = $this->buildConversionInputTask->run($conversion);
            $output = $this->mediaConverter->convert($input);

            $this->markAsCompletedTask->run($conversion, $output);
        } catch (InvalidMediaInputException $exception) {
            $this->markAsFailedTask->run($conversion, $exception);
        }
    }
}
