<?php

namespace App\Modules\Reminder\Channels;

use App\Modules\Reminder\Contracts\ReminderChannelInterface;
use App\Modules\Reminder\Contracts\WhatsAppProviderInterface;
use App\Modules\Reminder\DTOs\ReminderDTO;
use App\Modules\Reminder\DTOs\ReminderResult;

class WhatsAppChannel implements ReminderChannelInterface
{
    public function __construct(
        protected WhatsAppProviderInterface $provider
    ) {}

    public function send(ReminderDTO $reminder): ReminderResult
    {
        $result = $this->provider->sendMessage(
            phone: $reminder->recipientPhone,
            message: $reminder->message
        );

        if ($result->success) {
            return ReminderResult::success(
                channel: $this->getChannelName(),
                provider: $this->provider->getProviderName(),
                messageId: $result->messageId,
                durationMs: $result->durationMs
            );
        }

        return ReminderResult::failure(
            channel: $this->getChannelName(),
            error: $result->error ?? 'Unknown error',
            provider: $this->provider->getProviderName(),
            responseCode: $result->responseCode,
            durationMs: $result->durationMs
        );
    }

    public function getChannelName(): string
    {
        return 'whatsapp';
    }

    public function isAvailable(): bool
    {
        return $this->provider->checkStatus();
    }
}
