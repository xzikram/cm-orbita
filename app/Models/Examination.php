<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Examination extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'clinic_id', 'patient_id', 'doctor_id', 'ro_id', 'created_by',
        'examination_date',
        'od_sphere', 'od_cylinder', 'od_axis', 'od_visus',
        'os_sphere', 'os_cylinder', 'os_axis', 'os_visus',
        'lens_type', 'lens_brand', 'lens_power_od', 'lens_power_os',
        'clinical_notes', 'status',
        'is_downtime_entry', 'patient_status', 'registration_date', 'registration_number', 'guarantor', 'service_unit', 'tindakan', 'queue_number', 'total_payment',
    ];

    protected $casts = [
        'examination_date' => 'date',
        'registration_date' => 'date',
        'od_sphere' => 'decimal:2',
        'od_cylinder' => 'decimal:2',
        'os_sphere' => 'decimal:2',
        'os_cylinder' => 'decimal:2',
        'is_downtime_entry' => 'boolean',
        'total_payment' => 'decimal:2',
    ];

    // ── Relationships ──

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function refractionOptician(): BelongsTo
    {
        return $this->belongsTo(RefractionOptician::class, 'ro_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function followUpSchedules(): HasMany
    {
        return $this->hasMany(FollowUpSchedule::class);
    }

    public function followUpVisits(): HasMany
    {
        return $this->hasMany(FollowUpVisit::class);
    }

    // ── Scopes ──

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // ── Accessors ──

    public function getOdSummaryAttribute(): string
    {
        $parts = [];
        if ($this->od_sphere !== null) $parts[] = "S{$this->od_sphere}";
        if ($this->od_cylinder !== null) $parts[] = "C{$this->od_cylinder}";
        if ($this->od_axis !== null) $parts[] = "x{$this->od_axis}°";
        return implode(' ', $parts) ?: '-';
    }

    public function getOsSummaryAttribute(): string
    {
        $parts = [];
        if ($this->os_sphere !== null) $parts[] = "S{$this->os_sphere}";
        if ($this->os_cylinder !== null) $parts[] = "C{$this->os_cylinder}";
        if ($this->os_axis !== null) $parts[] = "x{$this->os_axis}°";
        return implode(' ', $parts) ?: '-';
    }
}
