{{-- TikTok Style Video Card --}}
@php
    $thumb = $video->thumbnail;
    if($thumb && !Str::startsWith($thumb, 'http')) {
        $thumb = Storage::url($thumb);
    }
    $dur = $video->duration ?? 0;
    $durStr = $dur > 0 ? sprintf('%d:%02d', floor($dur/60), $dur%60) : '';
    if($dur >= 3600) $durStr = sprintf('%d:%02d:%02d', floor($dur/3600), floor(($dur%3600)/60), $dur%60);
@endphp
<article class="group cursor-pointer relative transition-transform duration-300 hover:scale-[1.02] shadow-sm hover:shadow-xl hover:shadow-tubi-primary/20 rounded-2xl overflow-hidden bg-tubi-card border border-theme w-full aspect-[9/16]">
    <a href="{{ route('video.show', ['v' => $video->youtube_id ?? $video->slug]) }}" class="block w-full h-full relative">
        
        <!-- Thumbnail (Fills entire card) -->
        @if($thumb)
            <img src="{{ $thumb }}" alt="{{ $video->title }}" loading="lazy" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
        @else
            <div class="w-full h-full flex items-center justify-center bg-tubi-darker">
                <svg class="w-12 h-12 text-tubi-gray/30" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
            </div>
        @endif

        <!-- Dark Gradient Overlay for Text Readability -->
        <div class="absolute inset-0 bg-gradient-to-b from-black/30 via-transparent to-black/90 pointer-events-none"></div>

        <!-- Top Badges -->
        <div class="absolute top-2 left-2 flex flex-col gap-1 z-10">
            @if($video->status === 'processing')
                <span class="px-2 py-0.5 bg-yellow-500/90 backdrop-blur-md text-white text-[10px] rounded font-bold tracking-wide shadow-sm uppercase">Processando</span>
            @endif
        </div>

        <!-- Hover Play Button Icon -->
        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none z-10">
            <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white">
                <svg class="w-6 h-6 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
            </div>
        </div>

        <!-- Info Section (Overlay at bottom) -->
        <div class="absolute bottom-0 left-0 right-0 p-3 z-20 flex flex-col gap-2">
            
            <h3 class="text-[14px] font-bold text-white line-clamp-2 leading-snug drop-shadow-md">
                {{ $video->title }}
            </h3>

            <div class="flex items-center justify-between mt-1">
                <!-- Channel Info -->
                <div class="flex items-center gap-1.5 min-w-0" @click.prevent="window.location.href='{{ route('channel.show', $video->user->username) }}'">
                    <div class="w-6 h-6 rounded-full overflow-hidden bg-tubi-dark border border-white/20 flex-shrink-0">
                        @if($video->user->avatar)
                            <img src="{{ Storage::url($video->user->avatar) }}" alt="{{ $video->user->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-tubi-primary flex items-center justify-center text-white text-[10px] font-bold">
                                {{ strtoupper(substr($video->user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <span class="text-[12px] text-white/90 font-medium truncate hover:underline drop-shadow">{{ $video->user->name }}</span>
                </div>

                <!-- Stats -->
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="flex items-center gap-1 text-[11px] font-bold text-white/80 drop-shadow">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z"/></svg>
                        {{ number_format($video->likes_count) }}
                    </span>
                    <span class="flex items-center gap-1 text-[11px] font-bold text-white/80 drop-shadow">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        {{ number_format($video->views_count) }}
                    </span>
                </div>
            </div>

        </div>
    </a>
</article>
