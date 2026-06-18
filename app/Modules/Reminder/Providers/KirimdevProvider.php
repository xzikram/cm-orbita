<?php

namespace App\Modules\Reminder\Providers;

use App\Modules\Reminder\Contracts\WhatsAppProviderInterface;
use App\Modules\Reminder\DTOs\SendResult;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KirimdevProvider implements WhatsAppProviderInterface
{
    protected string $apiKey;
    protected string $phoneId;
    protected string $url;
    protected int $timeout;

    public function __construct()
    {
        $config = config('whatsapp.providers.kirimdev');
        $this->apiKey = $config['api_key'] ?? '';
        $this->phoneId = $config['phone_id'] ?? '';
        $this->url = $config['url'] ?? 'https://api.kirimdev.com/v1';
        $this->timeout = $config['timeout'] ?? 30;
    }

    public function sendMessage(string $phone, string $message): SendResult
    {
        $startTime = microtime(true);

        try {
            $endpoint = rtrim($this->url, '/') . "/phone-numbers/{$this->phoneId}/messages";
            
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($endpoint, [
                    'messaging_product' => 'whatsapp',
                    'to' => $phone,
                    'type' => 'text',
                    'text' => [
                        'body' => $message,
                    ],
                ]);

            $durationMs = (microtime(true) - $startTime) * 1000;

            if ($response->successful()) {
                return SendResult::success(
                    messageId: $response->json('messages.0.id') ?? 'kirimdev_' . uniqid(),
                    durationMs: $durationMs
                );
            }

            return SendResult::failure(
                error: $response->json('error.message') ?? 'Unknown error from Kirimdev',
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

    public function sendDocumentFile(string $phone, string $fileUrl, string $filename, string $caption): SendResult
    {
        $startTime = microtime(true);

        try {
            $endpoint = rtrim($this->url, '/') . "/phone-numbers/{$this->phoneId}/messages";
            
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($endpoint, [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $phone,
                    'type' => 'document',
                    'document' => [
                        'link' => $fileUrl,
                        'filename' => $filename,
                        'caption' => $caption,
                    ],
                ]);

            $durationMs = (microtime(true) - $startTime) * 1000;

            if ($response->successful()) {
                return SendResult::success(
                    messageId: $response->json('messages.0.id') ?? 'kirimdev_doc_' . uniqid(),
                    durationMs: $durationMs
                );
            }

            return SendResult::failure(
                error: $response->json('error.message') ?? 'Unknown error from Kirimdev',
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
        $message = $params['message'] ?? $templateName;
        return $this->sendMessage($phone, $message);
    }

    public function checkStatus(): bool
    {
        if (empty($this->apiKey) || empty($this->phoneId)) {
            return false;
        }

        try {
            $endpoint = rtrim($this->url, '/') . "/phone-numbers/{$this->phoneId}";
            $response = Http::timeout(10)
                ->withHeaders(['Authorization' => 'Bearer ' . $this->apiKey])
                ->get($endpoint);

            return $response->successful();
        } catch (\Exception) {
            return false;
        }
    }

    public function getProviderName(): string
    {
        return 'kirimdev';
    }
}
