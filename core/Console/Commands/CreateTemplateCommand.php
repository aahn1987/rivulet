<?php
namespace Rivulet\Console\Commands;

use Rivulet\Console\Command;

class CreateTemplateCommand extends Command
{
    protected string $name        = 'create:template';
    protected string $description = 'Create a new template';

    public function execute(array $args): void
    {
        $name = $this->getArg($args, 0);

        if (empty($name)) {
            $this->error('Template name is required');
            return;
        }

        $parsed   = $this->parseName($name);
        $fileName = $parsed['class'] . '.html';

        $stub    = $this->getStub('template');
        $content = $this->replaceInStub($stub, [
            'title' => $this->studlyCase($parsed['class']),
        ]);

        $path = resourceLocation('views/' . $parsed['path'] . '/' . $fileName);

        if ($this->createFile($path, $content)) {
            $this->success("Template {$fileName} created successfully at {$path}");
        } else {
            $this->error("Failed to create template {$fileName}");
        }
    }
}
