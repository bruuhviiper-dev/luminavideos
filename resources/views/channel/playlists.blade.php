@extends('layouts.app')
@section('title', $user->name . ' — Playlists — Lumina')
@section('content')
<div class="min-h-screen pb-20">
    <div class="relative h-40 md:h-56 w-full overflow-hidden bg-tubi-darker border-b border-theme">
        @if($user->banner)
            <img src="{{ Storage::url($user->banner) }}" class="w-full h-full object-cover opacity-80">
        @else
            <div class="w-full h-full bg-gradient-to-r from-tubi-primary via-purple-600 to-tubi-secondary opacity-70"></div>
        @endif
        <div class="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-tubi-dark to-transparent"></div>
    </div>

    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 relative -mt-12 z-10">
        <div class="flex items-end gap-5 pb-6 border-b border-theme">
            <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-tubi-dark bg-tubi-card shadow-xl">
                @if($user->avatar)
                    <img src="{{ Storage::url($user->avatar) }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-gradient-to-br from-tubi-primary to-tubi-secondary flex items-center justify-center text-white text-3xl font-bold">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            <div class="flex-1 pb-2">
                <h1 class="text-2xl font-display font-bold text-tubi-light">{{ $user->name }}</h1>
                <p class="text-sm text-tubi-gray mt-1">{{ number_format($user->subscribers_count) }} inscritos</p>
            </div>
        </div>

        <nav class="flex gap-8 mt-4 border-b border-theme">
            <a href="{{ route('channel.show', $user->username) }}" class="pb-3 text-sm font-bold text-tubi-gray border-b-2 border-transparent hover:text-tubi-light transition-all">Início</a>
            <a href="{{ route('channel.videos', $user->username) }}" class="pb-3 text-sm font-bold text-tubi-gray border-b-2 border-transparent hover:text-tubi-light transition-all">Vídeos</a>
            <a href="{{ route('channel.playlists', $user->username) }}" class="pb-3 text-sm font-bold text-tubi-light border-b-2 border-tubi-primary">Playlists</a>
        </nav>

        <div class="mt-8">
            @if($playlists->isEmpty())
                <div class="flex flex-col items-center justify-center py-24 text-center glass rounded-3xl border border-theme">
                    <div class="w-20 h-20 rounded-full bg-tubi-darker border-2 border-dashed border-theme flex items-center justify-center mb-5">
                        <svg class="w-8 h-8 text-tubi-gray" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a1 1 0 00-2 0v7.268a2 2 0 000 3.464V16a1 1 0 102 0v-1.268a2 2 0 000-3.464V4zM11 4a1 1 0 10-2 0v1.268a2 2 0 000 3.464V16a1 1 0 102 0V8.732a2 2 0 000-3.464V4zM16 3a1 1 0 011 1v7.268a2 2 0 010 3.464V16a1 1 0 11-2 0v-1.268a2 2 0 010-3.464V4a1 1 0 011-1z"/></svg>
                    </div>
                    <p class="text-tubi-light font-bold text-lg">Sem playlists públicas</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($playlists as $playlist)
                    <a href="{{ route('playlist.show', $playlist->id) }}" class="group glass bg-tubi-card rounded-2xl overflow-hidden border border-theme hover:border-tubi-primary/50 transition-all hover:shadow-xl hover:shadow-tubi-primary/10">
                        <div class="aspect-video bg-tubi-darker relative flex items-center justify-center">
                            <div class="absolute inset-0 bg-gradient-to-br from-tubi-primary/20 to-tubi-secondary/20"></div>
                            <svg class="w-12 h-12 text-tubi-gray/50 relative z-10" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a1 1 0 00-2 0v7.268a2 2 0 000 3.464V16a1 1 0 102 0v-1.268a2 2 0 000-3.464V4zM11 4a1 1 0 10-2 0v1.268a2 2 0 000 3.464V16a1 1 0 102 0V8.732a2 2 0 000-3.464V4zM16 3a1 1 0 011 1v7.268a2 2 0 010 3.464V16a1 1 0 11-2 0v-1.268a2 2 0 010-3.464V4a1 1 0 011-1z"/></svg>
                            <span class="absolute bottom-2 right-2 px-2 py-0.5 bg-black/70 text-white text-xs font-bold rounded">{{ $playlist->videos_count }} vídeos</span>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-tubi-light text-sm line-clamp-1 group-hover:text-tubi-primary transition-colors">{{ $playlist->name }}</h3>
                            <p class="text-xs text-tubi-gray mt-1">{{ $playlist->videos_count }} vídeos</p>
                        </div>
                    </a>
                    @endforeach
                </div>
                @if($playlists->hasPages())
                    <div class="mt-10 flex justify-center">{{ $playlists->links('partials.pagination') }}</div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
