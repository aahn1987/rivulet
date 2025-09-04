<?php
namespace Rivulet\Views;

class Engine
{
    private string $templatePath;
    private array $shared          = [];
    private array $sections        = [];
    private string $currentSection = '';
    private array $stacks          = [];

    public function __construct(string $templatePath = null)
    {
        $this->templatePath = $templatePath ?? resource_path('views');
    }

    public function render(string $template, array $data = []): string
    {
        $templatePath = $this->resolveTemplate($template);

        if (! $templatePath) {
            throw new \RuntimeException("Template not found: {$template}");
        }

        $data = array_merge($this->shared, $data);

        return $this->evaluate($templatePath, $data);
    }

    private function resolveTemplate(string $template): ?string
    {
        $template = str_replace('.', '/', $template);

        $paths = [
            $this->templatePath . '/' . $template . '.html',
            $this->templatePath . '/' . $template . '.php',
            $this->templatePath . '/' . $template,
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    private function evaluate(string $__path, array $__data): string
    {
        extract($__data);

        ob_start();

        include $__path;

        return ob_get_clean();
    }

    public function share(string $key, $value): void
    {
        $this->shared[$key] = $value;
    }

    public function startSection(string $name): void
    {
        $this->currentSection = $name;
        ob_start();
    }

    public function endSection(): void
    {
        $content = ob_get_clean();

        if (! isset($this->sections[$this->currentSection])) {
            $this->sections[$this->currentSection] = '';
        }

        $this->sections[$this->currentSection] .= $content;
        $this->currentSection = '';
    }

    public function yield (string $name, string $default = ''): string
    {
        return $this->sections[$name] ?? $default;
    }

    public function include(string $template, array $data = []): string
    {
        return $this->render($template, $data);
    }

    public function extend(string $template): void
    {
        $this->sections['@parent'] = $template;
    }

    public function parent(): string
    {
        return $this->sections['@parent'] ?? '';
    }

    public function stack(string $name, string $content = null): void
    {
        if ($content === null) {
            echo $this->stacks[$name] ?? '';
            return;
        }

        if (! isset($this->stacks[$name])) {
            $this->stacks[$name] = '';
        }

        $this->stacks[$name] .= $content;
    }

    public function push(string $name): void
    {
        ob_start();
        $this->currentSection = $name;
    }

    public function endPush(): void
    {
        $content = ob_get_clean();
        $this->stack($this->currentSection, $content);
        $this->currentSection = '';
    }

    public function escape(string $value, int $flags = ENT_QUOTES, string $encoding = 'UTF-8'): string
    {
        return htmlspecialchars($value, $flags, $encoding);
    }

    public function e(string $value): string
    {
        return $this->escape($value);
    }

    public function raw(string $value): string
    {
        return $value;
    }
}
