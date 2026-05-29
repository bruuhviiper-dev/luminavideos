@php
    $poster = $short->thumbnail ? (Str::startsWith($short->thumbnail, 'http') ? $short->thumbnail : Storage::url($short->thumbnail)) : '';
    $videoUrl = $short->video_url ?? Storage::url($short->video_path);
    $isLiked = false;
    if (Auth::check()) {
        $isLiked = \App\Models\Like::where('user_id', Auth::id())->where('video_id', $short->id)->where('type', 'like')->exists();
    }
@endphp

<!-- Individual Short Slide -->
<div class="snap-start w-full h-full relative flex items-center justify-center bg-black overflow-hidden select-none" data-video-index="{{ $index }}" data-video-id="{{ $short->id }}">
    
    <!-- Ambient Blur Background -->
    @if($poster)
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat blur-[60px] opacity-25 scale-125 pointer-events-none" style="background-image: url('{{ $poster }}');"></div>
    @endif
    <div class="absolute inset-0 bg-black/60 pointer-events-none"></div>

    <!-- Video Player (Vertical 9:16 layout) -->
    <video 
        class="short-video-player w-full h-full object-contain relative z-10 cursor-pointer"
        loop 
        playsinline
        webkit-playsinline
        preload="auto"
        poster="{{ $poster }}"
        src="{{ $videoUrl }}"
        onclick="togglePlayPause(this)"
    ></video>

    <!-- Big Play/Pause Overlay Indicator -->
    <div class="video-indicator absolute inset-0 z-20 flex items-center justify-center pointer-events-none opacity-0 transition-opacity duration-200">
        <div class="bg-black/50 backdrop-blur-md p-4 rounded-full border border-white/10 text-white scale-90 transition-transform duration-200">
            <svg class="play-icon w-8 h-8 hidden" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
            <svg class="pause-icon w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        </div>
    </div>

    <!-- Info Overlay (Bottom-Left) -->
    <div class="absolute bottom-6 left-4 right-16 z-20 pointer-events-none text-white flex flex-col gap-2">
        <div class="flex items-center gap-3 pointer-events-auto">
            <a href="{{ route('channel.show', $short->user->username) }}" class="flex items-center gap-2 group">
                <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-white/20 bg-zinc-800 shadow-md">
                    @if($short->user->avatar)
                        <img src="{{ Storage::url($short->user->avatar) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-tubi-primary to-tubi-secondary flex items-center justify-center text-white font-bold text-sm">
                            {{ strtoupper(substr($short->user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="flex flex-col">
                    <span class="font-bold text-sm hover:underline drop-shadow flex items-center gap-1">
                        {{ $short->user->name }}
                        @if($short->user->is_verified)
                            <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        @endif
                    </span>
                    <span class="text-xs text-white/70 drop-shadow">{{ '@' . $short->user->username }}</span>
                </div>
            </a>
            
            @auth
                @if(Auth::id() !== $short->user_id)
                    @php 
                        $isSubscribed = \Illuminate\Support\Facades\DB::table('subscriptions')
                            ->where('subscriber_id', Auth::id())
                            ->where('channel_id', $short->user_id)
                            ->exists();
                    @endphp
                    <button 
                        onclick="toggleSubscribe(this, '{{ $short->user->username }}')"
                        class="subscribe-btn ml-2 px-3.5 py-1 text-xs font-bold rounded-full border transition-all pointer-events-auto {{ $isSubscribed ? 'bg-white/10 border-white/20 text-white' : 'bg-white text-black border-transparent hover:scale-105' }}"
                    >
                        {{ $isSubscribed ? 'Seguindo' : 'Seguir' }}
                    </button>
                @endif
            @endauth
        </div>

        <div class="pointer-events-auto mt-1 max-h-[80px] overflow-y-auto scrollbar-hide">
            <h4 class="font-bold text-sm drop-shadow">{{ $short->title }}</h4>
            @if($short->description)
                <p class="text-xs text-white/80 drop-shadow mt-0.5 break-words">{{ $short->description }}</p>
            @endif
        </div>

        @if($short->tags)
            @php $tagsArray = is_string($short->tags) ? explode(',', str_replace(['[', ']', '"'], '', $short->tags)) : (is_array($short->tags) ? $short->tags : []); @endphp
            @if(count($tagsArray) > 0)
                <div class="flex flex-wrap gap-1 pointer-events-auto">
                    @foreach($tagsArray as $tag)
                        <a href="{{ route('search', ['q' => trim($tag)]) }}" class="text-xs font-bold text-tubi-secondary hover:text-white drop-shadow">#{{ trim($tag) }}</a>
                    @endforeach
                </div>
            @endif
        @endif
    </div>

    <!-- Floating Sidebar Actions (Right-Side) -->
    <div class="absolute bottom-16 right-3.5 z-20 flex flex-col items-center gap-4.5">
        
        <!-- Like Button -->
        <button 
            onclick="toggleShortLike(this, {{ $short->id }})" 
            class="like-btn flex flex-col items-center gap-1 group text-white pointer-events-auto"
        >
            <div class="w-12 h-12 rounded-full bg-black/40 backdrop-blur-md border border-white/10 flex items-center justify-center hover:bg-tubi-primary transition-all duration-200 {{ $isLiked ? 'text-red-500 bg-white/10' : '' }}">
                <svg class="like-icon w-6 h-6 transform active:scale-125 transition-transform duration-200" fill="{{ $isLiked ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </div>
            <span class="likes-count text-xs font-bold drop-shadow">{{ number_format($short->likes_count) }}</span>
        </button>

        <!-- Comments Button -->
        <button 
            onclick="openShortComments({{ $short->id }})" 
            class="flex flex-col items-center gap-1 group text-white pointer-events-auto"
        >
            <div class="w-12 h-12 rounded-full bg-black/40 backdrop-blur-md border border-white/10 flex items-center justify-center hover:bg-white/20 transition-all duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            </div>
            <span class="text-xs font-bold drop-shadow">{{ number_format($short->comments()->count()) }}</span>
        </button>

        <!-- Share Button -->
        <button 
            onclick="shareShort('{{ route('video.show', ['v' => $short->youtube_id ?? $short->slug]) }}')"
            class="flex flex-col items-center gap-1 group text-white pointer-events-auto"
        >
            <div class="w-12 h-12 rounded-full bg-black/40 backdrop-blur-md border border-white/10 flex items-center justify-center hover:bg-white/20 transition-all duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-5.368m0 5.368l5.662 3.775a3 3 0 100-5.368L8.684 13.342z"/></svg>
            </div>
            <span class="text-[10px] font-bold drop-shadow">Partilhar</span>
        </button>

        <!-- Sound Disk Rotation -->
        <div class="mt-1 w-10 h-10 rounded-full bg-zinc-900 border border-white/20 flex items-center justify-center animate-[spin_4s_linear_infinite] overflow-hidden shadow-lg pointer-events-none">
            @if($short->user->avatar)
                <img src="{{ Storage::url($short->user->avatar) }}" class="w-6 h-6 rounded-full object-cover">
            @else
                <svg class="w-5 h-5 text-white/50" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/></svg>
            @endif
        </div>

    </div>

</div>
