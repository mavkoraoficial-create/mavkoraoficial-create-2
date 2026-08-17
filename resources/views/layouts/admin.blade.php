@extends('adminlte::page')

@section('title')
    @yield('title', config('app.name'))
@stop

@section('content_header')
    @include('partials.breadcrumbs')
@stop

@section('content')

    @yield('content')

@stop

@section('css')

    @vite([
        'resources/css/admin/app.css',
        'resources/js/admin/app.js'
    ])

    @stack('css')

@stop

@section('js')

    @stack('js')

@stop