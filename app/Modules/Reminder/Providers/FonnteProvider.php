<?php

namespace App\Modules\Reminder\Providers;

use App\Modules\Reminder\Contracts\WhatsAppProviderInterface;
use App\Modules\Reminder\DTOs\SendResult;
use Illuminate\Support\Facades\Http;

/**
 * Fonnte WhatsApp provider implementation.
 * https://fonnte.com
 */
class FonnteProvider implements WhatsAppProviderInterface
{
    protected string $token;
    protected string $url;
    protected int $timeout;

    public function __construct(?string $token = null, ?string $url = null)
    {
        $config = config('whatsapp.providers.fonnte');
        $this->token = $token ?? $config['token'] ?? '';
        $this->url = $url ?? $config['url'] ?? 'https://api.fonnte.com/send';
        $this->timeout = $config['timeout'] ?? 30;
    }

    public function sendMessage(string $phone, string $message): SendResult
    {
        $startTime = microtime(true);

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => $this->token,
                ])
                ->post($this->url, [
                    'target' => $phone,
                    'message' => $message,
                ]);

            $durationMs = (microtime(true) - $startTime) * 1000;

            if ($response->successful() && $response->json('status')) {
                return SendResult::success(
                    messageId: $response->json('id'),
                    durationMs: $durationMs
                );
            }

            return SendResult::failure(
                error: $response->json('reason') ?? 'Unknown error from Fonnte',
                responseCode: $response->status(),
                durationMs: $durationMs
            );
        } catch (\Exception $e) {
            $durationMs = (microtime(true) - $startTime) * 1000;
            return SendResult::failure(
                error: $e->getMessage(),
                durationMs: $durationMs
            );
        }
    }

    public function sendTemplate(string $phone, string $templateName, array $params = []): SendResult
    {
        // Fonnte doesn't have native templates, so we build the message
        $message = $params['message'] ?? $templateName;
        return $this->sendMessage($phone, $message);
    }

    public function checkStatus(): bool
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders(['Authorization' => $this->token])
                ->post('https://api.fonnte.com/device');

            return $response->successful() && $response->json('status');
        } catch (\Exception) {
            return false;
        }
    }

    public function getProviderName(): string
    {
        return 'fonnte';
    }

    public function sendDocumentFile(string $phone, string $fileUrl, string $filename, string $caption): SendResult
    {
        $startTime = microtime(true);

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => $this->token,
                ])
                ->post($this->url, [
                    'target' => $phone,
                    'message' => $caption,
                    'file' => $fileUrl,
                ]);

            $durationMs = (microtime(true) - $startTime) * 1000;

            if ($response->successful() && $response->json('status')) {
                return SendResult::success(
                    messageId: $response->json('id'),
                    durationMs: $durationMs
                );
            }

            return SendResult::failure(
                error: $response->json('reason') ?? 'Unknown error from Fonnte',
                responseCode: $response->status(),
                durationMs: $durationMs
            );
        } catch (\Exception $e) {
            $durationMs = (microtime(true) - $startTime) * 1000;
            return SendResult::failure(
                error: $e->getMessage(),
                durationMs: $durationMs
            );
        }
    }
}
