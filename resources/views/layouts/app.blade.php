<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="nepse, nepal stock, portfolio, financial management, finance, money, investment, profit, bull, bullish, bear">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'NEPSE.TODAY') }}</title>
    <link rel="icon" href="{{ URL::to('favicon.ico') }}"> 

    <!-- Scripts -->
    <!-- <script src="{{ asset('js/app.js') }}" defer></script> -->

    <!-- Styles -->
    <!-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> -->
    <link href="{{ URL::to('css/style.css') }}" rel="stylesheet">
    <!-- <link rel="preconnect" href="https://fonts.gstatic.com"> -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Cutive&family=Lora:wght@700&family=Scope+One&display=swap" rel="stylesheet">
    @yield('custom_css')
</head>
<body>
    <div id="notice">
        @yield('notice')
    </div>
    <div id="app">

    <header class="c-header">

    <div class="c-header__wrapper">

    <div class="c-logo">
        <a href="/">
            <img src="{{ URL::to('assets/nepse-today-logo.png') }}" alt="NEPSE.TODAY" class="c-logo__img">
        </a>
    </div>                

    <div class="c-nav">
        
        @if(Auth::check())
        <div class="c-nav__user__wrapper">
            <span class="c-nav__user">
                {{ optional(Auth::user())->name }}
            </span>
            <span class="c_nav__logout">
                <a href="{{ route('logout') }}"
                onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">
                    {{ __('Logout') }}
                </a>
            </span>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
        @else
        <div class="links">
            <a href="{{url('feedbacks')}}">Feedbacks</a>
            @if (Route::has('login'))
                    <a href="{{ route('login') }}">Login</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}">Register</a>
                @endif
                @endif
        </div>
        @endif
        
        <div class="a_page_header">
            <h1>@yield('header_title')</h1>
        </div>

        @auth

        <nav class="c-nav__list">
            <ul class="navbar-nav">
                <li><a href="{{ url('portfolio') }}">Portfolio</a></li>
                <li><a href="{{ url('portfolio/new') }}">New Share</a></li>
                <li><a href="{{ url('sales') }}">Sales</a></li>
                <li><a href="{{ url('shareholders') }}">Shareholder</a></li>
            </ul>
        </nav>
        @endauth

    </div>

</div>
 
</header>

        <main class="c-main">
            <div class="c_content__wrapper">
                @yield('content')
            </div>
        </main>

        <footer class="page-footer">
        
            <section class="links" style="display:@guest none @endguest">

            <div class="feedbacks nav">  
                    <h3>Feedbacks</h3>
                    <ul>
                        <li><a href="{{ url('feedbacks') }}">Feedbacks</a></li>                
                        <li>Twitter: <a href="https://twitter.com/NepseToday" target="_blank" rel="noopener noreferrer">{{config('app.twitter')}}</a></li>                
                        <li>Facebook: <a href="https://www.facebook.com/NEPSE.today" target="_blank" rel="noopener noreferrer">{{config('app.facebook')}}</a></li>                
                    </ul>
                </div>

                <div class="support nav">  
                    <h3>Help & Support</h3>
                    <ul>
                        <li><a href="{{ url('guidelines') }}"><mark>Guidelines</mark></a></li>
                        <li><a href="{{ url('feedbacks') }}">Contact us</a></li>
                    </ul>
                </div>
                
                
                <div class="sales nav">
                    <h3>Manage sales</h3>
                    <ul>
                        <li><a href="{{ url('sales') }}">Sales</a></li>
                        <li><a href="{{ url('basket') }}">My Cart</a></li>
                    </ul>
                </div>

                <div class="portfolio nav">
                    <h3>Manage portfolio</h3>
                    <ul>
                        <li><a href="{{ url('portfolio') }}">Portfolio</a></li>
                        <li><a href="{{ url('/portfolio/new') }}">New Portfolio</a></li>
                        <li><a href="{{ url('/import/share') }}">Import</a></li>
                        <li><a href="{{ url('/import/meroshare') }}">Import (MeroShare)</a></li>
                        <li><a href="{{ url('/shareholders') }}">Shareholders</a></li>
                    </ul>
                </div>

            </section>
            
            <div class="copyright">
                 © {{ date("Y") }} {{ config('app.name', "NEPSE.TODAY") }}&nbsp; All rights reserved.
            </div>
        </footer>
        
    </div>
    
    <script src="{{ URL::to('js/app.js') }}"></script>
    
</body>
</html>
