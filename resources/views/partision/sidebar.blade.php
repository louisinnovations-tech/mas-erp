@php
    use App\Models\Utility;
    $settings = Utility::settings();

    $company_logo = $settings['company_logo'] ?? '';
    $company_small_logo = $settings['company_small_logo'] ?? '';
    $mode_setting = \App\Models\Utility::mode_layout();
    $logo = Utility::get_file('uploads/logo');

    $company_logo = Utility::get_company_logo();
    $SITE_RTL = !empty($settings['SITE_RTL']) ? $settings['SITE_RTL'] : 'off';

@endphp

<!-- [ Pre-loader ] start -->
<div class="loader-bg">
    <div class="loader-track">
        <div class="loader-fill"></div>
    </div>
</div>
<!-- [ Pre-loader ] End -->

<!-- [ navigation menu ] start -->
<nav
    class="dash-sidebar light-sidebar {{ isset($mode_setting['cust_theme_bg']) && $mode_setting['cust_theme_bg'] == 'on' ? 'transprent-bg' : '' }}">

    <div class="navbar-wrapper">
        <div class="m-header main-logo">
            <a href="{{ route('dashboard') }}" class="b-brand">
                <!-- ========   change your logo hear   ============ -->
                <img src="{{ $logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png') . '?' . time() }}"
                    alt="" class="logo logo-lg" />
            </a>
        </div>
        <div class="navbar-content">
            <ul class="dash-navbar">


                <li class="dash-item dash-hasmenu {{ \Request::route()->getName() == 'dashboard' ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="dash-link ">
                        <span class="dash-micon"><i class="ti ti-home"></i>
                        </span><span class="dash-mtext">{{ __('Dashboard') }}</span>
                        <span class="dash-arrow"></span>
                    </a>
                </li>

                
                @canany(['manage client', 'manage employee'])
                    <li
                        class="dash-item dash-hasmenu {{ Request::route()->getName() == 'users.edit' || Request::route()->getName() == 'users.list' || Request::route()->getName() == 'userlog.index' ? 'active dash-trigger' : '' }}">
                        <a href="#!" class="dash-link ">
                            <span class="dash-micon"><i class="ti ti-users"></i>
                            </span><span class="dash-mtext">{{ __('Stakeholders') }}</span>
                            <span class="dash-arrow"><i data-feather="chevron-right"></i></span>
                        </a>
                        <ul
                            class="dash-submenu {{ Request::segment(1) == 'roles' || Request::segment(1) == 'users' || Request::route()->getName() == 'users.list' ? 'show' : '' }}">

                            @can('manage client')
                                <li
                                    class="dash-item dash-hasmenu {{ in_array(Request::segment(1), ['client', 'client-list']) ? ' active' : '' }}">
                                    <a href="{{ route('client.index') }}" class="dash-link">
                                        <!-- <span class="dash-micon"><i class="ti ti-user-check"></i></span> -->
                                        <span class="dash-mtext">{{ __('Clients') }}</span>
                                    </a>
                                </li>
                            @endcan

                            @can('manage employee')
                                <li class="dash-item dash-hasmenu {{ in_array(Request::segment(1), ['advocate']) ? ' active' : '' }}">
                                    <a href="{{ route('employee.index') }}" class="dash-link">
                                        <!-- <span class="dash-micon"><i class="fa fa-tasks"></i></span> -->
                                        <span class="dash-mtext">{{ __('Employees') }}</span>
                                    </a>
                                </li>
                            @endcan

                            @can('manage advocate')
                                <li class="dash-item dash-hasmenu {{ in_array(Request::segment(1), ['advocate']) ? ' active' : '' }}">
                                    <a href="{{ route('advocate.index') }}" class="dash-link">
                                        <!-- <span class="dash-micon"><i class="fa fa-tasks"></i></span> -->
                                        <span class="dash-mtext">{{ __('Advocates') }}</span>
                                    </a>
                                </li>
                            @endcan
                            
                        </ul>
                    </li>
                @endcan

                @canany(['manage member', 'manage role', 'manage user'])
                    <li
                        class="dash-item dash-hasmenu {{ Request::route()->getName() == 'users.edit' || Request::route()->getName() == 'users.list' || Request::route()->getName() == 'userlog.index' ? 'active dash-trigger' : '' }}">
                        <a href="#!" class="dash-link ">
                            <span class="dash-micon"><i class="ti ti-users"></i>
                            </span><span class="dash-mtext">{{ __('Roles') }}</span>
                            <span class="dash-arrow"><i data-feather="chevron-right"></i></span>
                        </a>
                        <ul
                            class="dash-submenu {{ Request::segment(1) == 'roles' || Request::segment(1) == 'users' || Request::route()->getName() == 'users.list' || Request::segment(1) == 'groups' ? 'show' : '' }}">

                            @can('manage role')
                                <li class="dash-item {{ in_array(Request::segment(1), ['roles', '']) ? ' active' : '' }}">
                                    <a class="dash-link" href="{{ route('roles.index') }}">{{ __('Role') }}</a>
                                </li>
                            @endcan

                            @can('manage permission')
                                <li class="dash-item {{ in_array(Request::segment(1), ['permissions', '']) ? ' active' : '' }}">
                                    <a class="dash-link" href="{{ route('permissions.index') }}">{{ __('Permissions') }}</a>
                                </li>
                            @endcan

                            @can('manage user')
                                <li class="dash-item dash-hasmenu {{ request()->is('users*') ? 'active' : '' }}">
                                    <a href="{{ route('users.list') }}" class="dash-link"><span class="dash-micon"><i
                                                class="ti ti-users"></i></span><span class="dash-mtext">{{ __('User') }}</span>
                                    </a>
                                </li>
                            @endcan

                        </ul>
                    </li>
                @endcan
                {{-- @endif --}}

                @if(\Auth::user()->type == 'employee')
                    <li class="dash-item  {{ Request::segment(1) == 'users' ? 'active ' : '' }}">
                        <a href="{{ route('employee.show', \Crypt::encrypt(\Auth::user()->id)) }}" class="dash-link"><span
                                class="dash-micon"><i class="ti ti-accessible"></i></span><span
                                class="dash-mtext">{{ __('My Profile') }}</span></a>
                        
                        <!-- <a href="{{route('users.edit', Auth::user()->id)}}" class="dash-link"><span
                                class="dash-micon"><i class="ti ti-accessible"></i></span><span
                                class="dash-mtext">{{ __('My Profile') }}</span></a> -->
                    </li>
                @elseif(\Auth::user()->type == 'client')
                    <li class="dash-item {{ Request::segment(1) == 'users' ? 'active ' : '' }}">
                        <!-- <a href="{{ route('client.show', \Crypt::encrypt(\Auth::user()->id)) }}" class="dash-link"><span
                                class="dash-micon"><i class="ti ti-home"></i></span><span
                                class="dash-mtext">{{ __('My Profile') }}</span></a> -->

                        <a href="{{route('users.edit', Auth::user()->id)}}" class="dash-link"><span
                                class="dash-micon"><i class="ti ti-home"></i></span><span
                                class="dash-mtext">{{ __('My Profile') }}</span></a>

                    </li>
                @endif

                @can('manage case')
                    <li class="dash-item dash-hasmenu {{ in_array(Request::segment(1), ['cases']) ? ' active' : '' }}">
                        <a href="{{ route('cases.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-file-text"></i></span>
                            <span class="dash-mtext">{{ __('Case Register') }}</span>
                        </a>
                    </li>
                @endcan

                @canany(['manage cause', 'manage practice areas', 'manage court', 'manage highcourt', 'manage bench'])

                    <li class="dash-item dash-hasmenu {{ in_array(Request::segment(1), ['cause']) ? ' active' : '' }}">
                        <a href="#!" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-clipboard-list"></i></span>
                            <span class="dash-mtext">{{ __('Law Management') }}</span>
                            <span class="dash-arrow"><i data-feather="chevron-right"></i></span>
                        </a>
                        <ul class="dash-submenu">
                            
                            <li class="dash-item {{ request()->is('*cause*') ? ' active' : '' }}">
                                <a class="dash-link" href="{{ route('cause.index') }}">{{ __('Causes') }}</a>
                            </li>

                            @can('manage practice areas')
                                <li class="dash-item ">
                                    <a class="dash-link" href="{{ route('practice-area.index') }}">{{ __('Practice Areas') }}</a>
                                </li>
                            @endcan

                            @canany(['manage court', 'manage highcourt', 'manage bench'])
                                <li class="dash-item dash-hasmenu">
                                    <a class="dash-link" href="#">{{ __('Courts Categories') }}
                                        <span class="dash-arrow"><i data-feather="chevron-right"></i></span>
                                    </a>
                                    <ul class="dash-submenu">
                                        @can('manage court')
                                            <li class="dash-item">
                                                <a class="dash-link" href="{{ route('courts.index') }}">{{ __('Courts/Tribunal') }}</a>
                                            </li>
                                        @endcan
                                        @can('manage highcourt')
                                            <li class="dash-item">
                                                <a class="dash-link" href="{{ route('highcourts.index') }}">{{ __('High Court') }}</a>
                                            </li>
                                        @endcan
                                        @can('manage bench')
                                            <li class="dash-item">
                                                <a class="dash-link" href="{{ route('bench.index') }}">{{ __('Circuit/Devision') }}</a>
                                            </li>
                                        @endcan
                                        
                                    </ul>
                                </li>
                            @endcan
                           
                        </ul>
                    </li>
                @endcan

                @canany(['manage bill'])
                    <li class="dash-item dash-hasmenu {{ in_array(Request::segment(1), ['bills']) ? ' active' : '' }}">
                        <a href="#!" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-file-analytics"></i></span>
                            <span class="dash-mtext">{{ __('Finance') }}</span>
                            <span class="dash-arrow"><i data-feather="chevron-right"></i></span>
                        </a>
                        <ul class="dash-submenu">
                            @can('manage bill')
                                <li class="dash-item dash-hasmenu {{ in_array(Request::segment(1), ['bills']) ? ' active' : '' }}">
                                    <a href="{{ route('bills.index') }}" class="dash-link">
                                        <span class="dash-mtext">{{ __('Bills / Invoices') }}</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
               

                @can('manage todo')
                    <li class="dash-item dash-hasmenu {{ in_array(Request::segment(1), ['todo']) ? ' active' : '' }}">
                        <a href="{{ route('to-do.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-file-plus"></i></span>
                            <span class="dash-mtext">{{ __('Tasks') }}</span>
                        </a>
                    </li>
                @endcan
               
                {{-- @can('manage appointment')
                    <li class="dash-item dash-hasmenu {{ in_array(Request::segment(1), ['appointments']) ? ' active' : '' }}">
                        <a href="{{ route('appointments.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-bookmarks" aria-hidden="true"></i>

                            </span>
                            <span class="dash-mtext">{{ __('Appointment') }}</span>
                        </a>
                    </li>
                @endcan --}}

                @can('manage diary')
                    <li
                        class="dash-item dash-hasmenu {{ in_array(Request::segment(1), ['casediary']) || in_array(Request::segment(1), ['calendar']) ? ' active' : '' }}">
                        <a href="{{ route('casediary.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-license"></i></span>
                            <span class="dash-mtext">{{ __('Calendar') }}</span>
                        </a>
                    </li>
                @endcan

                @if (\Auth::user()->type == 'company' || \Auth::user()->type == 'employee' || \Auth::user()->type == 'advocate')
                            
                            
                    <li class="dash-item dash-hasmenu">
                        @if (\Auth::user()->type == 'employee' || \Auth::user()->type == 'advocate')
                            <a href="#!" class="dash-link"><span class="dash-micon"><i
                                        class="ti ti-hand-three-fingers"></i></span><span
                                    class="dash-mtext">{{ __('HRM') }}</span><span class="dash-arrow"><i
                                        data-feather="chevron-right" data-bs-toggle="tooltip"
                                        data-bs-original-title="{{ __('HR Management') }}"></i></span></a>
                        @else
                            <a href="#!" class="dash-link"><span class="dash-micon"><i
                                        class="ti ti-hand-three-fingers"></i></span><span
                                    class="dash-mtext">{{ __('HR') }}</span><span class="dash-arrow"><i
                                        data-feather="chevron-right"></i></span></a>
                        @endif
                        <ul class="dash-submenu">

                            @can('manage timesheet')
                                <li
                                    class="dash-item {{ in_array(Request::segment(1), ['timesheet']) ? ' active' : '' }}">
                                    <a href="{{ route('timesheet.index') }}" class="dash-link">{{ __('Timesheet') }}
                                        <!-- <span class="dash-micon"><i class="ti ti-list-check"></i></span> -->
                                        <!-- <span class="dash-mtext">{{ __('Timesheet') }}</span> -->
                                    </a>
                                </li>
                            @endif

                            @can('manage leave')
                                <li class="dash-item">
                                    <a class="dash-link" href="{{ route('leave.index') }}">{{ __('Leave') }}</a>
                                </li>
                            @endif

                            @can('manage monthly attendance')
                                <li class="dash-item">
                                    <a class="dash-link"
                                        href="{{ route('monthly.attendance') }}">{{ __('Monthly Attendance') }}</a>
                                </li>
                            @endif

                            @can('manage attendance')
                                <li class="dash-item">
                                    <a class="dash-link"
                                        href="{{ route('attendance.index') }}">{{ __('Employees Data') }}</a>
                                </li>
                            @endif

                            @can('manage training')
                                <li
                                    class="dash-item {{ Request::segment(1) == 'training' ? 'active' : '' }}">
                                    <a class="dash-link"
                                        href="{{ route('training.index') }}">{{ __('Trainings') }}</a>
                                </li>
                            @endcan

                            @can('manage contract')
                                <li class="dash-item">
                                    <a class="dash-link" href="{{ route('contracts.index') }}">{{ __('Recruitments') }}</a>
                                </li>
                            @endcan
                            
                            @can('manage meeting')
                                <li class="dash-item">
                                    <a class="dash-link"
                                        href="{{ !empty(\Auth::user()->getDefualtViewRouteByModule('meeting')) ? route(\Auth::user()->getDefualtViewRouteByModule('meeting')) : route('meeting.index') }}">
                                        {{ __('Staff Meetings') }}
                                    </a>
                                </li>
                            @endif
                                                        
                            @can('manage company policy')
                                <li class="dash-item">
                                    <a class="dash-link"
                                        href="{{ route('company-policy.index') }}">{{ __('Company Policy') }}</a>
                                </li>
                            @endif


                            <li class="dash-item dash-hasmenu">
                                <a class="dash-link" href="#">{{ __('HR Admin Setup') }}
                                    <span class="dash-arrow"><i data-feather="chevron-right"></i></span>
                                </a>
                                <ul class="dash-submenu">
                                   
                                    @can('manage award')
                                        <li class="dash-item">
                                            <a class="dash-link" href="{{ route('award.index') }}">{{ __('Award') }}</a>
                                        </li>
                                    @endcan

                                    @can('manage transfer')
                                        <li class="dash-item">
                                            <a class="dash-link"
                                                href="{{ route('transfer.index') }}">{{ __('Transfer') }}</a>
                                        </li>
                                    @endcan

                                    @can('manage resignation')
                                        <li class="dash-item">
                                            <a class="dash-link"
                                                href="{{ route('resignation.index') }}">{{ __('Resignation') }}</a>
                                        </li>
                                    @endcan

                                    @can('manage trip')
                                        <li class="dash-item">
                                            <a class="dash-link" href="{{ route('trip.index') }}">{{ __('Trip') }}</a>
                                        </li>
                                    @endcan

                                    @can('manage promotion')
                                        <li class="dash-item">
                                            <a class="dash-link"
                                                href="{{ route('promotion.index') }}">{{ __('Promotion') }}</a>
                                        </li>
                                    @endcan

                                    @can('manage complaint')
                                        <li class="dash-item">
                                            <a class="dash-link"
                                                href="{{ route('complaint.index') }}">{{ __('Complaints') }}</a>
                                        </li>
                                    @endcan

                                    @can('manage warning')
                                        <li class="dash-item">
                                            <a class="dash-link" href="{{ route('warning.index') }}">{{ __('Warning') }}</a>
                                        </li>
                                    @endcan

                                    @can('manage termination')
                                        <li class="dash-item">
                                            <a class="dash-link"
                                                href="{{ route('termination.index') }}">{{ __('Termination') }}</a>
                                        </li>
                                    @endcan

                                    @can('manage holiday')
                                        <li class="dash-item ">
                                            <a class="dash-link" href="{{ route('holiday.index') }}">{{ __('Holiday') }}</a>
                                        </li>
                                    @endif

                                    @can('manage asset')
                                        <li class="dash-item">
                                            <a class="dash-link" href="{{ route('account-assets.index') }}">{{ __('Asset') }}</a>
                                        </li>
                                    @endcan


                                    
                                </ul>
                            </li>
                            
                            @canany(['manage indicator','manage appraisal','manage goaltracking'])
                            <li class="dash-item dash-hasmenu">
                                <a class="dash-link" href="#">{{ __('Performance') }}<span class="dash-arrow"><i
                                            data-feather="chevron-right"></i></span></a>
                                <ul class="dash-submenu">
                                    @can('manage indicator')
                                        <li class="dash-item">
                                            <a class="dash-link"
                                                href="{{ route('indicator.index') }}">{{ __('Indicator') }}</a>
                                        </li>
                                    @endcan

                                    @can('manage appraisal')
                                        <li class="dash-item">
                                            <a class="dash-link"
                                                href="{{ route('appraisal.index') }}">{{ __('Appraisal') }}</a>
                                        </li>
                                    @endcan

                                    @can('manage goaltracking')
                                        <li class="dash-item">
                                            <a class="dash-link"
                                                href="{{ route('goaltracking.index') }}">{{ __('Goal Tracking') }}</a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                            @endcan

                            

                            {{--
                                <li
                                    class="dash-item dash-hasmenu {{ Request::segment(1) == 'training' ? 'active dash-trigger' : '' }}">
                                    <a class="dash-link" href="#">{{ __('Training') }}<span class="dash-arrow"><i
                                                data-feather="chevron-right"></i></span></a>
                                    <ul class="dash-submenu">
                                        
                                        @can('manage trainer')
                                            <li
                                                class="dash-item {{ Request::segment(1) == 'trainer' ? 'active' : '' }}">
                                                <a class="dash-link" href="{{ route('trainer.index') }}">{{ __('Trainer') }}</a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            --}}

                        </ul>
                    </li>
                @endif

                @can('manage contract type')
                    <li class="dash-item">
                        <a class="dash-link" href="{{ route('contract-types.index') }}">{{ __('Contract Types') }}</a>
                    </li> 
                @endcan

                @can('manage document upload')
                    <li class="dash-item">
                        <a class="dash-link" href="{{ route('document-upload.index') }}">{{ __('Contracts') }}</a>
                    </li>
                @endcan


                {{--
                    @if (Auth::user()->can('manage support'))
                        <li class="dash-item dash-hasmenu">
                            <a href="#!" class="dash-link">
                                <span class="dash-micon"><i class="ti ti-ticket"></i>
                                </span><span class="dash-mtext">{{ __('Support Ticket') }}</span>
                                <span class="dash-arrow"><i data-feather="chevron-right"></i></span>
                            </a>
                            <ul class="dash-submenu">
                                <li class="dash-item dash-hasmenu {{ request()->is('*ticket*') ? ' active' : '' }}">
                                    <a class="dash-link" href="{{ route('tickets.index') }}">{{ __('Tickets') }}</a>
                                </li>
                                <li class="dash-item dash-hasmenu {{ request()->is('*faq*') ? ' active' : '' }}">
                                    <a class="dash-link" href="{{ route('faq.index') }}">{{ __('FAQ') }}</a>
                                </li>
                                <li class="dash-item dash-hasmenu {{ request()->is('*knowledge*') ? ' active' : '' }}">
                                    <a class="dash-link" href="{{ route('knowledge') }}">{{ __('Knowledge Base') }}</a>
                                </li>
                            </ul>
                        </li>
                    @endif 
                --}}

                
                @can('manage crm')
                    <li class="dash-item dash-hasmenu">
                        <a href="#!" class="dash-link ">
                            <span class="dash-micon"><i class="ti ti-affiliate"></i></span>
                            <span class="dash-mtext">{{ __('CRM') }}</span>
                            <span class="dash-arrow"><i data-feather="chevron-right"></i></span>
                        </a>
                        <ul class="dash-submenu">

                            @can('manage lead')
                                <li
                                    class="dash-item dash-hasmenu {{ Request::segment(1) == 'lead' ? 'active' : '' }}">
                                    <a class="dash-link" href="{{ route('lead.index') }}">{{ __('Lead') }}</a>
                                </li>
                            @endcan

                            @can('manage lead stage')
                                <li class="dash-item ">
                                    <a class="dash-link"
                                        href="{{ route('leadStage.index') }}">{{ __('Lead Stage') }}</a>
                                </li>
                            @endcan

                            @can('manage deal')
                                <li
                                    class="dash-item dash-hasmenu {{ Request::segment(1) == 'deal' ? 'active' : '' }}">
                                    <a class="dash-link" href="{{ route('deal.index') }}">{{ __('Deal') }}</a>
                                </li>
                            @endif

                            @can('manage deal stage')
                                <li class="dash-item ">
                                    <a class="dash-link"
                                        href="{{ route('dealStage.index') }}">{{ __('Deal Stage') }}</a>
                                </li>
                            @endcan
                            
                        </ul>
                    </li>
                @endcan

                @can('manage virtual meeting')
                    <li class="dash-item {{ Request::route()->getName() == 'zoom-meetings.index' ? ' active' : '' }}">
                        <a class="dash-link" href="{{ route('zoom-meetings.index') }}">
                            <span class="dash-micon"><i class="ti ti-settings"></i></span><span
                                class="dash-mtext">{{ __('Virtual Meetings') }}</span>
                        </a>
                    </li>
                @endcan

                @can('manage group')
                    <li class="dash-item {{ in_array(Request::segment(1), ['groups']) ? ' active' : '' }}">
                        <a class="dash-link" href="{{ route('groups.index') }}">
                            <span class="dash-micon"><i class="ti ti-circle-square"></i></span>
                            {{ __('Group') }}
                        </a>
                    </li>
                @endcan

                @if (\Auth::user()->type == 'company')
                    <li class="dash-item dash-hasmenu">
                        <a href="#!" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-circle-square"></i></span>
                            <span class="dash-mtext">{{ __('Constant') }}</span>
                            <span class="dash-arrow"><i data-feather="chevron-right"></i></span>
                        </a>
                        <ul class="dash-submenu">
                            
                            {{--
                                <li class="dash-item dash-hasmenu">
                                    <a class="dash-link" href="#">{{ __('PreSale') }}<span class="dash-arrow"><i
                                                data-feather="chevron-right"></i></span></a>
                                    <ul class="dash-submenu">
                                        <li class="dash-item">
                                            <a class="dash-link"
                                                href="{{ route('pipeline.index') }}">{{ __('Pipeline') }}</a>
                                        </li>
                                        <li class="dash-item">
                                            <a class="dash-link"
                                                href="{{ route('leadStage.index') }}">{{ __('Lead Stage') }}</a>
                                        </li>
                                        <li class="dash-item">
                                            <a class="dash-link"
                                                href="{{ route('dealStage.index') }}">{{ __('Deal Stage') }}</a>
                                        </li>
                                        <li class="dash-item">
                                            <a class="dash-link"
                                                href="{{ route('source.index') }}">{{ __('Source') }}</a>
                                        </li>
                                        <li class="dash-item">
                                            <a class="dash-link" href="{{ route('label.index') }}">{{ __('Label') }}</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="dash-item">
                                    <a class="dash-link"
                                        href="{{ route('projectStage.index') }}">{{ __('Project Task Stage') }}</a>
                                </li>
                                <li class="dash-item">
                                    <a class="dash-link" href="{{ route('taxRate.index') }}">{{ __('Tax Rate') }}</a>
                                </li>
                                <li class="dash-item">
                                    <a class="dash-link" href="{{ route('unit.index') }}">{{ __('Unit') }}</a>
                                </li>
                            --}}
                            <li class="dash-item">
                                <a class="dash-link" href="{{ route('category.index') }}">{{ __('Category') }}</a>
                            </li>
                            {{-- <li class="dash-item">
                                <a class="dash-link"
                                    href="{{ route('paymentMethod.index') }}">{{ __('Payment Method') }}</a>
                            </li>
                            
                            --}}
                            <li class="dash-item">
                                <a class="dash-link" href="{{ route('branch.index') }}">{{ __('Branch') }}</a>
                            </li>
                            <li class="dash-item">
                                <a class="dash-link" href="{{ route('goaltype.index') }}">{{ __('Goal Type') }}</a>
                            </li>
                            @can('manage tax')
                                <li class="dash-item ">
                                    <a class="dash-link" href="{{ route('taxs.index') }}">{{ __('Tax') }}</a>
                                </li>
                            @endcan

                            @can('manage doctype')
                                <li class="dash-item ">
                                    <a class="dash-link"
                                        href="{{ route('doctype.index') }}">{{ __('Document Type') }}</a>
                                </li>

                                <li class="dash-item ">
                                    <a class="dash-link"
                                        href="{{ route('doctsubype.index') }}">{{ __('Document Sub-type') }}</a>
                                </li>
                            @endcan
                            {{-- <li class="dash-item ">
                                <a class="dash-link" href="{{ route('hearingType.index') }}">{{ __('Hearing Type') }}</a>
                            </li> --}}
                            @can('manage motions')
                                <li class="dash-item ">
                                    <a class="dash-link"
                                        href="{{ route('motions.index') }}">{{ __('Motions Types') }}</a>
                                </li>
                            @endcan
                            
                            @can('manage pipeline')
                                <li class="dash-item ">
                                    <a class="dash-link"
                                        href="{{ route('pipeline.index') }}">{{ __('Pipeline') }}</a>
                                </li>
                            @endcan
                            
                            @can('manage source')
                                <li class="dash-item ">
                                    <a class="dash-link" href="{{ route('source.index') }}">{{ __('Source') }}</a>
                                </li>
                            @endcan

                            @can('manage label')
                                <li class="dash-item ">
                                    <a class="dash-link" href="{{ route('label.index') }}">{{ __('Label') }}</a>
                                </li>
                            @endcan

                            <li class="dash-item {{ request()->is('category*') ? 'active' : '' }}">
                                <a href="{{ route('category.index') }}" class="dash-link">
                                    <!-- <span class="dash-micon"><i class="ti ti-layout-2"></i></span> -->
                                    <span class="dash-mtext">{{ __('Setup') }}</span>
                                </a>
                            </li>
                            <li class="dash-item">
                                <a class="dash-link"
                                    href="{{ route('department.index') }}">{{ __('Department') }}</a>
                            </li>
                            <li class="dash-item">
                                <a class="dash-link"
                                    href="{{ route('designation.index') }}">{{ __('Designation') }}</a>
                            </li>
                            <li class="dash-item">
                                <a class="dash-link"
                                    href="{{ route('salaryType.index') }}">{{ __('Salary Type') }}</a>
                            </li>
                            <li class="dash-item">
                                <a class="dash-link"
                                    href="{{ route('leaveType.index') }}">{{ __('Leave Type') }}</a>
                            </li>
                            <li class="dash-item">
                                <a class="dash-link"
                                    href="{{ route('award-type.index') }}">{{ __('Award Type') }}</a>
                            </li>
                            <li class="dash-item">
                                <a class="dash-link"
                                    href="{{ route('termination-type.index') }}">{{ __('Termination Type') }}</a>
                            </li>
                            <li class="dash-item">
                                <a class="dash-link"
                                    href="{{ route('training-type.index') }}">{{ __('Training Type') }}</a>
                            </li>
                            <li class="dash-item">
                                <a class="dash-link"
                                    href="{{ route('performanceType.index') }}">{{ __('Performance Type') }}</a>
                            </li>
                            <li class="dash-item">
                                <a class="dash-link"
                                    href="{{ route('competencies.index') }}">{{ __('Competencies') }}</a>
                            </li>
                        </ul>
                    </li>
                @endif

                @can('manage document')
                    <li class="dash-item dash-hasmenu {{ in_array(Request::segment(1), ['documents']) ? ' active' : '' }}">
                        <a href="{{ route('documents.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-files"></i></span>
                            <span class="dash-mtext">{{ __('Documents') }}</span>
                        </a>
                    </li>
                @endcan

                @can('manage expense')
                    <li class="dash-item dash-hasmenu {{ in_array(Request::segment(1), ['expenses']) ? ' active' : '' }}">
                        <a href="{{ route('expenses.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-report"></i></span>
                            <span class="dash-mtext">{{ __('Expense') }}</span>
                        </a>
                    </li>
                @endcan

                @can('manage feereceived')
                    <li
                        class="dash-item dash-hasmenu {{ in_array(Request::segment(1), ['fee-receive']) ? ' active' : '' }}">
                        <a href="{{ route('fee-receive.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-receipt-2"></i></span>
                            <span class="dash-mtext">{{ __('Fee Received') }}</span>
                        </a>
                    </li>
                @endcan

                <li class="dash-item {{ \Request::route()->getName() == 'chats' ? ' active' : '' }}">
                    <a href="{{ url('chats') }}"
                        class="dash-link {{ Request::segment(1) == 'chats' ? 'active' : '' }}">
                        <span class="dash-micon"><i class="ti ti-brand-messenger"></i></span><span
                            class="dash-mtext">{{ __('Messenger') }}</span>
                    </a>
                </li>


                @can('manage coupon')
                    <li class="dash-item {{ Request::segment(1) == 'coupons' ? 'active' : '' }}">
                        <a class="dash-link" href="{{ route('coupons.index') }}">
                            <span class="dash-micon"><i class="ti ti-gift"></i></span><span
                                class="dash-mtext">{{ __('Coupons') }}</span>
                        </a>
                    </li>
                @endcan

                @can('manage order')
                    <li class="dash-item {{ Request::segment(1) == 'orders' ? 'active' : '' }}">
                        <a class="dash-link" href="{{ route('order.index') }}">
                            <span class="dash-micon"><i class="ti ti-credit-card"></i></span><span
                                class="dash-mtext">{{ __('Order') }}</span>
                        </a>
                    </li>
                @endcan

                @if (\Auth::user()->type == 'company')
                    @include('landingpage::menu.landingpage')
                @endif

                @can('manage setting')
                    <li class="dash-item {{ Request::route()->getName() == 'admin.settings' ? ' active' : '' }}">
                        <a class="dash-link" href="{{ route('admin.settings') }}">
                            <span class="dash-micon"><i class="ti ti-settings"></i></span><span
                                class="dash-mtext">{{ __('System Settings') }}</span>
                        </a>
                    </li>
                    
                @endcan

            </ul>
        </div>
    </div>
</nav>
<!-- [ navigation menu ] end -->
