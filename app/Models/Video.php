<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Video extends Model
{
    protected $fillable = [
        'user_id', 'title', 'slug', 'description', 'thumbnail', 'video_path',
        'video_url', 'youtube_id', 'duration', 'views_count', 'likes_count', 'dislikes_count',
        'comments_count', 'status', 'visibility', 'category_id', 'tags', 'allow_comments',
        'hls_path', 'hls_360p_path', 'hls_720p_path', 'hls_1080p_path',
        'subtitle_srt_path', 'subtitle_vtt_path', 'subtitle_language',
        'is_short',
    ];

    protected $casts = [
        'tags' => 'json',
        'allow_comments' => 'boolean',
        'is_short' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function watchHistory(): HasMany
    {
        return $this->hasMany(WatchHistory::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function playlists()
    {
        return $this->belongsToMany(Playlist::class, 'playlist_video');
    }

    public function analytics(): HasMany
    {
        return $this->hasMany(VideoAnalytic::class);
    }

    public function interactions(): HasMany
    {
        return $this->hasMany(VideoInteraction::class);
    }

    public function getThumbnailUrlAttribute(): string
    {
        if (!$this->thumbnail) {
            return asset('images/default-thumbnail.jpg');
        }
        if (str_starts_with($this->thumbnail, 'http')) {
            return $this->thumbnail;
        }
        return Storage::disk('public')->url($this->thumbnail);
    }

    public function getVideoUrlAttribute(): ?string
    {
        if (!empty($this->attributes['video_url'])) {
            return $this->attributes['video_url'];
        }
        if (!$this->video_path) return null;
        if (str_starts_with($this->video_path, 'http')) {
            return $this->video_path;
        }
        return Storage::disk('public')->url($this->video_path);
    }

    public function getHlsUrlAttribute(): ?string
    {
        if (!$this->hls_path) return null;
        if (str_starts_with($this->hls_path, 'http')) {
            return $this->hls_path;
        }
        return Storage::disk('public')->url($this->hls_path);
    }

    public function getSubtitleVttUrlAttribute(): ?string
    {
        if (!$this->subtitle_vtt_path) return null;
        if (str_starts_with($this->subtitle_vtt_path, 'http')) {
            return $this->subtitle_vtt_path;
        }
        return Storage::disk('public')->url($this->subtitle_vtt_path);
    }

    public function getDurationFormattedAttribute(): string
    {
        $seconds = $this->duration ?? 0;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $secs);
        }
        return sprintf('%d:%02d', $minutes, $secs);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'active')->where('visibility', 'public');
    }

    public function scopeShorts($query)
    {
        return $query->where('is_short', true);
    }

    public function scopeVideos($query)
    {
        return $query->where('is_short', false);
    }
}
