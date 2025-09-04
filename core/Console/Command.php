<?php
namespace Rivulet\Console;

abstract class Command
{
    protected string $name;
    protected string $description;
    protected array $arguments = [];
    protected array $options   = [];

    abstract public function execute(array $args): void;

    protected function info(string $message): void
    {
        echo "\033[32m[INFO]\033[0m {$message}\n";
    }

    protected function error(string $message): void
    {
        echo "\033[31m[ERROR]\033[0m {$message}\n";
    }

    protected function warning(string $message): void
    {
        echo "\033[33m[WARNING]\033[0m {$message}\n";
    }

    protected function success(string $message): void
    {
        echo "\033[32m[SUCCESS]\033[0m {$message}\n";
    }

    protected function line(string $message): void
    {
        echo $message . "\n";
    }

    protected function ask(string $question, string $default = ''): string
    {
        echo "\033[36m[QUESTION]\033[0m {$question} ";
        $answer = trim(fgets(STDIN));
        return $answer ?: $default;
    }

    protected function confirm(string $question, bool $default = false): bool
    {
        $defaultText = $default ? 'Y/n' : 'y/N';
        $answer      = $this->ask("{$question} ({$defaultText})");

        if (empty($answer)) {
            return $default;
        }

        return strtolower($answer) === 'y';
    }

    protected function getArg(array $args, int $index, string $default = ''): string
    {
        return $args[$index] ?? $default;
    }

    protected function getOption(array $args, string $option, string $default = ''): string
    {
        $key = array_search("--{$option}", $args);
        if ($key !== false && isset($args[$key + 1])) {
            return $args[$key + 1];
        }
        return $default;
    }

    protected function hasOption(array $args, string $option): bool
    {
        return in_array("--{$option}", $args) || in_array("-{$option}", $args);
    }

    protected function createFile(string $path, string $content): bool
    {
        $dir = dirname($path);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (file_exists($path) && ! $this->confirm("File {$path} already exists. Overwrite?")) {
            return false;
        }

        return file_put_contents($path, $content) !== false;
    }

    protected function getStub(string $name): string
    {
        $stubPath = __DIR__ . "/stubs/{$name}.stub";
        return file_get_contents($stubPath) ?: '';
    }

    protected function replaceInStub(string $stub, array $replacements): string
    {
        foreach ($replacements as $search => $replace) {
            $stub = str_replace("{{{$search}}}", $replace, $stub);
        }
        return $stub;
    }

    protected function parseName(string $name): array
    {
        $parts     = explode('/', $name);
        $className = array_pop($parts);
        $namespace = implode('\\', $parts);

        return [
            'class'     => $className,
            'namespace' => $namespace,
            'path'      => implode('/', $parts),
        ];
    }

    protected function studlyCase(string $value): string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $value)));
    }

    protected function camelCase(string $value): string
    {
        return lcfirst($this->studlyCase($value));
    }

    protected function snakeCase(string $value): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $value));
    }
}
