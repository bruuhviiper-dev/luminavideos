<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\VideoAnalytic;
use App\Models\VideoInteraction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $period = $request->input('period', '28');
        $startDate = $this->getStartDate($period, $request->input('from'));
        $endDate = $request->input('to') ? \Carbon\Carbon::parse($request->input('to')) : now();

        // Overview cards
        $videos = Video::where('user_id', $user->id)->pluck('id');

        $totalViews = VideoAnalytic::whereIn('video_id', $videos)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('views');

        $totalWatchTime = VideoAnalytic::whereIn('video_id', $videos)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('watch_time_seconds');

        $subscribersGained = VideoAnalytic::whereIn('video_id', $videos)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('subscribers_gained');

        // Daily views chart data
        $dailyViews = VideoAnalytic::whereIn('video_id', $videos)
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('date, sum(views) as total_views')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $chartLabels = $dailyViews->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'));
        $chartData   = $dailyViews->pluck('total_views');

        // Top videos
        $topVideos = Video::where('user_id', $user->id)
            ->withSum(['analytics as period_views' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate]);
            }], 'views')
            ->withSum(['analytics as period_watch_time' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate]);
            }], 'watch_time_seconds')
            ->orderByDesc('period_views')
            ->limit(10)
            ->get();

        // Audience by hour
        $hourlyViews = VideoInteraction::whereIn('video_id', $videos)
            ->where('action', 'view')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('CAST(strftime("%H", created_at) AS INTEGER) as hour, count(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('count', 'hour')
            ->toArray();

        $hourlyLabels = range(0, 23);
        $hourlyData   = array_map(fn($h) => $hourlyViews[$h] ?? 0, $hourlyLabels);

        // Traffic sources — retorna objetos com ->source e ->views para a view
        $trafficSources = VideoInteraction::whereIn('video_id', $videos)
            ->where('action', 'view')
            ->whereNotNull('traffic_source')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('traffic_source as source, count(*) as views')
            ->groupBy('traffic_source')
            ->orderByDesc('views')
            ->get();

        // Currently watching (last 5 min)
        $watchingNow = VideoInteraction::whereIn('video_id', $videos)
            ->where('action', 'view')
            ->where('created_at', '>=', now()->subMinutes(5))
            ->count();

        // Aliases para a view (snake_case -> camelCase correto)
        $totalviews    = $totalViews;
        $totalwatchTime = $totalWatchTime;

        return view('studio.analytics', compact(
            'totalViews', 'totalviews', 'totalWatchTime', 'totalwatchTime', 'subscribersGained',
            'chartLabels', 'chartData',
            'topVideos',
            'hourlyLabels', 'hourlyData',
            'trafficSources',
            'watchingNow',
            'period', 'startDate', 'endDate'
        ));

    }

    public function videoDetail(Request $request, Video $video)
    {
        if ($video->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403);
        }

        $period = $request->input('period', '28');
        $startDate = $this->getStartDate($period, $request->input('from'));
        $endDate = now();

        $dailyAnalytics = VideoAnalytic::where('video_id', $video->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();

        $totalViews     = $dailyAnalytics->sum('views');
        $totalWatchTime = $dailyAnalytics->sum('watch_time_seconds');
        $totalLikes     = $dailyAnalytics->sum('likes');

        $chartLabels = $dailyAnalytics->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'));
        $viewsData   = $dailyAnalytics->pluck('views');
        $watchData   = $dailyAnalytics->pluck('watch_time_seconds')->map(fn($s) => round($s / 3600, 2));

        // Retention data: watch_percentage distribution
        $retentionData = VideoInteraction::where('video_id', $video->id)
            ->where('action', 'view')
            ->selectRaw('watch_percentage, count(*) as count')
            ->groupBy('watch_percentage')
            ->orderBy('watch_percentage')
            ->pluck('count', 'watch_percentage')
            ->toArray();

        return view('studio.analytics-video', compact(
            'video', 'totalViews', 'totalWatchTime', 'totalLikes',
            'chartLabels', 'viewsData', 'watchData',
            'retentionData', 'period'
        ));
    }

    public function recordInteraction(Request $request)
    {
        $request->validate([
            'video_id'       => 'required|exists:videos,id',
            'action'         => 'required|in:view,like,dislike,share,comment,skip',
            'watch_percentage' => 'nullable|integer|min:0|max:100',
            'traffic_source' => 'nullable|string|max:50',
        ]);

        VideoInteraction::create([
            'user_id'        => Auth::id(),
            'video_id'       => $request->video_id,
            'action'         => $request->action,
            'watch_percentage' => $request->watch_percentage ?? 0,
            'session_id'     => session()->getId(),
            'traffic_source' => $request->traffic_source ?? 'direct',
        ]);

        return response()->json(['success' => true]);
    }

    private function getStartDate(string $period, ?string $customFrom = null): \Carbon\Carbon
    {
        if ($customFrom) return \Carbon\Carbon::parse($customFrom);

        return match ($period) {
            '7'   => now()->subDays(7),
            '90'  => now()->subDays(90),
            '365' => now()->subDays(365),
            default => now()->subDays(28),
        };
    }
}
