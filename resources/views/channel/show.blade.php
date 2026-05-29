@extends('layouts.app')

@section('title', $user->name . ' - Lumina')

@section('content')
<div class="min-h-screen pb-20">
    <!-- Channel Banner -->
    <div class="relative h-48 md:h-64 lg:h-80 w-full overflow-hidden bg-tubi-darker border-b border-theme">
        @if($user->banner)
            <img src="{{ Storage::url($user->banner) }}" class="w-full h-full object-cover opacity-80" alt="Banner do canal">
        @else
            <!-- Default Gradient Banner -->
            <div class="w-full h-full bg-gradient-to-r from-tubi-primary via-purple-600 to-tubi-secondary opacity-70"></div>
            <!-- Animated overlay -->
            <div class="absolute inset-0 opacity-30" style="background-image: radial-gradient(circle at 2px 2px, currentColor 1px, transparent 0); background-size: 32px 32px; color: var(--tubi-light)"></div>
        @endif
        
        <!-- Gradient fade to content -->
        <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-tubi-dark to-transparent opacity-80"></div>
    </div>

    <!-- Channel Header Info -->
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 relative -mt-16 sm:-mt-20 z-10">
        <div class="flex flex-col sm:flex-row items-center sm:items-end gap-6 pb-6 border-b border-theme">
            <!-- Avatar -->
            <div class="w-32 h-32 sm:w-40 sm:h-40 rounded-full overflow-hidden border-4 border-tubi-dark bg-tubi-card shadow-xl relative group">
                @if($user->avatar)
                    <img src="{{ Storage::url($user->avatar) }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-gradient-to-br from-tubi-primary to-tubi-secondary flex items-center justify-center text-white text-5xl font-display font-bold shadow-inner">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            <!-- Info -->
            <div class="flex-1 text-center sm:text-left pt-4 sm:pt-0">
                <h1 class="text-3xl font-display font-bold text-tubi-light flex items-center justify-center sm:justify-start gap-2">
                    {{ $user->name }}
                    @if($user->is_verified)
                        <svg class="w-6 h-6 text-blue-500 drop-shadow-sm" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    @endif
                </h1>
                
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-x-4 gap-y-2 mt-2 text-sm text-tubi-gray">
                    <span class="font-bold text-tubi-light">{{ '@' . $user->username }}</span>
                    <span>{{ number_format($user->subscribers_count) }} inscritos</span>
                    <span>{{ $videos->total() ?? 0 }} vídeos</span>
                    <span>{{ number_format($totalViews ?? 0) }} visualizações</span>
                </div>

                @if($user->bio)
                <p class="mt-3 text-sm text-tubi-gray max-w-2xl line-clamp-2 hover:line-clamp-none cursor-pointer transition-all">
                    {{ $user->bio }}
                </p>
                @endif
                
                @if($user->website)
                <a href="{{ $user->website }}" target="_blank" rel="nofollow" class="mt-2 inline-flex items-center gap-1 text-sm text-tubi-primary font-medium hover:underline transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    {{ str_replace(['http://', 'https://'], '', $user->website) }}
                </a>
                @endif
            </div>

            <!-- Subscribe Button / Action -->
            <div class="sm:self-center mt-4 sm:mt-0 flex gap-3">
                @auth
                    @if(Auth::id() !== $user->id)
                        @php
                            $channelPlan = \Illuminate\Support\Facades\DB::table('membership_plans')->where('user_id', $user->id)->where('is_active', true)->first();
                            $isMember = \Illuminate\Support\Facades\DB::table('channel_memberships')->where('subscriber_id', Auth::id())->where('channel_id', $user->id)->where('status', 'active')->exists();
                        @endphp
                        
                        @if($channelPlan && !$isMember)
                            <button @click="$dispatch('open-checkout')" class="px-6 py-2.5 rounded-full bg-gradient-to-r from-yellow-400 to-yellow-500 text-black hover:scale-105 shadow-[0_4px_15px_rgba(250,204,21,0.3)] font-bold text-sm transition-all flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                Seja Membro
                            </button>
                        @elseif($isMember)
                            <button class="px-6 py-2.5 rounded-full bg-yellow-500/10 text-yellow-600 dark:text-yellow-500 border border-yellow-500/30 font-bold text-sm transition-all flex items-center gap-2 cursor-default">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Membro
                            </button>
                        @endif

                        <div x-data="subscribeBtn('{{ $user->username }}', {{ $isSubscribed ? 'true' : 'false' }})">
                            <button @click="toggle()" 
                                :class="subscribed ? 'bg-tubi-darker text-tubi-light border border-theme hover:bg-black/5 dark:hover:bg-white/5' : 'bg-tubi-light dark:bg-tubi-light text-tubi-darker dark:text-tubi-darker hover:opacity-90 shadow-[0_4px_15px_rgba(0,0,0,0.1)] dark:shadow-[0_4px_15px_rgba(255,255,255,0.2)]'"
                                class="px-6 py-2.5 rounded-full font-bold text-sm transition-all flex items-center gap-2">
                                <span x-show="!subscribed">Inscrever-se</span>
                                <span x-show="subscribed" style="display: none;" class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Inscrito
                                </span>
                            </button>
                        </div>
                    @else
                        <a href="{{ route('settings') }}" class="px-6 py-2.5 rounded-full bg-tubi-card border border-theme text-tubi-light hover:bg-black/5 dark:hover:bg-white/5 font-bold text-sm transition-all shadow-sm">
                            Personalizar Canal
                        </a>
                        <a href="{{ route('analytics.index') }}" class="px-6 py-2.5 rounded-full bg-gradient-to-r from-tubi-primary to-tubi-secondary text-white shadow-[0_4px_15px_rgba(124,58,237,0.3)] hover:shadow-[0_6px_25px_rgba(124,58,237,0.5)] font-bold text-sm transition-all">
                            Lumina Studio
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="px-6 py-2.5 rounded-full bg-tubi-light dark:bg-tubi-light text-tubi-darker dark:text-tubi-darker hover:opacity-90 shadow-md font-bold text-sm transition-all">
                        Inscrever-se
                    </a>
                @endauth
            </div>
        </div>

        <!-- Channel Navigation Tabs -->
        <nav class="flex overflow-x-auto scrollbar-hide gap-8 mt-4 border-b border-theme">
            <a href="{{ route('channel.show', $user->username) }}" class="pb-3 text-sm font-bold whitespace-nowrap border-b-2 {{ request()->routeIs('channel.show') ? 'text-tubi-light border-tubi-primary' : 'text-tubi-gray border-transparent hover:text-tubi-light hover:border-tubi-gray/30 transition-all' }}">Início</a>
            <a href="{{ route('channel.videos', $user->username) }}" class="pb-3 text-sm font-bold whitespace-nowrap border-b-2 {{ request()->routeIs('channel.videos') ? 'text-tubi-light border-tubi-primary' : 'text-tubi-gray border-transparent hover:text-tubi-light hover:border-tubi-gray/30 transition-all' }}">Vídeos</a>
            <a href="{{ route('channel.playlists', $user->username) }}" class="pb-3 text-sm font-bold whitespace-nowrap border-b-2 {{ request()->routeIs('channel.playlists') ? 'text-tubi-light border-tubi-primary' : 'text-tubi-gray border-transparent hover:text-tubi-light hover:border-tubi-gray/30 transition-all' }}">Playlists</a>
        </nav>
    </div>

    <!-- Content Area -->
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        
        @if(isset($shorts) && $shorts->isNotEmpty() && request()->routeIs('channel.show'))
        <!-- Shorts Section -->
        <section class="mb-12">
            <h2 class="text-xl font-display font-bold text-tubi-light mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-tubi-secondary" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
                Shorts
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($shorts as $short)
                <a href="{{ route('video.show', ['v' => $short->youtube_id ?? $short->slug]) }}" class="group block relative aspect-[9/16] rounded-2xl overflow-hidden bg-tubi-card border border-theme">
                    @if($short->thumbnail)
                        <img src="{{ Str::startsWith($short->thumbnail, 'http') ? $short->thumbnail : Storage::url($short->thumbnail) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent"></div>
                    <div class="absolute bottom-3 left-3 right-3 z-10">
                        <h3 class="text-white text-sm font-bold line-clamp-2 mb-1 group-hover:text-tubi-secondary transition-colors">{{ $short->title }}</h3>
                        <p class="text-white/70 font-medium text-xs">{{ number_format($short->views_count) }} views</p>
                    </div>
                </a>
                @endforeach
            </div>
        </section>
        @endif

        <!-- Videos Grid -->
        <section>
            @if(request()->routeIs('channel.show'))
            <h2 class="text-xl font-display font-bold text-tubi-light mb-6 flex items-center gap-2">
                Vídeos Publicados
            </h2>
            @endif

            @if($videos->isEmpty())
                <div class="flex flex-col items-center justify-center py-20 text-center glass rounded-3xl border border-theme">
                    <div class="w-24 h-24 rounded-full bg-tubi-darker border-2 border-dashed border-theme flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 text-tubi-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.069A1 1 0 0121 8.845v6.310a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </div>
                    <p class="text-tubi-light font-bold text-lg mb-2">Sem vídeos publicados</p>
                    <p class="text-tubi-gray text-sm">Este canal ainda não possui conteúdo aberto ao público.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-6 gap-y-10">
                    @foreach($videos as $video)
                        @include('partials.video-card', ['video' => $video])
                    @endforeach
                </div>

                @if($videos->hasPages())
                <div class="mt-12 flex justify-center">
                    {{ $videos->links('partials.pagination') }}
                </div>
                @endif
            @endif
        </section>

    </div>
</div>

@if(isset($channelPlan) && isset($isMember) && !$isMember)
<!-- Fake MercadoPago Checkout Modal -->
<div x-data="{ checkoutOpen: false }" @open-checkout.window="checkoutOpen = true">
    <div x-show="checkoutOpen" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="checkoutOpen = false"></div>
        <div class="bg-tubi-card relative p-8 rounded-3xl max-w-md w-full border border-theme shadow-2xl animate-fade-in" x-transition>
            
            <!-- Close Button -->
            <button @click="checkoutOpen = false" class="absolute top-4 right-4 text-tubi-gray hover:text-tubi-light bg-tubi-darker rounded-full p-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <!-- MP Header -->
            <div class="flex items-center gap-3 mb-8 justify-center">
                <div class="w-10 h-10 bg-[#009EE3] rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15v-2H9v-2h2v-2H9V9h2V7h2v2h2v2h-2v2h2v2h-2v2h-2z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-tubi-light tracking-tight">Assinatura Segura</h3>
            </div>

            <div class="text-center mb-8 glass bg-tubi-darker rounded-2xl p-6 border border-theme">
                <p class="text-sm font-bold text-tubi-primary mb-2">CLUBE DE MEMBROS</p>
                <p class="text-lg font-bold text-tubi-light mb-4">{{ $channelPlan->name }}</p>
                <p class="text-4xl font-display font-bold text-tubi-light">R$ {{ number_format($channelPlan->price, 2, ',', '.') }}<span class="text-sm text-tubi-gray font-normal"> /mês</span></p>
                
                <div class="inline-flex items-center gap-1.5 mt-4 px-3 py-1 rounded-full bg-green-500/10 border border-green-500/20">
                    <svg class="w-3.5 h-3.5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                    <span class="text-xs font-bold text-green-600 dark:text-green-400">Ambiente Simulado (Lumina)</span>
                </div>
            </div>

            <form action="{{ route('membership.subscribe', $user->username) }}" method="POST">
                @csrf
                <div class="space-y-3 mb-8">
                    <label class="flex items-center justify-between p-4 border-2 border-[#009EE3] bg-[#009EE3]/10 rounded-2xl cursor-pointer transition-all hover:bg-[#009EE3]/20">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="payment_method" value="pix" checked class="w-4 h-4 text-[#009EE3] border-gray-300 focus:ring-[#009EE3]">
                            <span class="text-tubi-light font-bold">Pix Instantâneo</span>
                        </div>
                        <svg class="w-6 h-6 text-[#009EE3]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                    </label>
                    <label class="flex items-center justify-between p-4 border border-theme bg-tubi-darker rounded-2xl cursor-pointer opacity-50">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="payment_method" value="card" disabled class="w-4 h-4">
                            <span class="text-tubi-gray font-bold">Cartão de Crédito</span>
                        </div>
                        <svg class="w-6 h-6 text-tubi-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </label>
                </div>
                
                <button type="submit" class="w-full bg-[#009EE3] hover:bg-[#008DD0] text-white py-4 rounded-2xl font-bold transition-all shadow-[0_4px_15px_rgba(0,158,227,0.3)] hover:shadow-[0_6px_25px_rgba(0,158,227,0.5)] hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Confirmar e Pagar
                </button>
            </form>
        </div>
    </div>
</div>
@endif

@section('scripts')
<script>
function subscribeBtn(username, initialStatus) {
    return {
        subscribed: initialStatus,
        async toggle() {
            const method = this.subscribed ? 'DELETE' : 'POST';
            const url = this.subscribed ? `/canal/@${username}/unsubscribe` : `/canal/@${username}/subscribe`;
            
            try {
                const res = await fetch(url, {
                    method: method,
                    headers: { 
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json'
                    }
                });
                
                if (res.ok) {
                    this.subscribed = !this.subscribed;
                }
            } catch (e) {
                console.error(e);
            }
        }
    }
}
</script>
@endsection
@endsection
