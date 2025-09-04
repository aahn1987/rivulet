<?php
namespace Rivulet\Validation\Rules;

class Required
{
    public function passes($value): bool
    {
        return ! is_null($value) && $value !== '';
    }

    public function message(): string
    {
        return 'This field is required.';
    }
}
