<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    protected $fillable = [
        'user_id', 'push_enabled',
        'email_new_videos', 'email_lives', 'email_weekly_digest', 'email_comments',
    ];

    protected $casts = [
        'push_enabled' => 'boolean',
        'email_new_videos' => 'boolean',
        'email_lives' => 'boolean',
        'email_weekly_digest' => 'boolean',
        'email_comments' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
