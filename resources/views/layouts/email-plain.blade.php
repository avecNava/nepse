<html>
<!doctype html>
    <head>
        <meta charset="utf-8">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Cutive&family=Lora:wght@700&family=Scope+One&display=swap" rel="stylesheet">
        <style>
            .c-header__wrapper {
                display: flex;
                justify-content: space-between;
                margin-bottom: 40px;
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
            a{
                text-decoration: none;
            }
            span.break-all {
                font-size: 15px;
            }
        
        </style>
    </head>
    <body>
        <div class="container">

            <header class="c-header__wrapper">
            
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

            <footer>

                <div class="copyright footer">
                    <p><a href="{{ config('app.url') }}">{{ config('app.name')}}</a></p>
                    <p>©<?=date("Y")?>&nbsp; All rights reserved.</p>
                </div>

                
                </div>

            </footer>
        </div>
       
    </body>
</html>