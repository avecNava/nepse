<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', "NEPSE.TODAY") }} - @yield('title')</title>
    <meta name="description" content="nepse, nepal stock, portfolio, financial management, finance, money, investment, profit, bull, bullish, bear">
    <link href="{{ URL::to('css/style.css') }}" rel="stylesheet">
    <link href="{{ URL::to('css/responsive.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ URL::to('favicon.ico') }}"> 
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Cutive&family=Lora:wght@700&family=Scope+One&display=swap" rel="stylesheet">
    <script src="{{ URL::to('js/app.js') }}"></script>
    @yield('custom_css')
</head>
<body>
    <div id="notice" style="display:none">
        <div class="notice_wrapper">
            <div>
                @yield('notice')
            </div>
            <div class="btn" onclick="hide_notice()">
                <a href="#">❌</a>
            </div>
        </div> 
    </div> 
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
                        @php
                        $basket = \App\Models\SalesBasket::where('shareholder_id', session()->get('shareholder_id'))->count('id');
                        @endphp
                        @if($basket > 0) 
                        <div id="basket-notify">
                            <a href="{{url('basket')}}" title="View Cart">View cart</a>
                        </div>
                        @endif
                    </div>
                    @endif
                    
                    <div class="a_page_header">
                        @yield('header_title')
                    </div>

                </div>

            </div>

            <nav class="c-nav__list home">
                <ul class="navbar-nav">
                    <li><a href="{{ url('home') }}">Home</a></li>
                    <li><a href="{{ url('portfolio') }}">Portfolio</a></li>
                    <li><a href="{{ url('portfolio/new') }}">New Share</a></li>
                    <li class="optional"><a href="{{ url('sales') }}">Sales</a></li>
                    <li class="optional"><a href="{{ url('shareholders') }}">Shareholder</a></li>
                </ul>
            </nav>
             
        </header>

        <main class="c_content">

            <div class="c_content__wrapper">
                @yield('content')
            </div>
            
        </main>

       
        <footer class="page-footer">
        
            <section class="links">
                
            <div class="feedbacks nav">  
                    <h3>Connect</h3>
                    <ul class="social">
                        <li>
                            <div class="c-social-nav">
                            <span class="c-social-nav__link email">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon icon-tabler icon-tabler-mail" width="24" height="24"  stroke-width="1.5" stroke="#ffffff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z"/>
                                    <rect x="3" y="5" width="18" height="14" rx="2" />
                                    <polyline points="3 7 12 13 21 7" />
                                </svg>    
                            </span>
                            <a href="{{ url('feedbacks') }}">Feedbacks</a>
                            </div>
                        </li>                
                        <li>
                        <div class="c-social-nav">
                            <span class="c-social-nav__link twitter">
                            <svg id="twitter" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="icon icon-tabler icon-tabler-mail" width="20" height="20"  stroke-width="1.5" stroke="#ffffff" fill="black" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M507.413,93.394c-3.709-2.51-8.607-2.383-12.174,0.327c-3.612,2.735-9.474,5.087-16.138,7.016
                                    c18.245-21.301,18.623-35.541,18.408-38.893c-0.245-3.801-2.541-7.168-5.985-8.791c-3.459-1.612-7.51-1.23-10.587,1.005 c-21.893,15.908-43.689,19.373-56.791,19.76c-20.337-19.342-46.704-29.944-74.74-29.944c-60.271,0-109.307,49.684-109.307,110.751 c0,4.944,0.327,9.878,0.969,14.771C138.176,167.645,54.665,69.155,53.803,68.119c-2.184-2.617-5.5-4.041-8.929-3.714 c-3.398,0.296-6.444,2.235-8.148,5.189c-29.005,50.322-11.286,94.725,6.505,121.327c-1.837-1.092-3.342-2.097-4.372-2.857 c-3.143-2.337-7.337-2.725-10.852-0.995c-3.521,1.735-5.771,5.286-5.837,9.209c-0.786,48.255,21.764,76.49,43.674,92.49 c-2.372,0.327-4.597,1.459-6.266,3.276c-2.51,2.724-3.393,6.576-2.311,10.122c15.194,49.735,52.041,67.352,76.373,73.587 c-49.22,37.138-120.557,25.016-121.348,24.867c-4.73-0.831-9.464,1.663-11.408,6.082c-1.939,4.413-0.612,9.587,3.225,12.51 c52.464,40.041,115.21,48.913,160.53,48.913c34.272,0,58.573-5.077,60.91-5.582c228.617-54.179,235.864-263.063,235.394-298.66 c42.888-39.929,49.633-55.255,50.684-59.067C512.811,100.502,511.117,95.91,507.413,93.394z M443.283,151.752 c-2.33,2.143-3.56,5.235-3.346,8.398c0.036,0.561,3.536,57.179-21.694,120.266c-33.709,84.291-100.164,138.725-197.307,161.746 c-1.041,0.219-90.905,18.831-169.792-18.689c33.725-1.414,80.429-10.913,113.292-47.806c2.745-3.077,3.398-7.833,1.709-11.593 c-1.689-3.75-5.439-6.51-9.551-6.51c-0.02,0-0.041,0-0.071,0c-2.76,0-50.337-0.357-73.133-46.306 c9.219,0.398,20.24-0.145,29.122-4.237c4.092-1.888,6.51-6.1,6.005-10.574c-0.505-4.475-3.821-8.079-8.23-9.008 c-2.556-0.541-57.649-12.836-66.143-72.693c8.464,3.526,19.015,6.257,29.51,4.685c4.031-0.602,7.332-3.5,8.474-7.413 c1.138-3.908-0.107-8.13-3.184-10.809c-2.383-2.07-54.327-48.273-30.541-107.973c28.158,29.332,108.46,102.368,205.833,96.786 c3.107-0.179,5.975-1.74,7.82-4.25c1.843-2.51,2.471-5.709,1.71-8.728c-1.837-7.316-2.77-14.857-2.77-22.418 c0-49.546,39.658-89.853,88.409-89.853c23.842,0,46.203,9.515,62.97,26.796c1.923,1.985,4.556,3.122,7.322,3.174 c9.658,0.092,25.561-0.949,43.531-7.633c-5.359,6.275-12.852,13.622-23.332,21.852c-3.622,2.847-4.954,7.735-3.276,12.026 c1.684,4.301,6.056,7.02,10.566,6.607c2.112-0.168,12.352-1.071,24.352-3.505C464.662,131.4,455.494,140.523,443.283,151.752z"/>
                                </svg>
                            
                            </span>
                            <a href="https://twitter.com/NepseToday" target="_blank" rel="noopener noreferrer">{{config('app.twitter')}}</a>
                        </div>
                        </li>                
                        <li>
                            <div class="c-social-nav">
                                <span class="c-social-nav__link facebook">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon icon-tabler icon-tabler-mail" width="24" height="24"  stroke-width="1.5" stroke="#ffffff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z"/>
                                    <path d="M7 10v4h3v7h4v-7h3l1 -4h-4v-2a1 1 0 0 1 1 -1h3v-4h-3a5 5 0 0 0 -5 5v2h-3" />
                                </svg>
                                </span>
                                <a href="https://www.facebook.com/NEPSE.today" target="_blank" rel="noopener noreferrer">{{config('app.facebook')}}</a>
                            </div>
                        </li>                
                    </ul>
                </div>

                <div class="support nav">  
                    <h3>Help & Support</h3>
                    <ul>
                        <li><a href="{{ url('guidelines') }}"><mark>Guidelines</mark></a></li>
                        <li><a href="{{ url('faq') }}"><mark>FAQs</mark></a></li>
                        <li><a href="{{ url('feedbacks') }}">Contact us</a></li>
                    </ul>
                </div>
                
                
                <div class="sales nav optional">
                    <h3>Manage sales</h3>
                    <ul>
                        <li><a href="{{ url('sales') }}">Sales</a></li>
                        <li><a href="{{ url('basket') }}">My Cart</a></li>
                    </ul>
                </div>

                <div class="portfolio nav optional">
                    <h3>Manage portfolio</h3>
                    <ul>
                        <li><a href="{{ url('portfolio') }}">Portfolio</a></li>
                        <li><a href="{{ url('/portfolio/new') }}">New Portfolio</a></li>
                        <li><a href="{{ url('/import/share') }}">Import (Spreadsheet)</a></li>
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