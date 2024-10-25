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
                <span class="drp-text"> {{ ucFirst($languages[$lang]) }}
                </span>
            </a>
            <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                @if ($loginType)
                    @foreach ($languages as $code => $language)
                        <a href="{{ route('login', $code) }}?loginType={{ $loginType }}" tabindex="0"
                            class="dropdown-item {{ $code == $lang ? 'active' : '' }}">
                            <span>{{ ucFirst($language) }}</span>
                        </a>
                    @endforeach
                @else
                    @foreach ($languages as $code => $language)
                        <a href="{{ route('login', $code) }}" tabindex="0"
                            class="dropdown-item {{ $code == $lang ? 'active' : '' }}">
                            <span>{{ ucFirst($language) }}</span>
                        </a>
                    @endforeach
                @endif
            </div>
        </li>
    </div>
@endsection


@section('content')
    <div class="card-body">
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
            <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate="">
                @csrf
                <div class="form-group mb-3">
                    <label class="form-label">{{ __('Email') }}</label>
                    <input id="email" type="email" class="form-control  @error('email') is-invalid @enderror"
                        name="email" placeholder="{{ __('Enter your email') }}" required autofocus>
                    @error('email')
                        <span class="error invalid-email text-danger" role="alert">
                            <small>{{ $message }}</small>
                        </span>
                    @enderror
                </div>
                <div class="form-group mb-3 pss-field">
                    <label class="form-label">{{ __('Password') }}</label>
                    <input id="password" type="password" class="form-control  @error('password') is-invalid @enderror"
                        name="password" placeholder="{{ __('Password') }}" required>
                    @error('password')
                        <span class="error invalid-password text-danger" role="alert">
                            <small>{{ $message }}</small>
                        </span>
                    @enderror
                </div>
                <!-- <div class="form-group mb-4">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                                            @if (Route::has('password.request'))
    <span>
                                                    <a href="{{ route('password.request', $lang) }}" tabindex="0">{{ __('Forgot Your Password?') }}</a>
                                                </span>
    @endif
                                        </div>
                                    </div> -->
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
                    <button class="btn btn-primary mt-2" type="submit">
                        {{ __('Login') }}
                    </button>
                </div>
            </form>

        </div>
    </div>
@endsection
