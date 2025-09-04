<?php
namespace Rivulet\Providers;

use Rivulet\Mail\Mailer;

class MailServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->bind('mailer', function () {
            return new Mailer();
        });

        $this->bind('mail', function () {
            return $this->make('mailer');
        });
    }

    public function boot(): void
    {
        $this->configureMail();
    }

    protected function configureMail(): void
    {
        $config = [
            'driver'     => config('mail.driver', 'smtp'),
            'host'       => config('mail.host'),
            'port'       => config('mail.port'),
            'username'   => config('mail.username'),
            'password'   => config('mail.password'),
            'encryption' => config('mail.encryption'),
            'from'       => [
                'address' => config('mail.from.address'),
                'name'    => config('mail.from.name'),
            ],
        ];

        $mailer = $this->make('mailer');
        $mailer->setConfig($config);
    }
}
