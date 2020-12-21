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
            <p>Hi there,</p>
            <p>You received a feedback from <strong>{{ $feedback->name }}</strong> ({{ $feedback->email}}) on {{ $feedback->created_at }}.</p>
            <div>
                <p>Category : <strong>{{ Str::upper($feedback->category) }}</strong> <a style="margin-left:20px" href= "{{url('feedback/view', $feedback->id)}}" target="_blank" rel="noopener noreferrer">View in app</a></p>
            </div>
        </div>
        
        <h3>{{ blank($feedback->title) ? 'user' : $feedback->title }}</h3>

        <p>
            {{ $feedback->description }}
        </p>
        <p>&nbsp;</p>
       
       <p></p>
        
       <hr>

    </body>

</html>

@endsection


