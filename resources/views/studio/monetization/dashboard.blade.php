@extends('layouts.app')

@section('title', 'Monetização - Lumina Studio')

@section('content')
<div class="px-4 py-8 max-w-[1200px] mx-auto animate-fade-in" x-data="{ tab: 'overview', showWithdrawalModal: false, showPlanModal: false }">
    
    <div class="flex items-center gap-4 mb-8">
        <svg class="w-10 h-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <h1 class="text-3xl font-display font-bold text-white">Monetização</h1>
    </div>

    @if(session('status'))
    <div class="mb-6 bg-green-500/10 border border-green-500/20 text-green-400 px-4 py-3 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('status') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-xl">
        <ul>
            @foreach($errors->all() as $error)
                <li>• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Tabs -->
    <div class="flex border-b border-theme mb-8">
        <button @click="tab = 'overview'" :class="tab === 'overview' ? 'border-tubi-primary text-tubi-primary' : 'border-transparent text-tubi-gray hover:text-white'" class="px-6 py-3 border-b-2 font-medium transition-colors">Visão Geral</button>
        <button @click="tab = 'membership'" :class="tab === 'membership' ? 'border-tubi-primary text-tubi-primary' : 'border-transparent text-tubi-gray hover:text-white'" class="px-6 py-3 border-b-2 font-medium transition-colors">Clube de Membros</button>
    </div>

    <!-- Overview Tab -->
    <div x-show="tab === 'overview'" x-transition>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="glass p-6 rounded-2xl relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                    <svg class="w-24 h-24 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                </div>
                <h3 class="text-tubi-gray font-medium mb-1 relative z-10">Ganhos Totais</h3>
                <p class="text-4xl font-display font-bold text-white relative z-10">R$ {{ number_format($earnings, 2, ',', '.') }}</p>
                <div class="mt-4 relative z-10">
                    <button @click="showWithdrawalModal = true" class="text-sm font-bold bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-full transition-colors">Solicitar Saque</button>
                </div>
            </div>

            <div class="glass p-6 rounded-2xl relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                    <svg class="w-24 h-24 text-tubi-primary" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/></svg>
                </div>
                <h3 class="text-tubi-gray font-medium mb-1 relative z-10">Membros Ativos</h3>
                <p class="text-4xl font-display font-bold text-white relative z-10">{{ $members }}</p>
            </div>
        </div>

        <h3 class="text-xl font-bold text-white mb-4">Transações Recentes</h3>
        <div class="glass rounded-2xl overflow-hidden">
            @if($recentTransactions->isEmpty())
                <div class="p-8 text-center text-tubi-gray">
                    Nenhuma transação encontrada.
                </div>
            @else
                <table class="w-full text-left">
                    <thead class="bg-black/20 text-tubi-gray text-sm">
                        <tr>
                            <th class="p-4 font-medium">Data</th>
                            <th class="p-4 font-medium">Descrição</th>
                            <th class="p-4 font-medium">Tipo</th>
                            <th class="p-4 font-medium text-right">Valor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-theme">
                        @foreach($recentTransactions as $tx)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="p-4 text-sm text-tubi-gray">{{ \Carbon\Carbon::parse($tx->created_at)->format('d/m/Y H:i') }}</td>
                            <td class="p-4 text-sm text-tubi-light">{{ $tx->description }}</td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 text-xs rounded-full bg-blue-500/20 text-blue-400 border border-blue-500/30">
                                    {{ ucfirst($tx->type) }}
                                </span>
                            </td>
                            <td class="p-4 text-right font-bold text-green-400">+ R$ {{ number_format($tx->amount, 2, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <!-- Membership Tab -->
    <div x-show="tab === 'membership'" x-transition style="display: none;">
        @if($plan)
            <div class="glass p-8 rounded-2xl border-2 border-tubi-primary/30 text-center max-w-2xl mx-auto">
                <div class="w-20 h-20 bg-tubi-primary/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-tubi-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h2 class="text-2xl font-bold text-white mb-2">Seu Clube de Membros está Ativo!</h2>
                <p class="text-tubi-gray mb-6">Os seus espectadores já podem se tornar membros através do seu canal e durante suas lives.</p>
                
                <div class="bg-black/20 p-6 rounded-xl text-left">
                    <p class="text-sm text-tubi-gray mb-1">Nome do Plano</p>
                    <p class="text-lg font-bold text-white mb-4">{{ $plan->name }}</p>
                    
                    <p class="text-sm text-tubi-gray mb-1">Preço Mensal</p>
                    <p class="text-xl font-display font-bold text-green-400">R$ {{ number_format($plan->price, 2, ',', '.') }}</p>
                </div>
                
                <button @click="showPlanModal = true" class="mt-6 text-tubi-primary hover:text-white transition-colors underline">Editar Plano</button>
            </div>
        @else
            <div class="glass p-12 rounded-2xl text-center max-w-2xl mx-auto border border-theme border-dashed">
                <svg class="w-16 h-16 text-tubi-gray/50 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                <h2 class="text-2xl font-bold text-white mb-2">Crie seu Clube de Membros</h2>
                <p class="text-tubi-gray mb-8">Permita que seus fãs apoiem seu canal mensalmente em troca de badges e benefícios exclusivos.</p>
                
                <button @click="showPlanModal = true" class="bg-tubi-primary hover:bg-tubi-primary/80 text-white px-8 py-3 rounded-full font-bold transition-all shadow-[0_0_20px_rgba(124,58,237,0.4)] hover:scale-105">
                    Configurar Agora
                </button>
            </div>
        @endif
    </div>

    <!-- Modal Withdrawal -->
    <div x-show="showWithdrawalModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center">
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="showWithdrawalModal = false"></div>
        <div class="glass relative p-8 rounded-3xl max-w-md w-full mx-4 border border-theme shadow-2xl animate-fade-in">
            <h3 class="text-2xl font-bold text-white mb-6">Solicitar Saque (Pix)</h3>
            
            <form action="{{ route('withdrawal.request') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-tubi-gray mb-2">Valor do Saque</label>
                    <div class="relative">
                        <span class="absolute left-4 top-3 text-tubi-gray font-bold">R$</span>
                        <input type="number" name="amount" step="0.01" min="50" max="{{ $earnings }}" value="{{ $earnings }}" required
                               class="w-full bg-tubi-darker border border-theme rounded-xl py-3 pl-12 pr-4 text-white focus:outline-none focus:border-tubi-primary focus:ring-1 focus:ring-tubi-primary">
                    </div>
                    <p class="text-xs text-tubi-gray mt-2">Mínimo: R$ 50,00. Disponível: R$ {{ number_format($earnings, 2, ',', '.') }}</p>
                </div>
                
                <div class="mb-8">
                    <label class="block text-sm font-medium text-tubi-gray mb-2">Chave Pix</label>
                    <input type="text" name="pix_key" placeholder="CPF, Email, Telefone ou Chave Aleatória" required
                           class="w-full bg-tubi-darker border border-theme rounded-xl py-3 px-4 text-white focus:outline-none focus:border-tubi-primary focus:ring-1 focus:ring-tubi-primary">
                </div>
                
                <div class="flex gap-4">
                    <button type="button" @click="showWithdrawalModal = false" class="flex-1 px-4 py-3 rounded-xl border border-theme text-tubi-gray hover:text-white transition-colors">Cancelar</button>
                    <button type="submit" class="flex-1 px-4 py-3 rounded-xl bg-green-500 hover:bg-green-600 text-white font-bold transition-colors">Confirmar Saque</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Plan Setup -->
    <div x-show="showPlanModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center">
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="showPlanModal = false"></div>
        <div class="glass relative p-8 rounded-3xl max-w-md w-full mx-4 border border-theme shadow-2xl animate-fade-in">
            <h3 class="text-2xl font-bold text-white mb-6">Configurar Clube</h3>
            
            <form action="{{ route('membership.plan.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-tubi-gray mb-2">Nome do Plano</label>
                    <input type="text" name="name" placeholder="Ex: Lumina VIP, Fã Clube Oficial" value="{{ $plan->name ?? '' }}" required
                           class="w-full bg-tubi-darker border border-theme rounded-xl py-3 px-4 text-white focus:outline-none focus:border-tubi-primary focus:ring-1 focus:ring-tubi-primary">
                </div>
                
                <div class="mb-8">
                    <label class="block text-sm font-medium text-tubi-gray mb-2">Valor Mensal (R$)</label>
                    <input type="number" name="price" step="0.01" min="1.99" max="500" value="{{ $plan->price ?? '7.99' }}" required
                           class="w-full bg-tubi-darker border border-theme rounded-xl py-3 px-4 text-white focus:outline-none focus:border-tubi-primary focus:ring-1 focus:ring-tubi-primary">
                </div>
                
                <div class="flex gap-4">
                    <button type="button" @click="showPlanModal = false" class="flex-1 px-4 py-3 rounded-xl border border-theme text-tubi-gray hover:text-white transition-colors">Cancelar</button>
                    <button type="submit" class="flex-1 px-4 py-3 rounded-xl bg-tubi-primary hover:bg-tubi-primary/80 text-white font-bold transition-colors">Salvar Plano</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
