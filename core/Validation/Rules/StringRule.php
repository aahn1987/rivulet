<?php
namespace Rivulet\Validation\Rules;

class StringRule
{
    public function passes($value): bool
    {
        return is_string($value);
    }

    public function message(): string
    {
        return 'This field must be a string.';
    }
}
