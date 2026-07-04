<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignClick extends Model
{
    // No updated_at for this table
    const UPDATED_AT = null;

    protected $fillable = [
        'campaign_id',
        'ip_address',
        'user_agent',
        'referrer',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(MarketingCampaign::class, 'campaign_id');
    }
}
