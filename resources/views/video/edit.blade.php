@extends('layouts.app')
@section('title', 'Editar Vídeo — Lumina Studio')
@section('content')
<div class="max-w-5xl mx-auto px-4 py-8 pb-24 md:pb-8 animate-fade-in" x-data="editForm()">
    <div class="flex items-center gap-4 mb-8">
        <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-tubi-primary to-tubi-secondary flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        </div>
        <div>
            <h1 class="text-3xl font-display font-bold text-tubi-light tracking-tight">Editar Vídeo</h1>
            <p class="text-sm text-tubi-gray mt-1">Atualize as informações do seu conteúdo</p>
        </div>
    </div>

    @if(session('status'))
        <div class="mb-6 p-4 rounded-xl bg-green-500/10 border border-green-500/30 text-green-500 text-sm font-bold flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('status') }}
        </div>
    @endif

    <form action="{{ route('video.update', $video->id) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

            {{-- Left Column --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Current Thumbnail --}}
                <div class="glass bg-tubi-card rounded-3xl p-6 border border-theme">
                    <label class="block text-sm font-bold text-tubi-light mb-4">Capa Atual</label>
                    <div class="relative aspect-video rounded-2xl overflow-hidden bg-tubi-darker border-2 border-dashed border-theme cursor-pointer group"
                        @click="$refs.thumbInput.click()">
                        <input type="file" name="thumbnail" x-ref="thumbInput" accept="image/*" class="hidden" @change="handleThumb($event)">
                        <template x-if="thumbPreview">
                            <img :src="thumbPreview" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!thumbPreview">
                            <div>
                                @if($video->thumbnail)
                                    @php $tUrl = Str::startsWith($video->thumbnail, 'http') ? $video->thumbnail : Storage::url($video->thumbnail); @endphp
                                    <img src="{{ $tUrl }}" class="w-full h-full object-cover">
                                @else
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <svg class="w-12 h-12 text-tubi-gray/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                            </div>
                        </template>
                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-sm">
                            <p class="text-white text-sm font-bold bg-black/40 px-4 py-2 rounded-full">Trocar Capa</p>
                        </div>
                    </div>
                    <p class="text-xs text-tubi-gray mt-2">1280x720 (16:9) • Máx: 5MB</p>
                </div>

                {{-- Visibility --}}
                <div class="glass bg-tubi-card rounded-3xl p-6 border border-theme">
                    <label class="block text-sm font-bold text-tubi-light mb-4">Visibilidade</label>
                    <select name="visibility" class="w-full px-4 py-3 rounded-xl bg-tubi-darker border border-theme text-tubi-light text-sm focus:outline-none focus:border-tubi-primary">
                        <option value="public" {{ $video->visibility === 'public' ? 'selected' : '' }}>🌐 Público</option>
                        <option value="unlisted" {{ $video->visibility === 'unlisted' ? 'selected' : '' }}>🔗 Não listado</option>
                        <option value="private" {{ $video->visibility === 'private' ? 'selected' : '' }}>🔒 Privado</option>
                    </select>
                </div>

                {{-- Danger Zone --}}
                <div class="glass bg-tubi-card rounded-3xl p-6 border border-red-500/30">
                    <h3 class="text-sm font-bold text-red-500 mb-3">Zona de Perigo</h3>
                    <form action="{{ route('video.destroy', $video->id) }}" method="POST" onsubmit="return confirm('Tem certeza? Esta ação não pode ser desfeita.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-red-500/10 border border-red-500/30 text-red-500 text-sm font-bold hover:bg-red-500/20 transition-colors">
                            Deletar Vídeo
                        </button>
                    </form>
                </div>
            </div>

            {{-- Right Column --}}
            <div class="lg:col-span-3 space-y-6">
                <div class="glass bg-tubi-card rounded-3xl p-8 border border-theme space-y-6">

                    <div>
                        <label for="title" class="block text-sm font-bold text-tubi-light mb-2">Título <span class="text-tubi-primary">*</span></label>
                        <input type="text" id="title" name="title" required x-model="title" value="{{ old('title', $video->title) }}" maxlength="255"
                            class="w-full px-5 py-3.5 rounded-xl bg-tubi-darker border border-theme text-tubi-light text-lg font-medium focus:outline-none focus:border-tubi-primary focus:ring-1 focus:ring-tubi-primary transition-all">
                        <p class="text-xs font-bold text-right mt-1" :class="title.length > 200 ? 'text-tubi-secondary' : 'text-tubi-gray'" x-text="`${title.length}/255`"></p>
                        @error('title')<p class="text-red-400 text-xs font-bold mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-bold text-tubi-light mb-2">Descrição</label>
                        <textarea id="description" name="description" rows="5"
                            class="w-full px-5 py-3.5 rounded-xl bg-tubi-darker border border-theme text-tubi-light text-sm focus:outline-none focus:border-tubi-primary focus:ring-1 focus:ring-tubi-primary resize-none transition-all">{{ old('description', $video->description) }}</textarea>
                    </div>

                    <div>
                        <label for="tags" class="block text-sm font-bold text-tubi-light mb-2">Tags</label>
                        <div class="relative">
                            <span class="absolute left-4 top-3.5 text-tubi-gray font-bold">#</span>
                            <input type="text" id="tags" name="tags"
                                value="{{ old('tags', is_array($video->tags) ? implode(', ', $video->tags) : $video->tags) }}"
                                placeholder="vlog, tutorial, games"
                                class="w-full pl-8 pr-5 py-3.5 rounded-xl bg-tubi-darker border border-theme text-tubi-light text-sm focus:outline-none focus:border-tubi-primary focus:ring-1 focus:ring-tubi-primary transition-all">
                        </div>
                        <p class="text-xs text-tubi-gray mt-1">Separadas por vírgula • até 10 tags</p>
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-bold text-tubi-light mb-2">Categoria <span class="text-tubi-primary">*</span></label>
                        <select id="category_id" name="category_id" required
                            class="w-full px-5 py-3.5 rounded-xl bg-tubi-darker border border-theme text-tubi-light text-sm focus:outline-none focus:border-tubi-primary">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ ($video->category_id == $category->id) ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pt-4 border-t border-theme">
                        <label class="flex items-center gap-4 p-4 rounded-xl border border-theme bg-tubi-darker cursor-pointer hover:border-tubi-primary/50 transition-colors">
                            <input type="checkbox" name="allow_comments" value="1" {{ $video->allow_comments ? 'checked' : '' }}
                                class="w-5 h-5 accent-tubi-primary rounded">
                            <div>
                                <span class="text-sm font-bold text-tubi-light block">Permitir Comentários</span>
                                <span class="text-xs text-tubi-gray">Permitir que usuários engajem com o vídeo</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="glass bg-tubi-card rounded-3xl p-6 border border-theme">
                    <div class="flex gap-3 justify-end">
                        <a href="{{ route('video.show', ['v' => $video->youtube_id ?? $video->slug]) }}" class="px-6 py-3 rounded-xl bg-tubi-darker border border-theme text-tubi-light text-sm font-bold hover:border-tubi-primary/50 transition-colors">
                            Ver Vídeo
                        </a>
                        <button type="submit" class="px-8 py-3 rounded-xl bg-gradient-to-r from-tubi-primary to-tubi-secondary text-white text-sm font-bold shadow-[0_4px_15px_rgba(124,58,237,0.3)] hover:shadow-[0_6px_25px_rgba(124,58,237,0.5)] hover:-translate-y-0.5 transition-all">
                            Salvar Alterações
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@section('scripts')
<script>
function editForm() {
    return {
        title: '{{ old("title", addslashes($video->title)) }}',
        thumbPreview: null,
        handleThumb(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (ev) => this.thumbPreview = ev.target.result;
                reader.readAsDataURL(file);
            }
        }
    }
}
</script>
@endsection
@endsection
