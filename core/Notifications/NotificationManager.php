<?php
namespace Rivulet\Notifications;

use Rivulet\Notifications\Drivers\FirebaseDriver;
use Rivulet\Notifications\Drivers\PusherDriver;
use Rivulet\Notifications\Drivers\SlackDriver;
use Rivulet\Notifications\Drivers\SmsDriver;
use Rivulet\Notifications\Drivers\WhatsappDriver;

class NotificationManager
{
    private array $drivers = [];

    public function __construct()
    {
        $this->initializeDrivers();
    }

    private function initializeDrivers(): void
    {
        if (env('FIREBASE_SERVER_KEY') && env('FIREBASE_SENDER_ID')) {
            $this->drivers['firebase'] = new FirebaseDriver(
                env('FIREBASE_SERVER_KEY'),
                env('FIREBASE_SENDER_ID')
            );
        }

        if (env('PUSHER_APP_ID') && env('PUSHER_APP_KEY') && env('PUSHER_APP_SECRET')) {
            $this->drivers['pusher'] = new PusherDriver(
                env('PUSHER_APP_ID'),
                env('PUSHER_APP_KEY'),
                env('PUSHER_APP_SECRET'),
                env('PUSHER_APP_CLUSTER', 'mt1')
            );
        }

        if (env('SLACK_WEBHOOK_URL')) {
            $this->drivers['slack'] = new SlackDriver(env('SLACK_WEBHOOK_URL'));
        }

        if (env('TWILIO_SID') && env('TWILIO_AUTH_TOKEN') && env('TWILIO_PHONE_NUMBER')) {
            $this->drivers['sms'] = new SmsDriver(
                env('TWILIO_SID'),
                env('TWILIO_AUTH_TOKEN'),
                env('TWILIO_PHONE_NUMBER')
            );
        }

        if (env('WHATSAPP_TOKEN') && env('WHATSAPP_PHONE_NUMBER_ID')) {
            $this->drivers['whatsapp'] = new WhatsappDriver(
                env('WHATSAPP_TOKEN'),
                env('WHATSAPP_PHONE_NUMBER_ID')
            );
        }
    }

    public function send(Notification $notification, $notifiables = null): bool
    {
        $notifiables = $notifiables ?? [];
        $notifiables = is_array($notifiables) ? $notifiables : [$notifiables];

        $results = true;

        foreach ($notifiables as $notifiable) {
            foreach ($notification->via() as $channel) {
                if ($this->canSend($channel, $notification)) {
                    $result  = $this->sendVia($channel, $notification, $notifiable);
                    $results = $results && $result;
                }
            }
        }

        return $results;
    }

    private function canSend(string $channel, Notification $notification): bool
    {
        return isset($this->drivers[$channel]) && $notification->shouldSend();
    }

    private function sendVia(string $channel, Notification $notification, $notifiable): bool
    {
        $driver = $this->drivers[$channel];
        $data   = $this->prepareData($channel, $notification, $notifiable);

        return $driver->send($data);
    }

    private function prepareData(string $channel, Notification $notification, $notifiable): array
    {
        $method = 'to' . ucfirst($channel);

        if (method_exists($notification, $method)) {
            return $notification->$method();
        }

        $data = $notification->toArray();

        if (method_exists($notifiable, 'routeNotificationFor')) {
            $data['to'] = $notifiable->routeNotificationFor($channel);
        }

        return $data;
    }

    public function driver(string $name)
    {
        return $this->drivers[$name] ?? null;
    }

    public function getAvailableDrivers(): array
    {
        return array_keys($this->drivers);
    }
}
