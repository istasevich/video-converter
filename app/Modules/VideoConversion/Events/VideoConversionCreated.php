<?php

namespace App\Modules\VideoConversion\Events;

use Illuminate\Foundation\Events\Dispatchable;

final class VideoConversionCreated
{
    use Dispatchable;

    public function __construct(public readonly string $conversionUuid)
    {
        // Nothing
    }
}
