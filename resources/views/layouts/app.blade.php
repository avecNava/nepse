<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="nepse, nepal stock, portfolio, financial management, finance, money, investment, profit, bull, bullish, bear">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'NEPSE.TODAY') }}</title>
    
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link rel="icon" href="{{ URL::to('favicon.ico') }}"> 

    <!-- Scripts -->
    <!-- <script src="{{ asset('js/app.js') }}" defer></script> -->

    <!-- Styles -->
    <!-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> -->
    <link href="{{ URL::to('css/style.css') }}" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Cutive&family=Lora:wght@700&family=Scope+One&display=swap" rel="stylesheet">
    <!-- <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet"> -->
</head>
<body>
    <div id="app">

        <header>
            <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
                <div class="container">
                    <div class="c-header__wrapper">

                        <a href="/" class="c-logo">
                            <img src="{{ URL::to('assets/nepse-today-logo.png') }}" alt="NEPSE.TODAY" class="c-logo__img">
                        </a>

                        <ul class="navbar-nav">
                            <!-- Authentication Links -->
                            @guest
                                
                                @if (Route::has('register'))
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                    </li>
                                @endif

                            @else
                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        {{ Auth::user()->name }}
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                                        document.getElementById('logout-form').submit();">
                                            {{ __('Logout') }}
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </div>
                                </li>
                            @endguest
                        </ul>

                    </div>
                </div>
            </nav>
        </header>

        <main class="c-main">
            <div class="c_content__wrapper">
                @yield('content')
            </div>
        </main>

        <footer>
            <div class="wlnz-footer">
                 © {{ date("Y") }} {{ config('app.name', "NEPSE.TODAY") }}&nbsp; All rights reserved.
            </div>
        </footer>
        
    </div>
    
    <script src="{{ URL::to('js/app.js') }}"></script>
    
</body>
</html>
