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
            <h3>The record for {{old('symbol')}} could not be saved</h3>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach                    
            </ul>
        </div>
    </div>
    @endif
    
    @if(session()->has('message'))
        <h3 class="success">{{ session()->get('message') }}</h3>
    @endif

    <article class="form article-stocks_crud">
        
        <header class="band stocks-list-header flex js-apart al-cntr">  

            <div class="flex js-together al-cntr ">
                <h2 class="title">Stocks</h2>&nbsp;
                <div class="notification">
                    ({{count($stocks)}} records)
                </div>
            </div>
            
            <div class="flex al-cntr">
                <div class="filter__wrap" style="padding:0 10px">

                @if (!empty($sectors))
                <select id="filter">
                    <option value="0">All sectors</option>
                    @foreach($sectors as $record)
                    <option value="{{ $record->id }}"
                    @if(session()->has('sector_id')) 
                        @if(session()->get('sector_id') == $record->id) SELECTED @endif 
                    @endif>
                        {{$record->sub_sector}}
                    </option>
                    @endforeach
                </select> 
                @endif

                </div>
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
                    <th class="optional">Updated by</th>
                    <th class="optional">Updated at</th>
                </tr>
                
                @foreach ($stocks as $record)

                <tr id="row{{$record->id}}"  @if(old('id')==$record->id) class="selected" @endif>
                    <td>
                        <input type="checkbox" name="s_id" id="{{ $record->id }}">
                    </td>
                    <td><label for="{{$record->id}}"> {{ $record->symbol }}</label></td>
                    <td><label for="{{$record->id}}"> {{$record->security_name }}</label></td>
                    <td>{{ optional($record->sector)->sector }}</td>
                    <td class="optional">{{ $record->active ? 'Yes':'No' }}</td>
                    <td class="optional">{{ optional($record->user)->name }}</td>
                    <td title="creatd at : {{$record->created_at}}">{{ $record->updated_at }}</td>
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
                <select name="sector_id" id="sector">
                <option value="">Choose a sector</option>
                    @if (!empty($sectors))
                        @foreach($sectors as $record)
                            <option value="{{ $record->id }}"
                            @if(strcasecmp( old('sector_id'), $record->sector ) == 0)
                                SELECTED
                            @endif
                            >{{$record->sub_sector}}
                            </option>
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

        //filter by sector
        document.querySelector('select#filter').addEventListener('change', function (e) {
            const sector = e.target.value;
           const url = `${window.location.origin}/stocks/sector/${sector}`;
           window.location.replace(url);
        });

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
            request.open('GET', '/stock/detail/' + stock_id, true);

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
            document.querySelector('input#id').value = data.id;
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