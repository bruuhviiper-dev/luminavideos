@extends('layouts.app')
@section('title', 'Notificações — Tubiii')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6 pb-20 md:pb-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold dark:text-white flex items-center gap-2">
            <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            Notificações
        </h1>
        <form action="{{ route('notifications.readAll') }}" method="POST">
            @csrf
            <button type="submit" class="text-sm text-brand-500 hover:underline">Marcar todas como lidas</button>
        </form>
    </div>

    @if($notifications->isEmpty())
    <div class="text-center py-20 dark:bg-dark-800 bg-white rounded-2xl">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        <h3 class="font-semibold dark:text-white">Tudo limpo por aqui!</h3>
        <p class="text-gray-500 text-sm mt-1">Você não tem notificações.</p>
    </div>
    @else
    <div class="dark:bg-dark-800 bg-white rounded-2xl shadow-sm overflow-hidden">
        @foreach($notifications as $notification)
        <a href="{{ $notification->url ?? '#' }}"
            class="flex items-start gap-4 px-5 py-4 border-b dark:border-dark-700 border-gray-100 last:border-0 hover:dark:bg-dark-700 hover:bg-gray-50 transition-colors {{ $notification->is_read ? 'opacity-70' : '' }}">

            <div class="w-10 h-10 rounded-full flex-shrink-0 flex items-center justify-center
                @switch($notification->type)
                    @case('new_video') bg-brand-600/20 @break
                    @case('new_subscriber') bg-blue-600/20 @break
                    @case('new_comment') bg-green-600/20 @break
                    @case('live_started') bg-red-600/20 @break
                    @default bg-gray-600/20
                @endswitch">
                @switch($notification->type)
                    @case('new_video')
                        <svg class="w-5 h-5 text-brand-500" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/></svg>
                        @break
                    @case('new_subscriber')
                        <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>
                        @break
                    @case('live_started')
                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
                        @break
                    @default
                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6z"/></svg>
                @endswitch
            </div>

            <div class="flex-1 min-w-0">
                <p class="text-sm dark:text-gray-200 text-gray-800 leading-relaxed">{{ $notification->message }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
            </div>

            @if(!$notification->is_read)
                <span class="w-2.5 h-2.5 bg-brand-500 rounded-full flex-shrink-0 mt-2"></span>
            @endif
        </a>
        @endforeach
    </div>

    @if($notifications->hasPages())
    <div class="mt-6">{{ $notifications->links('partials.pagination') }}</div>
    @endif
    @endif
</div>
@endsection
