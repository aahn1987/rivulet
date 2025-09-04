<?php
namespace Rivulet\Console\Commands;

use Rivulet\Console\Command;

class CreateMiddlewareCommand extends Command
{
    protected string $name        = 'create:middleware';
    protected string $description = 'Create a new middleware';

    public function execute(array $args): void
    {
        $name = $this->getArg($args, 0);

        if (empty($name)) {
            $this->error('Middleware name is required');
            return;
        }

        $parsed        = $this->parseName($name);
        $className     = $this->studlyCase($parsed['class']);
        $namespace     = $parsed['namespace'] ? $parsed['namespace'] : 'Middleware';
        $fullNamespace = "App\\{$namespace}";

        $stub    = $this->getStub('middleware');
        $content = $this->replaceInStub($stub, [
            'namespace' => $fullNamespace,
            'class'     => $className,
        ]);

        $path = appPath($parsed['path'] . '/' . $className . '.php');

        if ($this->createFile($path, $content)) {
            $this->success("Middleware {$className} created successfully at {$path}");
        } else {
            $this->error("Failed to create middleware {$className}");
        }
    }
}
