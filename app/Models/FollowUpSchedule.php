<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class FollowUpSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'examination_id', 'patient_id', 'clinic_id', 'label',
        'interval_days', 'scheduled_date', 'sequence', 'status',
        'rescheduled_date', 'notes', 'reminder_sent', 'reminder_sent_at',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'rescheduled_date' => 'date',
        'reminder_sent' => 'boolean',
        'reminder_sent_at' => 'datetime',
    ];

    // ── Relationships ──

    public function examination(): BelongsTo
    {
        return $this->belongsTo(Examination::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(FollowUpVisit::class);
    }

    public function latestVisit(): HasOne
    {
        return $this->hasOne(FollowUpVisit::class)->latestOfMany();
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class);
    }

    // ── Scopes ──

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending')
                     ->where('scheduled_date', '<', now()->toDateString());
    }

    public function scopeDueToday($query)
    {
        return $query->where('status', 'pending')
                     ->whereDate('scheduled_date', now()->toDateString());
    }

    public function scopeDueThisWeek($query)
    {
        return $query->where('status', 'pending')
                     ->whereBetween('scheduled_date', [
                         now()->startOfWeek()->toDateString(),
                         now()->endOfWeek()->toDateString(),
                     ]);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'pending')
                     ->where('scheduled_date', '>=', now()->toDateString())
                     ->orderBy('scheduled_date');
    }

    // ── Helpers ──

    public function getEffectiveDateAttribute()
    {
        return $this->rescheduled_date ?? $this->scheduled_date;
    }

    public function isOverdue(): bool
    {
        return $this->status === 'pending' && $this->effective_date->isPast();
    }

    public function markCompleted(): void
    {
        $this->update(['status' => 'completed']);
    }

    public function markMissed(): void
    {
        $this->update(['status' => 'missed']);
    }
}
