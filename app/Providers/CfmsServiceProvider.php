<?php

namespace App\Providers;

use App\Modules\Reminder\Channels\WhatsAppChannel;
use App\Modules\Reminder\Contracts\ReminderChannelInterface;
use App\Modules\Reminder\Contracts\WhatsAppProviderInterface;
use App\Modules\Reminder\Providers\FonnteProvider;
use App\Modules\Reminder\Providers\LogWhatsAppProvider;
use App\Modules\Reminder\Providers\KirimdevProvider;
use App\Modules\Reminder\Providers\SelfHostedWhatsAppProvider;
use App\Modules\Reminder\Services\ReminderService;
use Illuminate\Support\ServiceProvider;

class CfmsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind WhatsApp Provider based on config
        $this->app->singleton(WhatsAppProviderInterface::class, function () {
            $provider = config('whatsapp.provider', 'log');

            return match ($provider) {
                'fonnte' => new FonnteProvider(),
                'kirimdev' => new KirimdevProvider(),
                'selfhosted' => new SelfHostedWhatsAppProvider(),
                // 'wablas' => new WablasProvider(),
                // 'meta' => new MetaCloudProvider(),
                default => new LogWhatsAppProvider(),
            };
        });

        // Bind WhatsApp Channel
        $this->app->singleton(WhatsAppChannel::class, function ($app) {
            return new WhatsAppChannel($app->make(WhatsAppProviderInterface::class));
        });

        // Bind Reminder Service and register channels
        $this->app->singleton(ReminderService::class, function ($app) {
            $service = new ReminderService($app->make(\App\Core\Services\AuditLogService::class));

            // Register WhatsApp channel
            $service->registerChannel($app->make(WhatsAppChannel::class));

            // Future channels can be registered here:
            // $service->registerChannel($app->make(SmsChannel::class));
            // $service->registerChannel($app->make(EmailChannel::class));

            return $service;
        });
    }

    public function boot(): void
    {
        //
    }
}
