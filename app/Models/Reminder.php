<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reminder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'clinic_id', 'follow_up_schedule_id', 'reminder_template_id',
        'channel', 'recipient_type', 'recipient_name', 'recipient_phone',
        'message', 'status', 'scheduled_at', 'sent_at',
        'retry_count', 'error_message', 'metadata',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'metadata' => 'array',
    ];

    // ── Relationships ──

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function followUpSchedule(): BelongsTo
    {
        return $this->belongsTo(FollowUpSchedule::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(ReminderTemplate::class, 'reminder_template_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ReminderLog::class);
    }

    // ── Scopes ──

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeDue($query)
    {
        return $query->where('status', 'pending')
                     ->where('scheduled_at', '<=', now());
    }

    public function scopeRetryable($query)
    {
        $maxRetries = config('cfms.reminder.max_retries', 3);
        return $query->where('status', 'failed')
                     ->where('retry_count', '<', $maxRetries);
    }

    // ── Helpers ──

    public function markSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function markFailed(string $error): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $error,
            'retry_count' => $this->retry_count + 1,
        ]);
    }

    public function canRetry(): bool
    {
        return $this->retry_count < config('cfms.reminder.max_retries', 3);
    }
}
