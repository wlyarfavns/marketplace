@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col sm:flex-row items-center justify-between gap-4 py-4 text-xs">
        
        {{-- Results Counter Text --}}
        <div>
            <p class="text-xs text-neutral-500">
                Menampilkan
                @if ($paginator->firstItem())
                    <span class="font-semibold text-neutral-800">{{ $paginator->firstItem() }}</span>
                    sampai
                    <span class="font-semibold text-neutral-800">{{ $paginator->lastItem() }}</span>
                @else
                    <span class="font-semibold text-neutral-800">{{ $paginator->count() }}</span>
                @endif
                dari
                <span class="font-semibold text-neutral-800">{{ $paginator->total() }}</span>
                data
            </p>
        </div>

        {{-- Page Navigation Controls --}}
        <div class="flex items-center gap-1.5">

            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}" 
                      class="w-9 h-9 rounded-lg border border-neutral-200/80 bg-neutral-50/70 text-neutral-300 flex items-center justify-center cursor-not-allowed select-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}" 
                   class="w-9 h-9 rounded-lg border border-neutral-200 bg-white hover:bg-neutral-50 hover:border-neutral-300 text-neutral-700 hover:text-neutral-950 flex items-center justify-center transition-all shadow-2xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span aria-disabled="true" class="w-8 h-9 flex items-center justify-center text-neutral-400 font-semibold select-none">
                        {{ $element }}
                    </span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" 
                                  class="min-w-9 h-9 px-3 rounded-lg border border-neutral-300 bg-neutral-100 text-neutral-900 font-bold flex items-center justify-center shadow-2xs select-none">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" aria-label="{{ __('Go to page :page', ['page' => $page]) }}" 
                               class="min-w-9 h-9 px-3 rounded-lg border border-neutral-200 bg-white hover:bg-neutral-50 hover:border-neutral-300 text-neutral-600 hover:text-neutral-950 font-medium flex items-center justify-center transition-all shadow-2xs">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}" 
                   class="w-9 h-9 rounded-lg border border-neutral-200 bg-white hover:bg-neutral-50 hover:border-neutral-300 text-neutral-700 hover:text-neutral-950 flex items-center justify-center transition-all shadow-2xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            @else
                <span aria-disabled="true" aria-label="{{ __('pagination.next') }}" 
                      class="w-9 h-9 rounded-lg border border-neutral-200/80 bg-neutral-50/70 text-neutral-300 flex items-center justify-center cursor-not-allowed select-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            @endif

        </div>

    </nav>
@endif
