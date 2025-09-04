<?php
namespace Rivulet\Console\Commands;

use Rivulet\Console\Command;

class CreateResourceCommand extends Command
{
    protected string $name        = 'create:resource';
    protected string $description = 'Create a new migration resource';

    public function execute(array $args): void
    {
        $name = $this->getArg($args, 0);

        if (empty($name)) {
            $this->error('Resource name is required');
            return;
        }

        $parsed    = $this->parseName($name);
        $className = 'Create' . $this->studlyCase($parsed['class']) . 'Table';

        $stub    = $this->getStub('migration');
        $content = $this->replaceInStub($stub, [
            'class' => $className,
            'table' => $this->snakeCase($parsed['class']) . 's',
        ]);

        $timestamp = date('Y_m_d_His');
        $fileName  = $timestamp . '_' . $this->snakeCase($className) . '.php';
        $path      = basePath('database/Migrations/' . $fileName);

        if ($this->createFile($path, $content)) {
            $this->success("Migration {$fileName} created successfully at {$path}");
        } else {
            $this->error("Failed to create migration {$fileName}");
        }
    }
}
