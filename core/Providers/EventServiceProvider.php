<?php
namespace Rivulet\Providers;

use Rivulet\Events\Dispatcher;

class EventServiceProvider extends ServiceProvider
{
    protected array $listen = [];

    public function register(): void
    {
        $this->bind('events', function () {
            return new Dispatcher();
        });
    }

    public function boot(): void
    {
        $this->registerEvents();
    }

    protected function registerEvents(): void
    {
        $events = $this->make('events');

        foreach ($this->listen as $event => $listeners) {
            foreach ($listeners as $listener) {
                $events->listen($event, $listener);
            }
        }
    }
}
