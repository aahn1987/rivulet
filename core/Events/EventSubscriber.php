<?php
namespace Rivulet\Events;

abstract class EventSubscriber
{
    abstract public function subscribe(Dispatcher $dispatcher): void;
}
