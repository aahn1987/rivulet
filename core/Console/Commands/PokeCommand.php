<?php
namespace Rivulet\Console\Commands;

use Rivulet\Console\Command;

class PokeCommand extends Command
{
    protected string $name        = 'poke';
    protected string $description = 'Interactive shell (REPL)';

    public function execute(array $args): void
    {
        $this->info('Rivulet Interactive Shell (Poke)');
        $this->info('Type "exit" or "quit" to leave');
        $this->line('');

        while (true) {
            $input = $this->ask('poke');

            if (in_array(strtolower($input), ['exit', 'quit'])) {
                $this->info('Goodbye!');
                break;
            }

            if (empty($input)) {
                continue;
            }

            try {
                $result = eval($input . ';');
                if ($result !== null) {
                    $this->line("=> " . print_r($result, true));
                }
            } catch (\ParseError $e) {
                try {
                    eval('return ' . $input . ';');
                } catch (\ParseError $e2) {
                    $this->error("Parse error: " . $e->getMessage());
                }
            } catch (\Exception $e) {
                $this->error("Error: " . $e->getMessage());
            }

            $this->line('');
        }
    }
}
