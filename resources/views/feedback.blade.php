@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('js')
    
@endsection

@section('content')
    <div id="loading-message" style="display:none">Importing... Please wait...</div>
    <h1>Contact us</h1>
    <section class="contact-us">
        
        <p> <strong>Thanks for showing up here.</strong> </p>
        <p>Please use the form below to contact us, submit complaints, suggestions or just even a Thank You note 👀 </p>
        
        <div class="contact-us__form">

            <div id="message">            

                @if (\Session::has('message'))
                    <div class="message success">
                        Thank you for your time 🙏 <br>
                        {!! \Session::get('message') !!}
                    </div>
                    @endif

                    @if (\Session::has('error'))
                    <div class="message error"> </div>
                @endif

            </div>
                
            <form method="POST" action="feedbacks" enctype="multipart/form-data">
                @csrf()
            
                <div class="form-field buttons">
                    <button type="submit">Submit</button>
                    <button id="cancel" type="reset">Reset</button>
                </div>

                <div class="form-field">
                    <label for="name">Name</label>
                    <input type="hidden" name="user_id" value="{{old('id', optional($user)->id)}}">
                    <input type="text" name="name" value="{{old('name', optional($user)->name)}}" required class="@error('name') is-invalid @enderror">
                    @error('name')
                        <div class="is-invalid">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-field">
                    <label for="email">Email</label>
                    <input type="email" name="email" value="{{old('email', optional($user)->email)}}" required class="@error('email') is-invalid @enderror">
                    @error('email')
                        <div class="is-invalid">{{ $message }}</div>
                    @enderror
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

                    @error('category')
                        <div class="is-invalid">{{ $message }}</div>
                    @enderror

                </div>

                <div class="form-field">
                    <label for="title">Title</label>
                    <input type="title" name="title" value="{{old('title', '')}}" required class="@error('title') is-invalid @enderror">
                    @error('title')
                        <div class="is-invalid">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-field" style="display:flex">
                    <label for="feedback">Feedback</label>
                    <textarea name="feedback" id="feedback" cols="80" rows="20" class="@error('feedback') is-invalid @enderror">
                        {{old('feedback','')}}
                    </textarea>
                    @error('feedback')
                        <div class="is-invalid">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-field">

                    <label for="attachment">Attachment</label>

                    <input type="file" name="attachment"  class="@error('attachment') is-invalid @enderror" />
                    @error('attachment')
                        <div class="is-invalid">
                            {{ $message }}
                        </div>
                    @enderror
                        
                </div>

            </form>

        </div>

    </section>    

    <script>
        
    </script>

@endsection