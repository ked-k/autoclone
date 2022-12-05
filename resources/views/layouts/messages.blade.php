@if (count($errors) > 0)

    @foreach ($errors->all() as $error)

        <div class="alert  bg-light-danger alert-dismissible fade show">
            <div class="text-danger"><strong>{{ $error }}</strong></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endforeach

@endif

@if (session('success'))

    <div class="alert  bg-light-success alert-dismissible fade show">
        <div class="text-success"><strong>{{ session('success') }}</strong></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
@endif

@if (session('error'))
    <div class="alert  bg-light-warning alert-dismissible fade show">
        <div class="text-warning"><strong>{{ session('error') }}</strong></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
@endif
