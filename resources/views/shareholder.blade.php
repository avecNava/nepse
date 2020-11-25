@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('js')
    
@endsection

@section('content')

    <div id="loading-message" style="display:none">Importing... Please wait...</div>
    <section class="c_shareholders">

        <header>
            <h1 class="c_title">Shareholders</h1>
        </header>

        <main class="c_shareholder_form">            
            <form method="POST" action="/shareholders">                
                <div class="form-field button">
                    <button type="submit">Save</button>
                </div>
                
                <div class="fields">
                    
                    @csrf()
                    
                    <div class="form-field">
                        <input type="hidden" value="{{old('id')}}" name="id"> 
                        <label for="first_name">First name</label>
                        <input type="text" value="{{old('first_name')}}" name="first_name" required 
                        class="@error('first_name') is-invalid @enderror" />
                        @error('first_name')
                            <div class="is-invalid">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label for="last_name">Last name</label>
                        <input type="text" value="{{old('last_name')}}" name="last_name" required 
                        class="@error('last_name') is-invalid @enderror" />
                        @error('last_name')
                            <div class="is-invalid">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label for="email">Email</label>
                        <input type="email" value="{{old('email')}}" name="email" required 
                        class="@error('email') is-invalid @enderror" />
                        @error('email')
                            <div class="is-invalid">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label for="date_of_birth">Date of birth</label>
                        <input type="date" value="{{old('date_of_birth')}}" name="date_of_birth" 
                        class="@error('date_of_birth') is-invalid @enderror" />
                        @error('date_of_birth')
                            <div class="is-invalid">{{ $message }}</div>
                        @enderror
                    </div>       

                    <div class="form-field">
                        <label>Gender</label>
                        <label for="male">
                            <input type="radio" name="gender" value="male" id="male" 
                            {{ old('gender') == "male" ? 'checked' : '' }}
                            >Male
                        </label>
                        <label for="female">
                            <input type="radio" name="gender" value="female" id="female"
                            {{ old('gender') == "female" ? 'checked' : '' }}
                            >Female
                        </label>
                        <label for="other"> 
                            <input type="radio" name="gender" value="other" id="other"
                            {{ old('gender') == "other" ? 'checked' : '' }}
                            >Other
                        </label>
                        @error('gender')
                            <div class="is-invalid">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-field">
                        <label for="relation">Relation</label>
                        <select name="relation">
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
                        @error('relation')
                            <div class="is-invalid">{{ $message }}</div>
                        @enderror
                    </div>

                </div>                
            </form>        
        </main>
        <footer></footer>

    </section>

    @if( $shareholders->isNotEmpty() )
    <article class="c_shareholder_list">
    
        <header>
            <div class="action">
                <button id="edit" onClick="editShareholder()">Edit</button>
                <button id="delete" onClick="deleteShareholder()">Delete</button>
            </div>
            <div id="form-message" style="display:none">
                The selected transactions have been deleted.
            </div>
        </header>

        <main>
            <table>
                <tr>
                    <th></th>                    
                    <th>Name</th>                    
                    <th>Email</th>
                    <th>Date of birth</th>
                    <th>Gender</th>
                    <th>Relation</th>
                </tr>
                
                @foreach ($shareholders as $record)                
                <tr>
                    <td>
                        <input type="checkbox" name="t_id" id="{{ $record->id }}">
                    </td>
                    <td>
                        <label for="{{ $record->id }}">{{ $record->first_name }}{{ $record->last_name }}</label>
                    </td>
                    <td><label for="{{ $record->id }}">{{ $record->email }}</label</td>
                    <td>{{ $record->date_of_birth }}</td>
                    <td>{{ $record->gender }}</td>
                    <td>{{ empty($record->relation)? 'You' : $record->relation }}</td>
                </tr>
                @endforeach            
            </table>
        </main>
    
        <footer></footer>
        
    </article>
    @endif
    </section>
    
    <script>
        function showLoadingMessage() {
            let ele_loading = document.getElementById('loading-message');
            ele_loading.classList.add('loading');
        }
        function hideLoadingMessage() {
            let ele_loading = document.getElementById('loading-message');
            ele_loading.classList.remove('loading');
        }
        function showImportMessage($t=5000) {
            let ele_loading = document.getElementById('import-message');
            ele_loading.classList.add('success');
            setTimeout(function(){ 
                ele_loading.classList.remove('success');
             }, $t);
        }
        function hideImportMessage() {
            let ele_loading = document.getElementById('import-message');
            ele_loading.classList.remove('success');
        }
        function checkAll() {
            var select_all = document.getElementById('select_all');
            var flag = select_all.checked;            
            var elements = document.getElementsByName("t_id");
            Array.prototype.forEach.call(elements, function(el, i){
                el.checked=flag;
            });
        }
        function importToMyPortfolio() {
            let selected = [];
            let elements = document.getElementsByName("t_id");
            let ele_import = document.getElementById('import-message');
            
            showLoadingMessage();

            Array.prototype.forEach.call(elements, function(el, i){
                if(el.checked){
                    selected.push(el.id);
                }
            });

            //call ajax 
            let _token = document.getElementsByName('_token')[0].value;

            let request = new XMLHttpRequest();
            request.open('POST', '/meroshare/import-transaction', true);
            request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
            request.onload = function(ele_success, ele_loading) {
                if (this.status >= 200 && this.status < 400) {
                    $msg = JSON.parse(this.response);
                    console.log($msg);
                    hideLoadingMessage();
                    showImportMessage(5000*2);
                }
            }
            request.send(`_token=${_token}&trans_id=${selected.toString()}`);

        }
    </script>

@endsection