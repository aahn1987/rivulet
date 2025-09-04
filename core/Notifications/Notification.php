<?php
namespace Rivulet\Notifications;

abstract class Notification
{
    abstract public function via(): array;

    abstract public function toArray(): array;

    public function toMail(): array
    {
        return [];
    }

    public function toSlack(): array
    {
        return [];
    }

    public function toFirebase(): array
    {
        return [];
    }

    public function toPusher(): array
    {
        return [];
    }

    public function toSms(): array
    {
        return [];
    }

    public function toWhatsapp(): array
    {
        return [];
    }

    public function shouldSend(): bool
    {
        return true;
    }

    public function routeNotificationFor(string $driver): string | array
    {
        return [];
    }
}
