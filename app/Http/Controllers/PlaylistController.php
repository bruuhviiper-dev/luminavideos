<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Playlist;
use App\Models\PlaylistVideo;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlaylistController extends Controller
{
    public function index()
    {
        $playlists = Auth::user()->playlists()->get();
        $categories = Category::all();
        return view('playlist.index', compact('playlists', 'categories'));
    }

    public function show($id)
    {
        $playlist = Playlist::findOrFail($id);
        if ($playlist->visibility === 'private' && $playlist->user_id !== Auth::id()) {
            abort(403);
        }
        $videos = $playlist->videos()->paginate(20);
        $categories = Category::all();
        return view('playlist.show', compact('playlist', 'videos', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'visibility' => 'required|in:public,private',
        ]);

        Playlist::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'visibility' => $validated['visibility'],
        ]);

        return back()->with('status', 'Playlist criada com sucesso!');
    }

    public function update(Request $request, $id)
    {
        $playlist = Playlist::findOrFail($id);
        $this->authorize('update', $playlist);
        
        $validated = $request->validate([
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'visibility' => 'in:public,private',
        ]);

        $playlist->update($validated);
        return back()->with('status', 'Playlist atualizada com sucesso!');
    }

    public function destroy($id)
    {
        $playlist = Playlist::findOrFail($id);
        $this->authorize('delete', $playlist);
        $playlist->delete();
        
        return back()->with('status', 'Playlist deletada com sucesso!');
    }

    public function addVideo($id, $videoId)
    {
        $playlist = Playlist::findOrFail($id);
        $this->authorize('update', $playlist);
        
        PlaylistVideo::firstOrCreate([
            'playlist_id' => $playlist->id,
            'video_id' => $videoId,
        ]);

        return back()->with('status', 'Vídeo adicionado à playlist!');
    }

    public function removeVideo($id, $videoId)
    {
        $playlist = Playlist::findOrFail($id);
        $this->authorize('update', $playlist);
        
        PlaylistVideo::where('playlist_id', $playlist->id)
            ->where('video_id', $videoId)
            ->delete();

        return back()->with('status', 'Vídeo removido da playlist!');
    }
}
