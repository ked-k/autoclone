<x-guest-layout>
    <x-slot:title>
        {{ __('Forgot Password | AutoLab') }}
        </x-slot>
        <div class="row g-0">
            <div class="col-lg-6 d-flex align-items-center justify-content-center border-end">
                <img src="{{ asset('autolab-assets/images/brand-logo.png') }}" class="img-fluid" alt="">
            </div>
            <div class="col-lg-6">
                <div class="card-body p-4 p-sm-5">
                    <div class="text-center">
                        <h5 class="card-title">{{ __('Forgot your password?') }}</h5>
                        <p class="card-text mb-2">
                            {{ __('No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                        </p>
                    </div>
                    <hr>
                    <x-auth-session-status class="mb-4" :status="session('status')" />
                    <form class="form-body" method="POST" action="{{ route('password.email') }}">
                        @csrf
                        @include('layouts.messages')
                        <div class="row g-3">
                            <div class="col-12">
                                <x-label for="inputEmailid" class="form-label">{{ __('Email Address') }}</x-label>

                                <input type="email" id="inputEmailid" class="form-control radius-30 ps-5"
                                    placeholder="{{ __('Email Address') }}" name="email" value="{{ old('email') }}"
                                    required autofocus>

                            </div>
                            <div class="col-12">
                                <div class="d-grid gap-3">

                                    <x-button class="btn-lg">
                                        {{ __('Email Password ResetLink') }}
                                    </x-button>

                                    <a href="{{ route('login') }}"
                                        class="btn btn-lg btn-light radius-30">{{ __('Back to Login') }}</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
</x-guest-layout>
