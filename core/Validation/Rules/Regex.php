<?php
namespace Rivulet\Validation\Rules;

class Regex
{
    private string $pattern;

    public function __construct($pattern)
    {
        $this->pattern = $pattern;
    }

    public function passes($value): bool
    {
        return preg_match($this->pattern, $value) === 1;
    }

    public function message(): string
    {
        return 'This field format is invalid.';
    }
}
