<?php
namespace Rivulet\Validation\Rules;

class Max
{
    private int $max;

    public function __construct($max)
    {
        $this->max = (int) $max;
    }

    public function passes($value): bool
    {
        if (is_string($value)) {
            return strlen($value) <= $this->max;
        }

        if (is_numeric($value)) {
            return $value <= $this->max;
        }

        if (is_array($value)) {
            return count($value) <= $this->max;
        }

        return false;
    }

    public function message(): string
    {
        return "This field must not exceed {$this->max}.";
    }
}
