@php
    use App\Models\Utility;
    $settings = Utility::settings();

    $logo = Utility::get_file('uploads/logo');

    $company_favicon = $settings['company_favicon'] ?? '';

    $SITE_RTL = $settings['SITE_RTL'];
    $color = !empty($settings['color']) ? $settings['color'] : 'theme-1';
    if (isset($settings['color_flag']) && $settings['color_flag'] == 'true') {
        $themeColor = 'custom-color';
    } else {
        $themeColor = $color;
    }

    $SITE_RTL = 'off';
    if (!empty($settings['SITE_RTL'])) {
        $SITE_RTL = $settings['SITE_RTL'];
    }

    $logo_light = $settings['company_logo_light'] ?? '';
    $logo_dark = $settings['company_logo_dark'] ?? '';
    $company_logo = Utility::get_company_logo();
    $company_logos = $settings['company_logo_light'] ?? '';

    $lang = \App::getLocale('lang');
    if ($lang == 'ar' || $lang == 'he') {
        $SITE_RTL = 'on';
    }
@endphp

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $SITE_RTL == 'on' ? 'rtl' : '' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="keywords" content="Dashboard Template" />
    <meta name="author" content="Rajodiya Infotech" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        {{ Utility::getValByName('title_text') ? Utility::getValByName('title_text') : config('app.name', 'Mas-ERP') }}
        - @yield('page-title') </title>

    <!-- Primary Meta Tags -->
    <meta name="title" content={{ $settings['meta_keywords'] ?? '' }}>
    <meta name="description" content={{ $settings['meta_description'] ?? '' }}>

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content={{ env('APP_URL') }}>
    <meta property="og:title" content={{ $settings['meta_keywords'] ?? '' }}>
    <meta property="og:description" content={{ $settings['meta_description'] ?? '' }}>
    <meta property="og:image" content={{ asset(Storage::url('uploads/metaevent/' . $settings['meta_image'] ?? '')) }}>

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content={{ env('APP_URL') }}>
    <meta property="twitter:title" content={{ $settings['meta_keywords'] ?? '' }}>
    <meta property="twitter:description" content={{ $settings['meta_description'] ?? '' }}>
    <meta property="twitter:image"
        content={{ asset(Storage::url('uploads/metaevent/' . $settings['meta_image'] ?? '')) }}>


    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Favicon icon -->

    <link rel="icon"
        href="{{ $logo . '/' . (isset($company_favicon) && !empty($company_favicon) ? $company_favicon : 'favicon.png') . '?timestamp=' . time() }}"
        type="image" sizes="800x800">

    <!-- font css -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/plugins/notifier.css') }}">

    @if ($settings['cust_darklayout'] == 'on')
        @if (isset($settings['SITE_RTL']) && $settings['SITE_RTL'] == 'on')
            <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css') }}" id="main-style-link">
        @endif
        <link rel="stylesheet" href="{{ asset('assets/css/style-dark.css') }}">
    @else
        @if (isset($settings['SITE_RTL']) && $settings['SITE_RTL'] == 'on')
            <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css') }}" id="main-style-link">
        @else
            <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
            <!-- <link rel="stylesheet" href="{{ asset('assets/css/crmgo-style.css') }}" id="main-style-link"> -->
        @endif
    @endif
    @if (isset($settings['SITE_RTL']) && $settings['SITE_RTL'] == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/custom-auth-rtl.css') }}" id="main-style-link">
    @else
        <link rel="stylesheet" href="{{ asset('assets/css/custom-auth.css') }}" id="main-style-link">
    @endif
    @if ($settings['cust_darklayout'] == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/custom-dark.css') }}" id="main-style-link">
    @endif
    <link rel="stylesheet" href="{{ asset('assets/css/custom-color.css') }}">
    <!--Calendar -->
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/main.css') }}">
    @stack('css-page')
    <style>
        :root {
            --color-customColor: <?=$color ?>;
        }

        .brand-logo-nav {
            height: 45px;
            width: auto;
        }

        @media (min-width: 992px) {
            .brand-logo-nav {
                height: 55px;
            }
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .navbar-brand {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
        }

        .spacer {
            position: absolute;
            left: 0;
        }

        .nav-area-left {
            margin-left: auto
        }

        .navbar-expand-md li {
            list-style-type: none;
        }

        .drp-text-full {
            display: inline;
        }

        .drp-text-short {
            display: none;
        }

        @media (max-width: 767px) {
            .drp-text-full {
                display: none;
            }

            .drp-text-short {
                display: inline;
                font-size: 12px;
            }

            .custom-login .drp-language .btn {

                margin: 0;
                padding: 6px 24px 6px 6px;
            }

            .custom-login .dropdown-toggle::after {
                top: 13px;
            }

        }
    </style>
</head>

<body class="{{ $themeColor }}">

    <div class="custom-login">
        <!-- <div class="login-bg-img">
            <img src="{{ asset('assets/images/auth/' . $color . '.svg') }}" class="login-bg-1">
            <img src="{{ asset('assets/images/auth/common.svg') }}" class="login-bg-2">
        </div>
        <div class="bg-login bg-primary"></div> -->

        <div class="custom-login-inner">
            <header class="dash-login-header">
                <nav class="navbar navbar-expand-md default py-2">
                    <div class="container">

                        <div class="spacer"></div>
                        <div class="navbar-brand py-0">
                            <a href="#">
                                @if ($settings['cust_darklayout'] && $settings['cust_darklayout'] == 'on')
                                    <img src="{{ $logo . '/' . (isset($company_logos) && !empty($company_logos) ? $company_logos : 'logo-dark.png') . '?' . time() }}"
                                        alt="{{ config('app.name', 'Mas-ERP') }}" class="logo brand-logo-nav"
                                        loading="lazy">
                                @else
                                    <img src="/assets/images/mashael.png" alt="{{ config('app.name', 'Mas-ERP') }}"
                                        class="logo brand-logo-nav" loading="lazy">
                                    {{-- <img src="{{ $logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : '/assets/images/mashael.png') . '?' . time() }}"
                                        alt="{{ config('app.name', 'Mas-ERP') }}" class="logo brand-logo-nav"
                                        loading="lazy"> --}}
                                @endif
                            </a>
                        </div>
                        <div class="nav-area-left">
                            @yield('language-bar')
                            {{-- <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarlogin">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                         --}}

                            {{-- <div class="collapse navbar-collapse" id="navbarlogin">
                            <ul class="navbar-nav align-items-center ms-auto mb-2 mb-lg-0">
                                <!-- <li class="nav-item">
                                    @include('landingpage::layouts.buttons')
                                </li> -->
                                @yield('language-bar')
                            </ul>
                        </div> --}}
                        </div>
                    </div>
                </nav>
            </header>
            <main class="custom-wrapper">
                <div class="custom-row">
                    <div class="card">
                        @yield('content')
                    </div>
                </div>
            </main>
            <footer>
                <div class="auth-footer">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <a target="_blank" href="https://www.mas.com.qa/">
                                    <span>&copy; {{ __('Copyright') }}
                                        {{ $settings['footer_text'] ? $settings['footer_text'] : config('app.name', 'MAS ERP System') }}
                                        {{ date('Y') }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- [ auth-signup ] end -->
    @include('layouts.cookie_consent')

    <script src="{{ asset('assets/js/vendor-all.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/feather.js') }}"></script>
    <script src="{{ asset('js/jquery.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/notifier.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/main.min.js') }}"></script>
    @stack('pre-purpose-script-page')
    @stack('script-page')
    @stack('custom-scripts')

    @if ($message = Session::get('success'))
        <script>
            show_toastr('{{ __('Success') }}', '{!! $message !!}', 'success')
        </script>
    @endif

    @if ($message = Session::get('error'))
        <script>
            show_toastr('{{ __('Error') }}', '{!! $message !!}', 'error')
        </script>
    @endif
</body>

</html>
