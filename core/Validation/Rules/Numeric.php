<?php
namespace Rivulet\Validation\Rules;

class Numeric
{
    public function passes($value): bool
    {
        return is_numeric($value);
    }

    public function message(): string
    {
        return 'This field must be numeric.';
    }
}
