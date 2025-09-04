<?php
namespace Rivulet\Validation\Rules;

class Arr
{
    public function passes($value): bool
    {
        return is_array($value);
    }

    public function message(): string
    {
        return 'This field must be an array.';
    }
}
