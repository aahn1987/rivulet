<?php
namespace Rivulet\Validation\Rules;

class Bool
{
    public function passes($value): bool
    {
        return is_bool($value) || in_array($value, [0, 1, '0', '1', 'true', 'false']);
    }

    public function message(): string
    {
        return 'This field must be a boolean.';
    }
}
