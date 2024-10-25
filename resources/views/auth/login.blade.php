@extends('layouts.guest')
@php
    use App\Models\Utility;
    $languages = Utility::languages();
    $settings = Utility::settings();

@endphp

@push('custom-scripts')
    @if ($settings['recaptcha_module'] == 'on')
        {!! NoCaptcha::renderJs() !!}
    @endif
@endpush

@section('page-title')
    {{ __('Login') }}
@endsection
@php
    $loginType = request()->query('loginType');
@endphp

@section('language-bar')
    <div class="lang-dropdown-only-desk">
        <li class="dropdown dash-h-item drp-language">
            <a class="dash-head-link dropdown-toggle btn" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="drp-text">{{ ucFirst($languages[$lang]) }}</span>
            </a>
            <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                @foreach ($languages as $code => $language)
                    <a href="{{ route('login', $code) }}" tabindex="0"
                        class="dropdown-item {{ $code == $lang ? 'active' : '' }}">
                        <span>{{ ucFirst($language) }}</span>
                    </a>
                @endforeach
            </div>
        </li>
    </div>
@endsection

@section('content')
    <div class="card-body">
        <!-- Login Form -->
        <div id="login-form">
            @if ($loginType)
                @php
                    $loginText = $loginType . ' Login';
                @endphp
                <div>
                    <h2 class="mb-3 f-w-600">{{ __($loginText) }} </h2>
                </div>
            @else
                <div>
                    <h2 class="mb-3 f-w-600">
                        {{ __((request()->root() === config('app.client_url') ? 'Client' : 'Employee') . ' Login') }} </h2>
                </div>
            @endif
            <div class="custom-login-form">
                <form id="loginForm" method="POST" action="{{ route('login') }}" class="needs-validation" novalidate="">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="form-label">{{ __('Email') }}</label>
                        <input id="email" type="email" class="form-control" name="email"
                            placeholder="{{ __('Enter your email') }}" required autofocus>
                        <span class="error invalid-email text-danger" role="alert" id="login-email-error"></span>
                    </div>
                    <div class="form-group mb-3 pss-field">
                        <label class="form-label">{{ __('Password') }}</label>
                        <input id="password" type="password" class="form-control" name="password"
                            placeholder="{{ __('Password') }}" required>
                        <span class="error invalid-password text-danger" role="alert" id="login-password-error"></span>
                    </div>
                    {{-- <!-- <div class="form-group mb-4">
                                                <div class="d-flex flex-wrap align-items-center justify-content-between">
                                                    @if (Route::has('password.request'))
    <span>
                                                            <a href="{{ route('password.request', $lang) }}" tabindex="0">{{ __('Forgot Your Password?') }}</a>
                                                        </span>
    @endif
                                                </div>
                                            </div> --> --}}
                    @if ($settings['recaptcha_module'] == 'on')
                        <div class="form-group mb-4">
                            {!! NoCaptcha::display() !!}
                            @error('g-recaptcha-response')
                                <span class="error small text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    @endif
                    <div class="d-grid">
                        <button class="btn btn-primary mt-2" type="submit">{{ __('Login') }}</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- OTP Form -->
        <div id="otp-form" style="display: none;">
            <div>
                <h2 class="mb-3 f-w-600">{{ __('Verify OTP') }}</h2>
            </div>
            <div class="custom-login-form">
                <form id="otpForm" method="POST" action="{{ url('/verify-otp') }}" class="needs-validation"
                    novalidate="">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="form-label">{{ __('Email') }}</label>
                        <input id="otp-email" type="email" class="form-control" name="email" readonly required
                            autofocus>
                    </div>
                    <div class="form-group mb-3 pss-field">
                        <label class="form-label">{{ __('OTP') }}</label>
                        <input id="otp-code" type="text" class="form-control" name="otp"
                            placeholder="{{ __('Enter OTP') }}" required>
                        <span class="error invalid-otp text-danger" role="alert" id="otp-error"></span>
                    </div>
                    <div class="d-grid">
                        <button class="btn btn-light" id="backToLogin"
                            style="background-color: transparent; border: none; text-decoration: underline;">{{ __('Back to Login') }}</button>

                        <button class="btn btn-primary mt-2" type="submit">{{ __('Verify') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('custom-scripts')
    <script>
        $(document).ready(function() {
            $('#loginForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('login') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.showOtpForm) {
                            $('#login-form').hide();
                            $('#otp-form').show();
                            $('#otp-email').val(response.email);
                        }
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        $('#login-email-error').text(errors.email ? errors.email[0] : '');
                        $('#login-password-error').text(errors.password ? errors.password[0] :
                            '');
                    }
                });
            });

            $('#otpForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ url('/verify-otp') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            window.location.href = response.redirect;
                        }
                    },
                    error: function(xhr) {
                        $('#otp-error').text(xhr.responseJSON.error);
                    }
                });
            });

            $('#backToLogin').click(function() {
                $('#otp-form').hide();
                $('#login-form').show();
            });
        });
    </script>
@endpush
