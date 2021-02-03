@extends('layouts.default')

@section('title')
    showing users
@endsection

@section('js')
    <!-- <script src="{{ URL::to('js/app.js') }}"></script> -->
@endsection

@section('header_title')
<h1 class="c_title">Users</h1>
@endsection

@section('content')
    

<section id="users">
        
    <section class="message">
        <div class="message" id="message">                    
            @if(session()->has('message'))
            <span class="success">{{ session()->get('message') }}</span>
            @endif
            @if(session()->has('error'))
            <span class="error">{{ session()->get('message') }}</span>
            @endif
        </div>
    </section>

    <article class="form users_list">
        
        <header class="band flex js-apart">  

            <div class="flex js-together al-cntr ">
                <h2 class="title">Users</h2>&nbsp;
                <div class="notification">
                    ({{count($users)}} records)
                </div>
            </div>
            
            <div class="flex">
                <!-- <button id="edit">Edit</button>
                <button id="delete">Delete</button> -->
                <label for="last_name">               
                <select name="role_filter">
                    <option value="">All</option>
                    <option value="user">User</option>
                    <option value="editor">Editor</option>
                    <option value="admin">Admin</option>
                    <option value="superuser">Super admin</option>
                </select>
                </label> 
            </div>
            
        </header>
        
        @if( !empty($users) )
        <main>
            <table>
                <tr>
                    <th>&nbsp;&nbsp;SN</th>                    
                    <th>Name</th>                    
                    <th>Email</th>
                    <th>Email verified at</th>
                    <th>Created at</th>
                    <th>Role</th>
                    <th>Active</th>
                    <th></th>                    
                </tr>
                @php $row =0; @endphp
                @foreach ($users as $record)                
                <tr id="row{{$record->id}}" data-parent="{{$record->parent}}">
                    <td>&nbsp;{{ Str::padLeft(++$row,3,'00') }}</td>
                    <td>
                        {{ Str::title($record->name) }}
                    </td>
                    <td>{{ $record->email }}</td>
                    <td>{{ $record->email_verified_at }}</td>
                    <td>{{ $record->created_at }}</td>
                    <td>{{ $record->role }}</td>
                    <td align="center">{{ $record->active ? 'Y' : 'N' }}</td>
                    <td>
                        <button name="edit" data-id="{{ $record->id }}" title="Edit user">📝</button>
                    </td>
                </tr>
                @endforeach            
            </table>
        </main>
        @endif
    
        <footer></footer>
        
    </article>

    <div>&nbsp;</div>
    
    <dialog id="usersFormDialog">

        <form class="form" method="POST" action="/users">
           
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
                    <h2>Edit users</h2>                    
                </div>
            </header>

            <div class="form_content">
            
            @csrf()

            <div class="form-field">
                <input type="hidden" value="{{old('id')}}" name="id" id="id"> 
                <label for="name">Name</label>
                <input type="text" value="{{old('name')}}" name="name" id="name" required readonly />
            </div>

            <div class="form-field">
                <label for="email">Email</label>
                <input type="email" value="{{old('email')}}" name="email" id="email" required readonly />
            </div>

            <div class="form-field">
                <label for="email_verified_at">Email verified at</label>
                <div class="flex al-cntr">
                    <input type="text" value="{{old('email_verified_at')}}" name="email_verified_at" id="email_verified_at" readonly/>
                    <span onclick="showDate()">&nbsp;&nbsp;<a href="#"><strong>⏲ Now</strong></a></span>
                </div>
            </div>    
           
            <div class="form-field">
                <label for="last_name">Role</label>
                <select name="role" id="role" required>
                    <option value="user" @if( old('role')=='user') SELECTED @endif>User</option>
                    <option value="editor" @if( old('role')=='editor') SELECTED @endif>Editor</option>
                    <option value="admin" @if( old('role')=='admin') SELECTED @endif>Admin</option>
                    <option value="superuser" @if( old('role')=='superuser') SELECTED @endif>Super admin</option>
                </select>
            </div>
            <div class="form-field">
                <label for="active">
                <input type="checkbox" name="active" id="active">
                Active</label>
            </div>

            <div class="flex">
                <button id="update" class="focus">Update</button>
                <button id="cancel" type="button">Cancel</button>
            </div>
            <div class="form-field"></div>

        </form>
            
    </dialog>

    </section>
    
    <script>
        const dialog = document.querySelector('#usersFormDialog');
        const filter = document.querySelector('[name="role_filter"]');

        filter.addEventListener('change', function(){
            const role = filter.value;
            const url = `${window.location.origin}/users/${role}`;
            window.location.replace(url);
        });

        document.querySelectorAll('button[name=edit]').forEach(function(e){
            e.addEventListener('click', function(e){
                const id = e.target.dataset.id;
                const row = `tr#row${id}`;
                const name = document.querySelector(`${row} td:nth-child(2)`).innerText;
                const email = document.querySelector(`${row} td:nth-child(3)`).innerText;
                const email_verified_at = document.querySelector(`${row} td:nth-child(4)`).innerText;
                const role = document.querySelector(`${row} td:nth-child(6)`).innerText;
                var active = document.querySelector(`${row} td:nth-child(7)`).innerText;
                // console.log(id, email, name, role, email_verified_at, active);
                document.querySelector('input#name').value = name;
                document.querySelector('input#email').value = email;
                document.querySelector('input#id').value = id;
                document.querySelector('input#email_verified_at').value = email_verified_at;
                if(active == 'Y'){
                    document.querySelector('input#active').checked=true;
                }
                setOption(document.querySelector('#role'), role);
                // document.querySelector('.message span').innerText= '';
                dialog.showModal();
            });
        });
        
        document.querySelector('#cancel').addEventListener('click', function(){
            dialog.close();
        });
    </script>

@endsection