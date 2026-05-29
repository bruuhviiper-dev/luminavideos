<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChannelMembership extends Model
{
    protected $fillable = [
        'subscriber_id', 'channel_id', 'plan_id',
        'status', 'started_at', 'expires_at', 'gateway_subscription_id',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subscriber_id');
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(User::class, 'channel_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(MembershipPlan::class);
    }
}
