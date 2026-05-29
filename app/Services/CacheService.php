<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    /**
     * Remember a value in cache.
     * Falls back to file cache if Redis is unavailable.
     */
    public static function remember(string $key, int $ttlMinutes, callable $callback): mixed
    {
        try {
            return Cache::remember($key, now()->addMinutes($ttlMinutes), $callback);
        } catch (\Exception $e) {
            // Fallback: execute callback directly if cache fails
            return $callback();
        }
    }

    /**
     * Forget a cache key.
     */
    public static function forget(string $key): void
    {
        try {
            Cache::forget($key);
        } catch (\Exception $e) {
            // Silently fail
        }
    }

    /**
     * Forget multiple keys at once.
     */
    public static function forgetMany(array $keys): void
    {
        foreach ($keys as $key) {
            static::forget($key);
        }
    }

    // === Cache key helpers ===

    public static function homeFeedKey(): string
    {
        return 'home_feed';
    }

    public static function channelKey(string $username): string
    {
        return "channel:{$username}";
    }

    public static function categoriesKey(): string
    {
        return 'categories_all';
    }

    public static function relatedVideosKey(int $videoId): string
    {
        return "related_videos:{$videoId}";
    }

    public static function viewCountKey(int $videoId, string $sessionId): string
    {
        return "view_counted:{$videoId}:{$sessionId}";
    }

    public static function recommendationsKey(int $userId): string
    {
        return "recommendations:{$userId}";
    }

    public static function analyticsKey(int $videoId, string $period): string
    {
        return "analytics:{$videoId}:{$period}";
    }

    // === Invalidation helpers ===

    public static function invalidateOnNewVideo(int $channelUserId): void
    {
        static::forget(static::homeFeedKey());
        // Channel key requires username — cleared when channel updates
    }

    public static function invalidateOnVideoDelete(int $videoId): void
    {
        static::forget(static::homeFeedKey());
        static::forget(static::relatedVideosKey($videoId));
    }

    public static function invalidateChannel(string $username): void
    {
        static::forget(static::channelKey($username));
    }
}
