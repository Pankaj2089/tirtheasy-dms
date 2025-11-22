@php
$siteUrl = env('APP_URL');
@endphp
<!doctype html>
<html lang="en">
  <head>

    <meta charset="utf-8">
    <title>Welcome</title>
    <!-- Viewport -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- SEO Meta Tags -->
    <meta name="title" content="Tirtheasy">
    <meta name="description" content=""/>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">


    <!-- Main Theme Styles + Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{$siteUrl}}public/assets/css/style.css">    
    <link rel="stylesheet" href="{{$siteUrl}}public/assets/css/responsive.css">   

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <!-- <link rel="icon" type="image/x-icon" href="{{ asset('public/assets/img/favicon.png') }}" /> -->


    <link href="{{ asset('public/assets/css/sweet-alert.css') }}" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
  </head>
  <body>
    <style>
        html,body{
            overflow-y: hidden;
        }
    </style>
    @yield('content')
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.min.js"></script>
    <script src="{{ asset('public/assets/js/sweet-alert.min.js') }}" ></script>
  </body>
</html>
