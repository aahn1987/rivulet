<?php
namespace Rivulet\Queue;

use Rivulet\Queue\Drivers\QueueDriverInterface;

class Worker
{
    private QueueDriverInterface $driver;
    private bool $shouldStop = false;
    private int $sleep       = 3;
    private int $maxTries    = 3;
    private int $timeout     = 60;

    public function __construct(QueueDriverInterface $driver)
    {
        $this->driver = $driver;
    }

    public function daemon(string $queue = 'default'): void
    {
        $this->registerSignalHandlers();

        while (! $this->shouldStop) {
            $this->runNextJob($queue);

            if (! $this->shouldStop) {
                sleep($this->sleep);
            }
        }
    }

    public function runNextJob(string $queue = 'default'): void
    {
        $job = $this->driver->pop($queue);

        if (! $job) {
            return;
        }

        $this->process($job);
    }

    private function process(Job $job): void
    {
        try {
            $job->incrementAttempts();

            if ($job->getAttempts() > $job->getMaxAttempts()) {
                $this->fail($job, new \RuntimeException('Max attempts exceeded'));
                return;
            }

            $startTime = microtime(true);

            $job->handle();

            $this->driver->delete($job->getId());

            $duration = round((microtime(true) - $startTime) * 1000, 2);
            logs()->info("Job processed successfully", [
                'job_id'      => $job->getId(),
                'attempts'    => $job->getAttempts(),
                'duration_ms' => $duration,
            ]);

        } catch (\Throwable $e) {
            $this->handleException($job, $e);
        }
    }

    private function handleException(Job $job, \Throwable $exception): void
    {
        logs()->error("Job failed", [
            'job_id'   => $job->getId(),
            'attempts' => $job->getAttempts(),
            'error'    => $exception->getMessage(),
            'trace'    => $exception->getTraceAsString(),
        ]);

        if ($job->getAttempts() >= $job->getMaxAttempts()) {
            $this->fail($job, $exception);
        } else {
            $this->release($job, $exception);
        }
    }

    private function fail(Job $job, \Throwable $exception): void
    {
        $job->failed($exception);
        $this->driver->failed($job->getId(), $exception);
    }

    private function release(Job $job, \Throwable $exception): void
    {
        $delay = $this->calculateDelay($job->getAttempts());
        $this->driver->release($job->getId(), $delay);
    }

    private function calculateDelay(int $attempts): int
    {
        return min($attempts * $attempts * 10, 3600);
    }

    private function registerSignalHandlers(): void
    {
        if (extension_loaded('pcntl')) {
            pcntl_signal(SIGTERM, [$this, 'stop']);
            pcntl_signal(SIGINT, [$this, 'stop']);
        }
    }

    public function stop(): void
    {
        $this->shouldStop = true;
        logs()->info("Worker stopping");
    }

    public function setSleep(int $seconds): self
    {
        $this->sleep = $seconds;
        return $this;
    }

    public function setMaxTries(int $tries): self
    {
        $this->maxTries = $tries;
        return $this;
    }

    public function setTimeout(int $seconds): self
    {
        $this->timeout = $seconds;
        return $this;
    }
}
