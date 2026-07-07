<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FollowUpVisit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'follow_up_schedule_id', 'examination_id', 'patient_id', 'clinic_id',
        'examined_by', 'visit_date', 'visus_od', 'visus_os',
        'complaints', 'lens_condition_id', 'lens_condition_notes',
        'doctor_notes', 'follow_up_status_id', 'rescheduled_to', 'reschedule_reason',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'rescheduled_to' => 'date',
    ];

    // ── Relationships ──

    public function followUpSchedule(): BelongsTo
    {
        return $this->belongsTo(FollowUpSchedule::class);
    }

    public function examination(): BelongsTo
    {
        return $this->belongsTo(Examination::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class)->withTrashed();
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function examiner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'examined_by');
    }

    public function lensCondition(): BelongsTo
    {
        return $this->belongsTo(LensCondition::class);
    }

    public function followUpStatus(): BelongsTo
    {
        return $this->belongsTo(FollowUpStatus::class);
    }
}
