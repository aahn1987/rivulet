<?php
namespace Rivulet\Console\Commands;

use Rivulet\Console\Command;

class CreateControllerCommand extends Command
{
    protected string $name        = 'create:controller';
    protected string $description = 'Create a new controller';

    public function execute(array $args): void
    {
        $name = $this->getArg($args, 0);

        if (empty($name)) {
            $this->error('Controller name is required');
            return;
        }

        $parsed        = $this->parseName($name);
        $className     = $this->studlyCase($parsed['class']) . 'Controller';
        $namespace     = $parsed['namespace'] ? $parsed['namespace'] : 'Controllers';
        $fullNamespace = "App\\{$namespace}";

        $stub    = $this->getStub('controller');
        $content = $this->replaceInStub($stub, [
            'namespace' => $fullNamespace,
            'class'     => $className,
        ]);

        $path = appPath($parsed['path'] . '/' . $className . '.php');

        if ($this->createFile($path, $content)) {
            $this->success("Controller {$className} created successfully at {$path}");
        } else {
            $this->error("Failed to create controller {$className}");
        }
    }
}
