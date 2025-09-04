<?php
namespace Rivulet\Validation\Rules;

class Integer
{
    public function passes($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    public function message(): string
    {
        return 'This field must be an integer.';
    }
}
