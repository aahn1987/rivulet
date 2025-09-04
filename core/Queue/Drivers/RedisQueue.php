<?php
namespace Rivulet\Queue\Drivers;

use Rivulet\Queue\CallableJob;
use Rivulet\Queue\Job;

class RedisQueue implements QueueDriverInterface
{
    private \Redis $redis;
    private string $connection;

    public function __construct(array $config)
    {
        $this->redis = new \Redis();
        $this->redis->connect(
            $config['host'] ?? '127.0.0.1',
            $config['port'] ?? 6379
        );

        if (isset($config['password'])) {
            $this->redis->auth($config['password']);
        }

        if (isset($config['db'])) {
            $this->redis->select($config['db']);
        }

        $this->connection = $config['connection'] ?? 'default';
    }

    public function push($job, string $queue = 'default'): string
    {
        return $this->pushRaw($this->createPayload($job), $queue);
    }

    public function later(int $delay, $job, string $queue = 'default'): string
    {
        return $this->laterRaw(time() + $delay, $this->createPayload($job), $queue);
    }

    private function pushRaw(string $payload, string $queue): string
    {
        $id = strRandom(32);

        $this->redis->rpush("queues:{$queue}", json_encode([
            'id'       => $id,
            'payload'  => $payload,
            'attempts' => 0,
        ]));

        return $id;
    }

    private function laterRaw(int $timestamp, string $payload, string $queue): string
    {
        $id = strRandom(32);

        $this->redis->zadd("queues:{$queue}:delayed", $timestamp, json_encode([
            'id'       => $id,
            'payload'  => $payload,
            'attempts' => 0,
        ]));

        return $id;
    }

    private function createPayload($job): string
    {
        if ($job instanceof Job) {
            return serialize($job);
        }

        if (is_callable($job)) {
            return serialize(new CallableJob($job));
        }

        return serialize($job);
    }

    public function pop(string $queue = 'default'): ?Job
    {
        $this->migrateDelayedJobs($queue);

        $data = $this->redis->lpop("queues:{$queue}");

        if (! $data) {
            return null;
        }

        $jobData = json_decode($data, true);
        $job     = unserialize($jobData['payload']);

        $job->setId($jobData['id']);
        $job->setQueue($queue);
        $job->setAttempts($jobData['attempts'] + 1);

        $this->redis->zadd("queues:{$queue}:reserved", time() + 3600, json_encode($jobData));

        return $job;
    }

    private function migrateDelayedJobs(string $queue): void
    {
        $jobs = $this->redis->zrangebyscore("queues:{$queue}:delayed", 0, time());

        foreach ($jobs as $job) {
            $this->redis->zrem("queues:{$queue}:delayed", $job);
            $this->redis->rpush("queues:{$queue}", $job);
        }
    }

    public function size(string $queue = 'default'): int
    {
        return $this->redis->llen("queues:{$queue}");
    }

    public function clear(string $queue = 'default'): bool
    {
        $this->redis->del("queues:{$queue}");
        $this->redis->del("queues:{$queue}:delayed");
        $this->redis->del("queues:{$queue}:reserved");

        return true;
    }

    public function release(string $id, int $delay = 0): bool
    {
        // Implementation for releasing jobs back to queue
        return true;
    }

    public function delete(string $id): bool
    {
        // Redis doesn't need explicit deletion as jobs are popped
        return true;
    }

    public function failed(string $id, \Throwable $exception): bool
    {
        $this->redis->zadd("queues:failed", time(), json_encode([
            'id'        => $id,
            'exception' => $exception->getMessage(),
            'failed_at' => time(),
        ]));

        return true;
    }

    public function getFailedJobs(): array
    {
        $jobs = $this->redis->zrange("queues:failed", 0, -1);

        return array_map(fn($job) => json_decode($job, true), $jobs);
    }

    public function retry(string $id): bool
    {
        // Implementation for retrying failed jobs
        return true;
    }

    public function flushFailed(): bool
    {
        return $this->redis->del("queues:failed") > 0;
    }
}
