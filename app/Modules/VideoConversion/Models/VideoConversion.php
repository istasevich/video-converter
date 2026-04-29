<?php

namespace App\Modules\VideoConversion\Models;

use App\Modules\VideoConversion\Enums\VideoConversionStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $uuid
 * @property VideoConversionStatusEnum $status
 * @property int $progress
 * @property string $original_filename
 * @property string $input_disk
 * @property string $input_path
 * @property string|null $output_disk
 * @property string|null $output_path
 * @property string|null $download_url
 * @property string|null $mime_type
 * @property int|null $size
 * @property string|null $failure_reason
 * @property Carbon|null $started_at
 * @property Carbon|null $completed_at
 * @property Carbon|null $failed_at
 */
final class VideoConversion extends Model
{
    protected $fillable = [
        'uuid',
        'status',
        'progress',
        'original_filename',
        'input_disk',
        'input_path',
        'output_disk',
        'output_path',
        'download_url',
        'mime_type',
        'size',
        'failure_reason',
        'started_at',
        'completed_at',
        'failed_at',
    ];

    protected $casts = [
        'status' => VideoConversionStatusEnum::class,
        'progress' => 'integer',
        'size' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        self::creating(static function (VideoConversion $conversion): void {
            $conversion->uuid ??= (string) Str::uuid();
        });
    }
}
