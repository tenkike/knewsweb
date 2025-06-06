@extends('admin.index') 

@section('admin')
    @if(\Request::segment(2) == 'grid')
        <div id="content-{{\Request::segment(3)}}" class="table-responsive p-2">
            <table id="{{\Request::segment(3)}}" class="table table-secondary table-striped" style="width:100%"></table>
        </div>
    @endif
@endsection

@section('css_styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.1/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('admin/css/style.grid.css') }}">
@endsection

@section('js_script')
    
    <script src="https://cdn.datatables.net/2.3.1/js/dataTables.js" crossorigin="anonymous"></script>
    <script src="{{ asset('admin/js/GridDataTable.js') }}" crossorigin="anonymous"></script>
@endsection

@section('js_grid')
<script>
// Inicializa la grilla
document.addEventListener('DOMContentLoaded', () => {
   // console.log('Inicializando GridDataTable');
    const grid = new GridDataTable();
    grid.initialize();
});
</script>

@endsection