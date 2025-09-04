<?php
namespace Rivulet\Events;

class GenericEvent extends Event
{
    public function __construct(string $name, array $data = [])
    {
        parent::__construct($name, $data);
    }
}
