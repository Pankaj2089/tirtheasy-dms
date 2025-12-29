@php
$siteUrl = env('APP_URL');
@endphp

@extends('layout.default')
 <link rel="stylesheet" href="{{$siteUrl}}public/assets/css/custom.css">
 
@section('content')
    @include('element.header')

    <div class="calendar-shell d-flex flex-column flex-lg-row">
        <aside class="calendar-shell__sidebar d-none d-lg-block">
            @include('element.sidebar')
        </aside>

        <main class="calendar-shell__body flex-grow-1">
            @yield('calendar-content')
        </main>
    </div>

    @include('element.quick-reservation-modal')
    @include('element.footer')
@endsection

