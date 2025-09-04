<?php
namespace Rivulet\Validation\Rules;

class Url
{
    public function passes($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    public function message(): string
    {
        return 'This field must be a valid URL.';
    }
}
