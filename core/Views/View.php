<?php
namespace Rivulet\Views;

class View
{
    private Engine $engine;
    private array $data       = [];
    private ?string $template = null;

    public function __construct(Engine $engine = null)
    {
        $this->engine = $engine ?? new Engine();
    }

    public function render(string $template, array $data = []): string
    {
        return $this->engine->render($template, array_merge($this->data, $data));
    }

    public function make(string $template, array $data = []): self
    {
        $this->data     = array_merge($this->data, $data);
        $this->template = $template;

        return $this;
    }

    public function with(string $key, $value): self
    {
        $this->data[$key] = $value;

        return $this;
    }

    public function share(string $key, $value): void
    {
        $this->engine->share($key, $value);
    }

    public function __toString(): string
    {
        if ($this->template === null) {
            return '';
        }

        return $this->render($this->template, $this->data);
    }
}
