<?php
namespace Rivulet\System\Debug;

class Debug
{
    private static array $timers = [];
    private static array $memory = [];
    private static bool $enabled = false;

    public static function enable(): void
    {
        self::$enabled = true;
    }

    public static function disable(): void
    {
        self::$enabled = false;
    }

    public static function isEnabled(): bool
    {
        return self::$enabled;
    }

    public static function startTimer(string $name): void
    {
        if (! self::$enabled) {
            return;
        }

        self::$timers[$name] = microtime(true);
        self::$memory[$name] = memory_get_usage();
    }

    public static function endTimer(string $name): ?array
    {
        if (! self::$enabled || ! isset(self::$timers[$name])) {
            return null;
        }

        $endTime   = microtime(true);
        $endMemory = memory_get_usage();

        $result = [
            'duration'    => $endTime - self::$timers[$name],
            'memory'      => $endMemory - self::$memory[$name],
            'peak_memory' => memory_get_peak_usage(),
        ];

        unset(self::$timers[$name]);
        unset(self::$memory[$name]);

        return $result;
    }

    public static function getTimer(string $name): ?float
    {
        if (! isset(self::$timers[$name])) {
            return null;
        }

        return microtime(true) - self::$timers[$name];
    }

    public static function dump($var): void
    {
        if (! self::$enabled) {
            return;
        }

        echo '<pre>';
        var_dump($var);
        echo '</pre>';
    }

    public static function log($var, string $label = 'Debug'): void
    {
        if (! self::$enabled) {
            return;
        }

        $message = is_array($var) || is_object($var) ? json_encode($var) : strval($var);
        logs()->debug("{$label}: {$message}");
    }

    public static function measure(string $name, callable $callback)
    {
        self::startTimer($name);
        $result  = $callback();
        $metrics = self::endTimer($name);

        if ($metrics) {
            logs()->info("Performance: {$name} took {$metrics['duration']}ms and used {$metrics['memory']} bytes");
        }

        return $result;
    }

    public static function getMemoryUsage(): string
    {
        $bytes = memory_get_usage();
        return self::formatBytes($bytes);
    }

    public static function getPeakMemoryUsage(): string
    {
        $bytes = memory_get_peak_usage();
        return self::formatBytes($bytes);
    }

    private static function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow   = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow   = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
