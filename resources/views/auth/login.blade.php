<x-guest-layout>
    <x-slot:title>        
        {{ __('Login | AutoLab') }}
    </x-slot>
        <style>
            @media(max-width: 768px){
        
            .logo{
                width: 100%;
            }
            }
            @media(max-width: 500px){
            
            .logo{
                width: 120px;
            }
            }
                </style>
        <div class="row g-0">
            <div class="col-lg-6 bg-logi d-flex align-items-center justify-content-center">
               
                <img src="{{ asset('autolab-assets/images/logo-min.png') }}" width="325px" class="img-fluid logo" alt="">
            </div>
            <div class="col-lg-6">
                <div class="card-body p-4 p-sm-5">
                    <div class="text-center">
                        <h3 class="card-title">{{ __('LOGIN') }}</h3>
                        <hr>
                    </div>

                    <form method="POST" action="{{ route('login') }}" class="form-body">
                        @csrf
                        @include('layouts.messages')
                        <div class="row g-3">
                            <div class="col-12">
                                <x-label for="inputEmailAddress">{{ __('Email Address') }}</x-label>
                                <div class="ms-auto position-relative">
                                    <div class="position-absolute top-50 translate-middle-y search-icon px-3">
                                        <i class="bi bi-envelope-fill"></i>
                                    </div>
                                    <input type="email" class="form-control radius-30 ps-5" id="inputEmailAddress"
                                        placeholder="Email Address" required name="email" value="{{ old('email') }}">
                                </div>
                            </div>
                            <div class="col-12">
                                <x-label for="inputChoosePassword">
                                    {{ __('Enter Password') }}</x-label>                  
                                <div class="ms-auto position-relative">
                                    <div class="position-absolute top-50 translate-middle-y search-icon px-3">
                                        <i class="bi bi-lock-fill"></i>
                                    </div>
                                    <input type="password" class="form-control radius-30 ps-5" id="inputChoosePassword"
                                        placeholder="Enter Password" name="password" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked"
                                        name="remember">
                                    <x-label class="form-check-label" for="flexSwitchCheckChecked">{{ __('Remember Me') }}</x-label>
                                </div>
                            </div>
                            <div class="col-6 text-end">
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-muted float-end">
                                        <small>{{ __('Forgot your password?') }}</small></a>
                                    </a>
                                @endif
                            </div>
                            <div class="col-12">
                                <div class="d-grid">
                                    <x-button>{{ __('Login') }}</x-button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
</x-guest-layout>
