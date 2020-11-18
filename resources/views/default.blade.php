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

                <a href="/" class="c-logo">
                    <img src="{{ URL::to('assets/nepse-today-logo.png') }}" alt="NEPSE.TODAY" class="c-logo__img">
                </a>

                <div class="c-nav">
                    <nav class="c-nav__list">
                    </nav>
                </div>

            </div>
        </header>

        <main class="c_content">

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
    <script>
        @yield('js')
    </script>
</body>
</html>