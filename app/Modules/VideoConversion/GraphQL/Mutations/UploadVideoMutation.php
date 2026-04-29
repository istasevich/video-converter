<?php

namespace App\Modules\VideoConversion\GraphQL\Mutations;

use App\Modules\VideoConversion\Actions\CreateVideoConversionJobAction;
use App\Modules\VideoConversion\Models\VideoConversion;
use GraphQL\Error\Error;
use Illuminate\Http\UploadedFile;

final readonly class UploadVideoMutation
{
    public function __construct(
        protected CreateVideoConversionJobAction $createVideoConversionJobAction,
    ) {
        // Nothing
    }

    /**
     * @throws Error
     */
    public function __invoke(null $_, array $args): VideoConversion
    {
        $file = $args['file'] ?? null;

        if (!$file instanceof UploadedFile) {
            throw new Error('A valid uploaded video file is required.');
        }

        return $this->createVideoConversionJobAction->run($file);
    }
}
