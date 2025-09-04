<?php
namespace Rivulet\Jobs;

use Rivulet\Mail\Mailer;

class SendMailJob
{
    private string $template;
    private array $data;
    private ?\Closure $callback;

    public function __construct(string $template, array $data = [], \Closure $callback = null)
    {
        $this->template = $template;
        $this->data     = $data;
        $this->callback = $callback;
    }

    public function handle() : void
    {
        $mailer = new Mailer();

        if ($this->callback) {
            $mailer->view($this->template, $this->data, $this->callback);
        } else {
            $mailer->view($this->template, $this->data);
        }
    }
}
