<nav role="navigation" aria-label="Pagination" class="flex items-center justify-center gap-1 flex-wrap">
    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
        <span class="px-3 py-1.5 rounded-lg dark:bg-dark-700 bg-gray-100 text-gray-400 cursor-not-allowed text-sm">‹</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1.5 rounded-lg dark:bg-dark-700 bg-gray-100 hover:dark:bg-dark-600 hover:bg-gray-200 text-sm transition-colors">‹</a>
    @endif

    {{-- Pages --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="px-3 py-1.5 text-gray-400 text-sm">{{ $element }}</span>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="px-3 py-1.5 rounded-lg bg-brand-600 text-white text-sm font-medium">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="px-3 py-1.5 rounded-lg dark:bg-dark-700 bg-gray-100 hover:dark:bg-dark-600 hover:bg-gray-200 text-sm transition-colors">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1.5 rounded-lg dark:bg-dark-700 bg-gray-100 hover:dark:bg-dark-600 hover:bg-gray-200 text-sm transition-colors">›</a>
    @else
        <span class="px-3 py-1.5 rounded-lg dark:bg-dark-700 bg-gray-100 text-gray-400 cursor-not-allowed text-sm">›</span>
    @endif
</nav>
