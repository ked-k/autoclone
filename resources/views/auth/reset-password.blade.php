<x-guest-layout>
    <x-slot:title>
        {{ __('Reset Password | AutoLab') }}
        </x-slot>
        <x-auth-session-status class="mb-4" :status="session('status')" />
        <div class="row g-0">
            <div class="col-lg-6 d-flex align-items-center justify-content-center border-end">
                <img src="{{ asset('autolab-assets/images/brand-logo.png') }}" class="img-fluid" alt="">
            </div>
            <div class="col-lg-6">
                <div class="card-body p-4 p-sm-5">
                    <div class="text-center">
                        <h5 class="card-title">{{ __('Create New Password') }}</h5>
                        <p class="card-text mb-5">
                            {{ __('We received your reset password request. Please enter your new password!') }}</p>
                        <hr>
                    </div>
                    <x-auth-session-status class="mb-4" :status="session('status')" />
                    <form class="form-body" method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <!-- Password Reset Token -->
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">
                        @include('layouts.messages')

                        <div class="row g-3">
                            <div class="col-12">
                                <x-label for="inputEmailAddress">{{ __('Email Address') }}</x-label>
                                <div class="ms-auto position-relative">
                                    <div class="position-absolute top-50 translate-middle-y search-icon px-3">
                                        <i class="bi bi-envelope-fill"></i>
                                    </div>
                                    <input type="email" class="form-control radius-30 ps-5" id="inputEmailAddress"
                                        placeholder="Email Address" name="email"
                                        value="{{ old('email', $request->email) }}" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <x-label for="inputNewPassword">{{ __('New Password') }}</x-label>
                                <div class="ms-auto position-relative">
                                    <div class="position-absolute top-50 translate-middle-y search-icon px-3"><i
                                            class="bi bi-lock-fill"></i></div>
                                    <input type="password" class="form-control radius-30 ps-5" id="inputNewPassword"
                                        placeholder="Enter New Password" name="password" required autofocus>
                                </div>
                            </div>
                            <div class="col-12">
                                <x-label for="inputConfirmPassword">{{ __('Confirm Password') }}</x-label>
                                <div class="ms-auto position-relative">
                                    <div class="position-absolute top-50 translate-middle-y search-icon px-3"><i
                                            class="bi bi-lock-fill"></i></div>
                                    <input type="password" class="form-control radius-30 ps-5" id="inputConfirmPassword"
                                        placeholder="Confirm Password" name="password_confirmation" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-grid gap-3">
                                    <x-button class="btn-lg">
                                        {{ __('Change Password') }}

                                    </x-button>

                                    <a href="{{ route('login') }}"
                                        class="btn btn-lg btn-light radius-30">{{ __('Back to
                                                                                Login') }}</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
</x-guest-layout>
