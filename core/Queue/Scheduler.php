<?php
namespace Rivulet\Queue;

class Scheduler
{
    private array $tasks = [];
    private Queue $queue;

    public function __construct(Queue $queue = null)
    {
        $this->queue = $queue ?? new Queue();
    }

    public function call(callable $callback, string $expression): void
    {
        $this->tasks[] = [
            'type'       => 'callback',
            'callback'   => $callback,
            'expression' => $expression,
            'timezone'   => config('app.timezone', 'UTC'),
        ];
    }

    public function command(string $command, string $expression): void
    {
        $this->tasks[] = [
            'type'       => 'command',
            'command'    => $command,
            'expression' => $expression,
            'timezone'   => config('app.timezone', 'UTC'),
        ];
    }

    public function job(Job $job, string $expression): void
    {
        $this->tasks[] = [
            'type'       => 'job',
            'job'        => $job,
            'expression' => $expression,
            'timezone'   => config('app.timezone', 'UTC'),
        ];
    }

    public function everyMinute(callable $callback): void
    {
        $this->call($callback, '* * * * *');
    }

    public function everyFiveMinutes(callable $callback): void
    {
        $this->call($callback, '*/5 * * * *');
    }

    public function everyTenMinutes(callable $callback): void
    {
        $this->call($callback, '*/10 * * * *');
    }

    public function everyThirtyMinutes(callable $callback): void
    {
        $this->call($callback, '*/30 * * * *');
    }

    public function hourly(callable $callback): void
    {
        $this->call($callback, '0 * * * *');
    }

    public function daily(callable $callback): void
    {
        $this->call($callback, '0 0 * * *');
    }

    public function dailyAt(string $time, callable $callback): void
    {
        $parts  = explode(':', $time);
        $hour   = $parts[0] ?? '00';
        $minute = $parts[1] ?? '00';

        $this->call($callback, "{$minute} {$hour} * * *");
    }

    public function twiceDaily(callable $callback): void
    {
        $this->call($callback, '0 0,12 * * *');
    }

    public function weekly(callable $callback): void
    {
        $this->call($callback, '0 0 * * 0');
    }

    public function monthly(callable $callback): void
    {
        $this->call($callback, '0 0 1 * *');
    }

    public function run(): void
    {
        foreach ($this->tasks as $task) {
            if ($this->isDue($task)) {
                $this->runTask($task);
            }
        }
    }

    private function isDue(array $task): bool
    {
        $expression = $task['expression'];
        $timezone   = $task['timezone'];

        $cron = \Cron\CronExpression::factory($expression);
        $now  = new \DateTime('now', new \DateTimeZone($timezone));

        return $cron->isDue($now);
    }

    private function runTask(array $task): void
    {
        try {
            switch ($task['type']) {
                case 'callback':
                    call_user_func($task['callback']);
                    break;

                case 'command':
                    $this->runCommand($task['command']);
                    break;

                case 'job':
                    $this->queue->push($task['job']);
                    break;
            }

            logs()->info("Scheduled task executed", [
                'type'       => $task['type'],
                'expression' => $task['expression'],
            ]);

        } catch (\Throwable $e) {
            logs()->error("Scheduled task failed", [
                'type'  => $task['type'],
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function runCommand(string $command): void
    {
        exec("php luna {$command} > /dev/null 2>&1 &");
    }

    public function getTasks(): array
    {
        return $this->tasks;
    }
}
