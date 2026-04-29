<?php

return [
    'ffmpeg' => [
        'binary' => env('FFMPEG_BINARY', '/usr/bin/ffmpeg'),
        'ffprobe_binary' => env('FFPROBE_BINARY', '/usr/bin/ffprobe'),
        'timeout' => (int) env('FFMPEG_TIMEOUT', 1800),
    ],
];
