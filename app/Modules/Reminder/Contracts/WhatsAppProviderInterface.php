<?php

namespace App\Modules\Reminder\Contracts;

use App\Modules\Reminder\DTOs\SendResult;

interface WhatsAppProviderInterface
{
    /**
     * Send a text message via WhatsApp.
     */
    public function sendMessage(string $phone, string $message): SendResult;

    /**
     * Send a templated message via WhatsApp.
     */
    public function sendTemplate(string $phone, string $templateName, array $params = []): SendResult;

    /**
     * Check if the provider connection is healthy.
     */
    public function checkStatus(): bool;

    /**
     * Get the provider name.
     */
    public function getProviderName(): string;

    /**
     * Send a document file via WhatsApp.
     */
    public function sendDocumentFile(string $phone, string $fileUrl, string $filename, string $caption): SendResult;
}
