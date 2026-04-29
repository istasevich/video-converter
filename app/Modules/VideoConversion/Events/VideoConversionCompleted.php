<?php

namespace App\Modules\VideoConversion\Events;

use Illuminate\Foundation\Events\Dispatchable;

final class VideoConversionCompleted
{
    use Dispatchable;

    public function __construct(public readonly string $conversionUuid)
    {
        // Nothing
    }
}
