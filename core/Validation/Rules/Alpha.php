<?php
namespace Rivulet\Validation\Rules;

class Alpha
{
    public function passes($value): bool
    {
        return is_string($value) && preg_match('/^[a-zA-Z]+$/', $value);
    }

    public function message(): string
    {
        return 'This field may only contain letters.';
    }
}
