<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarketingCampaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'clinic_id',
        'name',
        'code',
        'source',
        'clicks_count',
        'conversions_count',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'clicks_count' => 'integer',
        'conversions_count' => 'integer',
    ];

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(CampaignClick::class, 'campaign_id');
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class, 'registration_source_id')
            ->where('registration_source', 'marketing');
    }
}
