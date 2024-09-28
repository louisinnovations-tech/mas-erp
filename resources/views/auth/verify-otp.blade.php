@extends('layouts.guest')
@php
    use App\Models\Utility;
    $languages = Utility::languages();
    $settings = Utility::settings();

@endphp
@section('page-title')
    {{ __('Verify') }}
@endsection
@section('content')

    <div class="card-body">
        <div>
            <h2 class="mb-3 f-w-600">{{ __('Verify OTP') }} </h2>
        </div>
        <div class="custom-login-form">
            <form method="POST" action="{{ url('/verify-otp') }}" class="needs-validation" novalidate="">
            @csrf
                <div class="form-group mb-3">
                    <label class="form-label">{{ __('Email') }}</label>
                    <input id="email" type="email" class="form-control  @error('email') is-invalid @enderror"
                        name="email" placeholder="{{ __('Enter your email') }}"
                        required autofocus>
                    @error('email')
                        <span class="error invalid-email text-danger" role="alert">
                            <small>{{ $message }}</small>
                        </span>
                    @enderror
                </div>
                <div class="form-group mb-3 pss-field">
                    <label class="form-label">{{ __('OTP') }}</label>
                    <input id="otp" type="text" class="form-control  @error('otp') is-invalid @enderror" name="otp" placeholder="{{ __('Enter OTP') }}" required>
                    @error('otp')
                        <span class="error invalid-otp text-danger" role="alert">
                            <small>{{ $message }}</small>
                        </span>
                    @enderror
                </div>
                <div class="d-grid">
                    <button class="btn btn-primary mt-2" type="submit">
                        {{ __('Verify') }}
                    </button>
                </div>
            </form>
     
        </div>
    </div>
@endsection
