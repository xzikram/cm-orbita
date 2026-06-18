<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReminderLog extends Model
{
    protected $fillable = [
        'reminder_id', 'channel', 'provider', 'status',
        'recipient_phone', 'message_sent', 'response',
        'error_message', 'response_code', 'duration_ms', 'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'duration_ms' => 'decimal:2',
    ];

    public function reminder(): BelongsTo
    {
        return $this->belongsTo(Reminder::class);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
