<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MembershipPlan extends Model
{
    protected $fillable = ['user_id', 'name', 'price', 'benefits', 'is_active'];

    protected $casts = [
        'benefits' => 'json',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function channel(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(ChannelMembership::class, 'plan_id');
    }
}
