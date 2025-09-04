<?php
namespace Rivulet\Console\Commands;

use Rivulet\Console\Command;

class CreateSeederCommand extends Command
{
    protected string $name        = 'create:seeder';
    protected string $description = 'Create a new database seeder';

    public function execute(array $args): void
    {
        $name = $this->getArg($args, 0);

        if (empty($name)) {
            $this->error('Seeder name is required');
            return;
        }

        $parsed    = $this->parseName($name);
        $className = $this->studlyCase($parsed['class']) . 'Seeder';

        $stub    = $this->getStub('seeder');
        $content = $this->replaceInStub($stub, [
            'class' => $className,
        ]);

        $path = basePath('database/Seeders/' . $className . '.php');

        if ($this->createFile($path, $content)) {
            $this->success("Seeder {$className} created successfully at {$path}");
        } else {
            $this->error("Failed to create seeder {$className}");
        }
    }
}
