<?php
namespace Rivulet\Queue;

class CallableJob extends Job
{
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function handle(): void
    {
        call_user_func($this->callback);
    }
}
