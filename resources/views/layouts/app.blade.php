<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{auth()->user()->color_scheme??'minimal-theme'}}">
 

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <title>{{ config('app.name', 'AutoLab') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('autolab-assets/images/favicon-32x32.png') }}" type="image/png" />

    <!--plugins-->
    <link href="{{ asset('autolab-assets/plugins/simplebar/css/simplebar.css') }}" rel="stylesheet" />
    <link href="{{ asset('autolab-assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="{{ asset('autolab-assets/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('autolab-assets/css/bootstrap-extended.css') }}" rel="stylesheet" />
    <link href="{{ asset('autolab-assets/css/style.css') }}" rel="stylesheet" />
    <link href="{{ asset('autolab-assets/css/icons.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" /> --}}
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" /> --}}
    <link href="{{ asset('js/izitoast/css/iziToast.min.css') }}" rel="stylesheet" type="text/css">

    <!-- Datatables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.bootstrap5.min.css" />

    <!-- loader-->
    <link href="{{ asset('autolab-assets/css/pace.min.css') }}" rel="stylesheet" />

    <!--Theme Styles-->
    <link href="{{ asset('autolab-assets/css/dark-theme.css') }}" rel="stylesheet" />
    <link href="{{ asset('autolab-assets/css/light-theme.css') }}" rel="stylesheet" />
    <link href="{{ asset('autolab-assets/css/semi-dark.css') }}" rel="stylesheet" />
    <link href="{{ asset('autolab-assets/css/header-colors.css') }}" rel="stylesheet" />
    @livewireStyles

</head>

<body>

    <!--start wrapper-->
    <div class="wrapper">
        <livewire:layout.header-component/>
        <livewire:layout.navigation-component/>
        {{-- @include('layouts.header')
        @include('layouts.navigation') --}}
        <!--start content-->
        <main class="page-content">
            {{-- @include('layouts.messages') --}}
            {{ $slot }}
        </main>
        <!--end page main-->

        <!--start overlay-->
        <div class="overlay nav-toggle-icon"></div>
        <!--end overlay-->

        <!--Start Back To Top Button-->
        <a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
        <!--End Back To Top Button-->
        {{-- @include('layouts.theme-customization') --}}
    </div>
    <!--end wrapper-->

    <!-- Bootstrap bundle JS -->
    <script src="{{ asset('autolab-assets/js/bootstrap.bundle.min.js') }}"></script>
    <!--plugins-->
    <script src="{{ asset('autolab-assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('autolab-assets/plugins/simplebar/js/simplebar.min.js') }}"></script>
    <script src="{{ asset('autolab-assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('autolab-assets/js/pace.min.js') }}"></script>

    <!--app-->
    <script src="{{ asset('autolab-assets/js/app.js') }}"></script>
    <script src="{{ asset('autolab-assets/js/sort.js') }}"></script>
    {{-- <script src="{{ asset('autolab-assets/js/index.js') }}"></script> --}}

    <!-- Datatables JS -->
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.bootstrap5.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>


    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.colVis.min.js"></script> 

    <script src="{{ asset('js/izitoast/js/iziToast.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert/sweetalert.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $("#datableButtons").DataTable({
                "responsive": false,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#datableButtons_wrapper .col-md-6:eq(0)');

            $('#example1').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });

            // document.body.style.zoom = "90%" 
        });
    </script>


    <script>
        window.addEventListener('alert', event => {

            if (event.detail.type == 'success') {
                iziToast.success({
                    title: 'Success!',
                    message: `${event.detail.message}`,
                    timeout: 5000,
                    position: 'topRight'
                });
            }

            if (event.detail.type == 'Error') {
                iziToast.error({
                    title: 'Error!',
                    message: `${event.detail.message}`,
                    timeout: 5000,
                    position: 'topRight'
                });
            }

            if (event.detail.type == 'warning') {
                iziToast.warning({
                    title: 'Warning!',
                    message: `${event.detail.message}`,
                    timeout: 5000,
                    position: 'topRight'
                });
            }
        });

        window.addEventListener('maximum-reached', event => {
            if (event.detail.type == 'warning') {
                swal('Warning', `${event.detail.message}`, 'warning');
            }
        });

        window.addEventListener('cant-delete', event => {
            if (event.detail.type == 'warning') {
                swal('Warning', `${event.detail.message}`, 'warning');
            }
        });

        window.addEventListener('mismatch', event => {
            if (event.detail.type == 'error') {
                swal('Error', `${event.detail.message}`, 'error');
            }
        });
        
        window.addEventListener('not-found', event => {
            if (event.detail.type == 'error') {
                swal('Not Found', `${event.detail.message}`, 'error');
            }
        });
        
        window.addEventListener('current-password-mismatch', event => {
            if (event.detail.type == 'error') {
                swal('Error', `${event.detail.message}`, 'error');
            }
        });

        window.addEventListener('switch-theme', event => {
            $("html").attr("class", `${event.detail.theme}`)
        });
    </script>
    @stack('scripts')
    
    @livewireScripts

</body>

</html>
