@extends('layouts.app')
@section('title', 'Criar Conta — Lumina')

@section('content')
<div class="min-h-[calc(100vh-64px)] flex items-center justify-center px-4 py-12 relative overflow-hidden">
    <!-- Decoração de Fundo Animada -->
    <div class="absolute top-10 right-10 w-64 h-64 bg-tubi-secondary/20 rounded-full blur-[100px] animate-pulse-slow"></div>
    <div class="absolute bottom-10 left-10 w-80 h-80 bg-tubi-primary/20 rounded-full blur-[120px] animate-pulse-slow" style="animation-delay: 1.5s;"></div>

    <div class="w-full max-w-md relative z-10 animate-fade-in">
        
        <!-- Logo -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-display font-bold text-tubi-light">Criar sua conta</h1>
            <p class="text-tubi-gray text-sm mt-2">Comece a publicar e assistir vídeos hoje.</p>
        </div>

        <div class="glass rounded-3xl shadow-2xl p-8 border border-theme">
            <form action="{{ route('register.store') }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-tubi-gray mb-1.5 pl-1">Nome completo</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            placeholder="João Silva"
                            class="w-full px-4 py-3 rounded-xl bg-tubi-darker border border-theme text-tubi-light text-sm focus:outline-none focus:ring-1 focus:ring-tubi-primary focus:border-tubi-primary transition-all @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="text-red-400 text-xs mt-1 pl-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-tubi-gray mb-1.5 pl-1">Username</label>
                        <div class="relative flex items-center">
                            <span class="absolute left-4 text-tubi-gray text-sm font-medium">@</span>
                            <input type="text" name="username" value="{{ old('username') }}" required
                                placeholder="joao"
                                class="w-full pl-8 pr-4 py-3 rounded-xl bg-tubi-darker border border-theme text-tubi-light text-sm focus:outline-none focus:ring-1 focus:ring-tubi-primary focus:border-tubi-primary transition-all @error('username') border-red-500 @enderror">
                        </div>
                        @error('username')
                            <p class="text-red-400 text-xs mt-1 pl-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-tubi-gray mb-1.5 pl-1">E-mail</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        placeholder="seu@email.com"
                        class="w-full px-4 py-3 rounded-xl bg-tubi-darker border border-theme text-tubi-light text-sm focus:outline-none focus:ring-1 focus:ring-tubi-primary focus:border-tubi-primary transition-all @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-red-400 text-xs mt-1 pl-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-tubi-gray mb-1.5 pl-1">Senha</label>
                    <input type="password" name="password" required placeholder="••••••••"
                        class="w-full px-4 py-3 rounded-xl bg-tubi-darker border border-theme text-tubi-light text-sm focus:outline-none focus:ring-1 focus:ring-tubi-primary focus:border-tubi-primary transition-all @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="text-red-400 text-xs mt-1 pl-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-tubi-gray mb-1.5 pl-1">Confirmar Senha</label>
                    <input type="password" name="password_confirmation" required placeholder="Repita a senha"
                        class="w-full px-4 py-3 rounded-xl bg-tubi-darker border border-theme text-tubi-light text-sm focus:outline-none focus:ring-1 focus:ring-tubi-primary focus:border-tubi-primary transition-all">
                </div>

                <button type="submit"
                    class="w-full py-3.5 mt-2 bg-tubi-primary hover:bg-tubi-primary/90 text-white rounded-xl font-bold transition-all shadow-[0_4px_15px_rgba(124,58,237,0.3)] hover:shadow-[0_6px_25px_rgba(124,58,237,0.4)] hover:-translate-y-0.5">
                    Criar Conta
                </button>
            </form>

            <p class="text-center text-xs text-tubi-gray/70 mt-6">
                Ao criar uma conta você concorda com nossos Termos de Uso
            </p>
            
            <p class="text-center text-sm text-tubi-gray mt-6">
                Já tem conta?
                <a href="{{ route('login') }}" class="text-tubi-primary hover:text-white transition-colors font-bold ml-1 hover:underline">Entrar agora</a>
            </p>
        </div>
    </div>
</div>
@endsection
