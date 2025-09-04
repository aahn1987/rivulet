<?php
namespace Rivulet\Events;

abstract class Event
{
    protected string $name;
    protected array $data              = [];
    protected bool $propagationStopped = false;

    public function __construct(string $name, array $data = [])
    {
        $this->name = $name;
        $this->data = $data;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getDataItem(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }

    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }
}
