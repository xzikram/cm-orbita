<?php

namespace App\Modules\Reminder\DTOs;

class ReminderDTO
{
    public function __construct(
        public readonly string $recipientPhone,
        public readonly string $recipientName,
        public readonly string $recipientType,
        public readonly string $message,
        public readonly string $channel = 'whatsapp',
        public readonly ?int $reminderId = null,
        public readonly array $metadata = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            recipientPhone: $data['recipient_phone'],
            recipientName: $data['recipient_name'],
            recipientType: $data['recipient_type'],
            message: $data['message'],
            channel: $data['channel'] ?? 'whatsapp',
            reminderId: $data['reminder_id'] ?? null,
            metadata: $data['metadata'] ?? [],
        );
    }
}
