@extends('layouts.app')

@section('title', $category->name . ' - Tubiii')

@section('content')
<h1 class="text-3xl font-bold mb-6 dark:text-white">{{ $category->name }}</h1>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    @forelse($videos as $video)
        <div class="rounded-lg overflow-hidden shadow-md hover:shadow-lg transition">
            <a href="{{ route('video.show', $video->slug) }}">
                <div class="bg-gray-300 dark:bg-gray-700 aspect-video flex items-center justify-center">
                    @if($video->thumbnail)
                        <img src="{{ Storage::url($video->thumbnail) }}" alt="{{ $video->title }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-4xl">🎬</span>
                    @endif
                </div>
            </a>
            <div class="p-3">
                <h3 class="font-semibold text-sm line-clamp-2 dark:text-white">{{ $video->title }}</h3>
                <p class="text-xs text-gray-600 dark:text-gray-400">
                    <a href="{{ route('channel.show', $video->user->username) }}">{{ $video->user->name }}</a>
                </p>
                <p class="text-xs text-gray-600 dark:text-gray-400">
                    {{ number_format($video->views_count) }} visualizações
                </p>
            </div>
        </div>
    @empty
        <div class="col-span-full text-center py-12">
            <p class="text-gray-600 dark:text-gray-400">Nenhum vídeo nesta categoria</p>
        </div>
    @endforelse
</div>

<div class="mt-8 flex justify-center">
    {{ $videos->links() }}
</div>
@endsection
