@extends('layouts.app')

@section('title', $video->title . ' — Lumina Shorts Mode')

@section('content')
@php
    $poster = $video->thumbnail ? (Str::startsWith($video->thumbnail, 'http') ? $video->thumbnail : Storage::url($video->thumbnail)) : '';
    $recs = \App\Models\Video::where('status', 'active')->where('id', '!=', $video->id)->inRandomOrder()->take(5)->get();
@endphp

<!-- Container TikTok/Reels Style -->
<div class="fixed top-16 left-0 md:left-64 right-0 bottom-0 bg-black overflow-hidden flex" x-data="{ commentsOpen: window.innerWidth >= 1280 }">
    
    <!-- Video Player Container (Center) -->
    <div class="relative flex-1 h-full w-full flex items-center justify-center bg-black transition-all duration-300"
         :class="commentsOpen ? 'mr-0 xl:mr-[400px]' : 'mr-0'">
        
        <!-- Ambient Blur Background Effect (Pega o Poster do Vídeo) -->
        <div id="ambient-blur" class="absolute inset-0 bg-cover bg-center bg-no-repeat blur-[60px] opacity-40 transform scale-110" style="{{ $poster ? 'background-image: url('.$poster.');' : '' }}"></div>
        <div class="absolute inset-0 bg-black/40"></div>

        <!-- The Video Element Wrapper (9:16 aspect ratio or responsive box) -->
        <div class="relative w-full h-[100dvh] md:h-full md:py-4 flex items-center justify-center">
            
            <div class="relative w-full h-full max-w-[500px] md:rounded-[32px] overflow-hidden bg-black shadow-[0_0_50px_rgba(0,0,0,0.5)] border-0 md:border md:border-white/10 group">
                
                <video id="lumina-player" class="video-js vjs-theme-sea w-full h-full object-contain" controls preload="auto" poster="{{ $poster }}" data-setup="{&quot;fluid&quot;: false, &quot;controls&quot;: true, &quot;controlBar&quot;: {&quot;pictureInPictureToggle&quot;: false}}">
                    @if($video->hls_url)
                        <source src="{{ $video->hls_url }}" type="application/x-mpegURL" />
                    @endif
                    @if($video->video_url)
                        <source src="{{ $video->video_url }}" type="video/mp4" />
                    @elseif($video->video_path)
                        <source src="{{ Storage::url($video->video_path) }}" type="video/mp4" />
                    @endif
                    <p class="vjs-no-js">Seu navegador não suporta vídeos HTML5.</p>
                </video>

                <!-- Video Overlay Actions & Info -->
                <div class="absolute bottom-0 left-0 right-0 p-4 pb-24 md:pb-6 bg-gradient-to-t from-black/90 via-black/40 to-transparent pointer-events-none flex items-end justify-between z-10">
                    
                    <!-- Left: Info, Title, User -->
                    <div class="flex-1 min-w-0 pr-16 pointer-events-auto">
                        <div class="flex items-center gap-3 mb-3">
                            <a href="{{ route('channel.show', $video->user->username) }}" class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-full overflow-hidden border-2 border-white/20 bg-tubi-dark">
                                    @if($video->user->avatar)
                                        <img src="{{ Storage::url($video->user->avatar) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-tubi-primary flex items-center justify-center text-white font-bold text-lg">
                                            {{ strtoupper(substr($video->user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                            </a>
                            <div class="flex flex-col">
                                <a href="{{ route('channel.show', $video->user->username) }}" class="text-white font-bold text-lg hover:underline flex items-center gap-1 drop-shadow-md">
                                    <span id="channel-name">{{ $video->user->name }}</span>
                                    @if($video->user->is_verified)
                                        <svg id="channel-verified" class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    @endif
                                </a>
                                <p class="text-white/80 text-[13px] drop-shadow-md"><span id="views-count">{{ number_format($video->views_count) }}</span> views</p>
                            </div>
                            
                            @auth
                                @if(Auth::id() !== $video->user_id)
                                    @php $isSub = \Illuminate\Support\Facades\DB::table('subscriptions')->where('subscriber_id', Auth::id())->where('channel_id', $video->user_id)->exists(); @endphp
                                    @if(!$isSub)
                                    <button class="ml-2 px-4 py-1.5 bg-tubi-primary text-white font-bold text-sm rounded-full shadow-lg hover:bg-tubi-primary/80 transition-colors">Seguir</button>
                                    @endif
                                @endif
                            @endauth
                        </div>

                        <div x-data="{ expanded: false }" class="mt-2 text-[15px] drop-shadow-md cursor-pointer" @click="expanded = !expanded">
                            <p class="text-white break-words" :class="expanded ? '' : 'line-clamp-2'">
                                <strong id="video-title" class="mr-1">{{ $video->title }}</strong>
                                <span id="video-desc">{{ $video->description }}</span>
                            </p>
                            <span x-show="!expanded" class="text-white/60 text-sm font-bold block mt-1 hover:text-white">Mais...</span>
                        </div>
                        
                        @if($video->tags)
                            @php $tagsArray = is_string($video->tags) ? explode(',', str_replace(['[', ']', '"'], '', $video->tags)) : (is_array($video->tags) ? $video->tags : []); @endphp
                            @if(count($tagsArray) > 0)
                                <div class="flex flex-wrap gap-1 mt-2">
                                    @foreach($tagsArray as $tag)
                                        @php $tag = trim($tag); @endphp
                                        @if(!empty($tag))
                                            <a href="{{ route('search', ['q' => $tag]) }}" class="text-sm font-bold text-white/90 hover:text-white drop-shadow-md">#{{ $tag }}</a>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Right Absolute: Floating Action Buttons -->
                <div class="absolute bottom-24 md:bottom-12 right-4 flex flex-col items-center gap-6 z-20 pointer-events-auto">
                    
                    <!-- Like -->
                    <button class="flex flex-col items-center gap-1 group">
                        <div class="w-12 h-12 rounded-full bg-black/40 backdrop-blur-sm border border-white/10 flex items-center justify-center text-white group-hover:bg-tubi-primary transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </div>
                        <span id="likes-count" class="text-white text-xs font-bold drop-shadow-md">{{ number_format($video->likes_count) }}</span>
                    </button>

                    <!-- Comments Toggle -->
                    <button @click="commentsOpen = !commentsOpen" class="flex flex-col items-center gap-1 group">
                        <div class="w-12 h-12 rounded-full bg-black/40 backdrop-blur-sm border border-white/10 flex items-center justify-center text-white group-hover:bg-white/20 transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        </div>
                        <span id="comments-count" class="text-white text-xs font-bold drop-shadow-md">{{ number_format($video->comments()->count()) }}</span>
                    </button>

                    <!-- Share -->
                    <button class="flex flex-col items-center gap-1 group">
                        <div class="w-12 h-12 rounded-full bg-black/40 backdrop-blur-sm border border-white/10 flex items-center justify-center text-white group-hover:bg-white/20 transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-5.368m0 5.368l5.662 3.775a3 3 0 100-5.368L8.684 13.342z"/></svg>
                        </div>
                        <span class="text-white text-xs font-bold drop-shadow-md">Compartilhar</span>
                    </button>

                    <!-- Sound Animation Record -->
                    <div class="mt-4 w-12 h-12 rounded-full bg-gradient-to-tr from-gray-800 to-black border-2 border-white/20 flex items-center justify-center animate-[spin_4s_linear_infinite] overflow-hidden shadow-[0_0_15px_rgba(0,0,0,0.5)]">
                        @if($video->user->avatar)
                            <img src="{{ Storage::url($video->user->avatar) }}" class="w-6 h-6 rounded-full object-cover">
                        @else
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/></svg>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        <!-- Desktop Only: Close/Back Button Float -->
        <a href="/" class="absolute top-6 left-6 w-12 h-12 rounded-full bg-black/40 backdrop-blur-sm border border-white/10 hidden md:flex items-center justify-center text-white hover:bg-white/20 transition-colors z-50">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>

        <!-- TikTok Style Up/Down Chevrons (Fake Scroll indicator) -->
        <div class="absolute right-4 md:right-8 top-1/2 -translate-y-1/2 flex flex-col gap-8 hidden md:flex z-50 opacity-50 hover:opacity-100 transition-opacity">
            @if($recs->count() > 0)
                <button onclick="loadVideo('{{ route('api.video.show', ['v' => $recs[0]->youtube_id ?? $recs[0]->slug]) }}')" class="p-3 bg-black/40 rounded-full hover:bg-white/20 text-white transition shadow-lg" title="Vídeo Anterior">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>
                </button>
            @endif
            @if($recs->count() > 1)
                <button onclick="loadVideo('{{ route('api.video.show', ['v' => $recs[1]->youtube_id ?? $recs[1]->slug]) }}')" class="p-3 bg-black/40 rounded-full hover:bg-white/20 text-white transition shadow-lg" title="Próximo Vídeo">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </button>
            @endif
        </div>

    </div>

    <!-- Right Sidebar (Comments & Related) TikTok Style -->
    <div class="fixed top-16 bottom-16 md:bottom-0 right-0 w-full xl:w-[400px] bg-tubi-card border-l border-theme z-50 flex flex-col shadow-2xl transition-transform duration-300 transform xl:translate-x-0"
         :class="commentsOpen ? 'translate-x-0' : 'translate-x-full'">
        
        <!-- Sidebar Header -->
        <div class="flex items-center justify-between p-4 border-b border-theme bg-tubi-card/90 backdrop-blur z-10">
            <h3 class="font-bold text-tubi-light text-lg">Comentários (<span id="sidebar-comments-count">{{ $video->comments()->count() }}</span>)</h3>
            <button @click="commentsOpen = false" class="p-2 text-tubi-gray hover:text-white rounded-full hover:bg-white/5 transition-colors xl:hidden">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Scrollable Content Area -->
        <div class="flex-1 overflow-y-auto scrollbar-hide p-4 bg-tubi-dark">
            
            <!-- Recomendações Integradas no Topo dos Comentarios (Estilo TikTok Web) -->
            <div class="mb-8 border-b border-theme pb-6">
                <p class="text-xs font-bold text-tubi-gray uppercase tracking-wider mb-4">Próximos Vídeos</p>
                <div class="flex overflow-x-auto gap-3 pb-2 scrollbar-hide snap-x">
                    @foreach($recs as $rec)
                    <a href="{{ route('video.show', ['v' => $rec->youtube_id ?? $rec->slug]) }}" class="snap-start flex-shrink-0 w-24 relative rounded-lg overflow-hidden aspect-[9/16] group bg-tubi-darker border border-theme">
                        @php 
                            $thumbStr = $rec->thumbnail;
                            $recThumb = $thumbStr ? (Str::startsWith($thumbStr, 'http') ? $thumbStr : Storage::url($thumbStr)) : ''; 
                        @endphp
                        @if($recThumb)
                            <img src="{{ $recThumb }}" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity">
                        @else
                            <div class="w-full h-full bg-tubi-darker flex items-center justify-center"><svg class="w-6 h-6 text-tubi-gray/30" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></div>
                        @endif
                        <div class="absolute bottom-0 left-0 right-0 p-1 bg-gradient-to-t from-black/80 to-transparent">
                            <p class="text-[9px] text-white font-bold truncate drop-shadow-md">{{ $rec->user->name }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>

            <!-- Comments List -->
            <div id="comments-container" class="space-y-6">
                @include('partials.comments-list', ['comments' => $video->comments()->with('user')->latest()->take(30)->get()])
            </div>
            
            <div class="h-24"></div> <!-- Padding botom para o input não cobrir -->
        </div>

        <!-- Add Comment Input (Fixed at bottom of sidebar) -->
        <div class="absolute bottom-0 left-0 right-0 bg-tubi-card border-t border-theme p-4 z-20">
            @auth
            <form action="{{ route('comment.store', $video->id) }}" method="POST" class="flex gap-3 items-center">
                @csrf
                <input type="text" name="content" placeholder="Adicione um comentário..." required autocomplete="off"
                    class="flex-1 bg-white/5 border border-theme rounded-full px-4 py-2.5 text-sm text-tubi-light placeholder-tubi-gray/80 focus:outline-none focus:border-tubi-primary transition-colors">
                <button type="submit" class="w-10 h-10 rounded-full bg-tubi-primary text-white flex items-center justify-center flex-shrink-0 hover:bg-tubi-primary/80 hover:scale-105 transition-all shadow-[0_0_15px_rgba(124,58,237,0.3)]">
                    <svg class="w-5 h-5 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </button>
            </form>
            @else
            <a href="{{ route('login') }}" class="block w-full py-2.5 bg-white/5 border border-theme text-center rounded-full text-sm font-bold text-tubi-light hover:bg-white/10 transition-colors">
                Fazer login para comentar
            </a>
            @endauth
        </div>

    </div>
</div>

@endsection

@section('scripts')
<script>
    function loadVideo(apiUrl) {
        // Fetch new video data silently
        fetch(apiUrl)
            .then(res => res.json())
            .then(data => {
                if(data.error) return alert('Video not found!');

                // Update Video.js Player
                let player = videojs('lumina-player');
                if (data.hls_url) {
                    player.src({ type: 'application/x-mpegURL', src: data.hls_url });
                } else {
                    player.src({ type: 'video/mp4', src: data.video_url });
                }
                if(data.poster) {
                    player.poster(data.poster);
                    document.getElementById('ambient-blur').style.backgroundImage = 'url(' + data.poster + ')';
                }
                player.play();

                // Update DOM Information Elements
                document.getElementById('video-title').innerText = data.title;
                document.getElementById('video-desc').innerText = data.description || '';
                document.getElementById('channel-name').innerText = data.user.name;
                document.getElementById('views-count').innerText = data.views_count;
                document.getElementById('likes-count').innerText = data.likes_count;
                document.getElementById('comments-count').innerText = data.comments_count;
                document.getElementById('sidebar-comments-count').innerText = data.comments_count;
                
                // Update Comments List from rendered HTML
                document.getElementById('comments-container').innerHTML = data.comments_html;

                // Push URL to History seamlessly
                window.history.pushState({ path: data.url }, '', data.url);
                
                // Next/Prev Buttons Logic could be re-fetched here based on a new $recs array if we expand the API.
            })
            .catch(err => console.error('Erro na navegação SPA:', err));
    }
</script>
<style>
/* Reset Header position just for this view to flow cleanly with fixed layout */
header {
    position: relative;
    z-index: 60;
}
body {
    overflow: hidden; /* Prevent page scroll since everything is fixed inside the player */
}
.video-js {
    background-color: transparent;
}
.vjs-theme-sea .vjs-big-play-button {
    background-color: rgba(0, 0, 0, 0.4) !important;
    border-radius: 50% !important;
    width: 70px !important;
    height: 70px !important;
    border: 2px solid rgba(255,255,255,0.2) !important;
    backdrop-filter: blur(8px);
    transition: all 0.3s ease;
}
.vjs-theme-sea:hover .vjs-big-play-button {
    background-color: rgba(124, 58, 237, 0.8) !important;
    transform: scale(1.1);
    border-color: transparent !important;
}
.vjs-control-bar {
    background: transparent !important;
    padding-bottom: 8px;
}
.vjs-play-progress {
    background: #ffffff !important;
}
.vjs-slider {
    background-color: rgba(255,255,255,0.2) !important;
}
</style>
@endsection
