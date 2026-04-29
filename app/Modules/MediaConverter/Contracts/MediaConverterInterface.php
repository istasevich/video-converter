<?php

namespace App\Modules\MediaConverter\Contracts;

use App\Modules\MediaConverter\DTO\ConversionInputDto;
use App\Modules\MediaConverter\DTO\ConversionOutputDto;

interface MediaConverterInterface
{
    public function convert(ConversionInputDto $input): ConversionOutputDto;
}
