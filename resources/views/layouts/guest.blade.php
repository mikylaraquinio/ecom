<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Styles / Scripts -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
    <div class="content-wrapper">
        <!-- Header & Navbar -->
        <header class="header">
            <div class="header-inner">
                <div class="container">
                    @if (Route::has('login')) 
                        <nav class="navbar navbar-expand-lg navbar-dark">
                            <a class="navbar-brand" href="#">FarmSmart</a>
                            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                            <div class="collapse navbar-collapse" id="navbarNav">
                                @auth
                                    <ul class="navbar-nav ms-auto">
                                        <li class="nav-item"><a class="nav-link active" href="{{ url('/welcome') }}">Home</a></li>
                                        <li class="nav-item"><a class="nav-link" href="#">Categories</a></li>
                                        <li class="nav-item"><a class="nav-link" href="#">Cart</a></li>
                                        <li class="nav-item"><a class="nav-link" href="{{ url('/profile') }}">Profile</a></li>
                                    </ul>
                                @else
                                    <ul class="navbar-nav ms-auto">
                                        <li class="nav-item"><a class="nav-link active" href="{{ url('/welcome') }}">Home</a></li>
                                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Log in</a></li>
                                        @if (Route::has('register'))
                                            <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
                                        @endif
                                    </ul>
                                @endauth
                            </div>
                        </nav>
                    @endif
                </div>
            </div>
        </header>

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
            {{ $slot }}
        </div>

        <footer class="footer text-center text-white mt-5 p-3">
            <p>&copy; 2025 All Rights Reserved - <a href="#" class="text-white" target="_blank">BUSINFO-T3</a></p>
        </footer>
    </body>


</html>
