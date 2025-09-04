<?php
namespace Rivulet\Queue;

abstract class Job
{
    protected string $id;
    protected string $queue;
    protected int $attempts    = 0;
    protected int $maxAttempts = 3;
    protected int $timeout     = 60;
    protected int $delay       = 0;

    abstract public function handle(): void;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getQueue(): string
    {
        return $this->queue;
    }

    public function setQueue(string $queue): self
    {
        $this->queue = $queue;
        return $this;
    }

    public function getAttempts(): int
    {
        return $this->attempts;
    }

    public function setAttempts(int $attempts): self
    {
        $this->attempts = $attempts;
        return $this;
    }

    public function incrementAttempts(): self
    {
        $this->attempts++;
        return $this;
    }

    public function getMaxAttempts(): int
    {
        return $this->maxAttempts;
    }

    public function setMaxAttempts(int $maxAttempts): self
    {
        $this->maxAttempts = $maxAttempts;
        return $this;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }

    public function getDelay(): int
    {
        return $this->delay;
    }

    public function setDelay(int $delay): self
    {
        $this->delay = $delay;
        return $this;
    }

    public function failed(\Throwable $exception): void
    {
        // Override in child classes
    }

    public function serialize(): string
    {
        return serialize($this);
    }

    public static function unserialize(string $serialized): self
    {
        return unserialize($serialized);
    }
}
