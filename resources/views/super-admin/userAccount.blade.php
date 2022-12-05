<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <title>User Account</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="An enterprise resource planning application For MakBRC" name="description">
    <meta content="AutoLab" name="MAKBRC">
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- App css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="light-style" />
    <link href="{{ asset('assets/css/app-dark.min.css') }}" rel="stylesheet" type="text/css" id="dark-style" />

</head>

<body class="loading d-flex flex-column min-vh-100" data-layout-config='{"darkMode":{!! Auth::user()->color_scheme === 'true' ? Auth::user()->color_scheme : 'false' !!}}'>
    <!-- NAVBAR START -->
    <nav class="navbar navbar-expand-lg py-lg-3 navbar-light">
        <div class="container border-bottom border-primary">

            <!-- logo -->
            <a href="index.html" class="navbar-brand me-lg-5">
                <img src="{{ asset('assets/images/logo-dark.png') }}" alt="" class="logo-dark" height="18">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
                aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <i class="mdi mdi-menu"></i>
            </button>

            <!-- menus -->
            <div class="collapse navbar-collapse" id="navbarNavDropdown">

                <!-- left menu -->
                <ul class="navbar-nav me-auto align-items-center">
                    <li class="nav-item mx-lg-1">
                        <a class="nav-link active" href="#">Home</a>
                    </li>
                </ul>

                <!-- right menu -->
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item me-0">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}" target="_blank" class="av-link d-lg-none"
                                onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                <i class="uil-sign-out-alt me-2"></i> {{ __('Logout') }}
                            </a>
                        </form>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}" target="_blank"
                                class="btn btn-sm btn-light btn-rounded d-none d-lg-inline-flex"
                                onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                <i class="uil-sign-out-alt me-2"></i> {{ __('Logout') }}
                            </a>
                        </form>
                    </li>
                </ul>

            </div>
        </div>
    </nav>

    <!-- START SERVICES -->
    <section class="py-2 mb-5">
        <div class="container">

            @include('layouts.messages')
            <div class="row">
                <div class="col-sm-12">
                    <!-- Profile -->
                    <div class="card bg-info">
                        <div class="card-body profile-user-box">
                            <div class="row">
                                <div class="col-sm-7">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <div class="avatar-lg">
                                                <img src="{{ asset('storage/' . $user->avatar) }}" alt=""
                                                    class="rounded-circle img-thumbnail">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div>
                                                <h4 class="mt-1 mb-1 text-white">{{ $user->title . ' ' . $user->name }}
                                                </h4>
                                                <p class="font-13 text-white-50">{{ $user->email }}</p>

                                                <ul class="mb-0 list-inline text-light">
                                                    <li class="list-inline-item me-3">
                                                        <h5 class="mb-1">{{ $user->contact }}</h5>
                                                        <p class="mb-0 font-13 text-white-50">Contact</p>
                                                    </li>
                                                    <li class="list-inline-item">
                                                        @if ($user->is_active == 1)
                                                            <h5 class="mb-1" style="color: rgb(160, 221, 160)">Active
                                                            </h5>
                                                        @else
                                                            <h5 class="mb-1" style="color: red">Suspended</h5>
                                                        @endif
                                                        <p class="mb-0 font-13 text-white-50">Status</p>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- end col-->
                                <div class="col-sm-3">
                                    <div class="text-center mt-sm-0 mt-3 text-sm-end">
                                        <button type="button" class="btn btn-light" data-bs-toggle="modal"
                                            data-bs-target="#updatePass{{ $user->id }}" data-bs-dismiss="modal">
                                            <i class="mdi mdi-account-edit me-1"></i> Change Password
                                        </button>
                                    </div>
                                </div> <!-- end col-->
                                <div class="col-sm-2">
                                    <div class="text-center mt-sm-0 mt-3 text-sm-end">
                                        <button type="button" class="btn btn-light" data-bs-toggle="modal"
                                            data-bs-target="#editUser{{ $user->id }}" data-bs-dismiss="modal">
                                            <i class="mdi mdi-account-edit me-1"></i> Edit Profile
                                        </button>
                                    </div>
                                </div> <!-- end col-->
                            </div> <!-- end row -->
                        </div> <!-- end card-body/ profile-user-box-->
                    </div>
                    <!--end profile/ card -->
                </div> <!-- end col-->
            </div>
        </div>
    </section>

    <!-- END SERVICES -->

    <!-- UPDATE USER ACCOUNT DETAILS -->
    <div class="modal fade" id="editUser{{ $user->id }}" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">UPDATE MY ACCOUNT</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div> <!-- end modal header -->
                <div class="modal-body">
                    <form method="POST" action="{{ route('users.update', [$user->id]) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="row col-md-12">
                                {{-- <div class="mb-3 col-md-4">
                                    <label for="emp_id2" class="form-label">Emp-No</label>
                                    <input type="text" style="text-transform: uppercase" id="emp_id2"
                                        class="form-control" name="emp_id" required readonly
                                        value="{{ $user->emp_id }}">
                                </div> --}}
                                <div class="mb-3 col-md-4">
                                    <label for="is_active2" class="form-label">Status</label>
                                    <select class="form-select" id="is_active2" name="is_active" required>
                                        @if ($user->status == 'Active')
                                            <option value="1" style="color: rgb(130, 199, 130)" selected>
                                                {{ $user->status }}</option>
                                        @else
                                            <option value="0" style="color: red" selected>
                                                {{ $user->status }}</option>
                                        @endif
                                    </select>
                                </div>
                                <input type="text" id="employee_id" hidden class="form-control"
                                    name="employee_id" value="{{ $user->id }}" required>
                                <div class="mb-3 col-md-4">
                                    <label for="title2" class="form-label">Title</label>
                                    <select class="form-select" id="title2" name="title" required>
                                        <option value="{{ $user->prefix }}">{{ $user->prefix }}
                                        </option>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="name2" class="form-label">Name</label>
                                    <input type="text" id="name2" class="form-control" readonly
                                        name="name" required
                                        value="{{ $user->first_name . ' ' . $user->surname }}">
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="email2" class="form-label">Email</label>
                                    <input type="email" id="email2" class="form-control" name="email"
                                        readonly required value="{{ $user->email }}">
                                </div>
                            </div> <!-- end col -->

                            <div class="mb-3 col-md-6">
                                <label for="contact2" class="form-label">Contact</label>
                                <input type="text" id="contact2" class="form-control" name="contact" readonly
                                    required value="{{ $user->contact }}">
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="image2" class="form-label">Image</label>
                                <input type="file" id="image2" class="form-control" name="avatar">
                            </div>


                        </div>
                        <!-- end row-->
                        <div class="d-grid mb-0 text-center">
                            <button class="btn btn-success" type="submit"
                                onclick="this.innerHTML='Processing please wait.....';" id="submitButton"> Update
                                User</button>
                        </div>
                    </form>
                </div>
            </div> <!-- end modal content-->
        </div> <!-- end modal dialog-->
    </div> <!-- end modal-->
    <!-- UPDATE PASSWORD -->
    <div class="modal fade" id="updatePass{{ $user->id }}" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">CHANGE PASSWORD</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div> <!-- end modal header -->
                <div class="modal-body">
                    <form method="POST" action="{{ route('users.update', [$user->id]) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="text" id="current_password" class="form-control"
                                        name="current_password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <input type="password" id="password" class="form-control" name="password"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                    <input type="password" id="password_confirmation" class="form-control"
                                        name="password_confirmation" required>
                                </div>

                            </div>

                        </div>
                        <!-- end row-->
                        <div class="d-grid mb-0 text-center">
                            <button class="btn btn-success" type="submit"
                                onclick="this.innerHTML='Processing please wait.....';">Change Password</button>
                        </div>
                    </form>
                </div>

            </div> <!-- end modal content-->
        </div> <!-- end modal dialog-->
    </div> <!-- end modal-->


    <footer class="mt-auto">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 text-center">
                    <hr>
                    <script>
                        document.write(new Date().getFullYear())
                    </script> © {{ $facilityInfo->facility_name }}
                </div>

            </div>
        </div>
    </footer>
    <!-- bundle -->
    <script src="{{ asset('assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.min.js') }}"></script>

</body>

</html>
