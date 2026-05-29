@extends('layouts.app')

@section('title', 'Lumina Shorts — O Futuro em Segundos')

@section('content')
<!-- Container TikTok/Reels Style: vertical scroll snap -->
<div class="fixed top-16 left-0 md:left-64 right-0 bottom-0 bg-black overflow-hidden flex justify-center">
    
    <!-- Vertical Snap Scroll Feed -->
    <div id="shorts-container" class="w-full max-w-[460px] h-full overflow-y-scroll snap-y snap-mandatory scrollbar-hide bg-zinc-950 border-x border-theme">
        @if($shorts->isEmpty())
            <div class="w-full h-full flex flex-col items-center justify-center text-center p-6 text-white">
                <div class="relative w-24 h-24 mb-6">
                    <div class="absolute inset-0 bg-tubi-secondary/20 blur-xl rounded-full"></div>
                    <svg class="w-24 h-24 text-tubi-secondary relative z-10 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.069A1 1 0 0121 8.845v6.31a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-xl font-display font-bold mb-2">Nenhum Short disponível</h3>
                <p class="text-sm text-tubi-gray max-w-xs mb-6">Seja o primeiro a publicar um vídeo vertical de até 60 segundos no Lumina!</p>
                <a href="{{ route('shorts.create') }}" class="px-6 py-2.5 rounded-full bg-gradient-to-r from-tubi-primary to-tubi-secondary text-sm font-bold shadow-lg hover:scale-105 transition-all">
                    Publicar Short
                </a>
            </div>
        @else
            @foreach($shorts as $index => $short)
                @include('partials.short-slide', ['short' => $short, 'index' => $index])
            @endforeach
        @endif
    </div>

    <!-- Right Sidebar Drawer for Comments (Shorts Context) -->
    <div id="shorts-comments-drawer" class="fixed top-16 bottom-16 md:bottom-0 right-0 w-full md:w-[400px] bg-tubi-card border-l border-theme z-50 flex flex-col shadow-2xl transition-transform duration-300 transform translate-x-full">
        <!-- Drawer Header -->
        <div class="flex items-center justify-between p-4 border-b border-theme bg-tubi-card/90 backdrop-blur">
            <h3 class="font-bold text-tubi-light text-lg">Comentários</h3>
            <button onclick="closeShortComments()" class="p-2 text-tubi-gray hover:text-white rounded-full hover:bg-white/5 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <!-- Scrollable Comments -->
        <div id="shorts-comments-list" class="flex-1 overflow-y-auto p-4 bg-tubi-dark space-y-6">
            <p class="text-sm text-tubi-gray text-center py-10">Carregando comentários...</p>
        </div>
        <!-- Comment input Form -->
        <div class="p-4 bg-tubi-card border-t border-theme">
            @auth
            <form id="short-comment-form" onsubmit="submitShortComment(event)" class="flex gap-3 items-center">
                <input type="hidden" id="comment-video-id" name="video_id">
                <input type="text" id="comment-input" placeholder="Adicione um comentário..." required autocomplete="off"
                    class="flex-1 bg-white/5 border border-theme rounded-full px-4 py-2.5 text-sm text-tubi-light placeholder-tubi-gray/80 focus:outline-none focus:border-tubi-primary transition-colors">
                <button type="submit" class="w-10 h-10 rounded-full bg-tubi-primary text-white flex items-center justify-center flex-shrink-0 hover:bg-tubi-primary/80 transition-all shadow-[0_0_15px_rgba(124,58,237,0.3)]">
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
    let activePlayer = null;
    const container = document.getElementById('shorts-container');

    // Keep track of original slides count
    const totalOriginalSlides = document.querySelectorAll('[data-video-index]').length;

    // IntersectionObserver to detect which short is active and play it
    const observerOptions = {
        root: container,
        rootMargin: '0px',
        threshold: 0.6 // 60% of the item must be visible
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            const video = entry.target.querySelector('.short-video-player');
            if (entry.isIntersecting) {
                // Pause any currently playing video first
                pauseAllVideos();
                
                // Play new video
                video.play()
                    .then(() => {
                        activePlayer = video;
                    })
                    .catch(err => {
                        console.log("Autoplay blocked or video error:", err);
                    });

                // Infinite Scroll Loop Logic
                const currentIndex = parseInt(entry.target.getAttribute('data-video-index'));
                const allSlides = container.querySelectorAll('[data-video-index]');
                
                // If we are close to the last slide, clone and append the list to enable endless scrolling
                if (currentIndex >= allSlides.length - 2) {
                    cloneAndAppendSlides();
                }
            } else {
                video.pause();
            }
        });
    }, observerOptions);

    // Initial observe
    observeAllSlides();

    function observeAllSlides() {
        document.querySelectorAll('[data-video-index]').forEach(slide => {
            observer.observe(slide);
        });
    }

    function pauseAllVideos() {
        const players = document.querySelectorAll('.short-video-player');
        players.forEach(p => {
            p.pause();
        });
    }

    function cloneAndAppendSlides() {
        // Extract the original slides loaded initially
        const originalSlides = Array.from(container.querySelectorAll('[data-video-index]')).slice(0, totalOriginalSlides);
        const currentTotal = container.querySelectorAll('[data-video-index]').length;

        originalSlides.forEach((origSlide, index) => {
            const clone = origSlide.cloneNode(true);
            const newIndex = currentTotal + index;
            
            // Set unique index
            clone.setAttribute('data-video-index', newIndex);
            
            // Reset player states in cloned slide
            const clonedVideo = clone.querySelector('.short-video-player');
            clonedVideo.pause();
            clonedVideo.currentTime = 0;

            // Append cloned slide to bottom
            container.appendChild(clone);
            
            // Observe the newly added slide
            observer.observe(clone);
        });
    }

    function togglePlayPause(video) {
        const slide = video.closest('[data-video-index]');
        const indicator = slide.querySelector('.video-indicator');
        const playIcon = indicator.querySelector('.play-icon');
        const pauseIcon = indicator.querySelector('.pause-icon');

        if (video.paused) {
            video.play();
            playIcon.classList.add('hidden');
            pauseIcon.classList.remove('hidden');
        } else {
            video.pause();
            playIcon.classList.remove('hidden');
            pauseIcon.classList.add('hidden');
        }

        // Trigger animation
        indicator.classList.remove('opacity-0');
        indicator.querySelector('div').classList.remove('scale-90');
        indicator.querySelector('div').classList.add('scale-100');
        
        setTimeout(() => {
            indicator.classList.add('opacity-0');
            indicator.querySelector('div').classList.remove('scale-100');
            indicator.querySelector('div').classList.add('scale-90');
        }, 600);
    }

    // Like short
    async function toggleShortLike(button, videoId) {
        const url = `/video/${videoId}/like`;
        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                }
            });
            if (res.ok) {
                const data = await res.json();
                const countSpan = button.querySelector('.likes-count');
                const svg = button.querySelector('.like-icon');
                const innerContainer = button.querySelector('div');

                countSpan.innerText = data.likes_count;
                
                if (data.status === 'liked') {
                    svg.setAttribute('fill', 'currentColor');
                    innerContainer.classList.add('text-red-500', 'bg-white/10');
                    window.showToast('Você curtiu este Short! ❤️');
                } else {
                    svg.setAttribute('fill', 'none');
                    innerContainer.classList.remove('text-red-500', 'bg-white/10');
                    window.showToast('Curtida removida.');
                }
            }
        } catch (err) {
            console.error('Error liking video:', err);
        }
    }

    // Subscribe channel
    async function toggleSubscribe(button, username) {
        const isSubscribed = button.innerText.trim() === 'Seguindo';
        const method = isSubscribed ? 'DELETE' : 'POST';
        const action = isSubscribed ? 'unsubscribe' : 'subscribe';
        const url = `/canal/@${username}/${action}`;

        try {
            const res = await fetch(url, {
                method,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                }
            });
            if (res.ok) {
                if (isSubscribed) {
                    button.innerText = 'Seguir';
                    button.classList.remove('bg-white/10', 'border-white/20', 'text-white');
                    button.classList.add('bg-white', 'text-black', 'border-transparent');
                    window.showToast('Deixou de seguir.');
                } else {
                    button.innerText = 'Seguindo';
                    button.classList.remove('bg-white', 'text-black', 'border-transparent');
                    button.classList.add('bg-white/10', 'border-white/20', 'text-white');
                    window.showToast('Seguindo canal! 🔔');
                }
            }
        } catch (err) {
            console.error('Error subscribing:', err);
        }
    }

    // Comments drawer logic
    const drawer = document.getElementById('shorts-comments-drawer');
    const commentsList = document.getElementById('shorts-comments-list');
    const commentVideoId = document.getElementById('comment-video-id');

    async function openShortComments(videoId) {
        drawer.classList.remove('translate-x-full');
        commentVideoId.value = videoId;
        commentsList.innerHTML = '<p class="text-sm text-tubi-gray text-center py-10">Carregando comentários...</p>';

        try {
            const res = await fetch(`/api/watch?v=${videoId}`);
            if (res.ok) {
                const data = await res.json();
                commentsList.innerHTML = data.comments_html || '<p class="text-sm text-tubi-gray text-center py-10">Nenhum comentário ainda. Seja o primeiro!</p>';
            } else {
                commentsList.innerHTML = '<p class="text-sm text-red-500 text-center py-10">Erro ao carregar comentários.</p>';
            }
        } catch (err) {
            commentsList.innerHTML = '<p class="text-sm text-red-500 text-center py-10">Erro de conexão.</p>';
        }
    }

    function closeShortComments() {
        drawer.classList.add('translate-x-full');
    }

    async function submitShortComment(event) {
        event.preventDefault();
        const videoId = commentVideoId.value;
        const input = document.getElementById('comment-input');
        const content = input.value;

        try {
            const res = await fetch(`/video/${videoId}/comment`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ content })
            });

            if (res.ok) {
                input.value = '';
                openShortComments(videoId); // Reload comments
                window.showToast('Comentário enviado com sucesso!');
            }
        } catch (err) {
            console.error('Error submitting comment:', err);
        }
    }

    // Share Short
    function shareShort(url) {
        navigator.clipboard.writeText(url)
            .then(() => {
                window.showToast('Link do Short copiado para a área de transferência! 🔗');
            })
            .catch(() => {
                window.showToast('Erro ao copiar link.', 'error');
            });
    }
</script>

<style>
    /* Reset header & body layout for fullscreen feel */
    header {
        position: relative;
        z-index: 60;
    }
    body {
        overflow: hidden; /* Prevent page scroll */
    }
</style>
@endsection
