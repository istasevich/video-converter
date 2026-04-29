<?php

declare(strict_types=1);

namespace Tests\Unit\VideoConversion;

use App\Modules\VideoConversion\Actions\CreateVideoConversionJobAction;
use App\Modules\VideoConversion\Enums\VideoConversionStatusEnum;
use App\Modules\VideoConversion\Jobs\ConvertVideoJob;
use App\Modules\VideoConversion\Models\VideoConversion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class CreateVideoConversionJobActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_conversion_record_stores_file_and_dispatches_conversion_job(): void
    {
        Queue::fake();

        $inputDisk = config('video-conversion.storage.input_disk', 'public');

        Storage::fake($inputDisk);

        $file = UploadedFile::fake()->create(
            'sample.mov',
            1024,
            'video/quicktime',
        );

        /** @var CreateVideoConversionJobAction $action */
        $action = app(CreateVideoConversionJobAction::class);

        $conversion = $action->run($file);

        $this->assertInstanceOf(VideoConversion::class, $conversion);
        $this->assertNotEmpty($conversion->uuid);

        $this->assertSame(VideoConversionStatusEnum::Queued, $conversion->status);
        $this->assertSame(0, $conversion->progress);
        $this->assertSame('sample.mov', $conversion->original_filename);
        $this->assertSame($inputDisk, $conversion->input_disk);
        $this->assertSame('video/quicktime', $conversion->mime_type);
        $this->assertNull($conversion->download_url);
        $this->assertNull($conversion->failure_reason);

        Storage::disk($inputDisk)->assertExists($conversion->input_path);

        $this->assertDatabaseHas('video_conversions', [
            'uuid' => $conversion->uuid,
            'status' => VideoConversionStatusEnum::Queued->value,
            'progress' => 0,
            'original_filename' => 'sample.mov',
            'input_disk' => $inputDisk,
            'mime_type' => 'video/quicktime',
        ]);

        Queue::assertPushed(ConvertVideoJob::class);
    }
}