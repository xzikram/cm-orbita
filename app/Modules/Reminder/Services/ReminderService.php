<?php

namespace App\Modules\Reminder\Services;

use App\Core\Services\AuditLogService;
use App\Models\Reminder;
use App\Models\ReminderLog;
use App\Modules\Reminder\Contracts\ReminderChannelInterface;
use App\Modules\Reminder\DTOs\ReminderDTO;
use App\Modules\Reminder\DTOs\ReminderResult;
use Illuminate\Support\Facades\Log;

class ReminderService
{
    protected array $channels = [];

    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    /**
     * Register a reminder channel.
     */
    public function registerChannel(ReminderChannelInterface $channel): void
    {
        $this->channels[$channel->getChannelName()] = $channel;
    }

    /**
     * Send a reminder using the specified channel.
     */
    public function send(Reminder $reminder): ReminderResult
    {
        $channel = $this->resolveChannel($reminder->channel);

        if (!$channel) {
            $error = "Channel '{$reminder->channel}' not registered";
            $this->logFailure($reminder, $error);
            return ReminderResult::failure($reminder->channel, $error);
        }

        $dto = new ReminderDTO(
            recipientPhone: $reminder->recipient_phone,
            recipientName: $reminder->recipient_name,
            recipientType: $reminder->recipient_type,
            message: $reminder->message,
            channel: $reminder->channel,
            reminderId: $reminder->id,
        );

        $result = $channel->send($dto);

        // Log the result
        $this->logResult($reminder, $result);

        // Update reminder status
        if ($result->success) {
            $reminder->markSent();
            $this->auditLogService->logReminder(
                'send_reminder',
                "Reminder sent to {$reminder->recipient_name} ({$reminder->recipient_phone})",
                ['reminder_id' => $reminder->id, 'channel' => $reminder->channel]
            );
        } else {
            $reminder->markFailed($result->error ?? 'Unknown error');
            $this->auditLogService->logReminder(
                'failed_reminder',
                "Reminder failed for {$reminder->recipient_name}: {$result->error}",
                ['reminder_id' => $reminder->id, 'channel' => $reminder->channel]
            );
        }

        return $result;
    }

    /**
     * Process all due reminders.
     */
    public function processDueReminders(): array
    {
        $dueReminders = Reminder::due()->get();
        $results = ['sent' => 0, 'failed' => 0, 'total' => $dueReminders->count()];

        foreach ($dueReminders as $reminder) {
            try {
                $result = $this->send($reminder);
                $result->success ? $results['sent']++ : $results['failed']++;
            } catch (\Exception $e) {
                Log::error("Failed to process reminder #{$reminder->id}: " . $e->getMessage());
                $reminder->markFailed($e->getMessage());
                $results['failed']++;
            }
        }

        return $results;
    }

    /**
     * Retry failed reminders.
     */
    public function retryFailedReminders(): array
    {
        $retryable = Reminder::retryable()->get();
        $results = ['retried' => 0, 'failed' => 0];

        foreach ($retryable as $reminder) {
            $reminder->update(['status' => 'pending']);
            $result = $this->send($reminder);
            $result->success ? $results['retried']++ : $results['failed']++;
        }

        return $results;
    }

    protected function resolveChannel(string $channelName): ?ReminderChannelInterface
    {
        return $this->channels[$channelName] ?? null;
    }

    protected function logResult(Reminder $reminder, ReminderResult $result): void
    {
        ReminderLog::create([
            'reminder_id' => $reminder->id,
            'channel' => $result->channel,
            'provider' => $result->provider,
            'status' => $result->success ? 'success' : 'failed',
            'recipient_phone' => $reminder->recipient_phone,
            'message_sent' => $reminder->message,
            'error_message' => $result->error,
            'response_code' => $result->responseCode,
            'duration_ms' => $result->durationMs,
        ]);
    }

    protected function logFailure(Reminder $reminder, string $error): void
    {
        ReminderLog::create([
            'reminder_id' => $reminder->id,
            'channel' => $reminder->channel,
            'status' => 'failed',
            'recipient_phone' => $reminder->recipient_phone,
            'message_sent' => $reminder->message,
            'error_message' => $error,
        ]);
    }
}
