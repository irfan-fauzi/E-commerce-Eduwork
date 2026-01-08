@if ($paginator->hasPages())
<nav class="flex items-center justify-center gap-1 text-sm">
    {{-- Previous --}}
    @if ($paginator->onFirstPage())
        <span class="px-3 py-2 text-gray-400 border border-gray-200 rounded-md cursor-not-allowed">
            <i class="bi bi-chevron-left"></i>
        </span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}"
           class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-100 transition">
            <i class="bi bi-chevron-left"></i>
        </a>
    @endif

    {{-- Pages --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="px-3 py-2 text-gray-400">…</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="px-3 py-2 bg-red-600 text-white rounded-md font-medium">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $url }}"
                       class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-100 transition">
                        {{ $page }}
                    </a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}"
           class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-100 transition">
            <i class="bi bi-chevron-right"></i>
        </a>
    @else
        <span class="px-3 py-2 text-gray-400 border border-gray-200 rounded-md cursor-not-allowed">
            <i class="bi bi-chevron-right"></i>
        </span>
    @endif
</nav>
@endif
