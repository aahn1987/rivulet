<?php
namespace Rivulet\System\Logging;

class Logs
{
    private string $logPath;
    private string $logLevel;
    private array $levels = [
        'debug'     => 100,
        'info'      => 200,
        'notice'    => 250,
        'warning'   => 300,
        'error'     => 400,
        'critical'  => 500,
        'alert'     => 550,
        'emergency' => 600,
    ];

    public function __construct()
    {
        $this->logPath  = storage_path('logs/rivulet.log');
        $this->logLevel = env('LOG_LEVEL', 'debug');
    }

    public function debug(string $message, array $context = []): void
    {
        $this->write('debug', $message, $context);
    }

    public function info(string $message, array $context = []): void
    {
        $this->write('info', $message, $context);
    }

    public function notice(string $message, array $context = []): void
    {
        $this->write('notice', $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->write('warning', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->write('error', $message, $context);
    }

    public function critical(string $message, array $context = []): void
    {
        $this->write('critical', $message, $context);
    }

    public function alert(string $message, array $context = []): void
    {
        $this->write('alert', $message, $context);
    }

    public function emergency(string $message, array $context = []): void
    {
        $this->write('emergency', $message, $context);
    }

    private function write(string $level, string $message, array $context = []): void
    {
        if ($this->levels[$level] < $this->levels[$this->logLevel]) {
            return;
        }

        $logDir = dirname($this->logPath);
        if (! is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $formattedMessage = $this->formatMessage($level, $message, $context);

        file_put_contents($this->logPath, $formattedMessage . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    private function formatMessage(string $level, string $message, array $context): string
    {
        $timestamp  = date('Y-m-d H:i:s');
        $contextStr = empty($context) ? '' : ' | Context: ' . json_encode($context);

        return "[{$timestamp}] {$level}.{$level}: {$message}{$contextStr}";
    }

    public function log(string $level, string $message, array $context = []): void
    {
        $this->write($level, $message, $context);
    }

    public function getLogPath(): string
    {
        return $this->logPath;
    }

    public function setLogPath(string $path): self
    {
        $this->logPath = $path;
        return $this;
    }

    public function setLogLevel(string $level): self
    {
        $this->logLevel = $level;
        return $this;
    }

    public function clear(): bool
    {
        if (file_exists($this->logPath)) {
            return file_put_contents($this->logPath, '') !== false;
        }
        return true;
    }

    public function getLogs(): array
    {
        if (! file_exists($this->logPath)) {
            return [];
        }

        $logs = file($this->logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        return array_map('trim', $logs);
    }

    public function search(string $keyword): array
    {
        $logs = $this->getLogs();
        return array_filter($logs, fn($log) => stripos($log, $keyword) !== false);
    }
}
