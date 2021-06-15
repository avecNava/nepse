@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('header_title')
    Contact us
@endsection

@section('custom_js')
    
@endsection

@section('content')
<div class="feedback__wrapper">

    <section class="feedback__intro">
        <div class="description">
            <h2>Hello
                @guest Guest @endguest
                @php
                $name = explode(' ', optional(Auth::user())->name);
                @endphp
                @auth{{ $name[0] }} ,@endauth
            </h2>
            <p> <strong>Thanks for showing up here.</strong> </p>
            <p>Please use the form below to contact us, submit complaints, suggestions</p>
        </div>
    </section>
    
    <section class="message">
        <div class="message">
            @if(session()->has('message'))
            <div class="success">
                {{ session()->get('message') }}
            </div>
            @endif     

            @if(session()->has('error'))
            <div class="error">
                {{ session()->get('error') }}
            </div>
            @endif
        </div>
    </section>

    <section class="form__wrapper feedback__form">

        <form method="POST" action="feedbacks" enctype="multipart/form-data">
            <header class="info band">
                <h2>Feedbacks</h2>
                <div class="flex">
                    <button type="submit" class="focus">Send Feedback</button>
                </div>
            </header>
            <main>
            <section class="contact-us__form">
                    
                <div class="block-left">
                    
                    @csrf()
                    
                    <div class="form-field">
                        <label for="name">Name</label>
                        <input type="hidden" name="user_id" value="{{old('id', optional($user)->id)}}">
                        <input type="text" name="name" value="{{old('name', optional($user)->name)}}" required class="@error('name') is-invalid @enderror">
                    </div>

                    <div class="form-field">
                        <label for="email">Email</label>
                        <input type="email" name="email" value="{{old('email', optional($user)->email)}}" required class="@error('email') is-invalid @enderror">
                    </div>
                    
                    <div class="form-field" title="Choose a category">
                        <label for="category">Category</label>   
                        <select name="category">
                            <option value="">Choose a category</option>
                            @if (!empty($categories))
                                @foreach($categories as $code=>$category)
                                    <option value="{{ $code }}" @if( old('category') == $code ) SELECTED @endif>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            @endif
                        </select> 
                    </div>

                    <div class="form-field">
                        <label for="title">Title</label>
                        <input type="title" name="title" value="{{old('title', '')}}" required class="@error('title') is-invalid @enderror">
                    </div>

                    <div class="form-field" style="display:flex">
                        <label for="feedback">Feedback</label>
                        <textarea name="feedback" id="feedback" cols="40" rows="20" class="@error('feedback') is-invalid @enderror">{{old('feedback','')}}</textarea>
                    </div>

                    <div class="form-field">
                        <label for="attachment">Attachment</label>
                        <input type="file" name="attachment"  class="@error('attachment') is-invalid @enderror" />
                    </div>

                    <div class="form-field">
                        <label for="g-recaptcha"></label>
                        <div class="g-recaptcha" data-sitekey="6LcIVCcaAAAAAH-4PTxeYOfUoj2La_BT0O7NiZJ8"></div>
                    </div>

                </div>

                <div class="block-right message">
                    <div class="validation-error">
                        @if ($errors->any())
                        <div class="error">
                            <h3>Attention :</h3>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </section>
            </main>
            <footer></footer>
        </form>

    </section>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</div>

@endsection