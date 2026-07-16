@if ($paginator->hasPages())
<div class="paginacion-tabla">
    <span class="paginacion-info">
        Mostrando {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} de {{ $paginator->total() }} registros
    </span>

    <div class="paginacion-controles">
        {{-- Anterior --}}
        @if ($paginator->onFirstPage())
            <span class="paginacion-btn disabled">
                <i class="bi bi-chevron-left"></i>
            </span>
        @else
            <a class="paginacion-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                <i class="bi bi-chevron-left"></i>
            </a>
        @endif

        {{-- Páginas --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="paginacion-btn disabled">…</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="paginacion-btn activa">{{ $page }}</span>
                    @else
                        <a class="paginacion-btn" href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Siguiente --}}
        @if ($paginator->hasMorePages())
            <a class="paginacion-btn" href="{{ $paginator->nextPageUrl() }}" rel="next">
                <i class="bi bi-chevron-right"></i>
            </a>
        @else
            <span class="paginacion-btn disabled">
                <i class="bi bi-chevron-right"></i>
            </span>
        @endif
    </div>
</div>
@endif