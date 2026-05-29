<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveChatMessage extends Model
{
    protected $fillable = [
        'live_id', 'user_id', 'message',
        'is_super_chat', 'super_chat_amount', 'is_deleted',
    ];

    protected $casts = [
        'is_super_chat' => 'boolean',
        'is_deleted' => 'boolean',
        'super_chat_amount' => 'decimal:2',
    ];

    public function live(): BelongsTo
    {
        return $this->belongsTo(Live::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
