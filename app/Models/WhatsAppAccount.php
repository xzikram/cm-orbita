<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WhatsAppAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'whatsapp_accounts';

    protected $fillable = [
        'clinic_id', 'name', 'phone_number', 'provider',
        'token', 'api_url', 'is_default', 'is_active',
    ];

    protected $casts = [
        'token' => 'encrypted',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function documentDeliveries(): HasMany
    {
        return $this->hasMany(DocumentDelivery::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
