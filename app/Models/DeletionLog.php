<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeletionLog extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'model_type',
        'model_id',
        'model_name',
        'model_identifier',
        'reason',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
