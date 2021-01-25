@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('js')
    <script src="{{ URL::to('js/shareholder.js') }}"></script>
@endsection

@section('header_title')
<h1 class="c_title">My Shareholders</h1>
@endsection

@section('content')
    
    <section id="shareholder-crud">

    <article class="form c_shareholder_list">
        
        <header class="band shareholder-list-header">  

            <div class="flex js-together al-cntr ">
                <h2 class="title">Shareholders</h2>&nbsp;
                <div class="notification">
                    ({{count($shareholders)}} records)
                </div>
            </div>
            
            <div class="flex">
                <button id="new">New</button>
                <button id="edit">Edit</button>
                <button id="delete">Delete</button>
            </div>
            
        </header>
        
        @if( !empty($shareholders) )
        <main>
            <table>
                <tr>
                    <td colspan="6">
                        <section class="message">
                            <div class="message" id="message"></div>
                        </section>
                    </td>
                </tr>
                <tr>
                    <th></th>                    
                    <th>Name</th>                    
                    <th class="optional">Email</th>
                    <th class="optional">Date of birth</th>
                    <th class="optional">Gender</th>
                    <th>Relation</th>
                </tr>
                
                @foreach ($shareholders as $record)                
                <tr id="row{{$record->id}}" data-parent="{{$record->parent}}">
                    <td>
                        <input type="checkbox" name="s_id" id="{{ $record->id }}">
                    </td>
                    <td>
                        <label for="{{ $record->id }}">{{ $record->first_name }} {{ $record->last_name }}</label>
                    </td>
                    <td class="optional"><label for="{{ $record->id }}">{{ $record->email }}</label</td>
                    <td class="optional">{{ $record->date_of_birth }}</td>
                    <td class="optional">{{ $record->gender }}</td>
                    <td>{{ empty($record->relation)? 'You' : $record->relation }}</td>
                </tr>
                @endforeach            
            </table>
        </main>
        @endif
    
        <footer></footer>
        
    </article>

    <div>&nbsp;</div>
    
    <div id="shareholder-form" class="form_container"  @if(session()->has('errors')) style="display:block" @endif>

        <form class="form" method="POST" action="/shareholders">
           
            @if ($errors->any())
            <div class="validation-error">
                    <div class="error">
                    <h2>Attention:</h2>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <header class="band">
                <div>
                    <h2>Create shareholder</h2>                    
                </div>
            </header>

            <div class="form_content">
            <section class="message">
                <div class="message">                    
                    @if(session()->has('message'))
                        <span class="success">{{ session()->get('message') }}</span>
                    @endif
                    @if(session()->has('error'))
                        <span class="error">{{ session()->get('message') }}</span>
                    @endif
                </div>
            </section>
            
            @csrf()

            <div class="form-field">
                <input type="hidden" value="{{old('id')}}" name="id" id="id"> 
                <input type="hidden" name="parent_id" id="parent_id"> 
                <label for="first_name">First name</label>
                <input type="text" value="{{old('first_name')}}" name="first_name" id="first_name" required 
                class="@error('first_name') is-invalid @enderror" />
            </div>

            <div class="form-field">
                <label for="last_name">Last name</label>
                <input type="text" value="{{old('last_name')}}" name="last_name" id="last_name" 
                class="@error('last_name') is-invalid @enderror" />
            </div>

            <div class="form-field">
                <label for="email">Email</label>
                <input type="email" value="{{old('email')}}" name="email" id="email" 
                class="@error('email') is-invalid @enderror" />
            </div>

            <div class="form-field">
                <label for="date_of_birth">Date of birth</label>
                <input type="date" value="{{old('date_of_birth')}}" name="date_of_birth" id="date_of_birth"
                class="@error('date_of_birth') is-invalid @enderror" />
            </div>       

            <div class="form-field">

                <label>Gender</label>

                <div class="form-field c_gender">

                    <label for="male">
                        <input type="radio" name="gender" value="male" id="male" {{ old('gender') == "male" ? 'checked' : '' }}>Male
                    </label>

                    <label for="female">                                
                        <input type="radio" name="gender" value="female" id="female" {{ old('gender') == "female" ? 'checked' : '' }}>Female
                    </label>
                    
                    <label for="other">
                        <input type="radio" name="gender" value="other" id="other" {{ old('gender') == "other" ? 'checked' : '' }}>Other
                    </label>

                </div>
            </div>

            <div class="form-field c_relation">
                <label for="relation">Relation</label>
                <select name="relation" id="relation">
                    @if (!empty($relationships))
                        @foreach($relationships as $record)
                            <option value="{{ $record->relation }}"
                            @if(strcasecmp( old('relation'), $record->relation ) == 0)
                                SELECTED
                            @endif
                            >{{$record->relation}}</option>
                        @endforeach
                    @endif
                </select> 
            </div>

            <div class="flex">
                <button type="submit" class="focus">Save</button>
                <button id="cancel" type="button" >Cancel</button>
            </div>
            <div class="form-field"></div>

        </form>
            
    </div>

    </section>
    
    <script>
        
    </script>

@endsection