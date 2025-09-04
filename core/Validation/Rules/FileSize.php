<?php
namespace Rivulet\Validation\Rules;

class FileSize
{
    private int $maxSize;

    public function __construct($maxSize)
    {
        $this->maxSize = (int) $maxSize * 1024; // Convert KB to bytes
    }

    public function passes($value): bool
    {
        if (! is_array($value) || ! isset($value['size'])) {
            return false;
        }

        return $value['size'] <= $this->maxSize;
    }

    public function message(): string
    {
        return "The file must be less than {$this->maxSize} bytes.";
    }
}
