@extends('layouts.app')


@section('page-title', __('Users'))


@section('action-button')
    <div class="row align-items-center mb-3">
        <div class="col-md-12 d-flex align-items-center  justify-content-end">
            <div class="text-end d-flex all-button-box justify-content-md-end justify-content-center">
                <a href="{{ route('users.list') }}" class="btn btn-sm btn-primary mx-1" data-ajax-popup="true" data-size="md"
                    data-title="Add User" data-toggle="tooltip" title="{{ __('List View') }}"
                    data-bs-original-title="{{ __('List View') }}" data-bs-placement="top" data-bs-toggle="tooltip">
                    <i class="ti ti-menu-2"></i>
                </a>
            </div>

            @if (\Auth::user()->type == 'company')
                <a href="{{ route('userlog.index') }}" class="btn btn-sm btn-primary btn-icon m-1"
                    data-bs-toggle="tooltip"title="{{ __('User Log') }}">
                    <i class="ti ti-user-check"></i>
                </a>
            @endif

            @canany(['create member', 'create user'])
                <div class="text-end d-flex all-button-box justify-content-md-end justify-content-center">
                    <a href="#" class="btn btn-sm btn-primary mx-1" data-ajax-popup="true" data-size="lg"
                        data-title="Add User" data-url="{{ route('users.create') }}" data-toggle="tooltip"
                        title="{{ __('Create New User') }}">
                        <i class="ti ti-plus"></i>
                    </a>
                </div>
            </div>
        </div>
    @endcan

@endsection

@section('breadcrumb')

    <li class="breadcrumb-item">{{ __('Users') }}</li>

@endsection

@section('content')


    <div class="row g-0 pt-0">
        <div class="col-xxl-12">
            <div class="row g-0">

                @foreach ($users as $user)
                    <div class="col-md-6 col-xxl-3 col-lg-4 col-sm-6 border-end border-bottom">
                        <div class="card  shadow-none bg-transparent border h-100 text-center rounded-0">
                            <div class="card-header border-0 pb-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">

                                        <div class="badge p-2 px-3 rounded bg-primary">{{ ucfirst($user->type) }}</div>

                                    </h6>
                                </div>

                                @if (Gate::check('delete member') || Gate::check('delete user'))
                                    <div class="card-header-right">
                                        <div class="btn-group card-option">

                                            @if (Auth::user()->type == 'company')
                                                <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    <i class="ti ti-dots-vertical"></i>
                                                </button>

                                                <div class="dropdown-menu dropdown-menu-end">

                                                    @if ($user->email_verified_at == null || $user->email_verified_at == '')
                                                        <a href="#" class="dropdown-item bs-pass-para "
                                                            data-confirm="{{ __('Are You Sure?') }}"
                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                            data-confirm-yes="verify-form-{{ $user->id }}"
                                                            title="{{ __('Verify Email') }}" data-bs-toggle="tooltip"
                                                            data-bs-placement="top">
                                                            <i class="ti ti-checks"></i>
                                                            {{ __('Verify Email') }}

                                                        </a>
                                                        {!! Form::open([
                                                            'method' => 'POST',
                                                            'route' => ['users.verify', $user->id],
                                                            'id' => 'verify-form-' . $user->id,
                                                        ]) !!}
                                                        {!! Form::close() !!}
                                                    @else
                                                        <a href="#" class="dropdown-item" data-bs-toggle="tooltip"
                                                            data-bs-placement="top" title="{{ __('verified Email') }}"
                                                            data-size="md" data-title="{{ __('verified Email') }}">
                                                            <i class="ti ti-checks "></i>
                                                            {{ __('Verified Email') }}
                                                        </a>
                                                    @endif

                                                    @canany(['edit member', 'edit user'])
                                                        <a href="{{ route('users.edit', $user->id) }}" class="dropdown-item"
                                                            data-bs-original-title="{{ __('Edit User') }}">
                                                            <i class="ti ti-pencil"></i>
                                                            <span>{{ __('Edit') }}</span>
                                                        </a>
                                                    @endcan

                                                    <a href="#!"
                                                        data-url="{{ route('company.reset', \Crypt::encrypt($user->id)) }}"
                                                        data-ajax-popup="true" data-size="md" class="dropdown-item"
                                                        data-bs-original-title="{{ __('Reset Password') }}"
                                                        data-title="{{ __('Reset Password') }}"
                                                        title="{{ __('Reset Password') }}">
                                                        <i class="ti ti-adjustments"></i>
                                                        <span> {{ __('Reset Password') }}</span>
                                                    </a>

                                                    @canany(['delete member', 'delete user'])
                                                        {!! Form::open([
                                                            'method' => 'DELETE',
                                                            'route' => ['users.destroy', $user->id],
                                                            'id' => 'delete-form-' . $user->id,
                                                        ]) !!}
                                                        <a href="#" class="dropdown-item bs-pass-para-user-delete"
                                                            data-id="{{ $user['id'] }}"
                                                            data-confirm="{{ __('Are You Sure?') }}"
                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                            data-confirm-yes="delete-form-{{ $user->id }}"
                                                            title="{{ __('Delete') }}" data-bs-toggle="tooltip"
                                                            data-bs-placement="top">
                                                            <i class="ti ti-archive"></i>
                                                            <span> {{ __('Delete') }}</span>
                                                        </a>
                                                        {!! Form::close() !!}
                                                    @endcan


                                                    @if ($user->is_enable_login == 1)
                                                        <a href="{{ route('users.login', \Crypt::encrypt($user->id)) }}"
                                                            class="dropdown-item">
                                                            <i class="ti ti-road-sign"></i>
                                                            <span class="text-danger">
                                                                {{ __('Login Disable') }}</span>
                                                        </a>
                                                    @elseif ($user->is_enable_login == 0 && $user->password == null)
                                                        <a href="#"
                                                            data-url="{{ route('users.reset', \Crypt::encrypt($user->id)) }}"
                                                            data-ajax-popup="true" data-size="md"
                                                            class="dropdown-item login_enable"
                                                            data-title="{{ __('New Password') }}" class="dropdown-item">
                                                            <i class="ti ti-road-sign"></i>
                                                            <span class="text-success">
                                                                {{ __('Login Enable') }}</span>
                                                        </a>
                                                    @else
                                                        <a href="{{ route('users.login', \Crypt::encrypt($user->id)) }}"
                                                            class="dropdown-item">
                                                            <i class="ti ti-road-sign"></i>
                                                            <span class="text-success">
                                                                {{ __('Login Enable') }}</span>
                                                        </a>
                                                    @endif

                                                </div>
                                            @else
                                                @if ($user->is_active == 1)
                                                    <button type="button" class="btn dropdown-toggle"
                                                        data-bs-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                        <i class="ti ti-dots-vertical"></i>
                                                    </button>

                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a data-url="{{ route('users.show', $user->id) }}" href="#"
                                                            class="dropdown-item" data-ajax-popup="true" data-size="md"
                                                            data-title="{{ $user->name . __("'s Group") }}">
                                                            <i class="ti ti-eye"></i>
                                                            <span>{{ __('View Groups') }}</span>
                                                        </a>

                                                        @canany(['edit member', 'edit user'])
                                                            <a href="{{ route('users.edit', $user->id) }}"
                                                                class="dropdown-item"
                                                                data-bs-original-title="{{ __('Edit User') }}">
                                                                <i class="ti ti-pencil"></i>
                                                                <span>{{ __('Edit') }}</span>
                                                            </a>
                                                        @endcan

                                                        <a href="#!"
                                                            data-url="{{ route('company.reset', \Crypt::encrypt($user->id)) }}"
                                                            data-ajax-popup="true" data-size="md" class="dropdown-item"
                                                            data-bs-original-title="{{ __('Reset Password') }}"
                                                            data-title="{{ __('Reset Password') }}"
                                                            title="{{ __('Reset Password') }}">
                                                            <i class="ti ti-adjustments"></i>
                                                            <span> {{ __('Reset Password') }}</span>
                                                        </a>

                                                        @canany(['delete member', 'delete user'])
                                                            {!! Form::open([
                                                                'method' => 'DELETE',
                                                                'route' => ['users.destroy', $user->id],
                                                                'id' => 'delete-form-' . $user->id,
                                                            ]) !!}
                                                            <a href="#" class="dropdown-item bs-pass-para"
                                                                data-id="{{ $user['id'] }}"
                                                                data-confirm="{{ __('Are You Sure?') }}"
                                                                data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                data-confirm-yes="delete-form-{{ $user->id }}"
                                                                title="{{ __('Delete') }}" data-bs-toggle="tooltip"
                                                                data-bs-placement="top">
                                                                <i class="ti ti-archive"></i>
                                                                <span> {{ __('Delete') }}</span>
                                                            </a>
                                                            {!! Form::close() !!}
                                                        @endcan


                                                    </div>
                                                @else
                                                    <a href="#" class="action-item"><i class="ti ti-lock"></i></a>
                                                @endif
                                            @endif


                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="card-body full-card">
                                <div class="img-fluid rounded-circle card-avatar">
                                    <img src="{{ !empty($user->avatar)
                                        ? asset('storage/uploads/profile/' . $user->avatar)
                                        : asset('storage/uploads/profile/avatar.png') }}"
                                        class="img-user wid-80 round-img
                                rounded-circle">
                                </div>
                                <h4 class=" mt-3 text-primary">{{ $user->name }}</h4>

                                <small class="text-primary">{{ $user->email }}</small>
                                <p></p>
                                <div class="text-center" data-bs-toggle="tooltip" title="{{ __('Last Login') }}">
                                    {{ !empty($user->last_login_at) ? $user->last_login_at : '' }}
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="col-md-6 col-xxl-3 col-lg-4 col-sm-6 border-end border-bottom">
                    <div class="card  shadow-none bg-transparent border h-100 text-center rounded-0">
                        <div class="card-body border-0 pb-0">
                            <a href="#" class="btn-addnew-project border-0" data-ajax-popup="true" data-size="lg"
                                data-title="Create New User" data-url="{{ route('users.create') }}">
                                <div class="bg-primary proj-add-icon">
                                    <i class="ti ti-plus"></i>
                                </div>
                                <h6 class="mt-4 mb-2">{{ __('New User') }}</h6>
                                <p class="text-muted text-center">{{ __('Click here to add New User') }}</p>
                            </a>
                        </div>
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
