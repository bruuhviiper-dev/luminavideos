<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoAnalytic extends Model
{
    protected $fillable = [
        'video_id', 'date', 'views', 'unique_views', 'watch_time_seconds',
        'likes', 'dislikes', 'comments', 'shares', 'subscribers_gained',
        'impressions', 'clicks', 'traffic_sources',
    ];

    protected $casts = [
        'date' => 'date',
        'traffic_sources' => 'json',
    ];

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }

    public function getCtrAttribute(): float
    {
        if ($this->impressions === 0) return 0;
        return round(($this->clicks / $this->impressions) * 100, 2);
    }

    public function getWatchTimeHoursAttribute(): float
    {
        return round($this->watch_time_seconds / 3600, 2);
    }
}
