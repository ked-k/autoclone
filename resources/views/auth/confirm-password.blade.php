
<x-guest-layout>
    <x-slot:title>
        {{ __('Confirm Password | AutoLab') }}
        </x-slot>
        <div class="row g-0">
            <div class="col-lg-6 d-flex align-items-center justify-content-center border-end">
                <img src="{{ asset('autolab-assets/images/brand-logo.png') }}" class="img-fluid" alt="">
            </div>
            <div class="col-lg-6">
                <div class="card-body p-4 p-sm-5">
                    <div class="text-center">
                        <p class="card-text mb-2">
                            {{ __('This is a restricted area of the application. Please confirm your password before continuing.') }}
                        </p>
                    </div>
                    <hr>
                    <form class="form-body" method="POST" action="{{ route('password.confirm') }}">
                        @csrf
                        @include('layouts.messages')
                        <div class="col-12">
                            <x-label for="inputNewPassword">{{ __('Password') }}</x-label>
                            <div class="ms-auto position-relative">
                                <div class="position-absolute top-50 translate-middle-y search-icon px-3"><i
                                        class="bi bi-lock-fill"></i></div>
                                <input type="password" class="form-control radius-30 ps-5" id="inputNewPassword"
                                    placeholder="Enter New Password" name="password" required autofocus>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-grid gap-3">

                                <x-button class="btn-lg">
                                    {{ __('Confirm') }}
                                </x-button>
                            </div>
                        </div>
                </div>
                </form>
            </div>
        </div>
        </div>
</x-guest-layout>
