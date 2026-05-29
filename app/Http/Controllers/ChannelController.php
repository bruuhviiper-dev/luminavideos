<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use App\Models\Video;
use App\Models\Subscription;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChannelController extends Controller
{
    public function show($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        $categories = CacheService::remember(CacheService::categoriesKey(), 60, fn() => Category::all());

        $videos = CacheService::remember(
            CacheService::channelKey($username) . ':videos',
            5,
            fn() => Video::where('user_id', $user->id)
                ->where('is_short', false)
                ->where('visibility', 'public')
                ->where('status', 'active')
                ->with('user')
                ->orderByDesc('created_at')
                ->paginate(20)
        );

        $shorts = Video::where('user_id', $user->id)
            ->where('is_short', true)
            ->where('status', 'active')
            ->orderByDesc('created_at')
            ->limit(12)
            ->get();

        $isSubscribed = Auth::check()
            ? Subscription::where('subscriber_id', Auth::id())->where('channel_id', $user->id)->exists()
            : false;

        $totalViews = Video::where('user_id', $user->id)->sum('views_count');

        return view('channel.show', compact('user', 'videos', 'shorts', 'isSubscribed', 'categories', 'totalViews'));
    }

    public function videos($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        $categories = CacheService::remember(CacheService::categoriesKey(), 60, fn() => Category::all());

        $videos = Video::where('user_id', $user->id)
            ->where('visibility', 'public')
            ->where('status', 'active')
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(24);

        $isSubscribed = Auth::check()
            ? Subscription::where('subscriber_id', Auth::id())->where('channel_id', $user->id)->exists()
            : false;

        return view('channel.videos', compact('user', 'videos', 'isSubscribed', 'categories'));
    }

    public function playlists($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        $categories = CacheService::remember(CacheService::categoriesKey(), 60, fn() => Category::all());

        $playlists = $user->playlists()
            ->where('visibility', 'public')
            ->withCount('videos')
            ->orderByDesc('created_at')
            ->paginate(16);

        $isSubscribed = Auth::check()
            ? Subscription::where('subscriber_id', Auth::id())->where('channel_id', $user->id)->exists()
            : false;

        return view('channel.playlists', compact('user', 'playlists', 'isSubscribed', 'categories'));
    }

    public function subscribe($username)
    {
        $user = User::where('username', $username)->firstOrFail();

        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'Você não pode se inscrever no seu próprio canal.']);
        }

        $existing = Subscription::where('subscriber_id', Auth::id())->where('channel_id', $user->id)->first();

        if (!$existing) {
            Subscription::create(['subscriber_id' => Auth::id(), 'channel_id' => $user->id]);
            $user->increment('subscribers_count');

            // Notify channel owner
            \App\Models\Notification::create([
                'user_id'      => $user->id,
                'from_user_id' => Auth::id(),
                'type'         => 'new_subscriber',
                'message'      => Auth::user()->name . ' se inscreveu no seu canal!',
                'url'          => route('channel.show', Auth::user()->username),
                'is_read'      => false,
            ]);

            CacheService::invalidateChannel($username);
        }

        if (request()->expectsJson()) {
            return response()->json(['subscribed' => true, 'count' => $user->fresh()->subscribers_count]);
        }

        return back()->with('status', 'Inscrito com sucesso!');
    }

    public function unsubscribe($username)
    {
        $user = User::where('username', $username)->firstOrFail();

        Subscription::where('subscriber_id', Auth::id())
            ->where('channel_id', $user->id)
            ->delete();

        if ($user->subscribers_count > 0) {
            $user->decrement('subscribers_count');
        }

        CacheService::invalidateChannel($username);

        if (request()->expectsJson()) {
            return response()->json(['subscribed' => false, 'count' => $user->fresh()->subscribers_count]);
        }

        return back()->with('status', 'Inscrição cancelada.');
    }
}
