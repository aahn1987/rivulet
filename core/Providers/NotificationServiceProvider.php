<?php
namespace Rivulet\Providers;

use Rivulet\Notifications\NotificationManager;

class NotificationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->bind('notification', function () {
            return new NotificationManager();
        });

        $this->bind('notifications', function () {
            return $this->make('notification');
        });
    }

    public function boot(): void
    {
        $this->configureNotifications();
    }

    protected function configureNotifications(): void
    {
        $config = [
            // Channel configurations
            'channels'    => [
                'firebase' => [
                    'server_key' => config('notifications.channels.firebase.server_key', env('FIREBASE_SERVER_KEY')),
                    'sender_id'  => config('notifications.channels.firebase.sender_id', env('FIREBASE_SENDER_ID')),
                    'url'        => config('notifications.channels.firebase.url', 'https://fcm.googleapis.com/fcm/send'),
                ],
                'pusher'   => [
                    'app_id'     => config('notifications.channels.pusher.app_id', env('PUSHER_APP_ID')),
                    'app_key'    => config('notifications.channels.pusher.app_key', env('PUSHER_APP_KEY')),
                    'app_secret' => config('notifications.channels.pusher.app_secret', env('PUSHER_APP_SECRET')),
                    'cluster'    => config('notifications.channels.pusher.cluster', env('PUSHER_APP_CLUSTER', 'mt1')),
                    'encrypted'  => config('notifications.channels.pusher.encrypted', true),
                ],
                'slack'    => [
                    'webhook_url' => config('notifications.channels.slack.webhook_url', env('SLACK_WEBHOOK_URL')),
                    'channel'     => config('notifications.channels.slack.channel', '#general'),
                    'username'    => config('notifications.channels.slack.username', env('APP_NAME', 'Rivulet')),
                    'icon_emoji'  => config('notifications.channels.slack.icon_emoji', ':information_source:'),
                ],
                'sms'      => [
                    'driver'       => config('notifications.channels.sms.driver', 'twilio'),
                    'sid'          => config('notifications.channels.sms.sid', env('TWILIO_SID')),
                    'auth_token'   => config('notifications.channels.sms.auth_token', env('TWILIO_AUTH_TOKEN')),
                    'phone_number' => config('notifications.channels.sms.phone_number', env('TWILIO_PHONE_NUMBER')),
                    'url'          => config('notifications.channels.sms.url', 'https://api.twilio.com/2010-04-01/Accounts/{sid}/Messages.json'),
                ],
                'whatsapp' => [
                    'driver'          => config('notifications.channels.whatsapp.driver', 'facebook'),
                    'token'           => config('notifications.channels.whatsapp.token', env('WHATSAPP_TOKEN')),
                    'phone_number_id' => config('notifications.channels.whatsapp.phone_number_id', env('WHATSAPP_PHONE_NUMBER_ID')),
                    'url'             => config('notifications.channels.whatsapp.url', 'https://graph.facebook.com/v18.0/{phone_number_id}/messages'),
                    'api_version'     => config('notifications.channels.whatsapp.api_version', 'v18.0'),
                ],
            ],

            // Queue configuration
            'queue'       => [
                'connection'  => config('notifications.queue.connection', env('QUEUE_CONNECTION', 'database')),
                'queue'       => config('notifications.queue.queue', 'notifications'),
                'delay'       => config('notifications.queue.delay', 0),
                'retry_after' => config('notifications.queue.retry_after', 90),
                'max_tries'   => config('notifications.queue.max_tries', 3),
            ],

            // Rate limiting
            'rate_limits' => config('notifications.rate_limits', []),

            // Retry configuration
            'retry'       => config('notifications.retry', [
                'attempts'            => 3,
                'delay'               => [60, 300, 900],
                'exponential_backoff' => true,
            ]),

            // Logging configuration
            'logging'     => [
                'enabled'  => config('notifications.logging.enabled', env('APP_DEBUG', false)),
                'level'    => config('notifications.logging.level', env('LOG_LEVEL', 'info')),
                'channels' => config('notifications.logging.channels', []),
            ],

            // Templates
            'templates'   => config('notifications.templates', []),

            // Testing
            'testing'     => [
                'enabled'                => config('notifications.testing.enabled', env('APP_ENV') === 'testing'),
                'fake_channels'          => config('notifications.testing.fake_channels', []),
                'log_fake_notifications' => config('notifications.testing.log_fake_notifications', true),
            ],

            // General settings
            'app_name'    => env('APP_NAME', 'Rivulet'),
            'app_env'     => env('APP_ENV', 'local'),
            'app_debug'   => env('APP_DEBUG', false),
            'app_url'     => env('APP_URL', 'http://localhost'),
        ];

        $manager = $this->make('notification');
        $manager->setConfig($config);
    }
}
