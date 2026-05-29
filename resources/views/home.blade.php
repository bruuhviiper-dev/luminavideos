@extends('layouts.app')

@section('title', 'Tubiii — Descubra o Novo')

@section('content')
<div class="px-2 py-6 md:px-4 md:py-8 max-w-[1800px] mx-auto animate-fade-in" x-data="{ activeFilter: '{{ request()->segment(2) ?? 'all' }}' }">

    <!-- Category Pills -->
    <div class="flex gap-3 overflow-x-auto pb-4 mb-6 scrollbar-hide -mx-1 px-1 snap-x">
        <a href="/" 
            class="px-6 py-2.5 rounded-full text-sm font-bold flex-shrink-0 transition-all duration-300 snap-start border flex items-center gap-2"
            :class="activeFilter === 'all' 
                ? 'bg-tubi-primary text-white border-transparent shadow-[0_4px_15px_rgba(124,58,237,0.3)]' 
                : 'dark:bg-white/5 dark:backdrop-blur-md dark:border-white/10 dark:text-tubi-light bg-black/5 border-black/10 text-gray-800 hover:dark:bg-white/10 hover:bg-black/10'">
            Todos
        </a>
        
        @foreach($categories as $cat)
        <a href="{{ route('category', $cat->slug) }}" 
            class="px-6 py-2.5 rounded-full text-sm font-bold flex-shrink-0 transition-all duration-300 snap-start border flex items-center gap-2"
            :class="activeFilter === '{{ $cat->slug }}' 
                ? 'bg-tubi-primary text-white border-transparent shadow-[0_4px_15px_rgba(124,58,237,0.3)]' 
                : 'dark:bg-white/5 dark:backdrop-blur-md dark:border-white/10 dark:text-tubi-light bg-black/5 border-black/10 text-gray-800 hover:dark:bg-white/10 hover:bg-black/10'">
            {!! $cat->icon ?? '' !!} <span>{{ $cat->name }}</span>
        </a>
        @endforeach
    </div>

    <!-- Main Feed -->
    <section>
        <div class="flex items-center justify-between mb-4 px-2">
            <h2 class="text-xl md:text-2xl font-bold dark:text-tubi-light text-gray-900 flex items-center gap-2">
                Descobrir
            </h2>
        </div>

        @if($videos->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 md:py-32 text-center px-4">
            <div class="w-20 h-20 bg-tubi-darker rounded-full border border-theme flex items-center justify-center mb-6">
                <svg class="w-8 h-8 text-tubi-gray" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
            </div>
            <h3 class="text-2xl font-bold dark:text-tubi-light text-gray-900 mb-2">Nenhum vídeo no radar</h3>
            <p class="text-gray-500 dark:text-tubi-gray mb-8">Navegue pelas categorias ou publique um conteúdo para animar o feed.</p>
        </div>
        @else
        <!-- Grid Vertical Estilo TikTok Explore -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-7 gap-3 sm:gap-4 md:gap-5">
            @foreach($videos as $video)
                @include('partials.video-card', ['video' => $video])
            @endforeach
        </div>

        <!-- Pagination -->
        @if($videos->hasPages())
        <div class="mt-12 flex justify-center">
            {{ $videos->links('partials.pagination') }}
        </div>
        @endif
        @endif
    </section>
</div>
@endsection
