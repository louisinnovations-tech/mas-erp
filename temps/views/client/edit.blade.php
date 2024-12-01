@php
    $file_validation = App\Models\Utility::file_upload_validation();
@endphp
@extends('layouts.app')
@section('page-title')
    @if ($user->id == Auth::user()->id && $user->type == 'company')
        {{ __('Edit Client Profile') }}
    @else
        {{ __('Edit Profile Info') }}
    @endif
@endsection


@php
    $logo = App\Models\Utility::get_file('uploads/profile');

    $settings = App\Models\Utility::settings();
@endphp
@section('breadcrumb')
    <li class="breadcrumb-item">
        @if ($user->id == Auth::user()->id && $user->type == 'company')
            {{ __('Edit Client Profile') }}
        @else
            {{ __('Edit Profile') }}
        @endif
    </li>
@endsection

@section('content')
    <div class="row p-0 g-0">

        <div class="col-sm-12">
            <div class="row g-0">
                <div class="col-xl-3 border-end border-bottom">
                    <div class="card shadow-none bg-transparent sticky-top" style="top:70px">
                        <div class="list-group list-group-flush rounded-0" id="useradd-sidenav">
                            <a href="#useradd-1" class="list-group-item list-group-item-action">{{ __('Client Profile') }} <div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            @if ($user->id == Auth::user()->id) 
                                <!-- <a href="#useradd-2"
                                    class="list-group-item list-group-item-action border-0">{{ __('Company Info') }} <div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a> -->
                                <!-- <a href="#useradd-2"
                                    class="list-group-item list-group-item-action">{{ __('Change Password') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a> -->
                            @endif
                        </div>
                    </div>
                </div>


                <div class="col-xl-9">
                    <div id="useradd-1" class="card  shadow-none rounded-0 border-bottom">

                        <div class="card-header">
                            <h5 class="mb-0">{{ __('Profile Information') }}</h5>
                        </div>
                        <div class="card-body">
                            {{ Form::model($user_detail, ['route' => ['client.update', $user->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
                                <div class=" setting-card">
                                    <div class="row">
                                        <div class="col-lg-4 col-sm-6 col-md-6">
                                            <div class="card-body text-center">
                                                <div class="logo-content">
                                                    <a href="{{ !empty($user->avatar) ? $logo . '/' . $user : $logo . '/avatar.png' }}"
                                                        target="_blank">
                                                        <img src="{{ !empty($user->avatar) ? $logo . '/' . $user->avatar : $logo . '/avatar.png' }}"
                                                            width="100" id="profile">
                                                    </a>
                                                </div>
                                                <div class="choose-files mt-4">
                                                    <label for="profile_pic">
                                                        <div class="bg-primary profile_update"
                                                            style="max-width: 100% !important;"> <i
                                                                class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                        </div>
                                                        <input type="file" class="file" name="profile" accept="image/*"
                                                            id="profile_pic"
                                                            onchange="document.getElementById('profile').src = window.URL.createObjectURL(this.files[0])"
                                                            style="width: 0px !important">
                                                        <p style="margin-top: -20px;text-align: center;"><span
                                                                class="text-muted m-0" data-toggle="tooltip"
                                                                title="{{ $file_validation['mimes'] }} {{ __('Max Size: ') }}{{ $file_validation['max_size'] }}"
                                                                data-bs-toggle="tooltip"
                                                                data-bs-placement="top">{{ __('Allowed file extension') }}</span>
                                                        </p>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-8 col-sm-6 col-md-6">
                                            <div class="card-body">

                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="col-form-label text-dark">{{ __('Full Name') }}</label>
                                                        <input class="form-control " name="name" type="text" id="fullname"
                                                        value="{{$user->name}}" placeholder="{{ __('Enter Your Full Name') }}"
                                                        required autocomplete="name">
                                                        @error('name')
                                                            <span class="invalid-name" role="alert">
                                                                <strong class="text-danger">{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="email"
                                                            class="col-form-label text-dark">{{ __('Email') }}</label>
                                                        <input class="form-control " name="email" type="text"
                                                            id="email" placeholder="{{ __('Enter Your Email Address') }}"
                                                            value="{{$user->email}}" required autocomplete="email">
                                                        @error('email')
                                                            <span class="invalid-email" role="alert">
                                                                <strong class="text-danger">{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row card-body">
                                    <div class="col-lg-6 col-sm-6">
                                        <div class="form-group">
                                            <label for="whats_app_number"
                                                class="col-form-label text-dark">{{ __('WhatsApp Number') }}</label>
                                            <input class="form-control " name="whats_app_number" type="number"
                                                id="whats_app_number" placeholder="{{ __('Enter Your WhatsApp Number') }}"
                                                value="{{ old('whats_app_number' , $user_detail->whats_app_number) }}"
                                                autocomplete="whats_app_number">
                                                @error('whats_app_number')
                                                    <span class="invalid-whats_app_number" role="alert">
                                                        <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-sm-6">
                                        <div class="form-group">
                                            <label for="mobile_number"
                                                class="col-form-label text-dark">{{ __('Mobile Number') }}</label>
                                            <input class="form-control " name="mobile_number" type="number"
                                                id="mobile_number" placeholder="{{ __('Enter Your Mobile Number') }}"
                                                value="{{ old('mobile_number', $user_detail->mobile_number) }}"
                                                autocomplete="mobile_number">
                                            @error('mobile_number')
                                                <span class="invalid-mobile_number" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-sm-6">
                                        <div class="form-group">
                                            <label for="address"
                                                class="col-form-label text-dark">{{ __('Address') }}</label>
                                            <input class="form-control " name="address" type="text" id="address"
                                                placeholder="{{ __('Enter Your Address') }}"
                                                value="{{ old('address', $user_detail->address) }}"
                                                autocomplete="address">
                                            @error('address')
                                                <span class="invalid-address" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    

                                    <div class="col-lg-6 col-sm-6">
                                        <div class="form-group">
                                            <label for="building_number"
                                                class="col-form-label text-dark">{{ __('Building Number') }}</label>
                                            <input class="form-control " name="building_number" type="text"
                                                id="building_number"
                                                placeholder="{{ __('Enter Your Building Number') }}"
                                                value="{{ old('building_number' , $user_detail->building_number) }}"
                                                autocomplete="building_number">
                                            @error('building_number')
                                                <span class="invalid-building_number" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-sm-6">
                                        <div class="form-group">
                                            <label for="street"
                                                class="col-form-label text-dark">{{ __('Street Number') }}</label>
                                            <input class="form-control " name="street_number" type="text"
                                                id="street" placeholder="{{ __('Enter Your Street Number') }}"
                                                value="{{ old('street_number' , $user_detail->street_number) }}"
                                                autocomplete="street_number">
                                            @error('street_number')
                                                <span class="invalid-street_number" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-sm-6">
                                        <div class="form-group">
                                            <label for="zone"
                                                class="col-form-label text-dark">{{ __('Zone Number') }}</label>
                                            <input class="form-control " name="zone_number" type="text"
                                                id="zone" placeholder="{{ __('Enter Your Zone Number') }}"
                                                value="{{ old('zone_number' , $user_detail->zone_number) }}"
                                                autocomplete="zone_number">
                                            @error('zone_number')
                                                <span class="invalid-zone_number" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-sm-6">
                                        <div class="form-group">
                                            <label for="city"
                                                class="col-form-label text-dark">{{ __('City') }}</label>
                                            <input class="form-control " name="city" type="text" id="city"
                                                placeholder="{{ __('Enter Your City') }}"
                                                value="{{ old('city' , $user_detail->city) }}"
                                                autocomplete="city">
                                            @error('city')
                                                <span class="invalid-city" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-sm-6">
                                        <div class="form-group">
                                            <label for="qid_number"
                                                class="col-form-label text-dark">{{ __('QID Number') }}</label>
                                            <input class="form-control " name="qid_number" type="number"
                                                id="qid_number" placeholder="{{ __('Enter Your QID Number') }}"
                                                value="{{ old('qid_number' , $user_detail->qid_number) }}"
                                                autocomplete="qid_number">
                                            @error('qid_number')
                                                <span class="invalid-qid_number" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-sm-6">
                                        <div class="form-group">
                                            <label for="passport_number"
                                                class="col-form-label text-dark">{{ __('Passport Number') }}</label>
                                            <input class="form-control " name="passport_number" type="number"
                                                id="passport_number"
                                                placeholder="{{ __('Enter Your Passport Number') }}"
                                                value="{{ old('passport_number' , $user_detail->passport_number) }}"
                                                autocomplete="passport_number">
                                            @error('passport_number')
                                                <span class="invalid-passport_number" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-sm-6">
                                        <div class="form-group">
                                            {{ Form::label('language', __('Select Language'), ['class' => 'col-form-label text-dark']) }}
                                            {!! Form::select(
                                                'language',
                                                ['en' => 'English', 'ar' => 'Arabic'],
                                                old('language' , $user_detail->language),
                                                [
                                                    'class' => 'form-control multi-select',
                                                    'placeholder' => __('Select a language'),
                                                ],
                                            ) !!}
                                            @error('language')
                                                <span class="invalid-language" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3 form-group mt-4">
                                        <label for="password_switch">{{ __('Login is enable') }}</label>
                                        <div class="form-check form-switch custom-switch-v1 float-end">
                                            <input type="checkbox" name="password_switch" class="form-check-input input-primary pointer"
                                                value="on" id="password_switch" checked>
                                            <label class="form-check-label" for="password_switch"></label>
                                            @error('password_switch')
                                                <span class="invalid-password_switch" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-12 text-end">
                                        <input type="submit" value="{{ __('Save Changes') }}"
                                            class="btn btn-print-invoice  btn-primary m-r-10">
                                    </div>
                                </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('custom-script')
    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300,

        })
        $(".list-group-item").on('click', function() {
            $('.list-group-item').filter(function() {
                return this.href == id;
            }).parent().removeClass('text-primary');
        });
    </script>

@endpush
