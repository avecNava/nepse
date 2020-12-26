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
                            <li><a href="{{ url('/portfolio/new') }}">New</a></li>
                            <li><a href="{{ url('portfolio') }}">Portfolio</a></li>
                            <li><a href="{{ url('share/import') }}">Import Share</a></li>
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

       
        <footer class="page-footer" style="display:none">
        
            <div class="feedback">        
                <h5>Feedbacks</h5>       
                
                If you have any feedbacks, suggestions or ideas, why not share with us ?
                <div class="input-field">
                    <textarea id="feedback" class="materialize-textarea"></textarea>                        
                </div>                      
                <button disabled type="submit" class="btn #8bc34a light-green" id="send-feedback">Send feedback</button>
            </div>

            <section class="links">

                <div class="articles">          
                    <h5>Articles</h5>
                    <ul>
                        <li>
                            <a href="article">Show article</a>
                        </li>  
                        <li>
                            <a href="article/add">Add article</a>
                        </li>  
                    </ul>
                </div>

                <div class="links">
                    <h5>Links</h5>
                    <ul>
                        <li>
                            <a href="portfolio">Dashboard</a>
                        </li>
                        <li>
                            <a href="#!Shareholder">Watchlist</a>
                        </li>
                        <li>
                            <a href="#!Shareholder">Companies</a>
                        </li>
                        <li>
                            <a href="main/stockprice">Market rate <span class="new_release">new</span></a>                      
                        </li>  
                    </ul>
                </div>

                <div class="accounts">
                    <h5>Account</h5>
                    <ul>
                        <li>
                        <a href="account/shareholder">Shareholder</a>
                        </li>
                        <li>
                            <a href="account/sharegroup">Share group</a>
                        </li>                          
                        <li>
                            <a href="account/change_password">Change password</a>
                        </li>
                        <li>
                            <a href="account/logout">Log Out</a>
                        </li>
                    </ul>
                </div>

            </section>
            <div>
                 © {{ date("Y") }} {{ config('app.name', "NEPSE.TODAY") }}&nbsp; All rights reserved.
            </div>
        </footer>
        
        <!-- <script src="{{asset('assets/js/shareholder.js')}}"></script> -->
        <script src="{{ URL::to('js/app.js') }}"></script>
        @yield('js')
    

</body>
</html>