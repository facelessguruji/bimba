@extends('Template::layouts.app')
@section('panel')
    @include('Template::partials.header')
    @include('Template::partials.breadcrumb')
    @yield('content')

    @include('Template::partials.footer')
@endsection
