@php
    $file_validation = App\Models\Utility::file_upload_validation();
@endphp
@extends('layouts.app')
@section('page-title')
    {{ __('Edit Employee Profile') }}
@endsection


@php
    $logo = App\Models\Utility::get_file('uploads/profile');

    $settings = App\Models\Utility::settings();
@endphp
@section('breadcrumb')
    <li class="breadcrumb-item">
        {{ __('Edit Employee Profile') }}
    </li>
@endsection

@section('content')
    <div class="row p-0 g-0">

        <div class="col-sm-12">
            <div class="row g-0">
                <div class="col-xl-3 border-end border-bottom">
                    <div class="card shadow-none bg-transparent sticky-top" style="top:70px">
                        <div class="list-group list-group-flush rounded-0" id="useradd-sidenav">
                            <a href="#useradd-1" class="list-group-item list-group-item-action">{{ __('Employee Profile') }} <div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            {{--@if ($user->id == Auth::user()->id) 
                                <a href="#useradd-2"
                                    class="list-group-item list-group-item-action border-0">{{ __('Company Info') }} <div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                <a href="#useradd-2"
                                    class="list-group-item list-group-item-action">{{ __('Change Password') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif --}}
                        </div>
                    </div>
                </div>


                <div class="col-xl-9">
                    <div id="useradd-1" class="card  shadow-none rounded-0 border-bottom">

                        <div class="card-header">
                            <h5 class="mb-0">{{ __('Profile Information') }}</h5>
                        </div>
                        <div class="card-body">

                            {{ Form::model($userDetails, ['route' => ['employee.update' , $user->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
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
                                                placeholder="{{ __('Enter Your Full Name') }}"
                                                value="{{old('name', $user->name)}}"
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
                                                        value="{{old('email', $user->email)}}"
                                                        required autocomplete="email">
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
                                        <label for="mobile_number"
                                            class="col-form-label text-dark">{{ __('Mobile Number') }}</label>
                                        <input class="form-control " name="mobile_number" type="number"
                                            id="mobile_number" placeholder="{{ __('Enter Your Mobile Number') }}"
                                            value="{{old('mobile_number', $userDetails->mobile_number)}}"
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
                                        <label for="landphone"
                                            class="col-form-label text-dark">{{ __('Land Phone') }}</label>
                                        <input class="form-control " name="land_phone" type="number" id="landphone"
                                            placeholder="{{ __('Enter Your Land Phone') }}"
                                            value="{{old('land_phone', $userDetails->land_phone)}}"
                                            autocomplete="land_phone">
                                            @error('land_phone')
                                                <span class="invalid-land_phone" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6 col-sm-6">
                                    <div class="form-group">
                                        <label for="extension_number"
                                            class="col-form-label text-dark">{{ __('Extension Number') }}</label>
                                        <input class="form-control " name="extension_number" type="number"
                                            id="extension_number"
                                            placeholder="{{ __('Enter Your Extension Number') }}"
                                            value="{{old('extension_number', $userDetails->extension_number)}}"
                                            autocomplete="extension_number">
                                            @error('extension_number')
                                                <span class="invalid-extension_number" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                    </div>
                                </div>

                                    <div class="col-lg-6 col-sm-6">
                                    <div class="form-group">
                                        <label for="profession"
                                            class="col-form-label text-dark">{{ __('Profession') }}</label>
                                        <input class="form-control " name="profession" type="text"
                                            id="profession"
                                            placeholder="{{ __('Enter Your Profession') }}"
                                            value="{{old('profession', $userDetails->profession)}}"
                                            autocomplete="profession">
                                            @error('profession')
                                                <span class="invalid-profession" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                    </div>
                                </div>

                                    <div class="col-lg-6 col-sm-6">
                                    <div class="form-group">
                                        <label for="department"
                                            class="col-form-label text-dark">{{ __('Department') }}</label>
                                        <input class="form-control " name="department" type="text"
                                            id="department"
                                            placeholder="{{ __('Enter Your Department') }}"
                                            value="{{old('department', $userDetails->department)}}"
                                            autocomplete="department">
                                            @error('department')
                                                <span class="invalid-department" role="alert">
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
                                    </div>
                                </div>

                                <div class="col-lg-12 text-end">
                                    <input type="submit" value="{{ __('Save Changes') }}"
                                        class="btn btn-print-invoice  btn-primary m-r-10">
                                </div>
                            </div>
                            {{--@else
                                <div class="row card-body">
                                    <div class="col-lg-6 col-sm-6">
                                        <div class="form-group">
                                            <label for="mobile_number"
                                                class="col-form-label text-dark">{{ __('Mobile Number') }}</label>
                                            <input class="form-control " name="mobile_number" type="number"
                                                id="mobile_number" placeholder="{{ __('Enter Your Mobile Number') }}"
                                                autocomplete="mobile_number">
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-sm-6">
                                        <div class="form-group">
                                            <label for="address"
                                                class="col-form-label text-dark">{{ __('Address') }}</label>
                                            <input class="form-control " name="address" type="text" id="address"
                                                placeholder="{{ __('Enter Your Address') }}"
                                                autocomplete="address">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-6">
                                        <div class="form-group">
                                            <label for="city"
                                                class="col-form-label text-dark">{{ __('City') }}</label>
                                            <input class="form-control " name="city" type="text" id="city"
                                                placeholder="{{ __('Enter Your City') }}"
                                                autocomplete="city">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-6">
                                        <div class="form-group">
                                            <label for="state"
                                                class="col-form-label text-dark">{{ __('State') }}</label>
                                            <input class="form-control " name="state" type="text" id="state"
                                                placeholder="{{ __('Enter Your State') }}"
                                                autocomplete="state">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-6">
                                        <div class="form-group">
                                            <label for="zip_code"
                                                class="col-form-label text-dark">{{ __('Zip/Postal Code') }}</label>
                                            <input class="form-control " name="zip_code" type="number" id="zip_code"
                                                placeholder="{{ __('Enter Your Zip/Postal Code') }}"
                                                autocomplete="zip_code">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-6">
                                        <div class="form-group">
                                            <label for="landmark"
                                                class="col-form-label text-dark">{{ __('Landmark') }}</label>
                                            <input class="form-control " name="landmark" type="text" id="landmark"
                                                placeholder="{{ __('Enter Your Landmark') }}"
                                                autocomplete="landmark">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-6">
                                        <div class="form-group">
                                            <label for="about"
                                                class="col-form-label text-dark">{{ __('Brief About Yourself') }}</label>
                                            <input class="form-control " name="about" type="text" id="about"
                                                placeholder="{{ __('Enter Your About Yourself') }}"
                                                autocomplete="about">
                                        </div>
                                    </div>
                                    <div class="col-lg-12 text-end">
                                        <input type="submit" value="{{ __('Save Changes') }}"
                                            class="btn btn-print-invoice  btn-primary m-r-10">
                                    </div>
                                </div>
                            @endif--}}
                            {{ Form::close() }}
                        </div>
                    </div>
                    {{--@if ($user->id == Auth::user()->id && Auth::user()->type == 'company')
                        <div id="useradd-2" class="card  shadow-none rounded-0 border-bottom">

                            <div class="card-header">
                                <h5 class="mb-0">{{ __('Change Password') }}</h5>
                                <small> {{ __('Details about your member account password change') }}</small>
                            </div>
                            <div class="card-body">
                                {{ Form::open(['route' => ['member.change.password', $user->id], 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="password"
                                                class="form-label col-form-label text-dark">{{ __('New Password') }}</label>
                                            <input class="form-control" name="password" type="password" id="password"
                                                required autocomplete="password"
                                                placeholder="{{ __('Enter New Password') }}">
                                            @error('password')
                                                <span class="invalid-password" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="confirm_password"
                                                class="form-label col-form-label text-dark">{{ __('Confirm Password') }}</label>
                                            <input class="form-control" name="confirm_password" type="password"
                                                id="confirm_password" required autocomplete="confirm_password"
                                                placeholder="{{ __('Confirm New Password') }}">
                                            @error('confirm_password')
                                                <span class="invalid-confirm_password" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer pr-0">
                                    {{ Form::submit(__('Update'), ['class' => 'btn  btn-primary']) }}
                                </div>
                                {{ Form::close() }}
                            </div>

                        </div>
                    @endif--}}
                    {{--@if ($user->id == Auth::user()->id && Auth::user()->type == 'company')
                        <div id="useradd-2" class="card shadow-none rounded-0 border-bottom">
                            <div class="card-header">
                                <h5>{{ __('Company Info') }}</h5>
                                <small class="text-muted">{{ __('Edit details about your company information') }}</small>
                            </div>
                            <div class="card-body">
                                {{ Form::model($employeeDetails, ['route' => ['employee.company.update', $employeeDetails->user_id], 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
                                    <div class="row mt-3">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                {!! Form::label('emp_id', __('Employee ID'), ['class' => 'form-label']) !!}
                                                {!! Form::text('emp_id', $employeesId, ['class' => 'form-control', 'readonly']) !!}
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('branch_id', __('Branch'), ['class' => 'form-label']) }}
                                            {{ Form::select('branch_id', $branches, null, ['class' => 'form-control select', 'required' => 'required', 'id' => 'branch_id']) }}
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('department_id', __('Department'), ['class' => 'form-label', 'placeholder' => 'Select Department']) }}
                                            <select class=" select form-control " id="department_id" name="department_id"
                                                required="required">
                                                <option value="">{{ __('Select any Department') }}</option>
                                                @foreach ($departmentData as $key => $val)
                                                    <option value="{{ $key }}"
                                                        {{ $key == $employeeDetails->department ? 'selected' : '' }}>
                                                        {{ $val }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6">
                                            {{ Form::label('designation_id', __('Designation'), ['class' => 'form-label']) }}
                                            <select class="select form-control " id="designation_id" name="designation_id"
                                                required="required"></select>
                                        </div>

                                        <div class="col-sm-6">
                                            <div cla`s="form-group">
                                                {!! Form::label('joining_date', __('Date of Joining'), ['class' => 'form-label']) !!}
                                                {!! Form::date('joining_date', null, ['class' => 'form-control', 'required' => 'required']) !!}
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                {!! Form::label('exit_date', __('Date of Exit'), ['class' => 'form-label']) !!}
                                                {!! Form::date('exit_date', !empty($employeeDetails->exit_date) ? null : '', [
                                                    'class' => 'form-control',
                                                    'required' => 'required',
                                                ]) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group ">
                                                {{ Form::label('salary_type', __('Salary Type'), ['class' => 'form-label']) }}
                                                {{ Form::select('salary_type', $salaryType, null, ['class' => 'form-control multi-select']) }}
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                {!! Form::label('salary', __('Salary'), ['class' => 'form-label']) !!}
                                                {!! Form::number('salary', null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            {{ Form::submit(__('Update'), ['class' => 'btn btn-primary d-flex align-items-center']) }}
                                        </div>
                                    </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                    @endif--}}
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

    @if (Auth::user()->type == 'advocate')
        <script>
            $(document).ready(function() {

                var get_selected =
                    '{{ !empty($advocate->ofc_country) ? $advocate->getCountryName($advocate->ofc_country) : $advocate->getCountryName(113) }}';
                var home_selected =
                    '{{ !empty($advocate->home_country) ? $advocate->getCountryName($advocate->home_country) : $advocate->getCountryName(113) }}';

                $.ajax({
                    url: "{{ route('get.country') }}",
                    type: "GET",
                    success: function(result) {

                        $.each(result.data, function(key, value) {
                            if (value.id == get_selected) {
                                var selected = 'selected';
                            } else {
                                var selected = '';
                            }

                            if (value.id == home_selected) {
                                var selected_home = 'selected';
                            } else {
                                var selected_home = '';
                            }

                            $("#country").append('<option value="' + value.id + '" ' + selected +
                                ' >' + value
                                .country + "</option>");

                            $("#home_country").append('<option value="' + value.id + '" ' +
                                selected_home + '>' + value
                                .country + "</option>");
                        });
                    },
                });


                $("#country").on("change", function() {
                    var country_id = this.value;

                    $("#state").html("");
                    $.ajax({
                        url: "{{ route('get.state') }}",
                        type: "POST",
                        data: {
                            country_id: country_id,
                            _token: "{{ csrf_token() }}",
                        },
                        dataType: "json",
                        success: function(result) {
                            $.each(result.data, function(key, value) {
                                $("#state").append('<option value="' + value.id + '">' +
                                    value.region + "</option>");
                            });
                            $("#city").html('<option value="">Select State First</option>');
                        },
                    });
                });

                $("#home_country").on("change", function() {
                    var country_id = this.value;
                    $("#home_state").html("");
                    $.ajax({
                        url: "{{ route('get.state') }}",
                        type: "POST",
                        data: {
                            country_id: country_id,
                            _token: "{{ csrf_token() }}",
                        },
                        dataType: "json",
                        success: function(result) {
                            $.each(result.data, function(key, value) {
                                $("#home_state").append('<option value="' + value.id +
                                    '">' +
                                    value.region + "</option>");
                            });
                            $("#home_city").html('<option value="">Select State First</option>');
                        },
                    });
                });

                $("#state").on("change", function() {
                    var state_id = this.value;
                    $("#city").html("");
                    $.ajax({
                        url: "{{ route('get.city') }}",
                        type: "POST",
                        data: {
                            state_id: state_id,
                            _token: "{{ csrf_token() }}",
                        },
                        dataType: "json",
                        success: function(result) {
                            $.each(result.data, function(key, value) {
                                $("#city").append('<option value="' + value.id + '">' +
                                    value.city + "</option>");
                            });
                        },
                    });
                });

                $("#home_state").on("change", function() {
                    var state_id = this.value;
                    $("#home_city").html("");
                    $.ajax({
                        url: "{{ route('get.city') }}",
                        type: "POST",
                        data: {
                            state_id: state_id,
                            _token: "{{ csrf_token() }}",
                        },
                        dataType: "json",
                        success: function(result) {
                            $.each(result.data, function(key, value) {
                                $("#home_city").append('<option value="' + value.id + '">' +
                                    value.city + "</option>");
                            });
                        },
                    });
                });
            });
        </script>

        <script src="{{ asset('public/assets/js/jquery-ui.js') }}"></script>
        <script src="{{ asset('public/assets/js/repeater.js') }}"></script>
        <script>
            var selector = "body";
            if ($(selector + " .repeater").length) {
                var $dragAndDrop = $("body .repeater tbody").sortable({
                    handle: '.sort-handler'
                });
                var $repeater = $(selector + ' .repeater').repeater({
                    initEmpty: false,
                    defaultValues: {
                        'status': 1
                    },
                    show: function() {
                        $(this).slideDown();
                        var file_uploads = $(this).find('input.multi');
                        if (file_uploads.length) {
                            $(this).find('input.multi').MultiFile({
                                max: 3,
                                accept: 'png|jpg|jpeg',
                                max_size: 2048
                            });
                        }
                        if ($('.select2').length) {
                            $('.select2').select2();
                        }

                    },
                    hide: function(deleteElement) {
                        if (confirm('Are you sure you want to delete this element?')) {
                            if ($('.disc_qty').length < 6) {
                                $(".add-row").show();

                            }
                            $(this).slideUp(deleteElement);
                            $(this).remove();

                            var inputs = $(".amount");
                            var subTotal = 0;
                            for (var i = 0; i < inputs.length; i++) {
                                subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
                            }
                            $('.subTotal').html(subTotal.toFixed(2));
                            $('.totalAmount').html(subTotal.toFixed(2));
                        }
                    },
                    ready: function(setIndexes) {
                        $dragAndDrop.on('drop', setIndexes);
                    },
                    isFirstItemUndeletable: true
                });
                var value = $(selector + " .repeater").attr('data-value');

                if (typeof value != 'undefined' && value.length != 0) {
                    value = JSON.parse(value);
                    $repeater.setList(value);
                }

            }

            $(".add-row").on('click', function(event) {
                var $length = $('.disc_qty').length;
                if ($length == 5) {
                    $(this).hide();
                }
            });
            $(".desc_delete").on('click', function(event) {

                var $length = $('.disc_qty').length;
            });
        </script>
    @endif
    <script type="text/javascript">
        $(document).on('change', '#branch_id', function() {
            var branch_id = $(this).val();
            getDepartment(branch_id);
        });

        function getDepartment(branch_id) {
            var data = {
                "branch_id": branch_id,
                "_token": "{{ csrf_token() }}",
            }

            $.ajax({
                url: '{{ route('employee.getdepartment') }}',
                method: 'POST',
                data: data,
                success: function(data) {
                    $('#department_id').empty();
                    $('#department_id').append(
                        '<option value="" disabled>{{ __('Select any Department') }}</option>');

                    $.each(data, function(key, value) {
                        $('#department_id').append('<option value="' + key + '">' + value +
                            '</option>');
                    });
                    $('#department_id').val('');
                }
            });
        }
    </script>
    <script type="text/javascript">
        function getDesignation(did) {
            $.ajax({
                url: '{{ route('employee.json') }}',
                type: 'POST',
                data: {
                    "department_id": did,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    $('#designation_id').empty();
                    $('#designation_id').append(
                        '<option value="">{{ __('Select any Designation') }}</option>');

                    $.each(data, function(key, value) {
                        var select = '';
                        {{--
                            if (key == '{{ $employeeDetails->designation }}') {
                                select = 'selected';
                            }
                        --}}

                        $('#designation_id').append('<option value="' + key + '"  ' + select + '>' +
                            value + '</option>');
                    });
                }
            });
        }

        $(document).ready(function() {
            var d_id = $('#department_id').val();
            {{-- var designation_id = '{{ $employeeDetails->designation }}';--}}
            getDesignation(d_id);
        });

        $(document).on('change', 'select[name=department_id]', function() {
            var department_id = $(this).val();
            getDesignation(department_id);
        });
    </script>
@endpush
