@extends('layouts.app')
@section('title', 'Lumina Studio — Enviar Vídeo')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8 pb-24 md:pb-8 animate-fade-in" x-data="uploadForm()">

    <!-- Header -->
    <div class="flex items-center gap-4 mb-8">
        <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-tubi-primary to-tubi-secondary flex items-center justify-center shadow-[0_0_20px_rgba(124,58,237,0.4)]">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
        </div>
        <div>
            <h1 class="text-3xl font-display font-bold text-tubi-light tracking-tight">Novo Vídeo</h1>
            <p class="text-sm text-tubi-gray mt-1">Preencha as informações para publicar no seu canal</p>
        </div>
    </div>

    <!-- Upload Progress Bar (visível durante upload XHR) -->
    <div x-show="uploading" style="display:none" class="mb-6 glass rounded-2xl p-5 border border-theme">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-tubi-primary/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-tubi-primary animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-tubi-light">Enviando vídeo...</p>
                    <p class="text-xs text-tubi-gray" x-text="uploadSpeed + ' • ' + uploadEta"></p>
                </div>
            </div>
            <span class="text-2xl font-display font-bold text-tubi-primary" x-text="uploadPercent + '%'"></span>
        </div>
        <div class="w-full bg-tubi-darker rounded-full h-3 overflow-hidden border border-theme">
            <div id="upload-progress-bar"
                 class="h-full rounded-full bg-gradient-to-r from-tubi-primary to-tubi-secondary shadow-[0_0_10px_rgba(124,58,237,0.5)] relative"
                 :style="'width:' + uploadPercent + '%'">
                <div class="absolute inset-0 bg-white/20 animate-pulse rounded-full"></div>
            </div>
        </div>
        <p class="text-xs text-tubi-gray mt-2 text-right" x-text="uploadedMB + ' / ' + totalMB"></p>
    </div>

    <form id="upload-form" @submit.prevent="submitWithProgress()">
        @csrf
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

            {{-- Left Column --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Video Drop Zone --}}
                <div class="glass bg-tubi-card rounded-3xl p-6 border border-theme relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-br from-tubi-primary/5 to-tubi-secondary/5 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
                    <label class="block text-sm font-bold text-tubi-light mb-4 relative z-10">
                        Mídia Base <span class="text-tubi-primary">*</span>
                    </label>

                    <div class="upload-zone rounded-2xl p-8 text-center cursor-pointer transition-all border-2 border-dashed relative z-10"
                        :class="dragging ? 'border-tubi-primary bg-tubi-primary/10 shadow-[0_0_20px_rgba(124,58,237,0.2)]' : selectedFile ? 'border-green-500 bg-green-500/5' : 'border-tubi-gray/30 hover:border-tubi-primary/50'"
                        @dragover.prevent="dragging = true"
                        @dragleave.prevent="dragging = false"
                        @drop.prevent="handleDrop($event)"
                        @click="$refs.videoInput.click()">

                        <input type="file" name="video" x-ref="videoInput"
                            accept="video/mp4,video/avi,video/mov,video/mkv,video/webm"
                            class="hidden" @change="handleFileSelect($event)">

                        <template x-if="!selectedFile">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-all border border-theme" style="background:var(--hover-overlay)">
                                    <svg class="w-8 h-8 text-tubi-gray group-hover:text-tubi-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.069A1 1 0 0121 8.845v6.31a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </div>
                                <p class="text-sm font-bold text-tubi-light mb-1">Selecione ou Arraste</p>
                                <p class="text-xs text-tubi-gray">MP4, WEBM, MKV • Máx: 2GB</p>
                            </div>
                        </template>

                        <template x-if="selectedFile">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 rounded-full bg-green-500/20 flex items-center justify-center mb-4 shadow-[0_0_15px_rgba(34,197,94,0.3)]">
                                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <p class="text-sm font-bold text-green-500 truncate w-full px-4" x-text="selectedFile.name"></p>
                                <p class="text-xs text-tubi-gray mt-1" x-text="formatSize(selectedFile.size)"></p>
                                <button type="button" @click.stop="clearFile()" class="mt-3 px-3 py-1 rounded-full bg-red-500/10 text-xs font-bold text-red-500 hover:bg-red-500/20 transition-colors">
                                    Remover
                                </button>
                            </div>
                        </template>
                    </div>
                    @error('video')
                        <p class="text-red-500 text-xs font-bold mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Thumbnail --}}
                <div class="glass bg-tubi-card rounded-3xl p-6 border border-theme">
                    <label class="block text-sm font-bold text-tubi-light mb-4">
                        Capa (Thumbnail)
                        <span class="text-xs font-normal ml-2 text-gradient">Altamente Recomendado</span>
                    </label>
                    <div class="relative aspect-video rounded-2xl overflow-hidden border-2 border-dashed border-theme cursor-pointer group shadow-inner"
                        style="background:var(--input-bg)"
                        @click="$refs.thumbInput.click()">
                        <input type="file" name="thumbnail" x-ref="thumbInput" accept="image/*" class="hidden" @change="handleThumb($event)">

                        <template x-if="!thumbPreview">
                            <div class="absolute inset-0 flex flex-col items-center justify-center transition-transform group-hover:scale-105">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center mb-3 border border-theme transition-colors" style="background:var(--hover-overlay)">
                                    <svg class="w-6 h-6 text-tubi-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <p class="text-xs font-bold text-tubi-gray">Fazer Upload da Capa</p>
                            </div>
                        </template>
                        <template x-if="thumbPreview">
                            <img :src="thumbPreview" class="w-full h-full object-cover">
                        </template>

                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-sm">
                            <p class="text-white text-sm font-bold bg-black/40 px-4 py-2 rounded-full border border-white/10">Trocar Capa</p>
                        </div>
                    </div>
                    <p class="text-xs text-tubi-gray mt-3">1280x720 (16:9) • Máx: 5MB</p>
                </div>

                {{-- Short Toggle --}}
                <div class="glass bg-tubi-card rounded-3xl p-6 border border-theme hover:border-tubi-secondary/50 transition-colors">
                    <label class="flex items-center justify-between cursor-pointer group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-tubi-secondary/10 flex items-center justify-center group-hover:bg-tubi-secondary/20 transition-colors">
                                <svg class="w-5 h-5 text-tubi-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-tubi-light">Lumina Shorts</p>
                                <p class="text-xs text-tubi-gray mt-0.5">Formato vertical de até 60s</p>
                            </div>
                        </div>
                        <div class="relative">
                            <input type="checkbox" name="is_short" value="1" x-model="isShort" class="sr-only">
                            <div :class="isShort ? 'bg-tubi-secondary shadow-[0_0_15px_rgba(255,107,107,0.4)]' : 'border border-theme'" class="w-12 h-6 rounded-full transition-all relative" style="background-color: var(--input-bg)">
                                <div :class="isShort ? 'translate-x-6 bg-white' : 'bg-tubi-gray'" class="absolute top-[2px] left-[2px] w-5 h-5 rounded-full shadow transition-transform"></div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Right Column --}}
            <div class="lg:col-span-3 space-y-6">

                <div class="glass bg-tubi-card rounded-3xl p-8 border border-theme space-y-6">

                    {{-- Title --}}
                    <div>
                        <label for="title" class="block text-sm font-bold text-tubi-light mb-2">
                            Título Principal <span class="text-tubi-primary">*</span>
                        </label>
                        <input type="text" id="title" name="title" required value="{{ old('title') }}"
                            maxlength="255" x-model="title"
                            placeholder="Use um título chamativo para a rede..."
                            class="w-full px-5 py-3.5 rounded-xl border text-lg font-medium focus:outline-none focus:border-tubi-primary focus:ring-1 focus:ring-tubi-primary transition-all"
                            style="background:var(--input-bg); color:var(--input-text); border-color:var(--input-border);">
                        <div class="flex justify-between mt-2">
                            @error('title')
                                <p class="text-red-500 text-xs font-bold">{{ $message }}</p>
                            @else
                                <span></span>
                            @enderror
                            <p class="text-xs font-bold" :class="title.length > 200 ? 'text-tubi-secondary' : 'text-tubi-gray'" x-text="`${title.length}/255`"></p>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div>
                        <label for="description" class="block text-sm font-bold text-tubi-light mb-2">Descrição / Resumo</label>
                        <textarea id="description" name="description" rows="5"
                            placeholder="Conte mais sobre o conteúdo, adicione links e referências..."
                            class="w-full px-5 py-3.5 rounded-xl border text-sm focus:outline-none focus:border-tubi-primary focus:ring-1 focus:ring-tubi-primary resize-none transition-all"
                            style="background:var(--input-bg); color:var(--input-text); border-color:var(--input-border);">{{ old('description') }}</textarea>
                    </div>

                    {{-- Tags --}}
                    <div>
                        <label for="tags" class="block text-sm font-bold text-tubi-light mb-2">Tags / Tópicos</label>
                        <div class="relative">
                            <span class="absolute left-4 top-3.5 text-tubi-gray font-bold">#</span>
                            <input type="text" id="tags" name="tags" value="{{ old('tags') }}"
                                placeholder="vlog, tutorial, games (separadas por vírgula)"
                                class="w-full pl-8 pr-5 py-3.5 rounded-xl border text-sm focus:outline-none focus:border-tubi-primary focus:ring-1 focus:ring-tubi-primary transition-all"
                                style="background:var(--input-bg); color:var(--input-text); border-color:var(--input-border);">
                        </div>
                        <p class="text-xs text-tubi-gray mt-2">Até 10 tags. Elas ajudam o algoritmo a distribuir seu vídeo!</p>
                    </div>

                    {{-- Category + Visibility --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-theme">
                        <div>
                            <label for="category_id" class="block text-sm font-bold text-tubi-light mb-2">
                                Nicho (Categoria) <span class="text-tubi-primary">*</span>
                            </label>
                            <div class="relative">
                                <select id="category_id" name="category_id" required
                                    class="w-full appearance-none px-5 py-3.5 rounded-xl border text-sm focus:outline-none focus:border-tubi-primary focus:ring-1 focus:ring-tubi-primary transition-all"
                                    style="background:var(--input-bg); color:var(--input-text); border-color:var(--input-border);">
                                    <option value="">Selecione o tema...</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-tubi-gray">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                            @error('category_id')<p class="text-red-500 text-xs font-bold mt-2">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="visibility" class="block text-sm font-bold text-tubi-light mb-2">Visibilidade</label>
                            <div class="relative">
                                <select id="visibility" name="visibility" required
                                    class="w-full appearance-none px-5 py-3.5 rounded-xl border text-sm focus:outline-none focus:border-tubi-primary focus:ring-1 focus:ring-tubi-primary transition-all"
                                    style="background:var(--input-bg); color:var(--input-text); border-color:var(--input-border);">
                                    <option value="public">🌐 Público</option>
                                    <option value="unlisted">🔗 Não listado</option>
                                    <option value="private">🔒 Privado</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-tubi-gray">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Allow Comments --}}
                    <div class="pt-2">
                        <label class="flex items-center gap-4 p-4 rounded-xl border border-theme cursor-pointer hover:border-tubi-primary/50 transition-colors" style="background:var(--hover-overlay)">
                            <input type="checkbox" name="allow_comments" value="1" checked class="w-5 h-5 accent-tubi-primary rounded">
                            <div>
                                <span class="text-sm font-bold text-tubi-light block">Ativar Comentários</span>
                                <span class="text-xs text-tubi-gray">Permitir que usuários engajem com o vídeo</span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="glass bg-tubi-card rounded-3xl p-6 border border-theme shadow-2xl">
                    <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
                        <p class="text-xs text-tubi-gray max-w-xs leading-relaxed">
                            Ao publicar, você concorda com os <a href="#" class="text-tubi-primary hover:underline">Termos do Lumina</a> e afirma possuir os direitos do conteúdo.
                        </p>
                        <div class="flex gap-3 w-full sm:w-auto">
                            <a href="{{ url('/') }}" class="px-6 py-3 rounded-xl border border-theme text-tubi-light text-sm font-bold hover:border-tubi-primary/50 transition-colors w-full sm:w-auto text-center" style="background:var(--hover-overlay)">
                                Cancelar
                            </a>
                            <button type="submit" :disabled="uploading || !selectedFile"
                                class="px-8 py-3 rounded-xl bg-gradient-to-r from-tubi-primary to-tubi-secondary text-white text-sm font-bold shadow-[0_4px_15px_rgba(124,58,237,0.3)] hover:shadow-[0_6px_25px_rgba(124,58,237,0.5)] hover:-translate-y-0.5 transition-all w-full sm:w-auto flex justify-center items-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                                <template x-if="!uploading">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        Publicar Agora
                                    </span>
                                </template>
                                <template x-if="uploading">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                        Enviando...
                                    </span>
                                </template>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@section('scripts')
<script>
function uploadForm() {
    return {
        selectedFile: null,
        thumbPreview: null,
        dragging: false,
        uploading: false,
        isShort: false,
        title: '{{ old("title", "") }}',
        uploadPercent: 0,
        uploadSpeed: '',
        uploadEta: '',
        uploadedMB: '0 MB',
        totalMB: '0 MB',
        _startTime: null,
        _lastLoaded: 0,

        handleDrop(e) {
            this.dragging = false;
            const file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('video/')) {
                this.selectedFile = file;
                // Assign files to input for form submission fallback
                const dt = new DataTransfer();
                dt.items.add(file);
                this.$refs.videoInput.files = dt.files;
            }
        },
        handleFileSelect(e) {
            const file = e.target.files[0];
            if (file) this.selectedFile = file;
        },
        handleThumb(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (ev) => this.thumbPreview = ev.target.result;
                reader.readAsDataURL(file);
            }
        },
        clearFile() {
            this.selectedFile = null;
            this.$refs.videoInput.value = '';
        },
        formatSize(bytes) {
            if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
            if (bytes < 1073741824) return (bytes / 1048576).toFixed(1) + ' MB';
            return (bytes / 1073741824).toFixed(2) + ' GB';
        },
        formatEta(seconds) {
            if (seconds < 60) return `~${Math.round(seconds)}s restantes`;
            return `~${Math.round(seconds / 60)}min restantes`;
        },

        async submitWithProgress() {
            if (!this.selectedFile || this.uploading) return;

            this.uploading = true;
            this._startTime = Date.now();
            this.uploadPercent = 0;

            const formEl = document.getElementById('upload-form');
            const formData = new FormData(formEl);
            // Garantir que o arquivo de vídeo está no FormData
            formData.set('video', this.selectedFile);

            const total = this.selectedFile.size;
            this.totalMB = this.formatSize(total);

            return new Promise((resolve) => {
                const xhr = new XMLHttpRequest();

                xhr.upload.addEventListener('progress', (e) => {
                    if (!e.lengthComputable) return;
                    const pct = Math.round((e.loaded / e.total) * 100);
                    this.uploadPercent = pct;
                    this.uploadedMB = this.formatSize(e.loaded);

                    const elapsed = (Date.now() - this._startTime) / 1000;
                    if (elapsed > 0.5 && e.loaded > 0) {
                        const speed = e.loaded / elapsed;
                        this.uploadSpeed = this.formatSize(speed) + '/s';
                        const remaining = (e.total - e.loaded) / speed;
                        this.uploadEta = this.formatEta(remaining);
                    }
                });

                xhr.addEventListener('load', () => {
                    this.uploadPercent = 100;
                    if (xhr.status >= 200 && xhr.status < 400) {
                        // Redirecionar para a URL retornada ou URL da resposta
                        const finalUrl = xhr.responseURL || '/';
                        window.location.href = finalUrl;
                    } else {
                        // Mostrar erro
                        this.uploading = false;
                        try {
                            const resp = JSON.parse(xhr.responseText);
                            const firstError = resp.errors ? Object.values(resp.errors).flat()[0] : 'Erro no upload';
                            window.showToast(firstError, 'error');
                        } catch(e) {
                            window.showToast('Erro ao enviar vídeo. Verifique o tamanho e formato.', 'error');
                        }
                    }
                    resolve();
                });

                xhr.addEventListener('error', () => {
                    this.uploading = false;
                    window.showToast('Falha na conexão. Tente novamente.', 'error');
                    resolve();
                });

                xhr.open('POST', '{{ route("video.store") }}');
                xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name=csrf-token]').content);
                xhr.setRequestHeader('Accept', 'text/html,application/json');
                xhr.send(formData);
            });
        }
    }
}
</script>
@endsection
@endsection
