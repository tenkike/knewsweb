@extends('admin.index')

@section('admin')
@if(\Request::segment(2) == 'dashboard')
    {{$title}}
    <div class="container">
        <div class="row row-cols-2">
            <div class="col">
                <canvas id="myChart" width="150" height="200"></canvas>
            </div>
            <div class="col">
                <canvas id="myChart2" width="150" height="200"></canvas>
            </div>
            <div class="col">
                <canvas id="myChart3" width="150" height="200"></canvas>
            </div>
        </div>
    </div>
  
@endif
@endsection

@section('css_styles')

@endsection

@section('js_script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.js" crossorigin="anonymous"></script>
    <script src="{{asset('admin/js/dashboard.chart.js')}}" crossorigin="anonymous" integrity="{{csrf_token()}}"></script>
@endsection

@section('js_dashboard')
<script>

    document.addEventListener("DOMContentLoaded", function() {
           
        const HandleHover= (evt, item, legend)=> {
            legend.chart.data.datasets[0].backgroundColor.forEach((color, index, colors) => {
                colors[index] = index === item.index || color.length === 9 ? color : color + '4D';
            });
            legend.chart.update();
        }

        const HandleLeave= (evt, item, legend) => {
            legend.chart.data.datasets[0].backgroundColor.forEach((color, index, colors) => {
                colors[index] = color.length === 9 ? color.slice(0, -2) : color;
            });
            legend.chart.update();
        }

        console.log("metricas", {!! $dataMetrics !!});
        new DashboardChart({!! $dataMetrics !!}, HandleLeave, HandleHover);
        
    });

        </script>
@endsection