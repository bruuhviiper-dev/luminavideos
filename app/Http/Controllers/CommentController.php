<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Notification;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, $videoId)
    {
        $video = Video::findOrFail($videoId);

        if (!$video->allow_comments) {
            return back()->withErrors(['error' => 'Comentários desativados neste vídeo.']);
        }

        $request->validate([
            'content'   => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $comment = Comment::create([
            'video_id'  => $video->id,
            'user_id'   => Auth::id(),
            'parent_id' => $request->parent_id,
            'content'   => $request->content,
        ]);

        $video->increment('comments_count');

        // Notify video owner
        if ($video->user_id !== Auth::id()) {
            Notification::create([
                'user_id'      => $video->user_id,
                'from_user_id' => Auth::id(),
                'type'         => 'new_comment',
                'message'      => Auth::user()->name . ' comentou: "' . \Str::limit($request->content, 60) . '"',
                'url'          => route('video.show', $video->slug),
                'is_read'      => false,
            ]);
        }

        return back()->with('status', 'Comentário enviado!');
    }

    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);

        if ($comment->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403);
        }

        $comment->video->decrement('comments_count');
        $comment->delete();

        return back()->with('status', 'Comentário deletado.');
    }

    public function like($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->increment('likes_count');
        return back();
    }
}
