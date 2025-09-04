<?php
namespace Rivulet\Console\Commands;

use Rivulet\Console\Command;

class KeyGenerateCommand extends Command
{
    protected string $name        = 'key:generate';
    protected string $description = 'Generate application key';

    public function execute(array $args): void
    {
        $key = $this->generateRandomKey();

        $this->info('Generating application key...');

        // Update .env file
        $envPath = basePath('.env');

        if (file_exists($envPath)) {
            $content = file_get_contents($envPath);

            if (str_contains($content, 'APP_KEY=')) {
                $content = preg_replace('/APP_KEY=.*/', 'APP_KEY=' . $key, $content);
            } else {
                $content .= "\nAPP_KEY=" . $key;
            }

            file_put_contents($envPath, $content);
        } else {
            // Create .env file if it doesn't exist
            $envExample = basePath('.env.example');
            if (file_exists($envExample)) {
                copy($envExample, $envPath);
                $content = file_get_contents($envPath);
                $content = preg_replace('/APP_KEY=.*/', 'APP_KEY=' . $key, $content);
                file_put_contents($envPath, $content);
            } else {
                file_put_contents($envPath, "APP_KEY=" . $key);
            }
        }

        $this->success("Application key [{$key}] set successfully");
    }

    private function generateRandomKey(): string
    {
        return 'base64:' . base64_encode(random_bytes(32));
    }
}
