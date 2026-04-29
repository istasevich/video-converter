<?php

return [
    'storage' => [
        'input_disk' => env('VIDEO_INPUT_DISK', 'local'),
        'output_disk' => env('VIDEO_OUTPUT_DISK', 'public'),
        'input_directory' => env('VIDEO_INPUT_DIRECTORY', 'videos/input'),
        'output_directory' => env('VIDEO_OUTPUT_DIRECTORY', 'videos/output'),
    ],

    'queues' => [
        'conversion' => env('VIDEO_CONVERSION_QUEUE', 'video-conversions'),
        'post_processing' => env('VIDEO_POST_PROCESSING_QUEUE', 'video-post-processing'),
    ],

    'job' => [
        'tries' => (int) env('VIDEO_CONVERSION_JOB_TRIES', 3),
        'timeout' => (int) env('VIDEO_CONVERSION_JOB_TIMEOUT', 1800),
        'backoff' => [60, 180, 600],
    ],

    'conversion' => [
        'format' => env('VIDEO_CONVERSION_FORMAT', 'mp4'),
        'max_resolution' => env('VIDEO_CONVERSION_MAX_RESOLUTION', '720p'),
        'fast_start' => env('VIDEO_CONVERSION_FAST_START', true),
    ],
];
