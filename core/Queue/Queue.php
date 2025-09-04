<?php
namespace Rivulet\Queue;

use Rivulet\Queue\Drivers\DatabaseQueue;
use Rivulet\Queue\Drivers\QueueDriverInterface;
use Rivulet\Queue\Drivers\RabbitMQQueue;
use Rivulet\Queue\Drivers\RedisQueue;

class Queue
{
    private QueueDriverInterface $driver;
    private array $config = [];

    public function __construct(string $driver = null, array $config = [])
    {
        $this->config = $config ?: config('queue', []);
        $driver       = $driver ?? env('QUEUE_CONNECTION', 'database');
        $this->driver = $this->createDriver($driver);
    }

    private function createDriver(string $driver): QueueDriverInterface
    {
        $config = $this->config['connections'][$driver] ?? [];

        switch ($driver) {
            case 'database':
                return new DatabaseQueue($config);
            case 'redis':
                return new RedisQueue($config);
            case 'rabbitmq':
                return new RabbitMQQueue($config);
            default:
                return new DatabaseQueue($config);
        }
    }

    public function push($job, string $queue = 'default'): string
    {
        return $this->driver->push($job, $queue);
    }

    public function later(int $delay, $job, string $queue = 'default'): string
    {
        return $this->driver->later($delay, $job, $queue);
    }

    public function pop(string $queue = 'default'): ?Job
    {
        return $this->driver->pop($queue);
    }

    public function size(string $queue = 'default'): int
    {
        return $this->driver->size($queue);
    }

    public function clear(string $queue = 'default'): bool
    {
        return $this->driver->clear($queue);
    }

    public function release(string $id, int $delay = 0): bool
    {
        return $this->driver->release($id, $delay);
    }

    public function delete(string $id): bool
    {
        return $this->driver->delete($id);
    }

    public function failed(string $id, \Throwable $exception): bool
    {
        return $this->driver->failed($id, $exception);
    }

    public function getFailedJobs(): array
    {
        return $this->driver->getFailedJobs();
    }

    public function retry(string $id): bool
    {
        return $this->driver->retry($id);
    }

    public function flushFailed(): bool
    {
        return $this->driver->flushFailed();
    }

    public function onConnection(string $connection): self
    {
        return new self($connection, $this->config);
    }

    public function onQueue(string $queue): self
    {
        $instance        = clone $this;
        $instance->queue = $queue;
        return $instance;
    }

    public function bulk(array $jobs, string $queue = 'default'): array
    {
        $ids = [];
        foreach ($jobs as $job) {
            $ids[] = $this->push($job, $queue);
        }
        return $ids;
    }

    public function worker(): Worker
    {
        return new Worker($this->driver);
    }
}
