<?php
namespace Rivulet\Validation\Rules;

class Ip
{
    public function passes($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }

    public function message(): string
    {
        return 'This field must be a valid IP address.';
    }
}
