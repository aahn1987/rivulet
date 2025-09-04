<?php
namespace Rivulet\Events;

abstract class Listener
{
    abstract public function handle(Event $event): void;

    public function shouldQueue(): bool
    {
        return false;
    }

    public function shouldStop(Event $event): bool
    {
        return false;
    }

    public function priority(): int
    {
        return 0;
    }
}
