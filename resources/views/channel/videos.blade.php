@extends('layouts.app')
@section('title', $user->name . ' — Vídeos — Lumina')
@section('content')
<div class="min-h-screen pb-20">
    {{-- Banner --}}
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
            @auth
                @if(Auth::id() !== $user->id)
                    <div x-data="subscribeBtn('{{ $user->username }}', {{ $isSubscribed ? 'true' : 'false' }})">
                        <button @click="toggle()"
                            :class="subscribed ? 'bg-tubi-card border border-theme text-tubi-light' : 'bg-tubi-light text-tubi-darker'"
                            class="px-6 py-2.5 rounded-full font-bold text-sm transition-all mb-2">
                            <span x-show="!subscribed">Inscrever-se</span>
                            <span x-show="subscribed" style="display:none">✓ Inscrito</span>
                        </button>
                    </div>
                @endif
            @endauth
        </div>

        <nav class="flex gap-8 mt-4 border-b border-theme">
            <a href="{{ route('channel.show', $user->username) }}" class="pb-3 text-sm font-bold text-tubi-gray border-b-2 border-transparent hover:text-tubi-light transition-all">Início</a>
            <a href="{{ route('channel.videos', $user->username) }}" class="pb-3 text-sm font-bold text-tubi-light border-b-2 border-tubi-primary">Vídeos</a>
            <a href="{{ route('channel.playlists', $user->username) }}" class="pb-3 text-sm font-bold text-tubi-gray border-b-2 border-transparent hover:text-tubi-light transition-all">Playlists</a>
        </nav>

        <div class="mt-8">
            @if($videos->isEmpty())
                <div class="flex flex-col items-center justify-center py-24 text-center glass rounded-3xl border border-theme">
                    <div class="w-20 h-20 rounded-full bg-tubi-darker border-2 border-dashed border-theme flex items-center justify-center mb-5">
                        <svg class="w-8 h-8 text-tubi-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.069A1 1 0 0121 8.845v6.31a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </div>
                    <p class="text-tubi-light font-bold text-lg">Sem vídeos públicos ainda</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($videos as $video)
                        @include('partials.video-card', ['video' => $video])
                    @endforeach
                </div>
                @if($videos->hasPages())
                    <div class="mt-10 flex justify-center">{{ $videos->links('partials.pagination') }}</div>
                @endif
            @endif
        </div>
    </div>
</div>
@section('scripts')
<script>
function subscribeBtn(username, initialStatus) {
    return {
        subscribed: initialStatus,
        async toggle() {
            const method = this.subscribed ? 'DELETE' : 'POST';
            const url = `/canal/@${username}/${this.subscribed ? 'unsubscribe' : 'subscribe'}`;
            const res = await fetch(url, { method, headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' } });
            if (res.ok) this.subscribed = !this.subscribed;
        }
    }
}
</script>
@endsection
@endsection
