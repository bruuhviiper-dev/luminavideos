@forelse($comments as $comment)
<div class="flex gap-3">
    <a href="{{ route('channel.show', $comment->user->username) }}" class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0 bg-tubi-darker">
        @if($comment->user->avatar)
            <img src="{{ Storage::url($comment->user->avatar) }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full bg-gradient-to-tr from-tubi-primary to-tubi-secondary flex items-center justify-center text-white font-bold text-xs">
                {{ strtoupper(substr($comment->user->name, 0, 1)) }}
            </div>
        @endif
    </a>
    <div class="flex-1">
        <div class="flex items-baseline gap-2 mb-0.5">
            <a href="{{ route('channel.show', $comment->user->username) }}" class="font-bold text-sm text-tubi-gray hover:text-white transition-colors">
                {{ $comment->user->name }}
            </a>
        </div>
        <p class="text-[14px] text-tubi-light font-medium leading-snug break-words">{{ $comment->content }}</p>
        <div class="flex items-center gap-4 mt-1.5">
            <span class="text-xs text-tubi-gray/50">{{ $comment->created_at->shortAbsoluteDiffForHumans() }}</span>
            <button class="flex items-center gap-1 text-xs text-tubi-gray hover:text-tubi-light transition-colors font-bold">
                Responder
            </button>
            <div class="flex items-center gap-1 text-tubi-gray hover:text-red-400 ml-auto cursor-pointer transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                <span class="text-xs">0</span>
            </div>
        </div>
    </div>
</div>
@empty
<div class="text-center py-10 flex flex-col items-center justify-center">
    <div class="w-16 h-16 rounded-full bg-tubi-darker flex items-center justify-center mb-4 text-tubi-gray/30">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
    </div>
    <p class="text-tubi-light font-bold">Seja o primeiro a comentar!</p>
    <p class="text-tubi-gray text-sm mt-1">Sua opinião é valiosa.</p>
</div>
@endforelse
