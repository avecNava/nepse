@extends('layouts.email-plain')

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
        <div class="top-bar">
            <p>Dear admin,</p>
            <p>You received a <mark>{{ $feedback->category }}</mark> from <strong>{{ $feedback->name }}</strong> ({{ $feedback->email}}) on {{ $feedback->created_at }}.</p>
        </div>
        
        <h2>{{$feedback->title }}</h2>

        <p>
            {{ $feedback->description }}
        </p>

        <p>&nbsp;</p>
        
        <p>
            <a href= "{{url('feedback/view', $feedback->id)}}" target="_blank" rel="noopener noreferrer">View in app</a>
        </p>
       
        <hr>

    </body>

</html>

@endsection


