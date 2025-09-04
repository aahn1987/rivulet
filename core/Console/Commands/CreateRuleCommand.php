<?php
namespace Rivulet\Console\Commands;

use Rivulet\Console\Command;

class CreateRuleCommand extends Command
{
    protected string $name        = 'create:rule';
    protected string $description = 'Create a new validation rule';

    public function execute(array $args): void
    {
        $name = $this->getArg($args, 0);

        if (empty($name)) {
            $this->error('Rule name is required');
            return;
        }

        $parsed        = $this->parseName($name);
        $className     = $this->studlyCase($parsed['class']);
        $namespace     = $parsed['namespace'] ? $parsed['namespace'] : 'Rules';
        $fullNamespace = "App\\{$namespace}";

        $stub    = $this->getStub('rule');
        $content = $this->replaceInStub($stub, [
            'namespace' => $fullNamespace,
            'class'     => $className,
        ]);

        $path = appPath($parsed['path'] . '/' . $className . '.php');

        if ($this->createFile($path, $content)) {
            $this->success("Rule {$className} created successfully at {$path}");
        } else {
            $this->error("Failed to create rule {$className}");
        }
    }
}
