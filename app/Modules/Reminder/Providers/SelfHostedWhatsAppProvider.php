<?php
 
namespace App\Modules\Reminder\Providers;
 
use App\Modules\Reminder\Contracts\WhatsAppProviderInterface;
use App\Modules\Reminder\DTOs\SendResult;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
 
class SelfHostedWhatsAppProvider implements WhatsAppProviderInterface
{
    protected string $url;
    protected int $timeout;
 
    public function __construct()
    {
        $config = config('whatsapp.providers.selfhosted');
        $this->url = $config['url'] ?? 'http://localhost:3000';
        $this->timeout = $config['timeout'] ?? 30;
    }
 
    public function sendMessage(string $phone, string $message): SendResult
    {
        $startTime = microtime(true);
        $clientId = \Illuminate\Support\Facades\Auth::check() ? 'user-' . \Illuminate\Support\Facades\Auth::id() : null;

        try {
            $endpoint = rtrim($this->url, '/') . '/send-message';
            
            $payload = [
                'phone' => $phone,
                'message' => $message,
            ];

            if ($clientId) {
                $payload['clientId'] = $clientId;
            }

            $response = Http::timeout($this->timeout)
                ->post($endpoint, $payload);

            $durationMs = (microtime(true) - $startTime) * 1000;

            if ($response->successful() && $response->json('success') !== false) {
                return SendResult::success(
                    messageId: $response->json('messageId') ?? 'selfhosted_' . uniqid(),
                    durationMs: $durationMs
                );
            }

            return SendResult::failure(
                error: $response->json('error') ?? 'Gagal mengirim pesan via Gateway Mandiri',
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
        $clientId = \Illuminate\Support\Facades\Auth::check() ? 'user-' . \Illuminate\Support\Facades\Auth::id() : null;

        try {
            $endpoint = rtrim($this->url, '/') . '/send-document';
            
            $payload = [
                'phone' => $phone,
                'fileUrl' => $fileUrl,
                'filename' => $filename,
                'caption' => $caption,
            ];

            if ($clientId) {
                $payload['clientId'] = $clientId;
            }

            $response = Http::timeout($this->timeout)
                ->post($endpoint, $payload);

            $durationMs = (microtime(true) - $startTime) * 1000;

            if ($response->successful() && $response->json('success') !== false) {
                return SendResult::success(
                    messageId: $response->json('messageId') ?? 'selfhosted_doc_' . uniqid(),
                    durationMs: $durationMs
                );
            }

            return SendResult::failure(
                error: $response->json('error') ?? 'Gagal mengirim dokumen via Gateway Mandiri',
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
        try {
            $clientId = \Illuminate\Support\Facades\Auth::check() ? 'user-' . \Illuminate\Support\Facades\Auth::id() : null;
            $endpoint = rtrim($this->url, '/') . '/status';
            
            $params = [];
            if ($clientId) {
                $params['clientId'] = $clientId;
            }

            $response = Http::timeout(5)->get($endpoint, $params);
 
            return $response->successful() && $response->json('ready') === true;
        } catch (\Exception) {
            return false;
        }
    }
 
    public function getProviderName(): string
    {
        return 'selfhosted';
    }
}
