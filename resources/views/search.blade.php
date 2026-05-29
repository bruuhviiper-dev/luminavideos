@extends('layouts.app')

@section('title', 'Resultados para "' . request('q') . '" — Lumina')

@section('content')
<div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-20">
    
    <!-- Cabeçalho de Busca -->
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl font-display font-bold text-tubi-light flex items-center gap-3">
            <svg class="w-8 h-8 text-tubi-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            Resultados para <span class="text-transparent bg-clip-text bg-gradient-to-r from-tubi-primary to-tubi-secondary">"{{ request('q') }}"</span>
        </h1>
        <p class="text-tubi-gray mt-2 font-medium">Encontramos {{ $videos->total() }} vídeos correspondentes na plataforma.</p>
    </div>

    <!-- Filtros Glassmorphism -->
    <div class="glass bg-tubi-darker/60 rounded-2xl p-4 mb-8 flex flex-wrap gap-4 items-center justify-between border border-theme shadow-sm">
        <div class="flex flex-wrap gap-3">
            <button class="px-5 py-2 rounded-full bg-tubi-primary text-white text-sm font-bold shadow-[0_4px_15px_rgba(124,58,237,0.3)] transition-all">Relevância</button>
            <button class="px-5 py-2 rounded-full bg-tubi-card border border-theme text-tubi-gray hover:text-tubi-light hover:bg-black/5 dark:hover:bg-white/5 text-sm font-bold transition-all">Mais Recentes</button>
            <button class="px-5 py-2 rounded-full bg-tubi-card border border-theme text-tubi-gray hover:text-tubi-light hover:bg-black/5 dark:hover:bg-white/5 text-sm font-bold transition-all">Visualizações</button>
        </div>
        <button class="flex items-center gap-2 px-4 py-2 rounded-xl text-tubi-gray hover:text-tubi-primary font-bold text-sm transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
            Filtros Avançados
        </button>
    </div>

    @if($videos->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center glass rounded-3xl border border-theme">
            <div class="w-24 h-24 rounded-full bg-tubi-darker border-2 border-dashed border-theme flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-tubi-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
            </div>
            <p class="text-tubi-light font-bold text-xl mb-2">Nenhum resultado encontrado</p>
            <p class="text-tubi-gray text-sm max-w-md">Não conseguimos encontrar vídeos ou canais com esse termo. Tente usar palavras-chave diferentes ou remova os filtros.</p>
        </div>
    @else
        <!-- Lista de Vídeos Horizontal (estilo YouTube Web Busca) -->
        <div class="flex flex-col gap-6">
            @foreach($videos as $video)
                @php
                    $thumb = $video->thumbnail ? (Str::startsWith($video->thumbnail, 'http') ? $video->thumbnail : Storage::url($video->thumbnail)) : null;
                    $dur = $video->duration ?? 0;
                    $durStr = $dur > 0 ? sprintf('%d:%02d', floor($dur/60), $dur%60) : '';
                    if($dur >= 3600) $durStr = sprintf('%d:%02d:%02d', floor($dur/3600), floor(($dur%3600)/60), $dur%60);
                @endphp
                <article class="group flex flex-col md:flex-row gap-4 p-4 rounded-2xl border border-transparent hover:border-theme hover:bg-black/5 dark:hover:bg-white/5 transition-all">
                    
                    <!-- Thumbnail -->
                    <a href="{{ route('video.show', ['v' => $video->youtube_id ?? $video->slug]) }}" class="block relative w-full md:w-80 lg:w-[360px] aspect-video rounded-xl overflow-hidden bg-tubi-card shadow-md flex-shrink-0">
                        @if($thumb)
                            <img src="{{ $thumb }}" alt="{{ $video->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-tubi-card">
                                <svg class="w-12 h-12 text-tubi-gray/30" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            </div>
                        @endif
                        
                        <div class="absolute bottom-2 right-2 flex flex-col gap-1">
                            @if($durStr)
                                <span class="px-2 py-1 bg-black/80 backdrop-blur-sm text-white text-xs font-bold rounded shadow-sm">{{ $durStr }}</span>
                            @endif
                        </div>
                    </a>

                    <!-- Informações do Vídeo -->
                    <div class="flex flex-col flex-1 min-w-0 pt-1">
                        <a href="{{ route('video.show', ['v' => $video->youtube_id ?? $video->slug]) }}" class="mb-2">
                            <h3 class="text-lg md:text-xl font-bold text-tubi-light line-clamp-2 leading-tight group-hover:text-tubi-primary transition-colors">
                                {{ $video->title }}
                            </h3>
                        </a>
                        
                        <div class="flex items-center gap-2 text-xs md:text-sm font-medium text-tubi-gray mb-3">
                            <span>{{ number_format($video->views_count) }} visualizações</span>
                            <span class="w-1 h-1 rounded-full bg-tubi-gray/50"></span>
                            <span>{{ $video->created_at->diffForHumans() }}</span>
                        </div>
                        
                        <!-- Canal Info Inline -->
                        <a href="{{ route('channel.show', $video->user->username) }}" class="flex items-center gap-3 mb-3 group/channel">
                            <div class="w-8 h-8 rounded-full overflow-hidden bg-tubi-card group-hover/channel:ring-2 ring-tubi-primary/50 transition-all">
                                @if($video->user->avatar)
                                    <img src="{{ Storage::url($video->user->avatar) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-tubi-primary to-tubi-secondary flex items-center justify-center text-white text-xs font-bold font-display">
                                        {{ strtoupper(substr($video->user->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <span class="text-sm font-bold text-tubi-gray group-hover/channel:text-tubi-light transition-colors">{{ $video->user->name }}</span>
                            @if($video->user->is_verified)
                                <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            @endif
                        </a>

                        @if($video->description)
                            <p class="text-sm text-tubi-gray line-clamp-2 hidden sm:block leading-relaxed">{{ Str::limit($video->description, 150) }}</p>
                        @endif

                        <!-- Tags (Badge) -->
                        @if($video->is_short)
                            <div class="mt-auto pt-3">
                                <span class="px-2.5 py-1 bg-tubi-secondary/10 text-tubi-secondary border border-tubi-secondary/20 text-xs rounded-full font-bold uppercase tracking-wider">Lumina Shorts</span>
                            </div>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        @if($videos->hasPages())
        <div class="mt-12 flex justify-center">
            {{ $videos->links('partials.pagination') }}
        </div>
        @endif
    @endif
</div>
@endsection
