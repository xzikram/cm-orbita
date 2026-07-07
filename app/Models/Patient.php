<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'clinic_id', 'medical_record_number', 'temporary_medical_record_number', 'name', 'nik', 'phone',
        'email', 'gender', 'date_of_birth', 'address', 'notes', 'is_active',
        'is_downtime_entry', 'parent_spouse_name', 'emergency_contact_name', 'emergency_contact_phone',
        'registration_source', 'registration_source_id', 'hospital_arrival_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
        'is_downtime_entry' => 'boolean',
        'hospital_arrival_at' => 'datetime',
    ];

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'registration_source_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(MarketingCampaign::class, 'registration_source_id');
    }

    public function examinations(): HasMany
    {
        return $this->hasMany(Examination::class);
    }

    public function followUpSchedules(): HasMany
    {
        return $this->hasMany(FollowUpSchedule::class);
    }

    public function followUpVisits(): HasMany
    {
        return $this->hasMany(FollowUpVisit::class);
    }

    public function documentDeliveries(): HasMany
    {
        return $this->hasMany(DocumentDelivery::class);
    }

    public function reminders(): HasManyThrough
    {
        return $this->hasManyThrough(
            Reminder::class,
            FollowUpSchedule::class,
            'patient_id',      // FK on follow_up_schedules
            'follow_up_schedule_id', // FK on reminders
            'id',              // local key on patients
            'id'               // local key on follow_up_schedules
        );
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, ?string $search)
    {
        if (empty($search)) return $query;
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('medical_record_number', 'LIKE', "%{$search}%")
              ->orWhere('phone', 'LIKE', "%{$search}%");
        });
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age;
    }
}
