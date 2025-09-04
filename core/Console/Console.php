<?php
namespace Rivulet\Console;

class Console
{
    private array $commands = [];
    private array $aliases  = [];

    public function __construct()
    {
        $this->registerCommands();
    }

    private function registerCommands(): void
    {
        $this->commands = [
            'run'               => Commands\RunServerCommand::class,
            'create:model'      => Commands\CreateModelCommand::class,
            'create:controller' => Commands\CreateControllerCommand::class,
            'create:service'    => Commands\CreateServiceCommand::class,
            'create:template'   => Commands\CreateTemplateCommand::class,
            'create:event'      => Commands\CreateEventCommand::class,
            'create:rule'       => Commands\CreateRuleCommand::class,
            'create:resource'   => Commands\CreateResourceCommand::class,
            'create:seeder'     => Commands\CreateSeederCommand::class,
            'create:middleware' => Commands\CreateMiddlewareCommand::class,
            'create'            => Commands\CreateMultiCommand::class,
            'migrate'           => Commands\DatabaseMigrateCommand::class,
            'seed'              => Commands\DatabaseSeedCommand::class,
            'rollback'          => Commands\DatabaseRollbackCommand::class,
            'ws:serve'          => Commands\WebSocketServeCommand::class,
            'poke'              => Commands\PokeCommand::class,
            'cache:clear'       => Commands\CacheClearCommand::class,
            'logs:clear'        => Commands\LogsClearCommand::class,
            'config:cache'      => Commands\ConfigCacheCommand::class,
            'routes:list'       => Commands\RoutesListCommand::class,
            'key:generate'      => Commands\KeyGenerateCommand::class,
            'storage:link'      => Commands\StorageLinkCommand::class,
            'optimize'          => Commands\OptimizeCommand::class,
            'routes:cache'      => Commands\RoutesCacheCommand::class,
            'routes:clear'      => Commands\RoutesClearCommand::class,
            'config:clear'      => Commands\ConfigClearCommand::class,
        ];

        $this->aliases = [
            's'      => 'serve',
            'c'      => 'create',
            'm'      => 'migrate',
            'r'      => 'rollback',
            'ws'     => 'ws:serve',
            'cc'     => 'cache:clear',
            'lc'     => 'logs:clear',
            'rc'     => 'routes:cache',
            'rclear' => 'routes:clear',
            'cclear' => 'config:clear',
        ];
    }

    public function run(): void
    {
        global $argv;

        if (count($argv) < 2) {
            $this->showHelp();
            return;
        }

        $command = $argv[1];
        $args    = array_slice($argv, 2);

        // Check aliases
        if (isset($this->aliases[$command])) {
            $command = $this->aliases[$command];
        }

        if (! isset($this->commands[$command])) {
            $this->error("Unknown command: {$command}");
            $this->showHelp();
            return;
        }

        $commandClass    = $this->commands[$command];
        $commandInstance = new $commandClass();

        try {
            $commandInstance->execute($args);
        } catch (\Exception $e) {
            $this->error("Command failed: " . $e->getMessage());
            if (env('APP_DEBUG')) {
                $this->error($e->getTraceAsString());
            }
        }
    }

    private function showHelp(): void
    {
        $this->info("Rivulet Framework Console - Luna");
        $this->info("Usage: php luna <command> [arguments]");
        $this->info("");
        $this->info("Available Commands:");
        $this->info("  run, serve, s          Start development server");
        $this->info("  create                 Create multiple components");
        $this->info("  create:model           Create a model");
        $this->info("  create:controller      Create a controller");
        $this->info("  create:service         Create a service");
        $this->info("  create:template        Create a template");
        $this->info("  create:event           Create an event");
        $this->info("  create:rule            Create a validation rule");
        $this->info("  create:resource        Create a migration resource");
        $this->info("  create:seeder          Create a database seeder");
        $this->info("  create:middleware      Create a middleware");
        $this->info("  migrate, m             Run database migrations");
        $this->info("  seed                   Run database seeders");
        $this->info("  rollback, r            Rollback database migrations");
        $this->info("  ws:serve, ws           Start WebSocket server");
        $this->info("  poke, tinker           Interactive shell (REPL)");
        $this->info("  cache:clear, cc        Clear cache");
        $this->info("  logs:clear, lc         Clear logs");
        $this->info("  config:cache           Cache configuration");
        $this->info("  config:clear           Clear configuration cache");
        $this->info("  routes:list            List all routes");
        $this->info("  routes:cache, rc       Cache routes");
        $this->info("  routes:clear, rclear   Clear routes cache");
        $this->info("  key:generate           Generate application key");
        $this->info("  storage:link           Create storage symlink");
        $this->info("  optimize               Optimize framework");
        $this->info("");
        $this->info("Examples:");
        $this->info("  php luna run");
        $this->info("  php luna create:model User");
        $this->info("  php luna create -mc User");
        $this->info("  php luna migrate");
        $this->info("  php luna poke");
    }

    public function info(string $message): void
    {
        echo "\033[32m[INFO]\033[0m {$message}\n";
    }

    public function error(string $message): void
    {
        echo "\033[31m[ERROR]\033[0m {$message}\n";
    }

    public function warning(string $message): void
    {
        echo "\033[33m[WARNING]\033[0m {$message}\n";
    }

    public function success(string $message): void
    {
        echo "\033[32m[SUCCESS]\033[0m {$message}\n";
    }
}
