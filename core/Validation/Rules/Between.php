<?php
namespace Rivulet\Validation\Rules;

class Between
{
    private int $min;
    private int $max;

    public function __construct($params)
    {
        [$this->min, $this->max] = array_map('intval', explode(',', $params));
    }

    public function passes($value): bool
    {
        if (is_string($value)) {
            return strlen($value) >= $this->min && strlen($value) <= $this->max;
        }

        if (is_numeric($value)) {
            return $value >= $this->min && $value <= $this->max;
        }

        if (is_array($value)) {
            return count($value) >= $this->min && count($value) <= $this->max;
        }

        return false;
    }

    public function message(): string
    {
        return "This field must be between {$this->min} and {$this->max}.";
    }
}
