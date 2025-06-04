<div class="container bg-dark rounded-bottom p-2">
@if ($grid['paginator']->hasPages())  
    <nav class="pagination" role="navigation">
    <ul class="pagination m-0">
        @if ($grid['paginator']->onFirstPage())
       
            <li class="page-item" aria-disabled="true" aria-label="@lang('pagination.previous')">
                <a class="page-link" aria-hidden="true">@lang('pagination.previous')</a>
            </li>
            
        @else
            <li class="page-item">
            <a class="page-link" href="{{ $grid['paginator']->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">@lang('pagination.previous')</a>
            </li>
        @endif

 
        @if ($grid['paginator']->hasMorePages())
        
        <li class="page-item">
            <a class="page-link" href="{{ $grid['paginator']->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">@lang('pagination.next')</a>
        </li>
        @else
            <li class="pagination__link pagination__link--disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                <a class="page-link" aria-hidden="true">@lang('pagination.next')</a>
            </li>
        @endif
        <li class="page-item"><span class="page-link text-dark">Pagina {{ $grid['paginator']->currentPage() }} de {{ $grid['paginator']->lastPage() }}</span>    
        </li>
    </ul>
    </nav>
    @endif
</div>
