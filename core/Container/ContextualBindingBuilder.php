<?php
namespace Rivulet\Container;

class ContextualBindingBuilder
{
    private Container $container;
    private string $concrete;
    private string $needs;
    private string $abstract;

    public function __construct(Container $container, string $concrete)
    {
        $this->container = $container;
        $this->concrete  = $concrete;
    }

    public function needs(string $abstract): self
    {
        $this->needs = $abstract;
        return $this;
    }

    public function give($implementation): void
    {
        $this->container->when($this->concrete)->needs($this->needs)->give($implementation);
    }

    public function giveTagged(string $tag): void
    {
        $this->give(function () use ($tag) {
            return $this->container->tagged($tag);
        });
    }
}
