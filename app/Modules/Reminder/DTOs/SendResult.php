<?php

namespace App\Modules\Reminder\DTOs;

class SendResult
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $messageId = null,
        public readonly ?string $error = null,
        public readonly ?int $responseCode = null,
        public readonly ?string $rawResponse = null,
        public readonly ?float $durationMs = null,
    ) {}

    public static function success(?string $messageId = null, ?float $durationMs = null): self
    {
        return new self(success: true, messageId: $messageId, durationMs: $durationMs);
    }

    public static function failure(string $error, ?int $responseCode = null, ?float $durationMs = null): self
    {
        return new self(success: false, error: $error, responseCode: $responseCode, durationMs: $durationMs);
    }
}
