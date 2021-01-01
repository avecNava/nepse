<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', "NEPSE.TODAY") }} - @yield('title')</title>
    <meta name="description" content="nepse, nepal stock, portfolio, financial management, finance, money, investment, profit, bull, bullish, bear">
    <link href="{{ URL::to('css/style.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ URL::to('favicon.ico') }}"> 
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Cutive&family=Lora:wght@700&family=Scope+One&display=swap" rel="stylesheet">
    <script src="{{ URL::to('js/app.js') }}"></script>
</head>
<body>
    <div id="container">
        
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
                    @endif
                    <div class="a_page_header">
                        @yield('header_title')
                    </div>

                    <nav class="c-nav__list">
                        <ul class="navbar-nav">
                            <li><a href="{{ url('/') }}">Home</a></li>
                            <li><a href="{{ url('portfolio/new') }}">New Portfolio</a></li>
                            <li><a href="{{ url('import/share') }}">Import Share</a></li>
                            <li><a href="{{ url('sales') }}">Sales</a></li>
                            <li><a href="{{ url('basket') }}">My Cart</a></li>
                            <li><a href="{{ url('shareholders') }}">Shareholder</a></li>
                        </ul>
                    </nav>
                </div>

            </div>
             
        </header>

        <main class="c_content">

            <div class="c_content__wrapper">
                @yield('content')
            </div>
            
            <!-- side menu -->
            <section id="side_menu" style="display:none">
                <ul id="slide-out" class="side-nav">
                    <li><div class="userView">
                    <div class="background">
                    <img src="images/background.jpg">
                    </div>
                    <a href="#!user"><img class="circle" src="images/default-avatar.png"></a>
                    <a href="#!name"><span class="name">
                        @if (!empty( Auth::user()->name ))
                            {{ Auth::user()->name }} 
                        @endif
                    </span></a>
                    <a href="#!email"><span class="email">
                    @if (!empty( Auth::user()->email ))
                            {{ Auth::user()->email }} 
                        @endif
                    </span></a>
                    </div></li>
                    <li>
                    <a href="portfolio" title="Home">
                        <i class="material-icons">dashboard</i>
                        Home
                    </a>
                    </li>
                    <li>
                    <a href="portfolio/addstock" title="Add stock">
                        <i class="material-icons">note_add</i>
                        Add stock
                    </a> 
                    </li>
                    <li>
                    <a href="account/shareholder" title="Shareholders">
                        <i class="material-icons">note_add</i>
                        Shareholders
                    </a> 
                    </li>
                    <li>
                    <a href="account/sharegroup" title="Share groups">
                        <i class="material-icons">note_add</i>
                        Share groups
                    </a> 
                    </li>
                    <li>
                    <a href="article" title="News and events">
                        <i class="material-icons">subject</i>
                        News and events
                    </a> 
                    </li>
                    
                    <li>
                    <a href="admin/users" title="Users">
                        <i class="material-icons">supervisor_account</i>
                        Users
                    </a> 
                    </li>
                    <li>
                    <a href="admin/companies" title="Companies">
                        <i class="material-icons">business</i>
                        Companies
                    </a> 
                    </li>
                    
                    <li>
                    <a href="account/logout" title="Log out">
                        <i class="material-icons prefix">power_settings_new</i>
                        Log out              
                    </a>
                    </li>    
                </ul>        
            </section>

        </main>

       
        <footer class="page-footer">
        
            <section class="links">

                <div class="contact-us nav">          
                    <h3>Contact us</h3>
                    <ul>
                        <li><a href="{{ url('feedbacks') }}">Contact us</a></li>
                        <li><a href="{{ url('feedbacks') }}">Feedbacks</a></li>
                    </ul>
                </div>

                <div class="sales nav">
                    <h3>Sales</h3>
                    <ul>
                        <li><a href="{{ url('sales') }}">Sales</a></li>
                        <li><a href="{{ url('basket') }}">My Cart</a></li>
                    </ul>
                </div>

                <div class="portfolio nav">
                    <h3>Portfolio</h3>
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
        
        <!-- <script src="{{asset('assets/js/shareholder.js')}}"></script> -->
        @yield('js')

</body>
</html>