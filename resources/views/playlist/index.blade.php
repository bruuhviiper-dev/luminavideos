@extends('layouts.app')

@section('title', 'Playlists — Tubiii')

@section('content')
<div class="px-4 py-8 max-w-[1600px] mx-auto" x-data="{ showModal: false }">
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-green-500/10 flex items-center justify-center">
                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h8"/></svg>
            </div>
            <div>
                <h1 class="text-2xl font-display font-bold text-white">Suas Playlists</h1>
                <p class="text-tubi-gray text-sm">Organize seus vídeos favoritos</p>
            </div>
        </div>

        <button @click="showModal = true" class="px-5 py-2.5 rounded-full bg-tubi-primary text-white text-sm font-semibold hover:bg-tubi-primary/90 transition-colors shadow-[0_0_15px_rgba(124,58,237,0.3)]">
            + Nova Playlist
        </button>
    </div>

    @if(!isset($playlists) || $playlists->isEmpty())
    <div class="flex flex-col items-center justify-center py-20 text-center">
        <div class="w-24 h-24 mb-6 rounded-full bg-tubi-card flex items-center justify-center border border-white/5">
            <svg class="w-10 h-10 text-tubi-gray/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-white mb-2">Nenhuma playlist criada</h3>
        <p class="text-tubi-gray">Crie coleções para agrupar vídeos sobre o mesmo tema.</p>
        <button @click="showModal = true" class="mt-6 px-6 py-2.5 rounded-full bg-tubi-primary/10 text-tubi-light hover:bg-tubi-primary hover:text-white transition-all text-sm font-medium">
            Criar primeira playlist
        </button>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($playlists as $playlist)
        <a href="{{ route('playlist.show', $playlist->id) }}" class="group block rounded-2xl overflow-hidden bg-tubi-card border border-white/5 hover:border-tubi-primary/50 transition-all hover:-translate-y-1 hover:shadow-[0_10px_30px_rgba(124,58,237,0.15)]">
            <div class="relative aspect-video bg-tubi-darker flex items-center justify-center overflow-hidden">
                @if($playlist->videos->isNotEmpty() && $playlist->videos->first()->thumbnail)
                    <img src="{{ Storage::url($playlist->videos->first()->thumbnail) }}" class="w-full h-full object-cover opacity-70 group-hover:scale-105 transition-transform duration-500">
                @else
                    <svg class="w-10 h-10 text-tubi-gray/30" fill="currentColor" viewBox="0 0 24 24"><path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h8v2H4z"/></svg>
                @endif
                
                <!-- Overlay badge -->
                <div class="absolute right-0 top-0 bottom-0 w-1/3 bg-black/60 backdrop-blur-sm flex flex-col items-center justify-center gap-1 border-l border-white/10">
                    <span class="text-white font-bold">{{ $playlist->videos_count ?? $playlist->videos->count() }}</span>
                    <svg class="w-5 h-5 text-tubi-gray" fill="currentColor" viewBox="0 0 24 24"><path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h8v2H4zm10 3v-6l5 3z"/></svg>
                </div>
            </div>
            <div class="p-4">
                <h3 class="text-base font-medium text-white group-hover:text-tubi-primary transition-colors truncate">{{ $playlist->name }}</h3>
                <p class="text-sm text-tubi-gray mt-1 flex items-center gap-2">
                    @if($playlist->visibility === 'public')
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Pública
                    @elseif($playlist->visibility === 'unlisted')
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg> Não listada
                    @else
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg> Privada
                    @endif
                    • Atualizada {{ $playlist->updated_at->diffForHumans() }}
                </p>
            </div>
        </a>
        @endforeach
    </div>
    @endif

    <!-- Create Playlist Modal -->
    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showModal = false"></div>
        <div class="relative bg-tubi-card border border-white/10 rounded-2xl p-6 w-full max-w-md shadow-2xl animate-fade-in" @click.stop>
            <h3 class="text-xl font-bold text-white mb-4">Nova Playlist</h3>
            <form action="{{ route('playlist.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-tubi-gray mb-1">Nome</label>
                    <input type="text" name="name" required placeholder="Ex: Músicas para Codar"
                        class="w-full bg-tubi-dark border border-white/10 rounded-xl px-4 py-2 text-white focus:border-tubi-primary focus:ring-1 focus:ring-tubi-primary outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-tubi-gray mb-1">Visibilidade</label>
                    <select name="visibility" class="w-full bg-tubi-dark border border-white/10 rounded-xl px-4 py-2 text-white focus:border-tubi-primary focus:ring-1 focus:ring-tubi-primary outline-none transition-all">
                        <option value="public">Pública</option>
                        <option value="unlisted">Não listada</option>
                        <option value="private">Privada</option>
                    </select>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="button" @click="showModal = false" class="flex-1 px-4 py-2 rounded-xl text-tubi-gray hover:text-white hover:bg-white/5 transition-colors font-medium">Cancelar</button>
                    <button type="submit" class="flex-1 px-4 py-2 rounded-xl bg-tubi-primary text-white font-medium hover:bg-tubi-primary/90 transition-colors">Criar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
