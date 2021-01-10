@extends('layouts.email')

@section('title')
    {{ config('app.name', "NEPSE.TODAY") }} 
@endsection

@section('content')


<DOCTYPE html>
<html lang="en-US">
     
    <head>
        <meta charset="utf-8">
    </head>

    <body>
        
        <p>Dear {{ !empty($user) ? $user->name : 'user' }},</p>
        <p>
            Thanks for signing up with us 🙏
        </p>
        <p>
            In managing your stocks portfolio, you have come to the right place.
        </p>
        <p> 
            With this application, you can manage your stocks in a single place, monitor stock related events and observe the market values of your stocks in almost realtime.
        </p>
        <p>
            We believe that you will have an wonderful experience in managing your stocks with us.
        </p>
        <p>
            Should you encounter any issues while using the app or have any comments, suggestions or something to share, we would love to hear from you. You can use the <a class="nav-link" href="{{ route('feedback') }}">{{ __('Contact us') }}</a> form to reach us.
        </p>
        <p>
            <mark>
                Since it is your first time using this application, some random records have been created for you. You can navigate around and experience the application without much effort. <strong>PLEASE DELETE THE SAMPLE RECORDS</strong> once you start adding your own records.
            </mark>
        </p>
        <p>
            Best regards, <br><br>
            <div class="signature">{{config('app.signature-founder')}}</div><br/>
            {{ config('app.signature-founder-title')}}
        </p>

    </body>

</html>

@endsection


