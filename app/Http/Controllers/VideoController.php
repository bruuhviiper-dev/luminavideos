<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateVideoQualitiesJob;
use App\Jobs\GenerateSubtitlesJob;
use App\Models\Category;
use App\Models\Like;
use App\Models\Report;
use App\Models\Video;
use App\Models\WatchHistory;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideoController extends Controller
{
    public function show(Request $request)
    {
        $identifier = $request->query('v');
        if (!$identifier) {
            abort(404);
        }

        $video = Video::where(function($query) use ($identifier) {
                $query->where('youtube_id', $identifier)
                      ->orWhere('slug', $identifier);
            })
            ->whereIn('status', ['active', 'processing'])
            ->with('user', 'category')
            ->firstOrFail();

        // Count view (once per session)
        $viewKey = CacheService::viewCountKey($video->id, session()->getId());
        if (!cache()->has($viewKey)) {
            $video->increment('views_count');
            cache()->put($viewKey, true, now()->addHours(24));

            // Track interaction
            if (Auth::check()) {
                \App\Models\VideoInteraction::create([
                    'user_id'        => Auth::id(),
                    'video_id'       => $video->id,
                    'action'         => 'view',
                    'session_id'     => session()->getId(),
                    'traffic_source' => request()->header('referer') ? 'external' : 'direct',
                ]);
            }
        }

        if (Auth::check()) {
            WatchHistory::updateOrCreate(
                ['user_id' => Auth::id(), 'video_id' => $video->id],
                ['watched_seconds' => 0]
            );
        }

        $relatedVideos = CacheService::remember(
            CacheService::relatedVideosKey($video->id),
            15,
            fn() => Video::where('category_id', $video->category_id)
                ->where('id', '!=', $video->id)
                ->where('status', 'active')
                ->where('visibility', 'public')
                ->with('user')
                ->orderByDesc('views_count')
                ->limit(12)
                ->get()
        );

        $comments = $video->comments()
            ->whereNull('parent_id')
            ->with('user', 'replies.user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $categories = CacheService::remember(CacheService::categoriesKey(), 60, fn() => Category::all());

        $userLike = null;
        if (Auth::check()) {
            $userLike = Like::where('user_id', Auth::id())
                ->where('video_id', $video->id)
                ->first();
        }

        return view('video.show', compact('video', 'relatedVideos', 'comments', 'categories', 'userLike'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('video.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string|max:10000',
            'category_id'    => 'required|exists:categories,id',
            'visibility'     => 'required|in:public,unlisted,private',
            'allow_comments' => 'nullable|boolean',
            'video'          => 'required|file|mimes:mp4,avi,mov,mkv,webm|max:2097152',
            'thumbnail'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'tags'           => 'nullable|string|max:500',
            'is_short'       => 'nullable|boolean',
        ]);

        $slug = Str::slug($validated['title']) . '-' . Str::random(8);
        // Ensure slug is unique
        while (Video::where('slug', $slug)->exists()) {
            $slug = Str::slug($validated['title']) . '-' . Str::random(8);
        }

        // Store video file
        $videoFile = $request->file('video');
        $videoPath = $videoFile->store('videos/original', 'public');

        // Process thumbnail
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        // Parse tags
        $tags = null;
        if (!empty($validated['tags'])) {
            $tags = array_map('trim', explode(',', $validated['tags']));
        }

        $youtube_id = Str::random(11);
        while (Video::where('youtube_id', $youtube_id)->exists()) {
            $youtube_id = Str::random(11);
        }

        $video = Video::create([
            'user_id'        => Auth::id(),
            'title'          => $validated['title'],
            'slug'           => $slug,
            'youtube_id'     => $youtube_id,
            'description'    => $validated['description'] ?? null,
            'category_id'    => $validated['category_id'],
            'visibility'     => $validated['visibility'],
            'allow_comments' => $request->boolean('allow_comments', true),
            'video_path'     => $videoPath,
            'thumbnail'      => $thumbnailPath,
            'tags'           => $tags ? json_encode($tags) : null,
            'status'         => 'active', // Ativo imediatamente — HLS é melhoria opcional
            'is_short'       => $request->boolean('is_short', false),
        ]);

        // Jobs de transcodição são opcionais — se não houver worker, vídeo original é servido
        try {
            GenerateVideoQualitiesJob::dispatch($video)->onQueue('videos');
            GenerateSubtitlesJob::dispatch($video)->onQueue('videos');
        } catch (\Exception $e) {
            \Log::info('Jobs de transcodificação não despachados (queue indisponível): ' . $e->getMessage());
        }

        // Invalidar cache do feed
        CacheService::invalidateOnNewVideo(Auth::id());

        // Notificar inscritos
        $this->notifySubscribers($video);

        return redirect()->route('video.show', ['v' => $video->youtube_id ?? $video->slug])
            ->with('status', '🎬 Vídeo publicado com sucesso!');
    }

    public function edit($id)
    {
        $video = Video::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $categories = Category::orderBy('name')->get();
        return view('video.edit', compact('video', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $video = Video::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'category_id'    => 'required|exists:categories,id',
            'visibility'     => 'required|in:public,unlisted,private',
            'allow_comments' => 'nullable|boolean',
            'tags'           => 'nullable|string',
            'thumbnail'      => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($video->thumbnail) {
                Storage::disk('public')->delete($video->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        if (isset($validated['tags'])) {
            $validated['tags'] = json_encode(array_map('trim', explode(',', $validated['tags'])));
        }

        $validated['allow_comments'] = $request->boolean('allow_comments', true);

        $video->update($validated);

        CacheService::invalidateOnVideoDelete($video->id);

        return back()->with('status', 'Vídeo atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $video = Video::findOrFail($id);

        if ($video->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403);
        }

        // Delete files
        if ($video->video_path) Storage::disk('public')->delete($video->video_path);
        if ($video->thumbnail) Storage::disk('public')->delete($video->thumbnail);

        CacheService::invalidateOnVideoDelete($video->id);

        $video->delete();

        return redirect('/')->with('status', 'Vídeo deletado com sucesso!');
    }

    public function like($id)
    {
        $video = Video::findOrFail($id);

        $existing = Like::where('user_id', Auth::id())->where('video_id', $video->id)->first();

        if ($existing) {
            if ($existing->type === 'like') {
                $existing->delete();
                $video->decrement('likes_count');
                $status = 'removed';
            } else {
                $existing->update(['type' => 'like']);
                $video->increment('likes_count');
                $video->decrement('dislikes_count');
                $status = 'liked';
            }
        } else {
            Like::create(['user_id' => Auth::id(), 'video_id' => $video->id, 'type' => 'like']);
            $video->increment('likes_count');
            $status = 'liked';
        }

        if (request()->expectsJson()) {
            return response()->json(['status' => $status, 'likes_count' => $video->likes_count]);
        }

        return back();
    }

    public function dislike($id)
    {
        $video = Video::findOrFail($id);

        $existing = Like::where('user_id', Auth::id())->where('video_id', $video->id)->first();

        if ($existing) {
            if ($existing->type === 'dislike') {
                $existing->delete();
                $video->decrement('dislikes_count');
                $status = 'removed';
            } else {
                $existing->update(['type' => 'dislike']);
                $video->increment('dislikes_count');
                $video->decrement('likes_count');
                $status = 'disliked';
            }
        } else {
            Like::create(['user_id' => Auth::id(), 'video_id' => $video->id, 'type' => 'dislike']);
            $video->increment('dislikes_count');
            $status = 'disliked';
        }

        if (request()->expectsJson()) {
            return response()->json(['status' => $status, 'dislikes_count' => $video->dislikes_count]);
        }

        return back();
    }

    public function report($id, Request $request)
    {
        $video = Video::findOrFail($id);

        $validated = $request->validate([
            'reason'      => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        Report::updateOrCreate(
            ['user_id' => Auth::id(), 'video_id' => $video->id],
            array_merge($validated, ['status' => 'pending'])
        );

        return back()->with('status', 'Denúncia enviada com sucesso!');
    }

    // Shorts section
    public function createShort()
    {
        $categories = Category::orderBy('name')->get();
        return view('shorts.create', compact('categories'));
    }

    public function shortsIndex()
    {
        $shorts = Video::published()->shorts()->with('user')->orderByDesc('created_at')->paginate(5);
        return view('shorts.index', compact('shorts'));
    }

    // Track interaction via AJAX
    public function trackInteraction(Request $request)
    {
        $request->validate([
            'video_id'        => 'required|exists:videos,id',
            'action'          => 'required|in:view,like,dislike,share,comment,skip',
            'watch_percentage'=> 'nullable|integer|min:0|max:100',
        ]);

        \App\Models\VideoInteraction::create([
            'user_id'          => Auth::id(),
            'video_id'         => $request->video_id,
            'action'           => $request->action,
            'watch_percentage' => $request->watch_percentage ?? 0,
            'session_id'       => session()->getId(),
            'traffic_source'   => $request->header('referer') ? 'external' : 'direct',
        ]);

        return response()->json(['ok' => true]);
    }

    private function notifySubscribers(Video $video): void
    {
        try {
            $subscribers = \App\Models\Subscription::where('channel_id', $video->user_id)->pluck('subscriber_id');

            foreach ($subscribers as $subscriberId) {
                \App\Models\Notification::create([
                    'user_id'      => $subscriberId,
                    'from_user_id' => $video->user_id,
                    'type'         => 'new_video',
                    'message'      => Auth::user()->name . ' publicou: ' . $video->title,
                    'url'          => route('video.show', ['v' => $video->youtube_id ?? $video->slug]),
                    'is_read'      => false,
                ]);
            }
        } catch (\Exception $e) {
            // Silently fail — don't block upload
        }
    }

    public function apiShow(Request $request)
    {
        $identifier = $request->query('v');
        if (!$identifier) {
            return response()->json(['error' => 'No identifier provided'], 404);
        }

        $video = Video::where(function($query) use ($identifier) {
                $query->where('youtube_id', $identifier)->orWhere('slug', $identifier);
            })->whereIn('status', ['active', 'processing'])->with('user')->first();

        if (!$video) {
            return response()->json(['error' => 'Video not found'], 404);
        }

        // Simula view increment
        $viewKey = CacheService::viewCountKey($video->id, session()->getId());
        if (!cache()->has($viewKey)) {
            $video->increment('views_count');
            cache()->put($viewKey, true, now()->addHours(24));
        }

        $comments = $video->comments()->with('user')->latest()->take(30)->get();
        $commentsHtml = view('partials.comments-list', compact('comments'))->render();

        return response()->json([
            'id' => $video->id,
            'title' => $video->title,
            'description' => $video->description,
            'video_url' => $video->video_url ?? Storage::url($video->video_path),
            'hls_url' => $video->hls_url,
            'poster' => $video->thumbnail ? (Str::startsWith($video->thumbnail, 'http') ? $video->thumbnail : Storage::url($video->thumbnail)) : '',
            'user' => [
                'name' => $video->user->name,
                'username' => $video->user->username,
                'avatar' => $video->user->avatar ? Storage::url($video->user->avatar) : null,
                'is_verified' => $video->user->is_verified,
                'channel_url' => route('channel.show', $video->user->username)
            ],
            'tags' => is_string($video->tags) ? explode(',', str_replace(['[', ']', '"'], '', $video->tags)) : (is_array($video->tags) ? $video->tags : []),
            'views_count' => number_format($video->views_count),
            'likes_count' => number_format($video->likes_count),
            'comments_count' => number_format($video->comments()->count()),
            'comments_html' => $commentsHtml,
            'url' => route('video.show', ['v' => $video->youtube_id ?? $video->slug])
        ]);
    }
}
