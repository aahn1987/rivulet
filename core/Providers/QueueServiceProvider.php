<?php
namespace Rivulet\Providers;

use Rivulet\Queue\Queue;

class QueueServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->bind('queue', function () {
            return new Queue();
        });

        $this->bind('queue.connection', function () {
            return $this->make('queue');
        });
    }

    public function boot(): void
    {
        $this->configureQueue();
    }

    protected function configureQueue(): void
    {
        $config = [
            'default'     => config('queue.default', 'database'),
            'connections' => [
                'database' => [
                    'driver' => 'database',
                    'table'  => config('queue.table', 'jobs'),
                ],
                'redis'    => [
                    'driver'     => 'redis',
                    'connection' => 'default',
                ],
            ],
        ];

        $queue = $this->make('queue');
        $queue->setConfig($config);
    }
}
