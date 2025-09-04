<?php
namespace Rivulet\Console\Commands;

use Rivulet\Console\Command;

class CreateModelCommand extends Command
{
    protected string $name        = 'create:model';
    protected string $description = 'Create a new model';

    public function execute(array $args): void
    {
        $name = $this->getArg($args, 0);

        if (empty($name)) {
            $this->error('Model name is required');
            return;
        }

        $parsed        = $this->parseName($name);
        $className     = $this->studlyCase($parsed['class']);
        $namespace     = $parsed['namespace'] ? $parsed['namespace'] : 'Models';
        $fullNamespace = "App\\{$namespace}";

        $stub    = $this->getStub('model');
        $content = $this->replaceInStub($stub, [
            'namespace' => $fullNamespace,
            'class'     => $className,
            'table'     => $this->snakeCase($parsed['class']) . 's',
        ]);

        $path = appPath($parsed['path'] . '/' . $className . '.php');

        if ($this->createFile($path, $content)) {
            $this->success("Model {$className} created successfully at {$path}");
        } else {
            $this->error("Failed to create model {$className}");
        }
    }
}
