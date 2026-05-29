@extends('layouts.app')
@section('title', 'Entrar — Lumina')

@section('content')
<div class="min-h-[calc(100vh-64px)] flex items-center justify-center px-4 py-12 relative overflow-hidden">
    <!-- Decoração de Fundo Animada -->
    <div class="absolute top-10 left-10 w-64 h-64 bg-tubi-primary/20 rounded-full blur-[100px] animate-pulse-slow"></div>
    <div class="absolute bottom-10 right-10 w-80 h-80 bg-tubi-secondary/20 rounded-full blur-[120px] animate-pulse-slow" style="animation-delay: 1.5s;"></div>

    <div class="w-full max-w-md relative z-10 animate-fade-in">

        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 bg-gradient-to-tr from-tubi-primary to-tubi-secondary shadow-[0_0_30px_rgba(124,58,237,0.3)]">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
            </div>
            <h1 class="text-3xl font-display font-bold text-tubi-light">Entrar no Lumina</h1>
            <p class="text-tubi-gray text-sm mt-2">Bem-vindo de volta! 👋</p>
        </div>

        <div class="glass rounded-3xl shadow-2xl p-8 border border-theme">
            <form action="{{ route('login.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-tubi-gray mb-1.5 pl-1">E-mail</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        placeholder="seu@email.com"
                        class="w-full px-5 py-3 rounded-xl bg-tubi-darker border border-theme text-tubi-light text-sm focus:outline-none focus:ring-1 focus:ring-tubi-primary focus:border-tubi-primary transition-all @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-red-400 text-xs mt-1 pl-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5 pl-1 pr-1">
                        <label class="text-sm font-medium text-tubi-gray">Senha</label>
                    </div>
                    <input type="password" name="password" required placeholder="••••••••"
                        class="w-full px-5 py-3 rounded-xl bg-tubi-darker border border-theme text-tubi-light text-sm focus:outline-none focus:ring-1 focus:ring-tubi-primary focus:border-tubi-primary transition-all">
                    @error('password')
                        <p class="text-red-400 text-xs mt-1 pl-1">{{ $message }}</p>
                    @enderror
                </div>

                <label class="flex items-center gap-3 cursor-pointer pl-1 group">
                    <input type="checkbox" name="remember" class="w-4 h-4 text-tubi-primary bg-tubi-darker border-theme rounded focus:ring-tubi-primary">
                    <span class="text-sm text-tubi-gray group-hover:text-tubi-light transition-colors">Lembrar de mim</span>
                </label>

                <button type="submit"
                    class="w-full py-3.5 bg-tubi-primary hover:bg-tubi-primary/90 text-white rounded-xl font-bold transition-all shadow-[0_4px_15px_rgba(124,58,237,0.3)] hover:shadow-[0_6px_25px_rgba(124,58,237,0.4)] flex items-center justify-center gap-2 hover:-translate-y-0.5 mt-2">
                    Entrar
                </button>
            </form>

            <p class="text-center text-sm text-tubi-gray mt-8">
                Não tem conta?
                <a href="{{ route('register') }}" class="text-tubi-primary hover:text-white transition-colors font-bold ml-1 hover:underline">Criar grátis</a>
            </p>
        </div>

        <!-- Test accounts hint -->
        <div class="mt-6 p-4 glass rounded-2xl border border-theme text-center">
            <p class="text-xs text-tubi-gray mb-1">Conta admin de teste:</p>
            <p class="text-sm text-tubi-light font-medium tracking-wider">admin@lumina.com / 12345678</p>
        </div>
    </div>
</div>
@endsection
