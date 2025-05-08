@if ($paginator->hasPages())
    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between">
        {{-- Page Info --}}
        <div class="text-muted mb-3 mb-md-0">
            Showing {{ $paginator->firstItem() ?? 0 }} to {{ $paginator->lastItem() ?? 0 }} of {{ $paginator->total() }} 
            {{ Str::plural('entry', $paginator->total()) }}
            @if(request('search'))
                for "<strong>{{ request('search') }}</strong>"
            @endif
        </div>

        <div class="d-flex align-items-center">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="d-flex align-items-center justify-content-center pe-3 text-muted">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-muted opacity-50">
                        <path d="M15 6L9 12L15 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="d-flex align-items-center justify-content-center pe-3 text-primary text-decoration-none">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15 6L9 12L15 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            @endif

            {{-- Pagination Elements --}}
            <div class="d-flex border border-1">
                @foreach ($elements as $element)
                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="d-flex align-items-center justify-content-center px-3 py-1 bg-primary text-dark">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="d-flex align-items-center justify-content-center px-3 py-1 text-primary text-decoration-none">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="d-flex align-items-center justify-content-center ps-3 text-primary text-decoration-none">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 6L15 12L9 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            @else
                <span class="d-flex align-items-center justify-content-center ps-3 text-muted opacity-50">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 6L15 12L9 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
            @endif
        </div>
    </div>
@endif 