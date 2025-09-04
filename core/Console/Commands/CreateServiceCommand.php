<?php
namespace Rivulet\Console\Commands;

use Rivulet\Console\Command;

class CreateServiceCommand extends Command
{
    protected string $name        = 'create:service';
    protected string $description = 'Create a new service';

    public function execute(array $args): void
    {
        $name = $this->getArg($args, 0);

        if (empty($name)) {
            $this->error('Service name is required');
            return;
        }

        $parsed        = $this->parseName($name);
        $className     = $this->studlyCase($parsed['class']) . 'Service';
        $namespace     = $parsed['namespace'] ? $parsed['namespace'] : 'Services';
        $fullNamespace = "App\\{$namespace}";

        $stub    = $this->getStub('service');
        $content = $this->replaceInStub($stub, [
            'namespace' => $fullNamespace,
            'class'     => $className,
        ]);

        $path = appPath($parsed['path'] . '/' . $className . '.php');

        if ($this->createFile($path, $content)) {
            $this->success("Service {$className} created successfully at {$path}");
        } else {
            $this->error("Failed to create service {$className}");
        }
    }
}
