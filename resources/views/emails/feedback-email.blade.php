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
            <p><h2>{{ $feedback->category }}</h2></p>
        </div>
        
        <h2>{{$feedback->title }}</h2>

        <p>
            {{ $feedback->description }}
            <br/>
            <a href= "{{url('feedback/view', $feedback->id)}}" target="_blank" rel="noopener noreferrer">View in app</a>
        </p>

        <p>
            Name :  {{ $feedback->name }} <br/>
            Email : {{ $feedback->email}} <br/>
            Date :  {{ $feedback->created_at }}
        </p>
        
        
       
        <hr>

    </body>

</html>

@endsection


