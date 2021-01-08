<html>
<!doctype html>
    <head>
        <meta charset="utf-8">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Cutive&family=Lora:wght@700&family=Scope+One&display=swap" rel="stylesheet">
        <style>
            .c-header__wrapper {
                display: flex;
                margin-bottom: 40px;
                flex-wrap: nowrap;
                justify-content: space-between;
                align-items: flex-start;
            }
            h1 {
                align-self: center;
            }
            img.c-logo__img {
                height: 130px;
                padding: 20px 0;
            }
            .container {
                width: 800px;
                margin: 0 auto;
                background: beige;
                padding: 40px;
            }
            body {
                margin: 0;
                font-size: 20px;
                font-family: 'Scope One', serif; 
            }
            a {
                text-decoration: none;
                font-weight: bold;
                color: #3f51b5d6;
            }
            .social-links>div {
                padding-right: 10px;
            }
            .social-links {
                display: flex;
            }
            .social-links a {
                font-weight: 100px;
            }
            .label {
                color: #9E9E9E;
            }
            .copyright.footer {
                align-self: center;
                font-weight: bold;
            }
            .copyright.footer>p {
                margin: 0;
                font-weight: 100;
            }
            footer {
                margin-top: 100px;
                display: flex;
                justify-content: space-between;
            }
            footer div {
                font-size: 14px;
            }
            .signature {padding:10px 0}
        </style>
    </head>
    <body>
        <div class="container">

            <header class="c-header__wrapper" style="display:flex;justify-content:space-between;">
            
                <a href="{{config('app.url')}}" class="c-logo">
                    <img src="{{ URL::to('assets/nepse-today-logo.png') }}" alt="NEPSE.TODAY" class="c-logo__img">
                </a>

                <h1>@yield('title')</h1>

            </header>
           
            <main>      

                <div id="mainContent">
                    @yield('content')
                </div>

            </main>

            <footer style="display:flex;justify-content:space-between;">

                <div class="shout-out">
                    <div class="social-links">
                        <div>
                            <div class="label">Twitter</div>
                            <a href="https://twitter.com/{{ config('app.twitter') }}" target="_blank" rel="noopener noreferrer">
                            &#64;{{ config('app.twitter') }}
                            </a>
                        </div>
                        <div>
                            <div class="label">Facebook</div>
                            <a href="https://www.facebook.com/{{ config('app.facebook') }}" target="_blank" rel="noopener noreferrer">
                                {{config('app.facebook')}}
                            </a>
                        </div>
                        <div>
                            <div class="label">Email</div>
                            {{ config('app.email')}}
                        </div>
                    </div>
                </div>
                <div class="copyright footer">
                    <p><a href="{{ config('app.url') }}">{{ config('app.name')}}</a></p>
                    <p>©<?=date("Y")?>&nbsp; All rights reserved.</p>
                </div>

            </footer>
        </div>
       
    </body>
</html>