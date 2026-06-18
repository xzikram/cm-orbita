<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'clinic_id', 'name', 'email_address', 'smtp_host', 'smtp_port',
        'smtp_username', 'smtp_password', 'encryption', 'is_default', 'is_active',
    ];

    protected $casts = [
        'smtp_password' => 'encrypted',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
