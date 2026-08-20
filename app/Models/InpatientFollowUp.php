<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InpatientFollowUp extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'clinic_id',
        'patient_id',
        'registration_no',
        'medical_record_number',
        'patient_name',
        'patient_phone',
        'patient_age',
        'gender',
        'admission_date',
        'discharge_date',
        'follow_up_due_date',
        'room_bed',
        'doctor_dpjp',
        'diagnosis_or_procedure',
        'nurse_name',
        'status',
        'source',
        'whatsapp_message',
        'whatsapp_message_id',
        'sent_at',
        'sent_by',
        'response_complaints',
        'response_medication_compliance',
        'response_side_effects',
        'response_wound_condition',
        'response_vision_progress',
        'response_notes',
        'needs_doctor_review',
        'responded_at',
        'responded_by',
    ];

    protected $casts = [
        'admission_date' => 'datetime',
        'discharge_date' => 'datetime',
        'follow_up_due_date' => 'date',
        'sent_at' => 'datetime',
        'responded_at' => 'datetime',
        'needs_doctor_review' => 'boolean',
    ];

    // ── Relationships ──

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class)->withTrashed();
    }

    public function sentBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function respondedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    // ── Scopes ──

    public function scopeDueToday($query)
    {
        return $query->whereDate('follow_up_due_date', today());
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending')
            ->whereDate('follow_up_due_date', '<', today());
    }

    // ── Helpers ──

    public function isDueToday(): bool
    {
        return $this->follow_up_due_date && $this->follow_up_due_date->isToday();
    }

    public function isOverdue(): bool
    {
        return $this->status === 'pending' && $this->follow_up_due_date && $this->follow_up_due_date->isPast() && !$this->follow_up_due_date->isToday();
    }

    public function getFormattedPhoneAttribute(): string
    {
        $phone = preg_replace('/\D/', '', $this->patient_phone ?? '');
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }
        return $phone;
    }
}
