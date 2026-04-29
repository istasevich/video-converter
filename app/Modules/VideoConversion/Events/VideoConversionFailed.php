<?php

namespace App\Modules\VideoConversion\Events;

use Illuminate\Foundation\Events\Dispatchable;

final class VideoConversionFailed
{
    use Dispatchable;

    public function __construct(
        public readonly string $conversionUuid,
        public readonly string $exceptionClass,
    ) {
        // Nothing
    }
}
