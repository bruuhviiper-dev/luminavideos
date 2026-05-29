<?php

namespace App\Jobs;

use App\Models\Video;
use App\Models\VideoAnalytic;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class RecordVideoAnalyticsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(private ?int $videoId = null) {}

    public function handle(): void
    {
        try {
            if ($this->videoId) {
                $this->recordForVideo($this->videoId);
            } else {
                // Process all active videos
                Video::where('status', 'active')
                    ->select('id')
                    ->chunk(100, function ($videos) {
                        foreach ($videos as $video) {
                            $this->recordForVideo($video->id);
                        }
                    });
            }
        } catch (\Exception $e) {
            Log::error('RecordVideoAnalyticsJob failed: ' . $e->getMessage());
        }
    }

    private function recordForVideo(int $videoId): void
    {
        $today = now()->toDateString();

        VideoAnalytic::updateOrCreate(
            ['video_id' => $videoId, 'date' => $today],
            [
                'views'            => $this->getTodayViews($videoId),
                'unique_views'     => $this->getTodayUniqueViews($videoId),
                'watch_time_seconds' => $this->getTodayWatchTime($videoId),
                'likes'            => $this->getTodayLikes($videoId),
                'dislikes'         => $this->getTodayDislikes($videoId),
                'comments'         => $this->getTodayComments($videoId),
                'traffic_sources'  => $this->getTrafficSources($videoId),
            ]
        );
    }

    private function getTodayViews(int $videoId): int
    {
        return \App\Models\VideoInteraction::where('video_id', $videoId)
            ->where('action', 'view')
            ->whereDate('created_at', today())
            ->count();
    }

    private function getTodayUniqueViews(int $videoId): int
    {
        return \App\Models\VideoInteraction::where('video_id', $videoId)
            ->where('action', 'view')
            ->whereDate('created_at', today())
            ->distinct('user_id')
            ->count();
    }

    private function getTodayWatchTime(int $videoId): int
    {
        $video = Video::select('duration')->find($videoId);
        $duration = $video?->duration ?? 0;

        $interactions = \App\Models\VideoInteraction::where('video_id', $videoId)
            ->where('action', 'view')
            ->whereDate('created_at', today())
            ->sum('watch_percentage');

        return (int) ($interactions / 100 * $duration);
    }

    private function getTodayLikes(int $videoId): int
    {
        return \App\Models\VideoInteraction::where('video_id', $videoId)
            ->where('action', 'like')
            ->whereDate('created_at', today())
            ->count();
    }

    private function getTodayDislikes(int $videoId): int
    {
        return \App\Models\VideoInteraction::where('video_id', $videoId)
            ->where('action', 'dislike')
            ->whereDate('created_at', today())
            ->count();
    }

    private function getTodayComments(int $videoId): int
    {
        return \App\Models\VideoInteraction::where('video_id', $videoId)
            ->where('action', 'comment')
            ->whereDate('created_at', today())
            ->count();
    }

    private function getTrafficSources(int $videoId): array
    {
        return \App\Models\VideoInteraction::where('video_id', $videoId)
            ->where('action', 'view')
            ->whereDate('created_at', today())
            ->whereNotNull('traffic_source')
            ->selectRaw('traffic_source, count(*) as count')
            ->groupBy('traffic_source')
            ->pluck('count', 'traffic_source')
            ->toArray();
    }
}
