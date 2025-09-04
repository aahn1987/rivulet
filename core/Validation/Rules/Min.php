<?php
namespace Rivulet\Validation\Rules;

class Min
{
    private int $min;

    public function __construct($min)
    {
        $this->min = (int) $min;
    }

    public function passes($value): bool
    {
        if (is_string($value)) {
            return strlen($value) >= $this->min;
        }

        if (is_numeric($value)) {
            return $value >= $this->min;
        }

        if (is_array($value)) {
            return count($value) >= $this->min;
        }

        return false;
    }

    public function message(): string
    {
        return "This field must be at least {$this->min}.";
    }
}
