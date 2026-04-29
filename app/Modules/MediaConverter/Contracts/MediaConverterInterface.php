<?php

declare(strict_types=1);

namespace App\Modules\MediaConverter\Contracts;

use App\Modules\MediaConverter\DTO\ConversionInputDto;
use App\Modules\MediaConverter\DTO\ConversionOutputDto;

interface MediaConverterInterface
{
    public function convert(ConversionInputDto $input, ?callable $onProgress = null): ConversionOutputDto;
}
