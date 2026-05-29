<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Live extends Model
{
    protected $fillable = [
        'user_id', 'title', 'description', 'thumbnail',
        'stream_key', 'status', 'viewers_count', 'started_at', 'ended_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(LiveChatMessage::class);
    }

    public function superChats(): HasMany
    {
        return $this->hasMany(SuperChat::class);
    }

    public function bans(): HasMany
    {
        return $this->hasMany(LiveBan::class);
    }

    public function isUserBanned(int $userId): bool
    {
        return $this->bans()->where('user_id', $userId)->exists();
    }

    public function getDurationAttribute(): string
    {
        if (!$this->started_at) return '0:00';
        $end = $this->ended_at ?? now();
        $diff = $this->started_at->diffInSeconds($end);
        return sprintf('%d:%02d', floor($diff / 60), $diff % 60);
    }
}
