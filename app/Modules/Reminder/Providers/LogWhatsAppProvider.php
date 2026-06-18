<?php

namespace App\Modules\Reminder\Providers;

use App\Modules\Reminder\Contracts\WhatsAppProviderInterface;
use App\Modules\Reminder\DTOs\SendResult;
use Illuminate\Support\Facades\Log;

/**
 * Log-based WhatsApp provider for development and testing.
 * Messages are logged instead of being sent to a real WhatsApp API.
 */
class LogWhatsAppProvider implements WhatsAppProviderInterface
{
    public function sendMessage(string $phone, string $message): SendResult
    {
        Log::channel('single')->info('[WhatsApp Log Provider] Message sent', [
            'phone' => $phone,
            'message' => $message,
            'timestamp' => now()->toIso8601String(),
        ]);

        return SendResult::success(
            messageId: 'log_' . uniqid(),
            durationMs: 0.5
        );
    }

    public function sendTemplate(string $phone, string $templateName, array $params = []): SendResult
    {
        Log::channel('single')->info('[WhatsApp Log Provider] Template sent', [
            'phone' => $phone,
            'template' => $templateName,
            'params' => $params,
            'timestamp' => now()->toIso8601String(),
        ]);

        return SendResult::success(
            messageId: 'log_tpl_' . uniqid(),
            durationMs: 0.5
        );
    }

    public function checkStatus(): bool
    {
        return true;
    }

    public function getProviderName(): string
    {
        return 'log';
    }

    public function sendDocumentFile(string $phone, string $fileUrl, string $filename, string $caption): SendResult
    {
        Log::channel('single')->info('[WhatsApp Log Provider] Document file sent', [
            'phone' => $phone,
            'file_url' => $fileUrl,
            'filename' => $filename,
            'caption' => $caption,
            'timestamp' => now()->toIso8601String(),
        ]);

        return SendResult::success(
            messageId: 'log_doc_' . uniqid(),
            durationMs: 0.5
        );
    }
}
