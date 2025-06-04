<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{asset('admin/favicon.png')}}" sizes="32x32">
	  <meta name="csrf-token" content="{{ csrf_token() }}">
	  <title>{{ config('app.name').' Admin' }}</title>

    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/css/dashboard.css')}}"  crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/css/style.admin.css')}}" crossorigin="anonymous">
    @yield('css_styles')

      <style>
    

          .hide-row {
            display: none;
          }
        
          .tdRefence.error-row {
            background-color: red !important;
          }

          table .vk_table{
            margin: 0 !important;
          }


        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
          }

          @media (min-width: 768px) {
            .bd-placeholder-img-lg {
              font-size: 3.5rem;
            }
          }

          .b-example-divider {
            width: 100%;
            height: 3rem;
            background-color: rgba(0, 0, 0, .1);
            border: solid rgba(0, 0, 0, .15);
            border-width: 1px 0;
            box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
          }

          .b-example-vr {
            flex-shrink: 0;
            width: 1.5rem;
            height: 100vh;
          }

          .bi {
            vertical-align: -.125em;
            fill: currentColor;
          }

          .nav-scroller {
            position: relative;
            z-index: 2;
            height: 2.75rem;
            overflow-y: hidden;
          }

          .nav-scroller .nav {
            display: flex;
            flex-wrap: nowrap;
            padding-bottom: 1rem;
            margin-top: -1px;
            overflow-x: auto;
            text-align: center;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
          }

          .btn-bd-primary {
            --bd-violet-bg: #712cf9;
            --bd-violet-rgb: 112.520718, 44.062154, 249.437846;

            --bs-btn-font-weight: 600;
            --bs-btn-color: var(--bs-white);
            --bs-btn-bg: var(--bd-violet-bg);
            --bs-btn-border-color: var(--bd-violet-bg);
            --bs-btn-hover-color: var(--bs-white);
            --bs-btn-hover-bg: #6528e0;
            --bs-btn-hover-border-color: #6528e0;
            --bs-btn-focus-shadow-rgb: var(--bd-violet-rgb);
            --bs-btn-active-color: var(--bs-btn-hover-color);
            --bs-btn-active-bg: #5a23c8;
            --bs-btn-active-border-color: #5a23c8;
          }
          .bd-mode-toggle {
            z-index: 1500;
          }

          /***dark***/
          .light {
            color: #333 !important;
          }

          .dark {
            color: #fff !important;
          }

          .light:hover {
            color: #957f06 !important;
          }

          .dark:hover {
            color: #f9db84 !important;
          }

          .dropdown-item.logout:hover{
            color: red !important;
          }

          .sidebar .nav-link.active.light,
          .dropdown-item.light.active{
            color: #376c04 !important;
          }

          .sidebar .nav-link.active.dark,
          .dropdown-item.dark.active{
            color: #6db12d !important;
          }

          .sidebar .nav-link.active.light:hover,
          .sidebar .nav-link.active.dark:hover,
          .dropdown-item.light.active:hover, 
          .dropdown-item.dark.active:hover{
            color: #9cf24a !important;
          }

          .sidebar .nav-link.active {
            color: #6db12d !important;
          }

          .sidebar .nav-link:hover .feather,
          .sidebar .nav-link.active.light .feather {
            color: inherit;
          }

          .sidebar .nav-link:hover .feather,
          .sidebar .nav-link.active.dark .feather {
            color: inherit;
          }
           /*** end dark ***/

          /***dropbox */
        
          #{{\Request::segment(3)}}{
              padding-top:25px;
          }


         .bgOption {
            background-color: #868585 !important; /* Color de fondo */
            color: red
          }

          /*
          .btn.btn-create{
            background-color: #868585
          }

          .btn.btn-update{
            background-color: #258801;
            color: var(--bs-primary-bg-subtle) !important;
          }

          .btn.btn-back{
            background-color: #868585;
            color: var(--bs-primary-bg-subtle) !important;
          }*/
      </style>
    </head>
  <body>
    
    
      @include('admin.components.drodownbtn')
      @include('admin.components.header')

      <div class="container-fluid">
          <div class="row">
          @include('admin.components.sidebar')
          <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            @include('admin.components.bartitle', [@$nameGrid, @$nameForm])
          <!--content-->
            @yield('admin')
          <!--endcontent-->
          </main>
        </div>
      </div>
      <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
      <script src="{{asset('js/Ajax.js')}}" crossorigin="anonymous"></script>
      <script src="{{ mix('js/app.js') }}" crossorigin="anonymous"></script>

      <script src="{{asset('admin/js/colorsmodes.js')}}" crossorigin="anonymous"></script>
      @yield('js_script')

      @yield('js_form')
      @yield('js_grid')
      @yield('js_dashboard')
    
  </body>
</html>

