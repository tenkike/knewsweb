<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    
   
   @php

    switch(Request::segment(2)){
        case 'dashboard':
        echo '<h3 class="text-capitalize">' . Request::segment(2) . '</h3>';
        break;
        case 'grid':
        $tableName = str_replace("vk_", " ", Request::segment(3)); 
        echo '<h3 class="text-capitalize">'. $nameGrid.' '.$tableName . '</h3>';
        break;
        case 'form':
        $tableName = str_replace("vk_", " ", Request::segment(3)); 
        echo '<h3 class="text-capitalize">' . $nameForm .' '. $tableName .' - '. Request::segment(4) . '</h3>';
        break;
    }
   @endphp
    
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary">Share</button>
            <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
        </div>
        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar align-text-bottom" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
             This week
        </button>
    </div>
</div>