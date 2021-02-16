@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('js')
@endsection

@section('header_title')
<h1 class="c_title">Stocks</h1>
@endsection

@section('content')
    
    <section id="stocks-crud">

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

    <article class="form article-stocks_crud">
        
        <header class="band stocks-list-header">  

            <div class="flex js-together al-cntr ">
                <h2 class="title">Stocks</h2>&nbsp;
                <div class="notification">
                    ({{count($stocks)}} records)
                </div>
            </div>
            
            <div class="flex">
                <button id="new">New</button>
                <button id="edit">Edit</button>
                <button id="delete">Delete</button>
            </div>
            
        </header>
        
        @if( !empty($stocks) )
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
                    <th>Symbol</th>                    
                    <th>Security name</th>
                    <th>Sector</th>
                    <th class="optional">Active</th>
                    <th class="optional">Created at</th>
                </tr>
                
                @foreach ($stocks as $record)                
                <tr id="row{{$record->id}}" data-parent="{{$record->parent}}">
                    <td>
                        <input type="checkbox" name="s_id" id="{{ $record->id }}">
                    </td>
                    <td>
                        {{ $record->symbol }}
                    </td>
                    <td>{{ $record->security_name }}</td>
                    <td>{{ optional($record->sector)->sector }}</td>
                    <td class="optional">{{ $record->active ? 'Yes':'No' }}</td>
                    <td>{{ $record->created_at }}</td>
                </tr>
                @endforeach            
            </table>
        </main>
        @endif
    
        <footer></footer>
        
    </article>

    
    <dialog id="stock-form" class="form_container">

        <form class="form" method="POST" action="/stocks">
           
            <header class="band">
                <div>
                    <h2>Create stocks</h2>                    
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
                <label for="symbol">Symbol</label>
                <input type="text" value="{{old('symbol')}}" name="symbol" id="symbol" required 
                class="@error('symbol') is-invalid @enderror" />
            </div>

            <div class="form-field">
                <label for="security_name">Security name</label>
                <input type="text" value="{{old('security_name')}}" name="security_name" id="security_name" 
                class="@error('security_name') is-invalid @enderror" />
            </div>

            <div class="form-field">
                <label for="active">Active</label>
                <input type="checkbox" name="active" id="active" 
                class="@error('active') is-invalid @enderror" />
            </div>

            <div class="form-field">
                <label for="sector">Sector</label>
                <select name="sector" id="sector">
                <option value="0">Choose a sector</option>
                    @if (!empty($sectors))
                        @foreach($sectors as $record)
                            <option value="{{ $record->id }}"
                            @if(strcasecmp( old('sector'), $record->sector ) == 0)
                                SELECTED
                            @endif
                            >{{$record->sector}}</option>
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
            
    </dialog>

    </section>
    
    <script>

        //handle checkbox click
        document.querySelectorAll('input[name="s_id"]').forEach(element => {
            element.addEventListener('click', function (e) {
                document.querySelector('button#edit').setAttribute('data-id', e.target.id);
                document.querySelector('button#delete').setAttribute('data-id', e.target.id);
                resetOthers(e.target.id);
            })
        });

        //new button
        document.querySelector('button#new').addEventListener('click',function(){
            resetInputFields();
            const dialog = document.querySelector('#stock-form');
            dialog.showModal();
        });

        //cancel button
        document.querySelector('button#cancel').addEventListener('click',function(){
            resetInputFields();
            const dialog = document.querySelector('#stock-form');
            dialog.removeAttribute('open');
        });

        //edit records
        document.querySelector('button#edit').addEventListener('click',function(e){

            let id = document.getElementById('edit').dataset.id;

            if(!id){
                let msg = 'Please select a record to edit';
                showMessage(msg,'message');    return;
            }

            resetInputFields();
            getData(id);
            const dialog = document.querySelector('#stock-form');
            dialog.showModal();
        });

        function getData(stock_id){
            
            //get record from db
            let request = new XMLHttpRequest();
            request.open('GET', '/stocks/id/' + stock_id, true);

            request.onload = function() {
                if (this.status >= 200 && this.status < 400) {
                    data = JSON.parse(this.response);
                    updateInputFields(data);
                }
            } 
            request.onerror = function() {
                // There was a connection error of some sort
                hideLoadingMessage();
            };
            request.send();

        }

        function updateInputFields(data){
            document.querySelector('input#symbol').value = data.symbol;
            document.querySelector('input#security_name').value = data.security_name;
            const active = document.querySelector('input#active');
            const sector = document.querySelector('select#sector');
            setOption(sector, data.sector_id);
            if(data.active==1) {
                active.checked = true;
            }
        }

        function resetInputFields(){
            document.querySelector('input#symbol').value = '';
            document.querySelector('input#security_name').value = '';
            document.querySelector('input#active').checked = false;
            const sector = document.querySelector('select#sector');
            setOption(sector, 0);
            document.querySelector('#message').innerText='';
        }

        function resetOthers(id) {
            document.querySelectorAll('[type="checkbox"]').forEach(element => {
                if(element.id != id){
                    element.checked = false;
                }
            })
        }

    </script>

@endsection