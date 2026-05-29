@extends('layouts.app')

@section('title', 'Ao Vivo — Tubiii')

@section('content')
<div class="px-4 py-8 max-w-[1600px] mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <div class="w-12 h-12 rounded-full bg-red-500/10 flex items-center justify-center relative">
            <div class="absolute inset-0 rounded-full border-2 border-red-500 animate-ping opacity-20"></div>
            <svg class="w-6 h-6 text-red-500 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <h1 class="text-2xl font-display font-bold text-white">Transmissões Ao Vivo</h1>
            <p class="text-tubi-gray text-sm">Acompanhe criadores em tempo real</p>
        </div>
    </div>

    <!-- Empty State -->
    <div class="flex flex-col items-center justify-center py-20 text-center">
        <div class="w-32 h-32 mb-6">
            <svg viewBox="0 0 100 100" fill="none" class="w-full h-full opacity-60">
                <circle cx="50" cy="50" r="40" stroke="#FF6B6B" stroke-width="2" stroke-dasharray="8 8" class="animate-[spin_10s_linear_infinite]"/>
                <circle cx="50" cy="50" r="25" fill="#151520" stroke="#7C3AED" stroke-width="4"/>
                <circle cx="50" cy="50" r="10" fill="#FF6B6B" class="animate-pulse"/>
            </svg>
        </div>
        <h3 class="text-xl font-bold text-white mb-2">Ninguém transmitindo agora</h3>
        <p class="text-tubi-gray max-w-sm">Parece que todos estão offline no momento. Que tal começar a sua própria live?</p>
        
        @auth
        <a href="#" class="mt-8 px-6 py-3 rounded-full bg-red-500/10 text-red-400 font-medium hover:bg-red-500/20 transition-colors border border-red-500/20">
            Começar Transmissão
        </a>
        @endauth
    </div>
</div>
@endsection
