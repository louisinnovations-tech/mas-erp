@extends('layouts.app')
@push('script-page')
@endpush
@section('page-title')
    {{__('Practice Area')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Practice Area')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Practice Area')}}</li>
@endsection
@section('action-button')
<div class="text-sm-end d-flex all-button-box justify-content-sm-end">
    @if(\Auth::user()->type=='company')
    <a href="#" class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="modal"
    data-bs-target="#exampleModal" data-url="{{ route('practice-area.create') }}"
    data-bs-whatever="{{__('Create New Practice Area')}}"> <span class="text-white"> 
        <i class="ti ti-plus text-white" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Create') }}"></i></span>
    </a>

    @endif
</div>
@endsection
@section('filter')
@endsection
@section('content')    
    <div class="col-xl-12 px-4 py-4">
        <div class="card">
            <div class="card-header card-body table-border-style">
                <!-- <h5></h5> -->
                <div class="table-responsive">
                    <table class="table" id="pc-dt-simple">
                        <thead>
                            <tr>
                                <th scope="col">{{__('Practice Area')}}</th>
                                @if(\Auth::user()->type=='company')
                                    <th scope="col" class="text-right">{{__('Action')}}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($practiceAreas as $practiceArea)
                            <tr>
                                <td>{{ $practiceArea->name }}</td>
                                @if(\Auth::user()->type=='company')
                                    <td class="text-right">
                                        <div class="action-btn bg-info ms-2">
                                        <a href="#" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="modal"
                                            data-bs-target="#exampleModal" data-url="{{ route('practice-area.edit',$practiceArea->id) }}"
                                            data-bs-whatever="{{__('Edit Practice Area')}}" > <span class="text-white"> <i
                                                    class="ti ti-edit" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Edit') }}"></i></span></a>
                                        </div>
                                        <div class="action-btn bg-danger ms-2">
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['practice-area.destroy', $practiceArea->id]]) !!}
                                                <a href="#!" class="mx-3 btn btn-sm  align-items-center show_confirm ">
                                                    <i class="ti ti-trash text-white" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Delete') }}"></i>
                                                </a>
                                            {!! Form::close() !!}

                                            
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

