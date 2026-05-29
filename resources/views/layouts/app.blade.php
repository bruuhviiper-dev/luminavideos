<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Lumina — O Futuro do Vídeo')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;500;600;700&family=Syne:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Video.js -->
    <link href="https://vjs.zencdn.net/8.6.1/video-js.css" rel="stylesheet" />

    <!-- Theme Init (antes do CSS para evitar flash) -->
    <script>
        (function() {
            const saved = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (saved === 'dark' || (!saved && prefersDark)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();

        function toggleTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        }
    </script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        tubi: {
                            dark:      'var(--tubi-dark)',
                            darker:    'var(--tubi-darker)',
                            card:      'var(--tubi-card)',
                            primary:   'var(--tubi-primary)',
                            secondary: 'var(--tubi-secondary)',
                            light:     'var(--tubi-light)',
                            gray:      'var(--tubi-gray)'
                        }
                    },
                    fontFamily: {
                        sans:    ['"DM Sans"', 'sans-serif'],
                        display: ['"Syne"', 'sans-serif']
                    },
                    animation: {
                        'fade-in':    'fadeIn 0.3s ease-out forwards',
                        'float':      'float 6s ease-in-out infinite',
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'swing':      'swing 0.8s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%':   { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%':      { transform: 'translateY(-10px)' },
                        },
                        swing: {
                            '20%':  { transform: 'rotate(15deg)' },
                            '40%':  { transform: 'rotate(-10deg)' },
                            '60%':  { transform: 'rotate(5deg)' },
                            '80%':  { transform: 'rotate(-5deg)' },
                            '100%': { transform: 'rotate(0deg)' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* ============================================================
           CSS Custom Properties — Sistema de Design Lumina
           Suporte completo a dark/light mode via variáveis
        ============================================================ */
        :root {
            /* Light Mode — Suave e Premium */
            --tubi-dark:      #F1F5F9;
            --tubi-darker:    #FFFFFF;
            --tubi-card:      #FFFFFF;
            --tubi-primary:   #7C3AED;
            --tubi-secondary: #FF6B6B;
            --tubi-light:     #0F172A;      /* texto principal no light */
            --tubi-gray:      #64748B;

            --border-color:   rgba(0, 0, 0, 0.08);
            --glass-bg:       rgba(255, 255, 255, 0.92);
            --input-bg:       #F8FAFC;
            --input-text:     #0F172A;
            --input-border:   rgba(0, 0, 0, 0.12);
            --hover-overlay:  rgba(0, 0, 0, 0.04);
            --sidebar-bg:     rgba(255, 255, 255, 0.97);
            --shadow-color:   rgba(0, 0, 0, 0.08);
        }

        .dark {
            /* Dark Mode — Profundo */
            --tubi-dark:      #0D0D14;
            --tubi-darker:    #05050A;
            --tubi-card:      #151520;
            --tubi-primary:   #7C3AED;
            --tubi-secondary: #FF6B6B;
            --tubi-light:     #F8FAFC;      /* texto principal no dark */
            --tubi-gray:      #8C8C9A;

            --border-color:   rgba(255, 255, 255, 0.05);
            --glass-bg:       rgba(21, 21, 32, 0.85);
            --input-bg:       #0D0D14;
            --input-text:     #F8FAFC;
            --input-border:   rgba(255, 255, 255, 0.08);
            --hover-overlay:  rgba(255, 255, 255, 0.05);
            --sidebar-bg:     rgba(5, 5, 10, 0.97);
            --shadow-color:   rgba(0, 0, 0, 0.4);
        }

        /* Base */
        *, *::before, *::after { box-sizing: border-box; }

        body {
            background-color: var(--tubi-dark);
            color: var(--tubi-light);
            -webkit-font-smoothing: antialiased;
            transition: background-color 0.25s ease, color 0.25s ease;
        }

        /* Inputs — funcionam em ambos os modos */
        input, textarea, select {
            background-color: var(--input-bg) !important;
            color: var(--input-text) !important;
            border-color: var(--input-border) !important;
        }
        input::placeholder, textarea::placeholder {
            color: var(--tubi-gray) !important;
            opacity: 0.8;
        }
        select option {
            background-color: var(--tubi-darker);
            color: var(--tubi-light);
        }

        /* Utilitários temáticos */
        .border-theme    { border-color: var(--border-color); }
        .bg-input        { background-color: var(--input-bg); }
        .text-input      { color: var(--input-text); }
        .hover-overlay:hover { background-color: var(--hover-overlay); }

        /* Scrollbar */
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(124, 58, 237, 0.3); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--tubi-primary); }

        /* Gradiente de texto */
        .text-gradient {
            background: linear-gradient(to right, var(--tubi-primary), var(--tubi-secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Glass effect */
        .glass {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
        }

        /* Sidebar */
        .sidebar-link {
            position: relative;
            overflow: hidden;
        }
        .sidebar-link::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 0;
            background: linear-gradient(90deg, var(--tubi-primary) 0%, transparent 100%);
            opacity: 0.1;
            transition: width 0.3s ease;
            z-index: 0;
        }
        .sidebar-link:hover::before { width: 100%; }
        .sidebar-link.active {
            background: var(--tubi-card);
            border-left: 3px solid var(--tubi-primary);
            color: var(--tubi-primary);
            font-weight: 700;
        }
        .sidebar-link.active svg { color: var(--tubi-primary); }
        .sidebar-link span, .sidebar-link svg { position: relative; z-index: 1; }

        /* Toast de notificação */
        #toast-container {
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .toast {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1.25rem;
            border-radius: 0.875rem;
            font-size: 0.875rem;
            font-weight: 600;
            background: var(--glass-bg);
            border: 1px solid var(--border-color);
            backdrop-filter: blur(16px);
            color: var(--tubi-light);
            box-shadow: 0 8px 32px var(--shadow-color);
            animation: slideInRight 0.3s ease-out;
            max-width: 360px;
        }
        .toast.success { border-color: rgba(34, 197, 94, 0.3); }
        .toast.error   { border-color: rgba(239, 68, 68, 0.3); }
        @keyframes slideInRight {
            from { transform: translateX(120%); opacity: 0; }
            to   { transform: translateX(0);    opacity: 1; }
        }

        /* Progresso de upload */
        #upload-progress-bar {
            transition: width 0.3s ease;
        }
    </style>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="flex flex-col h-screen overflow-hidden selection:bg-tubi-primary selection:text-white"
      x-data="{ sidebarOpen: window.innerWidth >= 768, mobileSearchOpen: false }">

    {{-- ==================== NAVBAR ==================== --}}
    <header class="h-16 flex items-center justify-between px-4 lg:px-6 glass sticky top-0 z-50">

        <!-- Left: Menu & Logo -->
        <div class="flex items-center gap-3 md:gap-4">
            <button @click="sidebarOpen = !sidebarOpen"
                class="p-2 rounded-full text-tubi-light transition-colors focus:outline-none"
                style="background: transparent;"
                onmouseenter="this.style.background='var(--hover-overlay)'"
                onmouseleave="this.style.background='transparent'"
                aria-label="Toggle Menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            <a href="{{ route('home') }}" class="flex items-center gap-2 group outline-none rounded-lg focus-visible:ring-2 focus-visible:ring-tubi-primary">
                <div class="relative w-8 h-8 flex items-center justify-center transform transition-transform group-hover:scale-110 group-hover:rotate-[15deg] duration-500">
                    <svg viewBox="0 0 100 100" fill="none" class="w-full h-full relative z-10">
                        <path d="M50 10 L90 30 L90 70 L50 90 L10 70 L10 30 Z" fill="url(#prismGrad)" class="opacity-90"/>
                        <path d="M50 10 L50 50 L90 30 Z" fill="#FFFFFF" fill-opacity="0.3"/>
                        <path d="M50 50 L10 30 L10 70 Z" fill="#000000" fill-opacity="0.2"/>
                        <path d="M50 50 L90 70 L50 90 Z" fill="#000000" fill-opacity="0.4"/>
                        <circle cx="50" cy="50" r="10" fill="#FFFFFF"/>
                        <defs>
                            <linearGradient id="prismGrad" x1="10" y1="10" x2="90" y2="90" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#7C3AED"/>
                                <stop offset="1" stop-color="#FF6B6B"/>
                            </linearGradient>
                        </defs>
                    </svg>
                    <div class="absolute inset-0 bg-tubi-primary blur-md opacity-40 group-hover:opacity-70 transition-opacity rounded-full"></div>
                </div>
                <span class="text-2xl font-display font-bold tracking-tight text-tubi-light group-hover:text-gradient transition-all hidden sm:block">Lumina</span>
            </a>
        </div>

        <!-- Center: Search -->
        <form action="{{ route('search') }}" method="GET"
            class="hidden md:flex items-center flex-1 max-w-2xl mx-8 relative group" role="search">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-tubi-gray group-focus-within:text-tubi-primary transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input type="text" name="q" id="search-input"
                placeholder="Buscar vídeos, canais ou assuntos..."
                value="{{ request('q') }}"
                autocomplete="off"
                class="w-full rounded-full py-2.5 pl-12 pr-24 focus:outline-none focus:ring-2 focus:ring-tubi-primary/30 transition-all font-medium text-sm shadow-sm"
                style="background:var(--input-bg); border:1px solid var(--input-border); color:var(--input-text);">
            <button type="submit"
                class="absolute right-1.5 top-1.5 bottom-1.5 px-5 rounded-full hover:bg-tubi-primary/10 text-tubi-primary font-bold text-sm transition-colors border border-transparent">
                Buscar
            </button>
        </form>

        <!-- Right: Actions -->
        <div class="flex items-center gap-1 sm:gap-2">
            <!-- Mobile search -->
            <button @click="mobileSearchOpen = !mobileSearchOpen"
                class="md:hidden p-2 rounded-full text-tubi-light transition-colors"
                style="background:transparent"
                onmouseenter="this.style.background='var(--hover-overlay)'"
                onmouseleave="this.style.background='transparent'">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </button>

            <!-- Theme Switcher -->
            <button onclick="toggleTheme()" title="Alternar Tema"
                class="p-2 rounded-full text-tubi-gray hover:text-tubi-primary transition-colors"
                style="background:transparent"
                onmouseenter="this.style.background='var(--hover-overlay)'"
                onmouseleave="this.style.background='transparent'">
                <!-- Moon (light mode → click to go dark) -->
                <svg class="w-6 h-6 dark:hidden block" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                </svg>
                <!-- Sun (dark mode → click to go light) -->
                <svg class="w-6 h-6 hidden dark:block text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
                </svg>
            </button>

            <!-- Upload Button -->
            <a href="{{ route('video.create') }}"
                class="hidden sm:flex items-center gap-2 px-5 py-2.5 rounded-full bg-gradient-to-r from-tubi-primary to-tubi-secondary text-sm font-bold text-white transition-all hover:scale-105 shadow-[0_4px_15px_rgba(124,58,237,0.3)] hover:shadow-[0_6px_20px_rgba(124,58,237,0.5)]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>Criar</span>
            </a>

            @auth
                <!-- Notifications -->
                <a href="{{ route('notifications.index') }}"
                    class="relative p-2 rounded-full text-tubi-gray hover:text-tubi-light transition-colors group"
                    style="background:transparent"
                    onmouseenter="this.style.background='var(--hover-overlay)'"
                    onmouseleave="this.style.background='transparent'">
                    <svg class="w-6 h-6 group-hover:animate-swing" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/></svg>
                    @if(Auth::user()->notifications()->where('is_read', false)->count() > 0)
                        <span id="notif-badge" class="absolute top-1 right-1 w-3 h-3 bg-tubi-secondary rounded-full ring-2 ring-tubi-dark animate-pulse shadow-[0_0_10px_rgba(255,107,107,0.8)]"></span>
                    @endif
                </a>

                <!-- User Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="block rounded-full focus:outline-none focus:ring-4 focus:ring-tubi-primary/30 transition-all hover:scale-105 ml-1">
                        <div class="w-9 h-9 rounded-full overflow-hidden bg-tubi-card border-2 border-tubi-primary/80 relative">
                            @if(Auth::user()->avatar)
                                <img src="{{ Storage::url(Auth::user()->avatar) }}" class="w-full h-full object-cover" alt="{{ Auth::user()->name }}">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-tubi-primary to-tubi-secondary flex items-center justify-center text-white text-sm font-bold font-display">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                    </button>

                    <div x-show="open" @click.outside="open = false" x-transition.origin.top.right style="display: none;"
                        class="absolute right-0 mt-3 w-64 rounded-2xl shadow-2xl py-2 z-50 border border-theme"
                        style="background:var(--glass-bg); backdrop-filter:blur(16px);">
                        <div class="px-4 py-4 border-b border-theme flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full overflow-hidden flex-shrink-0 border border-theme">
                                @if(Auth::user()->avatar)
                                    <img src="{{ Storage::url(Auth::user()->avatar) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-tubi-primary to-tubi-secondary flex items-center justify-center text-white text-lg font-bold font-display">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-tubi-light truncate">{{ Auth::user()->name }}</p>
                                <p class="text-xs font-medium text-tubi-primary truncate">{{ '@'.Auth::user()->username }}</p>
                            </div>
                        </div>

                        <div class="py-2">
                            <a href="{{ route('channel.show', Auth::user()->username) }}"
                                class="flex items-center gap-3 px-5 py-2.5 text-sm font-medium text-tubi-light transition-colors group"
                                style="background:transparent"
                                onmouseenter="this.style.background='var(--hover-overlay)'"
                                onmouseleave="this.style.background='transparent'">
                                <svg class="w-5 h-5 text-tubi-gray group-hover:text-tubi-primary transition-colors" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                                Meu Canal
                            </a>
                            <a href="{{ route('analytics.index') }}"
                                class="flex items-center gap-3 px-5 py-2.5 text-sm font-medium text-tubi-light transition-colors group"
                                style="background:transparent"
                                onmouseenter="this.style.background='var(--hover-overlay)'"
                                onmouseleave="this.style.background='transparent'">
                                <svg class="w-5 h-5 text-tubi-gray group-hover:text-blue-400 transition-colors" fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/></svg>
                                Lumina Studio
                            </a>
                            <a href="{{ route('settings') }}"
                                class="flex items-center gap-3 px-5 py-2.5 text-sm font-medium text-tubi-light transition-colors group"
                                style="background:transparent"
                                onmouseenter="this.style.background='var(--hover-overlay)'"
                                onmouseleave="this.style.background='transparent'">
                                <svg class="w-5 h-5 text-tubi-gray group-hover:text-tubi-primary transition-colors" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>
                                Configurações
                            </a>
                        </div>

                        <div class="py-2 border-t border-theme">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-3 px-5 py-2.5 text-sm font-bold text-red-500 transition-colors group"
                                    style="background:transparent"
                                    onmouseenter="this.style.background='rgba(239,68,68,0.08)'"
                                    onmouseleave="this.style.background='transparent'">
                                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Sair da Conta
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}"
                    class="px-5 py-2 text-sm font-bold rounded-full border-2 border-tubi-primary text-tubi-primary hover:bg-tubi-primary hover:text-white transition-all">
                    Fazer Login
                </a>
            @endauth
        </div>
    </header>

    <!-- Mobile Search Bar -->
    <div x-show="mobileSearchOpen" x-transition style="display: none;"
        class="md:hidden absolute top-16 left-0 right-0 p-3 border-b border-theme z-40"
        style="background:var(--glass-bg); backdrop-filter:blur(16px);">
        <form action="{{ route('search') }}" method="GET" class="relative">
            <input type="text" name="q" placeholder="Buscar..." value="{{ request('q') }}" autofocus
                class="w-full rounded-full py-3 pl-5 pr-12 focus:outline-none focus:ring-2 focus:ring-tubi-primary font-medium text-sm shadow-sm"
                style="background:var(--input-bg); border:1px solid var(--input-border); color:var(--input-text);">
            <button type="submit" class="absolute right-2 top-2 bottom-2 px-3 text-tubi-primary">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </button>
        </form>
    </div>

    {{-- ==================== MAIN LAYOUT ==================== --}}
    <div class="flex-1 flex overflow-hidden">

        <!-- SIDEBAR -->
        <aside class="w-64 flex-shrink-0 border-r border-theme overflow-y-auto hidden md:flex flex-col transition-all duration-300 relative z-10"
               :class="sidebarOpen ? 'ml-0' : '-ml-64'"
               style="background: var(--sidebar-bg); backdrop-filter: blur(12px);">
            <nav class="py-6 px-3 space-y-1.5 flex-1">

                <a href="{{ route('home') }}"
                    class="sidebar-link flex items-center gap-4 px-4 py-3 rounded-xl text-tubi-gray hover:text-tubi-light transition-all group {{ request()->routeIs('home') ? 'active' : '' }}">
                    <svg class="w-6 h-6 transition-transform group-hover:scale-110 group-hover:text-tubi-primary {{ request()->routeIs('home') ? 'text-tubi-primary' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                    <span class="font-bold text-sm">Início</span>
                </a>

                <a href="{{ route('shorts.index') }}"
                    class="sidebar-link flex items-center gap-4 px-4 py-3 rounded-xl text-tubi-gray hover:text-tubi-light transition-all group {{ request()->routeIs('shorts.*') ? 'active' : '' }}">
                    <svg class="w-6 h-6 transition-transform group-hover:scale-110 group-hover:text-tubi-secondary {{ request()->routeIs('shorts.*') ? 'text-tubi-secondary' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
                    <span class="font-bold text-sm">Lumina Shorts</span>
                </a>

                <a href="{{ route('live.index') }}"
                    class="sidebar-link flex items-center gap-4 px-4 py-3 rounded-xl text-tubi-gray hover:text-tubi-light transition-all group {{ request()->routeIs('live.*') ? 'active' : '' }}">
                    <svg class="w-6 h-6 transition-transform group-hover:scale-110 group-hover:text-red-500 {{ request()->routeIs('live.*') ? 'text-red-500' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd"/></svg>
                    <span class="font-bold text-sm text-red-500">Ao Vivo</span>
                    <span class="ml-auto w-2 h-2 rounded-full bg-red-500 animate-pulse shadow-[0_0_8px_rgba(239,68,68,0.8)]"></span>
                </a>

                @auth
                <hr class="border-theme my-4">
                <h3 class="px-4 text-[10px] font-black text-tubi-gray uppercase tracking-widest mb-2">Biblioteca</h3>

                <a href="{{ route('history.index') }}"
                    class="sidebar-link flex items-center gap-4 px-4 py-2.5 rounded-xl text-tubi-gray hover:text-tubi-light transition-all group">
                    <svg class="w-5 h-5 transition-transform group-hover:scale-110 group-hover:text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                    <span class="font-bold text-sm">Histórico</span>
                </a>
                <a href="{{ route('playlist.index') }}"
                    class="sidebar-link flex items-center gap-4 px-4 py-2.5 rounded-xl text-tubi-gray hover:text-tubi-light transition-all group">
                    <svg class="w-5 h-5 transition-transform group-hover:scale-110 group-hover:text-green-500" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a1 1 0 00-2 0v7.268a2 2 0 000 3.464V16a1 1 0 102 0v-1.268a2 2 0 000-3.464V4zM11 4a1 1 0 10-2 0v1.268a2 2 0 000 3.464V16a1 1 0 102 0V8.732a2 2 0 000-3.464V4zM16 3a1 1 0 011 1v7.268a2 2 0 010 3.464V16a1 1 0 11-2 0v-1.268a2 2 0 010-3.464V4a1 1 0 011-1z"/></svg>
                    <span class="font-bold text-sm">Playlists</span>
                </a>
                @endauth

                <hr class="border-theme my-4">
                <h3 class="px-4 text-[10px] font-black text-tubi-gray uppercase tracking-widest mb-2">Explorar</h3>

                <div class="space-y-1">
                    @foreach(\App\Models\Category::all() ?? [] as $cat)
                    <a href="{{ route('category', $cat->slug) }}"
                        class="sidebar-link flex items-center gap-4 px-4 py-2 rounded-xl text-tubi-gray hover:text-tubi-light transition-all group {{ request()->is('categoria/'.$cat->slug) ? 'active' : '' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-tubi-gray group-hover:text-tubi-primary group-hover:scale-110 transition-all group-hover:bg-tubi-primary/10 border border-transparent group-hover:border-tubi-primary/20"
                             style="background:var(--hover-overlay)">
                            {!! $cat->icon ?? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>' !!}
                        </div>
                        <span class="font-bold text-sm">{{ $cat->name }}</span>
                    </a>
                    @endforeach
                </div>
            </nav>

            <div class="p-5 mt-auto border-t border-theme" style="background:var(--hover-overlay)">
                <p class="text-[11px] text-tubi-gray font-bold">© {{ date('Y') }} Lumina Plataforma.</p>
                <div class="flex gap-3 mt-2">
                    <a href="#" class="text-[10px] text-tubi-gray hover:text-tubi-primary transition-colors">Termos</a>
                    <a href="#" class="text-[10px] text-tubi-gray hover:text-tubi-primary transition-colors">Privacidade</a>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="flex-1 overflow-y-auto relative scroll-smooth" style="background:var(--tubi-dark)">

            {{-- Flash messages --}}
            @if(session('status'))
                <div id="flash-toast" class="fixed top-20 right-4 z-[9998] flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-2xl border border-green-500/30 text-sm font-bold text-green-500 animate-fade-in max-w-sm"
                     style="background:var(--glass-bg); backdrop-filter:blur(16px)">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('status') }}
                    <button onclick="document.getElementById('flash-toast').remove()" class="ml-2 text-green-400/60 hover:text-green-400 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <script>setTimeout(() => { const t = document.getElementById('flash-toast'); if(t) t.remove(); }, 5000);</script>
            @endif

            @if(session('error') || $errors->any())
                <div id="error-toast" class="fixed top-20 right-4 z-[9998] flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-2xl border border-red-500/30 text-sm font-bold text-red-500 animate-fade-in max-w-sm"
                     style="background:var(--glass-bg); backdrop-filter:blur(16px)">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('error') ?? $errors->first() }}
                </div>
                <script>setTimeout(() => { const t = document.getElementById('error-toast'); if(t) t.remove(); }, 6000);</script>
            @endif

            @yield('content')
        </main>
    </div>

    {{-- BOTTOM NAVIGATION (Mobile) --}}
    <nav class="md:hidden fixed bottom-0 left-0 right-0 border-t border-theme z-50 flex justify-around items-center px-2 py-2 pb-safe"
         style="background:var(--sidebar-bg); backdrop-filter:blur(20px);">
        <a href="{{ route('home') }}" class="flex flex-col items-center gap-1 p-2 {{ request()->routeIs('home') ? 'text-tubi-primary' : 'text-tubi-gray' }} transition-colors">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
            <span class="text-[10px] font-bold">Início</span>
        </a>
        <a href="{{ route('shorts.index') }}" class="flex flex-col items-center gap-1 p-2 {{ request()->routeIs('shorts.*') ? 'text-tubi-secondary' : 'text-tubi-gray' }} transition-colors">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
            <span class="text-[10px] font-bold">Shorts</span>
        </a>
        <a href="{{ route('video.create') }}" class="relative -top-5 flex flex-col items-center justify-center w-14 h-14 rounded-full bg-gradient-to-tr from-tubi-primary to-tubi-secondary text-white shadow-[0_4px_20px_rgba(124,58,237,0.5)] transform transition-transform active:scale-95 border-4" style="border-color:var(--tubi-dark)">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
        </a>
        <a href="{{ route('notifications.index') }}" class="flex flex-col items-center gap-1 p-2 text-tubi-gray transition-colors relative">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/></svg>
            <span class="text-[10px] font-bold">Avisos</span>
            @auth
                @if(Auth::user()->notifications()->where('is_read', false)->count() > 0)
                    <span class="absolute top-2 right-2 w-2 h-2 bg-tubi-secondary rounded-full border-2" style="border-color:var(--tubi-dark)"></span>
                @endif
            @endauth
        </a>
        @auth
        <a href="{{ route('channel.show', Auth::user()->username) }}" class="flex flex-col items-center gap-1 p-2">
            <div class="w-6 h-6 rounded-full overflow-hidden border-2 border-tubi-primary bg-tubi-card">
                @if(Auth::user()->avatar)
                    <img src="{{ Storage::url(Auth::user()->avatar) }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-tubi-primary flex items-center justify-center text-white text-[10px] font-bold">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            <span class="text-[10px] font-bold text-tubi-gray">Perfil</span>
        </a>
        @else
        <a href="{{ route('login') }}" class="flex flex-col items-center gap-1 p-2 text-tubi-gray transition-colors">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
            <span class="text-[10px] font-bold">Entrar</span>
        </a>
        @endauth
    </nav>

    <!-- Toast Container -->
    <div id="toast-container"></div>

    <!-- Video.js -->
    <script src="https://vjs.zencdn.net/8.6.1/video.min.js"></script>

    <!-- Global JS Utilities -->
    <script>
        // Toast notification system
        window.showToast = function(message, type = 'success', duration = 4000) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            const icon = type === 'success'
                ? '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>'
                : '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
            toast.className = `toast ${type}`;
            toast.innerHTML = `${icon}<span>${message}</span>`;
            container.appendChild(toast);
            setTimeout(() => { toast.style.opacity = '0'; toast.style.transform = 'translateX(120%)'; toast.style.transition = 'all 0.3s'; setTimeout(() => toast.remove(), 300); }, duration);
        };

        // Polling de notificações a cada 30s
        @auth
        (function pollNotifications() {
            setInterval(async () => {
                try {
                    const res = await fetch('/notificacoes', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content } });
                    if (res.ok) {
                        const badge = document.getElementById('notif-badge');
                        // Badge já é renderizado pelo servidor; só re-polamos se quisermos SPA
                    }
                } catch(e) {}
            }, 30000);
        })();
        @endauth
    </script>

    @yield('scripts')
</body>
</html>
