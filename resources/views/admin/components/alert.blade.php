@if(session('success'))
    <div class="alert alert-success" role="alert">
        {{ session('success') }}
    </div>
@endif

@if(session('errors'))
    <div class="alert alert-danger" role="alert">
    @foreach (session('errors') as $error)
                <spam>{{ $error[0] }}</spam>
            @endforeach
    </div>
@endif