@extends('layouts.app')
@section('title', 'Configurações — Lumina')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8 pb-20 md:pb-8 animate-fade-in" x-data="{ activeTab: 'profile' }">

    <div class="flex items-center gap-3 mb-8">
        <div class="w-10 h-10 rounded-full bg-tubi-primary/20 flex items-center justify-center">
            <svg class="w-5 h-5 text-tubi-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Configurações</h1>
    </div>

    <!-- Tabs -->
    <div class="flex gap-2 mb-8 glass rounded-2xl p-2 border border-theme w-fit">
        <button @click="activeTab = 'profile'" :class="activeTab === 'profile' ? 'bg-tubi-primary text-white shadow-lg' : 'text-tubi-gray hover:text-white'"
            class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all">Perfil</button>
        <button @click="activeTab = 'notifications'" :class="activeTab === 'notifications' ? 'bg-tubi-primary text-white shadow-lg' : 'text-tubi-gray hover:text-white'"
            class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all">Notificações</button>
        <button @click="activeTab = 'security'" :class="activeTab === 'security' ? 'bg-tubi-primary text-white shadow-lg' : 'text-tubi-gray hover:text-white'"
            class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all">Segurança</button>
    </div>

    @if(session('status'))
        <div class="mb-6 p-4 rounded-xl bg-green-500/10 border border-green-500/20 text-green-400 text-sm font-bold flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('status') }}
        </div>
    @endif

    {{-- Profile Tab --}}
    <div x-show="activeTab === 'profile'" x-transition class="glass rounded-3xl shadow-xl p-8 border border-theme relative overflow-hidden">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6 relative z-10">
            @csrf @method('PUT')

            {{-- Banner --}}
            <div>
                <label class="block text-sm font-bold text-tubi-light mb-2">Banner do Canal</label>
                <div class="relative h-40 rounded-2xl overflow-hidden bg-tubi-darker border border-theme border-dashed cursor-pointer group" x-data="{ preview: null }"
                    @click="$refs.bannerInput.click()">
                    @if($user->banner)
                        <img src="{{ Storage::url($user->banner) }}" class="w-full h-full object-cover" id="banner-preview">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center gap-2 text-tubi-gray group-hover:text-tubi-primary transition-colors" id="banner-empty">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="text-sm font-bold">Clique para adicionar banner</p>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-sm">
                        <p class="text-white text-sm font-bold flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Trocar banner
                        </p>
                    </div>
                    <input type="file" name="banner" x-ref="bannerInput" accept="image/*" class="hidden"
                        @change="const f=$event.target.files[0]; if(f){const r=new FileReader(); r.onload=e=>{document.getElementById('banner-preview') ? document.getElementById('banner-preview').src=e.target.result : ''}; r.readAsDataURL(f)}">
                </div>
            </div>

            {{-- Avatar + Name --}}
            <div class="flex gap-6 items-center">
                <div class="flex-shrink-0 cursor-pointer relative group" x-data="{ preview: null }" @click="$refs.avatarInput.click()">
                    <div class="w-24 h-24 rounded-full overflow-hidden bg-tubi-darker border-4 border-tubi-primary shadow-[0_0_15px_rgba(124,58,237,0.3)]">
                        @if($user->avatar)
                            <img src="{{ Storage::url($user->avatar) }}" class="w-full h-full object-cover" id="avatar-preview">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-tubi-primary to-tubi-secondary flex items-center justify-center text-white text-3xl font-bold" id="avatar-initial">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="absolute inset-0 bg-black/60 rounded-full opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <input type="file" name="avatar" x-ref="avatarInput" accept="image/*" class="hidden"
                        @change="const f=$event.target.files[0]; if(f){const r=new FileReader(); r.onload=e=>{document.getElementById('avatar-preview') ? document.getElementById('avatar-preview').src=e.target.result : ''}; r.readAsDataURL(f)}">
                </div>

                <div class="flex-1 space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-tubi-light mb-1.5">Nome de Exibição</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full px-5 py-3 rounded-xl bg-tubi-darker border border-theme text-white text-sm focus:outline-none focus:border-tubi-primary focus:ring-1 focus:ring-tubi-primary transition-all">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-tubi-light mb-1.5">Biografia (Sobre)</label>
                <textarea name="bio" rows="4" placeholder="Fale um pouco sobre seu canal..."
                    class="w-full px-5 py-3 rounded-xl bg-tubi-darker border border-theme text-white text-sm focus:outline-none focus:border-tubi-primary focus:ring-1 focus:ring-tubi-primary resize-none transition-all">{{ old('bio', $user->bio) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-bold text-tubi-light mb-1.5">Links Sociais (Website)</label>
                <div class="relative">
                    <span class="absolute left-4 top-3 text-tubi-gray">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    </span>
                    <input type="url" name="website" value="{{ old('website', $user->website) }}" placeholder="https://seusite.com"
                        class="w-full pl-12 pr-5 py-3 rounded-xl bg-tubi-darker border border-theme text-white text-sm focus:outline-none focus:border-tubi-primary focus:ring-1 focus:ring-tubi-primary transition-all">
                </div>
            </div>

            <div class="pt-4 border-t border-theme flex justify-end">
                <button type="submit" class="px-8 py-3 bg-tubi-primary hover:bg-tubi-primary/90 text-white rounded-xl font-bold transition-all shadow-[0_4px_15px_rgba(124,58,237,0.3)] hover:shadow-[0_6px_25px_rgba(124,58,237,0.4)] hover:-translate-y-0.5">
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>

    {{-- Notifications Tab --}}
    <div x-show="activeTab === 'notifications'" style="display: none;" x-transition class="glass rounded-3xl shadow-xl p-8 border border-theme">
        <form action="{{ route('notifications.prefs') }}" method="POST" class="space-y-4">
            @csrf @method('PUT')

            <h3 class="text-xl font-bold text-white mb-6">Preferências de Alertas</h3>

            <div class="bg-tubi-darker rounded-2xl border border-theme overflow-hidden">
                @foreach([
                    ['key' => 'push_enabled', 'label' => 'Notificações push no navegador', 'icon' => '🔔'],
                    ['key' => 'email_new_videos', 'label' => 'E-mail quando canais publicarem vídeos', 'icon' => '📹'],
                    ['key' => 'email_comments', 'label' => 'E-mail para comentários nos meus vídeos', 'icon' => '💬'],
                    ['key' => 'email_lives', 'label' => 'E-mail quando canais forem ao vivo', 'icon' => '🔴'],
                    ['key' => 'email_weekly_digest', 'label' => 'Resumo semanal de estatísticas', 'icon' => '📊'],
                ] as $pref)
                <label class="flex items-center justify-between p-4 border-b border-theme last:border-0 cursor-pointer hover:bg-white/5 transition-colors group">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                            {{ $pref['icon'] }}
                        </div>
                        <span class="text-sm font-medium text-tubi-light">{{ $pref['label'] }}</span>
                    </div>
                    <div class="relative inline-block w-12 mr-2 align-middle select-none transition duration-200 ease-in">
                        <input type="checkbox" name="{{ $pref['key'] }}" value="1"
                            {{ ($notifPrefs->{$pref['key']} ?? false) ? 'checked' : '' }}
                            class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer transition-transform duration-200 ease-in-out">
                        <label class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-600 cursor-pointer transition-colors duration-200 ease-in-out"></label>
                    </div>
                </label>
                @endforeach
            </div>

            <div class="pt-6 flex justify-end">
                <button type="submit" class="px-8 py-3 bg-tubi-primary hover:bg-tubi-primary/90 text-white rounded-xl font-bold transition-all shadow-[0_4px_15px_rgba(124,58,237,0.3)] hover:-translate-y-0.5">
                    Atualizar Notificações
                </button>
            </div>
        </form>
    </div>

    {{-- Security Tab --}}
    <div x-show="activeTab === 'security'" style="display: none;" x-transition class="glass rounded-3xl shadow-xl p-8 border border-theme">
        <h3 class="text-xl font-bold text-white mb-6">Segurança e Acesso</h3>
        
        <div class="space-y-4">
            <div class="flex items-center justify-between p-5 bg-tubi-darker border border-theme rounded-2xl">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-green-500/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <p class="font-bold text-white">Endereço de E-mail</p>
                        <p class="text-sm text-tubi-gray mt-1">{{ $user->email }}</p>
                    </div>
                </div>
                <span class="px-3 py-1 text-xs rounded-full bg-green-500/20 text-green-400 font-bold border border-green-500/30">Verificado</span>
            </div>

            <div class="flex items-center justify-between p-5 bg-tubi-darker border border-theme rounded-2xl">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-tubi-secondary/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-tubi-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <div>
                        <p class="font-bold text-white">Senha da Conta</p>
                        <p class="text-sm text-tubi-gray mt-1">Sua senha de acesso segura.</p>
                    </div>
                </div>
                <button class="px-4 py-2 text-sm font-bold bg-white/5 hover:bg-white/10 text-white border border-white/10 rounded-xl transition-colors">Alterar</button>
            </div>

            <div class="mt-8 p-6 bg-red-500/5 border border-red-500/20 rounded-2xl">
                <p class="font-bold text-red-500 flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Zona de Perigo
                </p>
                <p class="text-sm text-tubi-gray mb-4">A exclusão da conta apaga permanentemente todos os seus vídeos, comentários e o Clube de Membros.</p>
                <button class="px-5 py-2.5 text-sm font-bold text-red-400 bg-red-500/10 hover:bg-red-500/20 border border-red-500/30 rounded-xl transition-colors">
                    Excluir Minha Conta
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Toggle Switch CSS nativo */
    .toggle-checkbox:checked {
        right: 0;
        border-color: #7C3AED;
    }
    .toggle-checkbox:checked + .toggle-label {
        background-color: #7C3AED;
    }
    .toggle-checkbox {
        right: 0;
        z-index: 1;
        border-color: #4b5563;
        top: 0;
        bottom: 0;
        margin: auto;
        transform: translateX(0);
    }
    .toggle-checkbox:checked {
        transform: translateX(100%);
    }
    .toggle-label {
        background-color: #4b5563;
    }
</style>
@endsection
