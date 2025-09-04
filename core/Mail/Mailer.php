<?php
namespace Rivulet\Mail;

use Rivulet\Views\View;

class Mailer
{
    private array $config     = [];
    private array $globalData = [];

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    public function to(string | array $addresses): Message
    {
        $message = new Message($this->config);
        $message->to($addresses);

        return $message;
    }

    public function send(string | Message $message): bool
    {
        if (is_string($message)) {
            $message = $this->to('')->subject('')->body($message);
        }

        return $message->send();
    }

    public function raw(string $body, \Closure $callback = null): bool
    {
        $message = $this->to('')->subject('')->body($body);

        if ($callback) {
            $callback($message);
        }

        return $message->send();
    }

    public function html(string $html, \Closure $callback = null): bool
    {
        $message = $this->to('')->subject('')->html($html);

        if ($callback) {
            $callback($message);
        }

        return $message->send();
    }

    public function view(string $template, array $data = [], \Closure $callback = null): bool
    {
        $html    = $this->renderTemplate($template, $data);
        $message = $this->to('')->subject('')->html($html);

        if ($callback) {
            $callback($message);
        }

        return $message->send();
    }

    private function renderTemplate(string $template, array $data): string
    {
        $view = new View();
        return $view->render($template, $data);
    }

    public function queue(string $template, array $data = [], \Closure $callback = null): void
    {
        dispatch(new \Rivulet\Jobs\SendMailJob($template, $data, $callback));
    }

    public function later(int $delay, string $template, array $data = [], \Closure $callback = null): void
    {
        dispatch(new \Rivulet\Jobs\SendMailJob($template, $data, $callback))->delay($delay);
    }

    public function fake(): void
    {
        $this->config['driver'] = 'fake';
    }
}
