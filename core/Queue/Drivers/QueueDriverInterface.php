<?php
namespace Rivulet\Queue\Drivers;

use Rivulet\Queue\Job;

interface QueueDriverInterface
{
    public function push($job, string $queue = 'default'): string;
    public function later(int $delay, $job, string $queue = 'default'): string;
    public function pop(string $queue = 'default'): ?Job;
    public function size(string $queue = 'default'): int;
    public function clear(string $queue = 'default'): bool;
    public function release(string $id, int $delay = 0): bool;
    public function delete(string $id): bool;
    public function failed(string $id, \Throwable $exception): bool;
    public function getFailedJobs(): array;
    public function retry(string $id): bool;
    public function flushFailed(): bool;
}
