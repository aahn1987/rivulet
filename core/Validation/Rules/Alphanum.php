<?php
namespace Rivulet\Validation\Rules;

class Alphanum
{
    public function passes($value): bool
    {
        return is_string($value) && preg_match('/^[a-zA-Z0-9]+$/', $value);
    }

    public function message(): string
    {
        return 'This field may only contain letters and numbers.';
    }
}
