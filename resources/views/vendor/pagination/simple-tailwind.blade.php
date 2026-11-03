@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between py-4 text-xs">
        
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center gap-1.5 px-4 py-2 border border-neutral-200/80 bg-neutral-50/70 text-neutral-300 font-medium rounded-lg cursor-not-allowed select-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                <span>{!! __('pagination.previous') !!}</span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" 
               class="inline-flex items-center gap-1.5 px-4 py-2 border border-neutral-200 bg-white hover:bg-neutral-50 hover:border-neutral-300 text-neutral-700 hover:text-neutral-950 font-medium rounded-lg transition-all shadow-2xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                <span>{!! __('pagination.previous') !!}</span>
            </a>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" 
               class="inline-flex items-center gap-1.5 px-4 py-2 border border-neutral-200 bg-white hover:bg-neutral-50 hover:border-neutral-300 text-neutral-700 hover:text-neutral-950 font-medium rounded-lg transition-all shadow-2xs">
                <span>{!! __('pagination.next') !!}</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        @else
            <span class="inline-flex items-center gap-1.5 px-4 py-2 border border-neutral-200/80 bg-neutral-50/70 text-neutral-300 font-medium rounded-lg cursor-not-allowed select-none">
                <span>{!! __('pagination.next') !!}</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </span>
        @endif

    </nav>
@endif
