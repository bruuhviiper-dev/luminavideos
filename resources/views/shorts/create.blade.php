@extends('layouts.app')
@section('title', 'Criar Short — Lumina')
@section('content')
<div class="max-w-2xl mx-auto px-4 py-8 pb-24 animate-fade-in" x-data="shortForm()">
    <div class="flex items-center gap-4 mb-8">
        <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-tubi-secondary to-pink-400 flex items-center justify-center shadow-[0_0_20px_rgba(255,107,107,0.3)]">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
        </div>
        <div>
            <h1 class="text-3xl font-display font-bold text-tubi-light">Novo Lumina Short</h1>
            <p class="text-sm text-tubi-gray mt-1">Vídeo vertical de até 60 segundos</p>
        </div>
    </div>

    <form action="{{ route('video.store') }}" method="POST" enctype="multipart/form-data" @submit="submitting = true">
        @csrf
        <input type="hidden" name="is_short" value="1">
        <input type="hidden" name="visibility" value="public">

        <div class="space-y-6">
            <div class="glass bg-tubi-card rounded-3xl p-6 border border-theme">
                <label class="block text-sm font-bold text-tubi-light mb-4">Vídeo Vertical <span class="text-tubi-secondary">*</span></label>
                <div class="border-2 border-dashed border-tubi-gray/30 rounded-2xl p-8 text-center cursor-pointer transition-all"
                    :class="selectedFile ? 'border-tubi-secondary bg-tubi-secondary/10' : ''"
                    @click="$refs.videoInput.click()"
                    @dragover.prevent @drop.prevent="handleDrop($event)">
                    <input type="file" name="video" x-ref="videoInput" accept="video/mp4,video/webm" required class="hidden" @change="handleFileSelect($event)">
                    <template x-if="!selectedFile">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-24 rounded-xl bg-tubi-darker border-2 border-dashed border-theme flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-tubi-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </div>
                            <p class="font-bold text-tubi-light">Selecione um vídeo vertical</p>
                            <p class="text-xs text-tubi-gray mt-1">MP4, WEBM • Até 60s • Máx 500MB</p>
                        </div>
                    </template>
                    <template x-if="selectedFile">
                        <div class="flex flex-col items-center">
                            <div class="w-14 h-14 rounded-full bg-tubi-secondary/20 flex items-center justify-center mb-3">
                                <svg class="w-7 h-7 text-tubi-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <p class="font-bold text-tubi-secondary truncate max-w-xs" x-text="selectedFile.name"></p>
                            <p class="text-xs text-tubi-gray mt-1" x-text="formatSize(selectedFile.size)"></p>
                        </div>
                    </template>
                </div>
            </div>

            <div class="glass bg-tubi-card rounded-3xl p-6 border border-theme space-y-4">
                <div>
                    <label class="block text-sm font-bold text-tubi-light mb-2">Título <span class="text-tubi-secondary">*</span></label>
                    <input type="text" name="title" required x-model="title" maxlength="100" placeholder="Título curto e chamativo..."
                        class="w-full px-4 py-3 rounded-xl bg-tubi-darker border border-theme text-tubi-light text-sm focus:outline-none focus:border-tubi-secondary transition-all">
                    <p class="text-xs text-right mt-1" :class="title.length > 80 ? 'text-tubi-secondary' : 'text-tubi-gray'" x-text="`${title.length}/100`"></p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-tubi-light mb-2">Categoria <span class="text-tubi-secondary">*</span></label>
                    <select name="category_id" required class="w-full px-4 py-3 rounded-xl bg-tubi-darker border border-theme text-tubi-light text-sm focus:outline-none focus:border-tubi-secondary">
                        <option value="">Selecione...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <button type="submit" :disabled="submitting || !selectedFile"
                class="w-full py-4 rounded-2xl bg-gradient-to-r from-tubi-secondary to-pink-400 text-white font-bold text-lg shadow-[0_4px_20px_rgba(255,107,107,0.4)] hover:shadow-[0_6px_30px_rgba(255,107,107,0.6)] hover:-translate-y-0.5 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                <span x-show="!submitting">🚀 Publicar Short</span>
                <span x-show="submitting" style="display:none">Enviando...</span>
            </button>
        </div>
    </form>
</div>
@section('scripts')
<script>
function shortForm() {
    return {
        selectedFile: null, title: '', submitting: false,
        handleDrop(e) { const f = e.dataTransfer.files[0]; if(f && f.type.startsWith('video/')) { this.selectedFile = f; this.$refs.videoInput.files = e.dataTransfer.files; } },
        handleFileSelect(e) { const f = e.target.files[0]; if(f) this.selectedFile = f; },
        formatSize(b) { if(b < 1048576) return (b/1024).toFixed(1)+' KB'; return (b/1048576).toFixed(1)+' MB'; }
    }
}
</script>
@endsection
@endsection
