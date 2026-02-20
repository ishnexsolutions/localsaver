@if ($paginator->hasPages())
    <nav role="navigation" class="flex items-center justify-between gap-2 mt-6">
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="px-4 py-2 rounded-lg bg-white/5 text-white/40">Previous</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-4 py-2 rounded-lg bg-white/10 hover:bg-white/20">Previous</a>
            @endif
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-4 py-2 rounded-lg bg-white/10 hover:bg-white/20">Next</a>
            @else
                <span class="px-4 py-2 rounded-lg bg-white/5 text-white/40">Next</span>
            @endif
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-white/60">
                    Showing <span class="font-medium">{{ $paginator->firstItem() }}</span> to <span class="font-medium">{{ $paginator->lastItem() }}</span> of <span class="font-medium">{{ $paginator->total() }}</span>
                </p>
            </div>
            <div class="flex gap-2">
                @if ($paginator->onFirstPage())
                    <span class="px-3 py-1 rounded-lg bg-white/5 text-white/40">←</span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1 rounded-lg bg-white/10 hover:bg-white/20">←</a>
                @endif
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1 rounded-lg bg-white/10 hover:bg-white/20">→</a>
                @else
                    <span class="px-3 py-1 rounded-lg bg-white/5 text-white/40">→</span>
                @endif
            </div>
        </div>
    </nav>
@endif
