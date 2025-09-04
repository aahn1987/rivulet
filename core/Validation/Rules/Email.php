<?php
namespace Rivulet\Validation\Rules;

class Email
{
    public function passes($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function message(): string
    {
        return 'This field must be a valid email address.';
    }
}
