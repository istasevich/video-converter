<?php

declare(strict_types=1);

namespace Tests\Unit\VideoConversion;

use App\Modules\MediaConverter\Contracts\MediaConverterInterface;
use App\Modules\MediaConverter\DTO\ConversionInputDto;
use App\Modules\MediaConverter\DTO\ConversionOutputDto;
use App\Modules\VideoConversion\Actions\ProcessVideoConversionAction;
use App\Modules\VideoConversion\Enums\VideoConversionStatusEnum;
use App\Modules\VideoConversion\Models\VideoConversion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class ProcessVideoConversionActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_persists_real_converter_progress_and_marks_conversion_completed(): void
    {
        Storage::fake('public');

        $conversion = VideoConversion::query()->create([
            'status' => VideoConversionStatusEnum::Queued,
            'progress' => 0,
            'original_filename' => 'sample.mov',
            'input_disk' => 'local',
            'input_path' => 'videos/input/sample.mov',
            'mime_type' => 'video/quicktime',
            'size' => 1000,
        ]);

        $this->mock(MediaConverterInterface::class)
            ->shouldReceive('convert')
            ->once()
            ->andReturnUsing(function (ConversionInputDto $input, ?callable $onProgress = null) use ($conversion): ConversionOutputDto {
                $this->assertNotNull($onProgress);

                $onProgress(45);

                $this->assertSame(45, $conversion->fresh()->progress);

                Storage::disk($input->outputDisk)->put($input->outputPath, 'converted');

                return new ConversionOutputDto(
                    disk: $input->outputDisk,
                    path: $input->outputPath,
                    mimeType: 'video/mp4',
                    format: $input->options->format->value,
                    size: 9,
                );
            });

        /** @var ProcessVideoConversionAction $action */
        $action = app(ProcessVideoConversionAction::class);

        $action->run($conversion);

        $conversion->refresh();

        $this->assertSame(VideoConversionStatusEnum::Completed, $conversion->status);
        $this->assertSame(100, $conversion->progress);
        $this->assertNotNull($conversion->download_url);
    }
}
