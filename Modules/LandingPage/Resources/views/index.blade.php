@extends('landingpage::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('landingpage.name') !!}</p>
@endsection
