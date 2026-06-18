<?php

namespace App\Modules\Reminder\DTOs;

class ReminderResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $channel,
        public readonly ?string $provider = null,
        public readonly ?string $messageId = null,
        public readonly ?string $error = null,
        public readonly ?int $responseCode = null,
        public readonly ?float $durationMs = null,
        public readonly array $metadata = [],
    ) {}

    public static function success(string $channel, ?string $provider = null, ?string $messageId = null, ?float $durationMs = null): self
    {
        return new self(
            success: true,
            channel: $channel,
            provider: $provider,
            messageId: $messageId,
            durationMs: $durationMs,
        );
    }

    public static function failure(string $channel, string $error, ?string $provider = null, ?int $responseCode = null, ?float $durationMs = null): self
    {
        return new self(
            success: false,
            channel: $channel,
            provider: $provider,
            error: $error,
            responseCode: $responseCode,
            durationMs: $durationMs,
        );
    }
}
