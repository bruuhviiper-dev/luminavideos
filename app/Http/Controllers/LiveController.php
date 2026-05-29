<?php

namespace App\Http\Controllers;

use App\Jobs\NotifySubscribersLiveStartedJob;
use App\Models\Live;
use App\Models\LiveChatMessage;
use App\Models\LiveBan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class LiveController extends Controller
{
    public function index()
    {
        $lives = Live::where('status', 'live')
            ->with('user')
            ->orderByDesc('viewers_count')
            ->paginate(12);

        return view('live.index', compact('lives'));
    }

    public function create()
    {
        return view('live.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
        ]);

        $live = Live::create([
            'user_id'    => Auth::id(),
            'title'      => $request->title,
            'description'=> $request->description,
            'stream_key' => Str::random(32),
            'status'     => 'waiting',
        ]);

        return redirect()->route('live.show', $live->id)
            ->with('status', 'Live criada! Configure seu OBS e inicie a transmissão.');
    }

    public function show(Live $live)
    {
        $live->load('user', 'messages.user');

        $recentMessages = $live->messages()
            ->where('is_deleted', false)
            ->with('user')
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->reverse()
            ->values();

        $isBanned = false;
        if (Auth::check()) {
            $isBanned = $live->isUserBanned(Auth::id());
        }

        $superChats = $live->superChats()
            ->where('status', 'approved')
            ->with('user')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('live.show', compact('live', 'recentMessages', 'isBanned', 'superChats'));
    }

    public function end(Live $live)
    {
        if ($live->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403);
        }

        $live->update([
            'status'   => 'ended',
            'ended_at' => now(),
        ]);

        Cache::forget("live_viewers_{$live->id}");

        return redirect()->route('home')->with('status', 'Transmissão encerrada.');
    }

    public function webhook(Request $request)
    {
        $streamKey = $request->input('name'); // NGINX sends stream key as 'name'
        $call      = $request->input('call'); // 'publish' or 'publish_done'

        $live = Live::where('stream_key', $streamKey)->first();

        if (!$live) {
            return response('Not found', 404);
        }

        if ($call === 'publish') {
            $live->update(['status' => 'live', 'started_at' => now()]);
            NotifySubscribersLiveStartedJob::dispatch($live);
        } elseif ($call === 'publish_done') {
            $live->update(['status' => 'ended', 'ended_at' => now()]);
        }

        return response('OK', 200);
    }

    public function sendMessage(Request $request, Live $live)
    {
        if ($live->status === 'ended') {
            return response()->json(['error' => 'Live encerrada'], 422);
        }

        if ($live->isUserBanned(Auth::id())) {
            return response()->json(['error' => 'Você foi banido deste chat'], 403);
        }

        // Rate limit: 1 message per second per user
        $rateLimitKey = "chat_rate_{$live->id}_" . Auth::id();
        if (Cache::has($rateLimitKey)) {
            return response()->json(['error' => 'Aguarde 1 segundo entre mensagens'], 429);
        }
        Cache::put($rateLimitKey, true, 1);

        $request->validate([
            'message' => 'required|string|max:200',
        ]);

        $message = LiveChatMessage::create([
            'live_id' => $live->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);

        $message->load('user');

        // Broadcast event (Reverb)
        try {
            event(new \App\Events\LiveChatMessageSent($message));
        } catch (\Exception $e) {
            // Reverb may not be configured
        }

        return response()->json([
            'id'         => $message->id,
            'message'    => $message->message,
            'user_name'  => $message->user->name,
            'user_avatar'=> $message->user->avatar ?? null,
            'created_at' => $message->created_at->toISOString(),
        ]);
    }

    public function deleteMessage(Request $request, Live $live, LiveChatMessage $message)
    {
        if ($live->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403);
        }

        $message->update(['is_deleted' => true]);

        return response()->json(['success' => true]);
    }

    public function banUser(Request $request, Live $live)
    {
        if ($live->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403);
        }

        $request->validate(['user_id' => 'required|exists:users,id']);

        LiveBan::firstOrCreate([
            'live_id' => $live->id,
            'user_id' => $request->user_id,
        ]);

        return response()->json(['success' => true]);
    }

    public function updateViewers(Live $live)
    {
        $key = "live_viewers_{$live->id}";
        $viewers = Cache::increment($key);
        Cache::put($key, $viewers, 300);

        $live->update(['viewers_count' => $viewers]);

        return response()->json(['viewers_count' => $viewers]);
    }

    public function getMessages(Live $live, Request $request)
    {
        $since = $request->input('since_id', 0);

        $messages = $live->messages()
            ->where('id', '>', $since)
            ->where('is_deleted', false)
            ->with('user')
            ->orderBy('created_at')
            ->limit(50)
            ->get();

        return response()->json($messages->map(fn($m) => [
            'id'          => $m->id,
            'message'     => $m->message,
            'user_name'   => $m->user->name,
            'user_avatar' => $m->user->avatar ?? null,
            'is_super_chat'      => $m->is_super_chat,
            'super_chat_amount'  => $m->super_chat_amount,
            'created_at'  => $m->created_at->toISOString(),
        ]));
    }
}
