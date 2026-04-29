<?php

namespace App\Modules\VideoConversion\Settings;

final readonly class VideoConversionQueueSettings
{
    /**
     * @param  list<int>  $backoffSeconds
     */
    public function __construct(
        public string $conversionQueue,
        public string $postProcessingQueue,
        public int $tries,
        public int $timeoutSeconds,
        public array $backoffSeconds,
    ) {}

    /**
     * @param  array{queues: array{conversion: string, post_processing: string}, job: array{tries: int, timeout: int, backoff: list<int>}}  $config
     */
    public static function fromConfig(array $config): self
    {
        return new self(
            conversionQueue: $config['queues']['conversion'],
            postProcessingQueue: $config['queues']['post_processing'],
            tries: $config['job']['tries'],
            timeoutSeconds: $config['job']['timeout'],
            backoffSeconds: $config['job']['backoff'],
        );
    }
}
