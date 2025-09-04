<?php
namespace Rivulet\Events;

class Dispatcher
{
    private array $listeners = [];
    private array $sorted    = [];
    private array $queue     = [];

    public function listen(string $event, $listener, int $priority = 0): void
    {
        $this->listeners[$event][$priority][] = $listener;
        unset($this->sorted[$event]);
    }

    public function dispatch(string $event, array $data = []): Event
    {
        $eventObj = new GenericEvent($event, $data);

        $this->fire($eventObj);

        return $eventObj;
    }

    public function fire(Event $event): void
    {
        $eventName = $event->getName();

        if (! isset($this->listeners[$eventName])) {
            return;
        }

        $listeners = $this->getEventListeners($eventName);

        foreach ($listeners as $listener) {
            if ($event->isPropagationStopped()) {
                break;
            }

            $this->callListener($listener, $event);
        }
    }

    private function getEventListeners(string $event): array
    {
        if (! isset($this->sorted[$event])) {
            $this->sortListeners($event);
        }

        return $this->sorted[$event];
    }

    private function sortListeners(string $event): void
    {
        $this->sorted[$event] = [];

        if (isset($this->listeners[$event])) {
            krsort($this->listeners[$event]);

            foreach ($this->listeners[$event] as $priority => $listeners) {
                foreach ($listeners as $listener) {
                    $this->sorted[$event][] = $listener;
                }
            }
        }
    }

    private function callListener($listener, Event $event): void
    {
        if (is_string($listener) && class_exists($listener)) {
            $listener = new $listener();
        }

        if (is_object($listener) && method_exists($listener, 'handle')) {
            if ($listener instanceof Listener && $listener->shouldQueue()) {
                $this->queueListener($listener, $event);
            } else {
                $listener->handle($event);
            }
        } elseif (is_callable($listener)) {
            call_user_func($listener, $event);
        }
    }

    private function queueListener(Listener $listener, Event $event): void
    {
        dispatch(function () use ($listener, $event) {
            $listener->handle($event);
        });
    }

    public function subscribe(string $subscriber): void
    {
        if (is_string($subscriber) && class_exists($subscriber)) {
            $subscriber = new $subscriber();
        }

        if (method_exists($subscriber, 'subscribe')) {
            $subscriber->subscribe($this);
        }
    }

    public function until(string $event, array $data = []): ?Event
    {
        return $this->dispatch($event, $data);
    }

    public function flush(string $event): void
    {
        unset($this->listeners[$event]);
        unset($this->sorted[$event]);
    }

    public function forget(string $event): void
    {
        $this->flush($event);
    }

    public function getListeners(string $event): array
    {
        return $this->getEventListeners($event);
    }

    public function hasListeners(string $event): bool
    {
        return isset($this->listeners[$event]) && ! empty($this->listeners[$event]);
    }
}
