<?php
namespace Rivulet\Validation;

class Validator
{
    private array $data;
    private array $rules;
    private array $messages;
    private array $errors    = [];
    private array $validated = [];

    public function __construct(array $data, array $rules, array $messages = [])
    {
        $this->data     = $data;
        $this->rules    = $rules;
        $this->messages = $messages;
    }

    public function validate(): bool
    {
        $this->errors    = [];
        $this->validated = [];

        foreach ($this->rules as $field => $rules) {
            $rulesArray = is_string($rules) ? explode('|', $rules) : $rules;
            $value      = $this->data[$field] ?? null;

            foreach ($rulesArray as $rule) {
                $this->validateRule($field, $value, $rule);
            }
        }

        if (empty($this->errors)) {
            $this->validated = $this->data;
        }

        return empty($this->errors);
    }

    private function validateRule(string $field, $value, string $rule): void
    {
        [$ruleName, $ruleValue] = $this->parseRule($rule);

        $ruleClass = $this->getRuleClass($ruleName);
        if (! $ruleClass) {
            return;
        }

        $ruleInstance = new $ruleClass($ruleValue);

        if (! $ruleInstance->passes($value)) {
            $this->addError($field, $ruleName, $ruleInstance->message());
        }
    }

    private function parseRule(string $rule): array
    {
        if (strpos($rule, ':') !== false) {
            return explode(':', $rule, 2);
        }

        return [$rule, null];
    }

    private function getRuleClass(string $ruleName): ?string
    {
        $classMap = [
            'required' => \Rivulet\Validation\Rules\Required::class,
            'email'    => \Rivulet\Validation\Rules\Email::class,
            'string'   => \Rivulet\Validation\Rules\StringRule::class,
            'integer'  => \Rivulet\Validation\Rules\Integer::class,
            'numeric'  => \Rivulet\Validation\Rules\Numeric::class,
            'boolean'  => \Rivulet\Validation\Rules\Bool::class,
            'array'    => \Rivulet\Validation\Rules\Arr::class,
            'file'     => \Rivulet\Validation\Rules\File::class,
            'filesize' => \Rivulet\Validation\Rules\FileSize::class,
            'min'      => \Rivulet\Validation\Rules\Min::class,
            'max'      => \Rivulet\Validation\Rules\Max::class,
            'between'  => \Rivulet\Validation\Rules\Between::class,
            'alpha'    => \Rivulet\Validation\Rules\Alpha::class,
            'alphanum' => \Rivulet\Validation\Rules\Alphanum::class,
            'url'      => \Rivulet\Validation\Rules\Url::class,
            'ip'       => \Rivulet\Validation\Rules\Ip::class,
            'date'     => \Rivulet\Validation\Rules\Date::class,
            'regex'    => \Rivulet\Validation\Rules\Regex::class,
            'unique'   => \Rivulet\Validation\Rules\Unique::class,
            'exists'   => \Rivulet\Validation\Rules\Exists::class,
        ];

        return $classMap[$ruleName] ?? null;
    }

    private function addError(string $field, string $rule, string $message): void
    {
        if (! isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }

        $this->errors[$field][] = $message;
    }

    public function fails(): bool
    {
        return ! $this->validate();
    }

    public function passes(): bool
    {
        return $this->validate();
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function validated(): array
    {
        return $this->validated;
    }

    public function first(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    public function has(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    public function addCustomRule(string $name, string $class): void
    {
        // Allow custom rules registration
    }
}
