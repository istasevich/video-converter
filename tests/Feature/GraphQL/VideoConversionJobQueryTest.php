<?php

declare(strict_types=1);

namespace Tests\Feature\GraphQL;

use App\Modules\VideoConversion\Enums\VideoConversionStatusEnum;
use App\Modules\VideoConversion\Models\VideoConversion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class VideoConversionJobQueryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_video_conversion_job_by_uuid(): void
    {
        $conversion = VideoConversion::query()->create([
            'status' => VideoConversionStatusEnum::Queued,
            'progress' => 0,
            'original_filename' => 'sample.mov',
            'input_disk' => 'public',
            'input_path' => 'videos/input/sample.mov',
            'mime_type' => 'video/quicktime',
            'size' => 1000,
        ]);

        $response = $this->postJson('/graphql', [
            'query' => '
                query VideoConversionJob($uuid: String!) {
                    videoConversionJob(uuid: $uuid) {
                        uuid
                        status
                        progress
                        originalFilename
                        downloadUrl
                        failureReason
                    }
                }
            ',
            'variables' => [
                'uuid' => $conversion->uuid,
            ],
        ]);

        $response->assertOk();

        $this->assertNull(
            $response->json('errors'),
            json_encode($response->json('errors'), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        $response->assertJsonPath('data.videoConversionJob.uuid', $conversion->uuid);
        $response->assertJsonPath('data.videoConversionJob.status', 'Queued');
        $response->assertJsonPath('data.videoConversionJob.progress', 0);
        $response->assertJsonPath('data.videoConversionJob.originalFilename', 'sample.mov');
        $response->assertJsonPath('data.videoConversionJob.downloadUrl', null);
        $response->assertJsonPath('data.videoConversionJob.failureReason', null);
    }

    public function test_it_returns_null_for_unknown_uuid(): void
    {
        $response = $this->postJson('/graphql', [
            'query' => '
                query VideoConversionJob($uuid: String!) {
                    videoConversionJob(uuid: $uuid) {
                        uuid
                        status
                        progress
                        originalFilename
                        downloadUrl
                        failureReason
                    }
                }
            ',
            'variables' => [
                'uuid' => '00000000-0000-4000-8000-000000000000',
            ],
        ]);

        $response->assertOk();

        $this->assertNull(
            $response->json('errors'),
            json_encode($response->json('errors'), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        $response->assertJsonPath('data.videoConversionJob', null);
    }
}