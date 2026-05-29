@extends('layouts.app')

@section('title', 'Histórico — Tubiii')

@section('content')
<div class="px-4 py-8 max-w-[1200px] mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-blue-500/10 flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h1 class="text-2xl font-display font-bold text-white">Histórico de Visualização</h1>
                <p class="text-tubi-gray text-sm">Vídeos que você assistiu recentemente</p>
            </div>
        </div>

        @if(isset($history) && $history->isNotEmpty())
        <form action="{{ route('history.clear') }}" method="POST" onsubmit="return confirm('Tem certeza que deseja limpar todo o histórico?');">
            @csrf @method('DELETE')
            <button type="submit" class="px-4 py-2 text-sm text-tubi-gray hover:text-white hover:bg-white/5 rounded-full transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Limpar Histórico
            </button>
        </form>
        @endif
    </div>

    @if(!isset($history) || $history->isEmpty())
    <div class="flex flex-col items-center justify-center py-20 text-center">
        <div class="w-24 h-24 mb-6 rounded-full bg-tubi-card flex items-center justify-center border border-white/5">
            <svg class="w-10 h-10 text-tubi-gray/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-white mb-2">Sem histórico</h3>
        <p class="text-tubi-gray">Os vídeos que você assistir aparecerão aqui.</p>
        <a href="{{ route('home') }}" class="mt-6 px-6 py-2.5 rounded-full bg-tubi-primary/10 text-tubi-light hover:bg-tubi-primary hover:text-white transition-all text-sm font-medium">
            Explorar vídeos
        </a>
    </div>
    @else
    <div class="space-y-4">
        @foreach($history as $item)
        <a href="{{ route('video.show', $item->video->slug) }}" class="flex gap-4 p-3 rounded-2xl hover:bg-white/5 transition-colors group">
            <div class="relative w-48 aspect-video rounded-xl overflow-hidden bg-tubi-card flex-shrink-0">
                @if($item->video->thumbnail)
                    <img src="{{ Storage::url($item->video->thumbnail) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                @endif
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-white/20">
                    <div class="h-full bg-tubi-secondary" style="width: {{ $item->video->duration ? min(100, ($item->watched_seconds / $item->video->duration) * 100) : 0 }}%"></div>
                </div>
            </div>
            <div class="flex-1 py-1">
                <h3 class="text-base font-medium text-white line-clamp-2 group-hover:text-tubi-primary transition-colors">{{ $item->video->title }}</h3>
                <p class="text-sm text-tubi-gray mt-1">{{ $item->video->user->name }}</p>
                <div class="flex items-center gap-2 text-xs text-tubi-gray/70 mt-1">
                    <span>{{ number_format($item->video->views_count) }} views</span>
                    <span>•</span>
                    <span>Assistido {{ $item->updated_at->diffForHumans() }}</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    <div class="mt-8">
        {{ $history->links('partials.pagination') }}
    </div>
    @endif
</div>
@endsection
