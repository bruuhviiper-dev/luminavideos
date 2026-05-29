@extends('layouts.app')
@section('title', 'Analytics — Lumina Studio')

@section('content')
<div class="px-4 py-8 max-w-[1400px] mx-auto animate-fade-in" x-data="{ period: '{{ $period ?? '28' }}' }">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-blue-500/20 flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <div>
                <h1 class="text-3xl font-display font-bold text-white tracking-tight">Estatísticas do Canal</h1>
                <p class="text-tubi-gray text-sm mt-1">Acompanhe seu desempenho em tempo real</p>
            </div>
        </div>
        
        <!-- Period Selector -->
        <div class="glass p-1.5 rounded-xl border border-theme flex">
            <a href="?period=7" :class="period == '7' ? 'bg-tubi-primary text-white shadow-lg' : 'text-tubi-gray hover:text-white'" class="px-4 py-1.5 rounded-lg text-sm font-bold transition-all">7 dias</a>
            <a href="?period=28" :class="period == '28' ? 'bg-tubi-primary text-white shadow-lg' : 'text-tubi-gray hover:text-white'" class="px-4 py-1.5 rounded-lg text-sm font-bold transition-all">28 dias</a>
            <a href="?period=90" :class="period == '90' ? 'bg-tubi-primary text-white shadow-lg' : 'text-tubi-gray hover:text-white'" class="px-4 py-1.5 rounded-lg text-sm font-bold transition-all">90 dias</a>
        </div>
    </div>

    <!-- Top KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Visualizações -->
        <div class="glass p-6 rounded-2xl border border-theme relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:scale-110 transition-transform">
                <svg class="w-24 h-24 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
            </div>
            <h3 class="text-tubi-gray font-bold text-sm mb-2 relative z-10">Visualizações Totais</h3>
            <p class="text-4xl font-display font-bold text-tubi-light relative z-10">{{ number_format($totalviews ?? 0) }}</p>
            <p class="text-xs text-green-400 font-bold mt-2 relative z-10 flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                +12% no período
            </p>
        </div>

        <!-- Tempo de Exibição -->
        <div class="glass p-6 rounded-2xl border border-theme relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:scale-110 transition-transform">
                <svg class="w-24 h-24 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
            </div>
            <h3 class="text-tubi-gray font-bold text-sm mb-2 relative z-10">Tempo de Exibição (horas)</h3>
            <p class="text-4xl font-display font-bold text-tubi-light relative z-10">{{ number_format(($totalwatchTime ?? 0) / 3600, 1) }}</p>
        </div>

        <!-- Inscritos -->
        <div class="glass p-6 rounded-2xl border border-theme relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:scale-110 transition-transform">
                <svg class="w-24 h-24 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/></svg>
            </div>
            <h3 class="text-tubi-gray font-bold text-sm mb-2 relative z-10">Inscritos Ganhos</h3>
            <p class="text-4xl font-display font-bold text-tubi-light relative z-10">+{{ number_format($subscribersGained ?? 0) }}</p>
        </div>

        <!-- Watching Now (Realtime) -->
        <div class="glass p-6 rounded-2xl border border-red-500/30 relative overflow-hidden group bg-gradient-to-br from-transparent to-red-500/10">
            <div class="absolute top-3 right-3 flex items-center gap-2">
                <span class="relative flex h-3 w-3">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                </span>
                <span class="text-xs font-bold text-red-400 uppercase tracking-wider">Ao Vivo</span>
            </div>
            <h3 class="text-tubi-gray font-bold text-sm mb-2 mt-4 relative z-10">Pessoas Assistindo Agora</h3>
            <p class="text-4xl font-display font-bold text-tubi-light relative z-10">{{ number_format($watchingNow ?? 0) }}</p>
            <p class="text-xs text-tubi-gray mt-2">Últimos 5 minutos</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: Graph & Top Videos -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Principal Graph Placeholder -->
            <div class="glass p-6 rounded-3xl border border-theme h-[400px] flex flex-col">
                <h3 class="text-lg font-bold text-white mb-6">Desempenho Geral</h3>
                <div class="flex-1 flex items-center justify-center border border-dashed border-white/10 rounded-2xl">
                    <p class="text-tubi-gray font-bold">Gráfico Interativo de Visualizações (Em Breve com Chart.js)</p>
                </div>
            </div>

            <!-- Top Videos Table -->
            <div class="glass rounded-3xl border border-theme overflow-hidden">
                <div class="p-6 border-b border-theme">
                    <h3 class="text-lg font-bold text-white">Seus Vídeos Mais Populares</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-black/20 text-tubi-gray text-xs uppercase tracking-wider">
                            <tr>
                                <th class="p-4 font-bold">Vídeo</th>
                                <th class="p-4 font-bold text-right">Visualizações</th>
                                <th class="p-4 font-bold text-right">Tempo (h)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-theme">
                            @forelse($topVideos ?? [] as $v)
                            <tr class="hover:bg-white/5 transition-colors group">
                                <td class="p-4 flex items-center gap-3">
                                    <div class="w-16 h-10 bg-tubi-darker rounded border border-theme overflow-hidden flex-shrink-0">
                                        @if($v->thumbnail)
                                            <img src="{{ Storage::url($v->thumbnail) }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <a href="#" class="text-sm font-bold text-tubi-light group-hover:text-tubi-primary transition-colors line-clamp-1">{{ $v->title }}</a>
                                </td>
                                <td class="p-4 text-right font-medium text-tubi-light">{{ number_format($v->views_count) }}</td>
                                <td class="p-4 text-right font-medium text-tubi-gray">{{ number_format(($v->watch_time_sum ?? $v->period_watch_time ?? 0) / 3600, 1) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="p-8 text-center text-tubi-gray">Nenhum dado disponível neste período.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>

        <!-- Right Column: Traffic Sources & Demographics -->
        <div class="space-y-8">
            <!-- Traffic Sources -->
            <div class="glass p-6 rounded-3xl border border-theme">
                <h3 class="text-lg font-bold text-white mb-6">Fontes de Tráfego</h3>
                <div class="space-y-4">
                        @forelse($trafficSources ?? [] as $source)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-tubi-light font-medium">{{ ucfirst($source->source ?? 'Direto') }}</span>
                            <span class="text-tubi-light font-bold">{{ number_format($source->views ?? 0) }}</span>
                        </div>
                        <div class="w-full bg-tubi-darker rounded-full h-2">
                            <div class="bg-tubi-primary h-2 rounded-full" style="width: {{ min((($source->views ?? 0) / max($totalviews ?? 1, 1)) * 100, 100) }}%"></div>
                        </div>
                    </div>
                    @empty
                    <p class="text-tubi-gray text-sm text-center">Dados insuficientes</p>
                    @endforelse
                    
                    <!-- Dummy Data Se não houver db traffic -->
                    @if(empty($trafficSources) || count($trafficSources) == 0)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-tubi-light font-medium">Recomendações da IA Lumina</span>
                            <span class="text-white font-bold">64%</span>
                        </div>
                        <div class="w-full bg-tubi-darker rounded-full h-2">
                            <div class="bg-gradient-to-r from-tubi-primary to-tubi-secondary h-2 rounded-full shadow-[0_0_10px_rgba(124,58,237,0.5)]" style="width: 64%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-tubi-light font-medium">Busca no Lumina</span>
                            <span class="text-white font-bold">20%</span>
                        </div>
                        <div class="w-full bg-tubi-darker rounded-full h-2">
                            <div class="bg-blue-400 h-2 rounded-full" style="width: 20%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-tubi-light font-medium">Externo (Google/Links)</span>
                            <span class="text-white font-bold">16%</span>
                        </div>
                        <div class="w-full bg-tubi-darker rounded-full h-2">
                            <div class="bg-green-400 h-2 rounded-full" style="width: 16%"></div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Quick Tips Box -->
            <div class="bg-gradient-to-br from-tubi-primary/20 to-transparent border border-tubi-primary/30 p-6 rounded-3xl relative overflow-hidden">
                <div class="absolute -right-4 -top-4 opacity-20">
                    <svg class="w-32 h-32 text-tubi-primary" fill="currentColor" viewBox="0 0 20 20"><path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 16v-1h4v1a2 2 0 11-4 0zM12 14c.015-.34.208-.646.477-.859a4 4 0 10-4.954 0c.27.213.462.519.476.859h4z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Dica da IA</h3>
                <p class="text-sm text-tubi-light leading-relaxed">
                    A IA de Recomendação do Lumina notou que seus vídeos com tags curtas (ex: #vlog, #dev) recebem <strong>25% mais retenção</strong>. Tente manter seus títulos limpos e objetivos!
                </p>
            </div>
            
        </div>
    </div>
</div>
@endsection
