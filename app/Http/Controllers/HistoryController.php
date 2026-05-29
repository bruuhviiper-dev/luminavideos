<?php

namespace App\Http\Controllers;

use App\Models\WatchHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    public function index()
    {
        $categories = \App\Services\CacheService::remember(
            \App\Services\CacheService::categoriesKey(), 60, fn() => \App\Models\Category::all()
        );

        $history = WatchHistory::where('user_id', Auth::id())
            ->with('video.user')
            ->orderByDesc('updated_at')
            ->paginate(24);

        return view('history', compact('history', 'categories'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'video_id'        => 'required|exists:videos,id',
            'watched_seconds' => 'nullable|integer|min:0',
        ]);

        WatchHistory::updateOrCreate(
            ['user_id' => Auth::id(), 'video_id' => $request->video_id],
            ['watched_seconds' => $request->watched_seconds ?? 0, 'updated_at' => now()]
        );

        return response()->json(['ok' => true]);
    }

    public function clear()
    {
        WatchHistory::where('user_id', Auth::id())->delete();
        return back()->with('status', 'Histórico limpo com sucesso!');
    }
}
