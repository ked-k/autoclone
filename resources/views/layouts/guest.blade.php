<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="minimal-theme">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('autolab-assets/images/favicon-32x32.png') }}" type="image/png" />

    <!-- Bootstrap CSS -->
    <link href="{{ asset('autolab-assets/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('autolab-assets/css/bootstrap-extended.css') }}" rel="stylesheet" />
    <link href="{{ asset('autolab-assets/css/style.css') }}" rel="stylesheet" />
    <link href="{{ asset('autolab-assets/css/icons.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

    <!-- loader-->
    <link href="{{ asset('autolab-assets/css/pace.min.css') }}" rel="stylesheet" />
</head>

<body>

    <!--start wrapper-->
    <div class="wrapper">

        <!--start content-->
        <main class="authentication-content">
            <div class="container-fluid mt-1 mb-5">
                <div class="authentication-card">
                    <div class="card shadow rounded-0 overflow-hidden">
                      {{$slot}}
                    </div>
                </div>
            </div>
        </main>

        <!--end page main-->
        <footer class="bg-white border-top p-3 text-center fixed-bottom">
            <p class="mb-0">Makerere University Biomedical Research Centre © {{date('Y')}}. All right reserved.</p>
        </footer>

    </div>
    <!--end wrapper-->


    <!--plugins-->
    <script src="{{ asset('autolab-assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('autolab-assets/js/pace.min.js') }}"></script>

     <!-- Bootstrap bundle JS -->
  <script src="{{ asset('autolab-assets/js/bootstrap.bundle.min.js') }}"></script>
  <!--plugins-->

</body>

</html>
