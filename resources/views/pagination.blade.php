@if ($paginator->hasPages())
    <ul class="flex text-gray-600 font-semibold" role="navigation">

        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="mr-1" aria-disabled="true" aria-label="@lang('pagination.previous')">
                <span class="block px-3 py-2 border border-transparent bg-gray-300 rounded text-gray-500" aria-hidden="true">Prev</span>
            </li>
        @else
            <li class="mr-1">
                <a class="block px-3 py-2 border border-gray-400 rounded hover:bg-white" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">Prev</a>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)

            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="px-2 flex items-center mr-1" aria-disabled="true">
                    <span class="block text-gray-500">{{ $element }}</span>
                </li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="mr-1" aria-current="page">
                            <span class="block px-3 py-2 border border-gray-400 rounded bg-white hover:bg-white">{{ $page }}</span>
                        </li>
                    @else
                        <li class="mr-1">
                            <a class="block px-3 py-2 border border-gray-400 rounded hover:bg-white" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="mr-1">
                <a class="block px-3 py-2 border border-gray-400 rounded hover:bg-white" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">Next</a>
            </li>
        @else
            <li class="mr-1" aria-disabled="true" aria-label="@lang('pagination.next')">
                <span class="block px-3 py-2 border border-transparent bg-gray-300 rounded text-gray-500" aria-hidden="true">Next</span>
            </li>
        @endif
    </ul>
@endif
