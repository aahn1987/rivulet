<?php
namespace Rivulet\Validation\Rules;

class File
{
    public function passes($value): bool
    {
        return is_array($value) && isset($value['tmp_name']) && is_uploaded_file($value['tmp_name']);
    }

    public function message(): string
    {
        return 'This field must be a valid file.';
    }
}
