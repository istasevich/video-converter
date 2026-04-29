<?php

namespace App\Modules\VideoConversion\GraphQL\Queries;

use App\Modules\VideoConversion\Models\VideoConversion;

final readonly class VideoConversionJobQuery
{
    public function __invoke(null $_, array $args): ?VideoConversion
    {
        return VideoConversion::query()
            ->where('uuid', $args['uuid'])
            ->first();
    }
}
