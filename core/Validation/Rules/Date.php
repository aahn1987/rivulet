<?php
namespace Rivulet\Validation\Rules;

class Date
{
    public function passes($value): bool
    {
        return strtotime($value) !== false;
    }

    public function message(): string
    {
        return 'This field must be a valid date.';
    }
}
