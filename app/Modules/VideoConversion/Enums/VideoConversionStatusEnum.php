<?php

namespace App\Modules\VideoConversion\Enums;

enum VideoConversionStatusEnum: string
{
    case Queued = 'queued';
    case Processing = 'processing';
    case Completed = 'completed';
    case Failed = 'failed';
}
