<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuperChat extends Model
{
    protected $fillable = [
        'user_id', 'live_id', 'message', 'amount', 'gateway_payment_id', 'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function live(): BelongsTo
    {
        return $this->belongsTo(Live::class);
    }

    public function getHighlightDurationAttribute(): int
    {
        // Highlight duration in seconds proportional to amount
        return match (true) {
            $this->amount >= 100 => 300,
            $this->amount >= 50  => 180,
            $this->amount >= 20  => 120,
            $this->amount >= 10  => 60,
            default              => 30,
        };
    }

    public function getColorAttribute(): string
    {
        return match (true) {
            $this->amount >= 100 => '#FF0000',
            $this->amount >= 50  => '#FF6D00',
            $this->amount >= 20  => '#FFAB00',
            $this->amount >= 10  => '#00BFA5',
            default              => '#1565C0',
        };
    }
}
