<?php
namespace Rivulet\Console\Commands;

use Rivulet\Console\Command;

class CreateMultiCommand extends Command
{
    protected string $name        = 'create';
    protected string $description = 'Create multiple components at once';

    public function execute(array $args): void
    {
        $options = $this->getArg($args, 0, '');
        $name    = $this->getArg($args, 1);

        if (empty($name)) {
            $this->error('Component name is required');
            return;
        }

        if (empty($options)) {
            $this->error('Options are required (e.g., -mcster)');
            return;
        }

        $options = ltrim($options, '-');
        $created = [];

        foreach (str_split($options) as $option) {
            switch ($option) {
                case 'm':
                    $this->createModel($name);
                    $created[] = 'model';
                    break;
                case 'c':
                    $this->createController($name);
                    $created[] = 'controller';
                    break;
                case 's':
                    $this->createService($name);
                    $created[] = 'service';
                    break;
                case 't':
                    $this->createTemplate($name);
                    $created[] = 'template';
                    break;
                case 'e':
                    $this->createEvent($name);
                    $created[] = 'event';
                    break;
                case 'r':
                    $this->createResource($name);
                    $created[] = 'resource';
                    break;
            }
        }

        $this->success("Created: " . implode(', ', $created));
    }

    private function createModel(string $name): void
    {
        $command = new CreateModelCommand();
        $command->execute([$name]);
    }

    private function createController(string $name): void
    {
        $command = new CreateControllerCommand();
        $command->execute([$name]);
    }

    private function createService(string $name): void
    {
        $command = new CreateServiceCommand();
        $command->execute([$name]);
    }

    private function createTemplate(string $name): void
    {
        $command = new CreateTemplateCommand();
        $command->execute([$name]);
    }

    private function createEvent(string $name): void
    {
        $command = new CreateEventCommand();
        $command->execute([$name]);
    }

    private function createResource(string $name): void
    {
        $command = new CreateResourceCommand();
        $command->execute([$name]);
    }
}
