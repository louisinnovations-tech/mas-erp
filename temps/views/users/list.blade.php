@extends('layouts.app')
@if (Auth::user()->type == 'company')
    @section('page-title', __('Users'))
@else
    @section('page-title', __('Employees'))
@endif

@section('action-button')
    <div class="row align-items-end mb-3">
        <div class="col-md-12 d-flex justify-content-sm-end">
            <div class="text-end d-flex all-button-box justify-content-md-end justify-content-center">
                <a href="{{ route('users.index') }}" class="btn btn-sm btn-primary mx-1" data-ajax-popup="true" data-size="md"
                    data-title="Add Employee" data-toggle="tooltip" title="{{ __('Grid View') }}"
                    data-bs-original-title="{{ __('Grid View') }}" data-bs-placement="top" data-bs-toggle="tooltip">
                    <i class="ti ti-border-all"></i>
                </a>
            </div>


            @if (Auth::user()->can('create member') ||
                    Auth::user()->can('create user') ||
                    (\Auth::user()->can('manage crm') && \Auth::user()->can('manage support')))
                <div class="text-end d-flex all-button-box justify-content-md-end justify-content-center">
                    <a href="#" class="btn btn-sm btn-primary mx-1" data-ajax-popup="true" data-size="lg"
                        data-title="Add Employee" data-url="{{ route('users.create') }}" data-toggle="tooltip"
                        title="{{ __('Create New Employee') }}">
                        <i class="ti ti-plus"></i>
                    </a>
                </div>
            @endif
        </div>
    </div>


@endsection

@section('breadcrumb')

    <li class="breadcrumb-item">{{ __('Employees') }}</li>

@endsection

@section('content')
    <div class="row p-0">
        <div class="col-xl-12">
            <div class="">
                <div class="card-header card-body table-border-style">
                    <h5></h5>
                    <div class="table-responsive">
                        <table class="table dataTable data-table user-datatable ">
                            <thead>
                                <tr>
                                    <th>{{ __('#') }}</th>

                                    <th>{{ __('Name') }}</th>
                                    @if (Auth::user()->type == 'company')
                                        <th>{{ __('Designation') }}</th>
                                    @endif
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Phone Number') }}</th>

                                    <th width="100px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($users as $key => $user)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>

                                        <td>{{ $user->name }}</td>
                                        @if (Auth::user()->type == 'company')
                                            <td>{{ $user->type }}</td>
                                        @endif
                                        <td>{{ $user->email }}</td>

                                        <td>{{ $user->user_detail->mobile_number ?? '-' }}</td>


                                        <td>

                                            @if ($user->email_verified_at == null || $user->email_verified_at == '')
                                                <div class="action-btn bg-light-secondary ms-2">
                                                    <a href="#"
                                                        class="mx-3 btn btn-sm d-inline-flex align-items-center bs-pass-para "
                                                        data-confirm="{{ __('Are You Sure?') }}"
                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="verify-form-{{ $user->id }}"
                                                        title="{{ __('Verify Email') }}" data-bs-toggle="tooltip"
                                                        data-bs-placement="top">
                                                        <i class="ti ti-checks"></i>
                                                    </a>
                                                    {!! Form::open([
                                                        'method' => 'POST',
                                                        'route' => ['users.verify', $user->id],
                                                        'id' => 'verify-form-' . $user->id,
                                                    ]) !!}
                                                    {!! Form::close() !!}
                                                </div>
                                            @else
                                                <div class="action-btn bg-primary ms-2">
                                                    <a href="#"
                                                        class="mx-3 btn btn-sm d-inline-flex align-items-center"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="{{ __('verified Email') }}" data-size="md"
                                                        data-title="{{ __('verified Email') }}">
                                                        <i class="ti ti-checks text-white"></i>
                                                    </a>
                                                </div>
                                            @endif

                                            @if (Auth::user()->type == 'company' || (\Auth::user()->can('manage crm') || \Auth::user()->can('manage support')))
                                                <div class="action-btn bg-light-secondary ms-2">
                                                    <a href="{{ route('users.detail', $user->id) }}" href="#"
                                                        class="mx-3 btn btn-sm d-inline-flex align-items-center"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="{{ __('View Details') }}" data-size="md"
                                                        data-title="{{ __('View Details') }}">
                                                        <i class="ti ti-eye "></i>
                                                    </a>
                                                </div>
                                            @endif
                                            @if (Auth::user()->type == 'company')
                                                <div class="action-btn bg-light-secondary ms-2">
                                                    <a data-url="{{ route('users.show', $user->id) }}" href="#"
                                                        class="mx-3 btn btn-sm d-inline-flex align-items-center"
                                                        data-ajax-popup="true" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="{{ __('View Groups') }}"
                                                        data-size="md" data-title="{{ $user->name . __("'s Group") }}"><i
                                                            class="ti ti-eye "></i>

                                                    </a>
                                                </div>
                                            @endif

                                            @if (Auth::user()->type == 'company')
                                                <div class="action-btn bg-light-secondary ms-2">

                                                    @if ($user->is_enable_login == 1)
                                                        <a href="{{ route('users.login', \Crypt::encrypt($user->id)) }}"
                                                            data-bs-toggle="tooltip" data-tooltip="Login Disable"
                                                            title="{{ __('Login Disable') }}"
                                                            class="mx-3 btn btn-sm d-inline-flex align-items-center">
                                                            <i class="ti ti-road-sign"></i>

                                                        </a>
                                                    @elseif ($user->is_enable_login == 0 && $user->password == null)
                                                        <a href="#"
                                                            data-url="{{ route('users.reset', \Crypt::encrypt($user->id)) }}"
                                                            data-bs-toggle="tooltip" data-tooltip="Login Enable"
                                                            title="{{ __('Login Enable') }}" data-ajax-popup="true"
                                                            data-size="md"
                                                            class="mx-3 btn btn-sm d-inline-flex align-items-center login_enable"
                                                            data-title="{{ __('New Password') }}"
                                                            class="mx-3 btn btn-sm d-inline-flex align-items-center">
                                                            <i class="ti ti-road-sign"></i>
                                                        </a>
                                                    @else
                                                        <a href="{{ route('users.login', \Crypt::encrypt($user->id)) }}"
                                                            data-bs-toggle="tooltip" data-tooltip="Login Enable"
                                                            title="{{ __('Login Enable') }}"
                                                            class="mx-3 btn btn-sm d-inline-flex align-items-center">
                                                            <i class="ti ti-road-sign"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            @endif

                                            @if (Auth::user()->can('edit member') ||
                                                    Auth::user()->can('edit user') ||
                                                    (\Auth::user()->can('manage crm') || \Auth::user()->can('manage support')))
                                                <div class="action-btn bg-light-secondary ms-2">
                                                    <a href="{{ route('users.edit', $user->id) }}"
                                                        class="mx-3 btn btn-sm d-inline-flex align-items-center "
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="{{ __('Edit') }}">

                                                        <i class="ti ti-edit "></i>

                                                    </a>
                                                </div>
                                            @endif


                                            <div class="action-btn bg-light-secondary ms-2">
                                                <a href="#"
                                                    data-url="{{ route('company.reset', \Crypt::encrypt($user->id)) }}"
                                                    class="mx-3 btn btn-sm d-inline-flex align-items-center "
                                                    data-tooltip="Edit" data-ajax-popup="true"
                                                    data-title="{{ __('Reset Password') }}" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="{{ __('Reset Password') }}">

                                                    <i class="ti ti-key "></i>

                                                </a>
                                            </div>


                                            @if (Auth::user()->can('delete member') ||
                                                    Auth::user()->can('delete user') ||
                                                    (\Auth::user()->can('manage crm') || \Auth::user()->can('manage support')))
                                                <div class="action-btn bg-light-secondary ms-2">
                                                    <a href="#"
                                                        class="mx-3 btn btn-sm d-inline-flex align-items-center bs-pass-para "
                                                        data-confirm="{{ __('Are You Sure?') }}"
                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="delete-form-{{ $user->id }}"
                                                        title="{{ __('Delete') }}" data-bs-toggle="tooltip"
                                                        data-bs-placement="top">
                                                        <i class="ti ti-trash"></i>
                                                    </a>
                                                </div>
                                            @endif

                                            {!! Form::open([
                                                'method' => 'DELETE',
                                                'route' => ['users.destroy', $user->id],
                                                'id' => 'delete-form-' . $user->id,
                                            ]) !!}
                                            {!! Form::close() !!}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('custom-script')
    <script>
        $(document).on('change', '#password_switch', function() {
            if ($(this).is(':checked')) {
                $('.ps_div').removeClass('d-none');
                $('#password').attr("required", true);

            } else {
                $('.ps_div').addClass('d-none');
                $('#password').val(null);
                $('#password').removeAttr("required");
            }
        });
        $(document).on('click', '.login_enable', function() {
            setTimeout(function() {
                $('.modal-body').append($('<input>', {
                    type: 'hidden',
                    val: 'true',
                    name: 'login_enable'
                }));
            }, 2000);
        });
    </script>
@endpush