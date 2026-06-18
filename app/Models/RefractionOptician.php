<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefractionOptician extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'clinic_id', 'user_id', 'name', 'sip_number',
        'phone', 'email', 'photo', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function examinations(): HasMany
    {
        return $this->hasMany(Examination::class, 'ro_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
