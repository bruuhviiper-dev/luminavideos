<?php

namespace App\Services;

use App\Models\Video;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RecommendationService
{
    /**
     * Get recommended video IDs for a user.
     * Tries Python ML script first, falls back to popular videos.
     */
    public function getRecommendations(int $userId, int $limit = 10): array
    {
        $cacheKey = CacheService::recommendationsKey($userId);

        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($userId, $limit) {
            return $this->fetchRecommendations($userId, $limit);
        });
    }

    private function fetchRecommendations(int $userId, int $limit): array
    {
        // Try Python ML script
        try {
            $scriptPath = base_path('scripts/recommend_videos.py');
            if (file_exists($scriptPath)) {
                $result = \Illuminate\Support\Facades\Process::run(
                    "python \"{$scriptPath}\" --user_id={$userId} --limit={$limit} --db_path=" . database_path('database.sqlite')
                );

                if ($result->successful()) {
                    $data = json_decode($result->output(), true);
                    if (isset($data['video_ids']) && is_array($data['video_ids'])) {
                        return $data['video_ids'];
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Recommendation Python script failed: ' . $e->getMessage());
        }

        // Fallback: popular videos
        return $this->getPopularVideoIds($userId, $limit);
    }

    private function getPopularVideoIds(int $userId, int $limit): array
    {
        return Video::published()
            ->where('user_id', '!=', $userId)
            ->orderByDesc('views_count')
            ->limit($limit)
            ->pluck('id')
            ->toArray();
    }

    /**
     * Get recommended videos as Eloquent collection.
     */
    public function getRecommendedVideos(int $userId, int $limit = 10)
    {
        $ids = $this->getRecommendations($userId, $limit);

        if (empty($ids)) {
            return Video::published()->orderByDesc('views_count')->limit($limit)->with('user')->get();
        }

        return Video::whereIn('id', $ids)
            ->with('user')
            ->get()
            ->sortBy(fn($v) => array_search($v->id, $ids))
            ->values();
    }
}
