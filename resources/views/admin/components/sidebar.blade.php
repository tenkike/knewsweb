<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-body-tertiary sidebar collapse text-capitalize">
      <div class="position-sticky pt-3 sidebar-sticky">
        <ul class="nav flex-column">
          <li class="nav-item">
            <a class="nav-link" aria-current="page" href="{{url('admin/dashboard')}}">
            <i class="fa-solid fa-gauge" style="padding-right: 5px;"></i>
              Dashboard
            </a>
          </li>
          @if(isset($routes))
          @if(is_array($routes))
          @php 
          $prefix = 'vk_';
          $filteredTables = array_filter($routes, function ($table) use ($prefix) {
              return strpos($table, $prefix) === 0;
          });
          @endphp
          @foreach($filteredTables as $key => $route)
          @php $tableName = str_replace("vk_", "", $route); // Eliminamos el prefijo "vk_" @endphp
          <li class="nav-item">
            <a class="nav-link" href="{{url('admin/grid/'.$route)}}">
              @php  $getIcons= (\Config::get('appweb.admin.icons.'.$key) !== null )? \Config::get('appweb.admin.icons.'.$key): '';
              //dd($getIcons);
              @endphp
                <i class="fa-solid fa-{{ $getIcons }}" style="padding-right: 5px;"></i> {{$tableName}}</a>
          </li>
          @endforeach
          @endif
          @endif
        </ul>
      </div>
    </nav>