<?php
namespace Rivulet\Mail;

class Message
{
    private array $config;
    private array $data = [];

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function from(string $address, string $name = null): self
    {
        $this->data['from']      = $address;
        $this->data['from_name'] = $name;
        return $this;
    }

    public function to(string | array $addresses): self
    {
        $this->data['to'] = is_array($addresses) ? $addresses : [$addresses];
        return $this;
    }

    public function cc(string | array $addresses): self
    {
        $this->data['cc'] = is_array($addresses) ? $addresses : [$addresses];
        return $this;
    }

    public function bcc(string | array $addresses): self
    {
        $this->data['bcc'] = is_array($addresses) ? $addresses : [$addresses];
        return $this;
    }

    public function subject(string $subject): self
    {
        $this->data['subject'] = $subject;
        return $this;
    }

    public function body(string $body): self
    {
        $this->data['body'] = $body;
        return $this;
    }

    public function html(string $html): self
    {
        $this->data['html'] = $html;
        return $this;
    }

    public function attach(string $file, string $as = null, string $mime = null): self
    {
        $this->data['attachments'][] = [
            'file' => $file,
            'as'   => $as,
            'mime' => $mime,
        ];
        return $this;
    }

    public function replyTo(string $address, string $name = null): self
    {
        $this->data['reply_to']      = $address;
        $this->data['reply_to_name'] = $name;
        return $this;
    }

    public function header(string $name, string $value): self
    {
        $this->data['headers'][$name] = $value;
        return $this;
    }

    public function send(): bool
    {
        $driver = $this->getDriver();
        return $driver->send($this->data);
    }

    private function getDriver()
    {
        $driver = $this->config['driver'] ?? 'smtp';
        $class  = '\\Rivulet\\Mail\\Drivers\\' . ucfirst($driver) . 'Driver';

        if (class_exists($class)) {
            switch ($driver) {
                case 'mailgun':
                    return new $class($this->config['domain'], $this->config['secret']);
                case 'sendgrid':
                    return new $class($this->config['api_key']);
                case 'smtp':
                case 'phpmail':
                case 'sendmail':
                default:
                    return new $class();
            }
        }

        return new \Rivulet\Mail\Drivers\PhpMailDriver();
    }

    public function getData(): array
    {
        return $this->data;
    }
}
