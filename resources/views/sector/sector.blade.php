@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('header_title')
    Sectors
@endsection

@section('content')
    
    <section id="stocks-crud">
    @if ($errors->any())
    <div class="validation-error">
            <div class="error">
            <h3>The record for {{old('sector')}} could not be saved</h3>
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
                <h2 class="title">Sectors</h2>&nbsp;
                <div class="notification">
                    ({{count($sectors)}} records)
                </div>
            </div>
            
            <div class="flex al-cntr">
                <button id="new">New</button>
                <button id="edit">Edit</button>
                <button id="delete">Delete</button>
            </div>
            
        </header>
        
        @if( !empty($sectors) )
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
                    <th>Sector group</th>                    
                    <th>Sub sector</th>
                </tr>
                
                @foreach ($sectors as $record)                
                <tr id="row{{$record->id}}"  @if(old('id')==$record->id) class="selected" @endif>
                    <td>
                        <input type="checkbox" name="s_id" id="{{ $record->id }}">
                    </td>
                    <td><label for="{{$record->id}}"> {{ $record->sector }}</label></td>
                    <td><label for="{{$record->id}}"> {{$record->sub_sector }}</label></td>
                </tr>
                @endforeach            
            </table>
        </main>
        @endif
    
        <footer></footer>
        
    </article>

    
    <dialog id="stock-form" class="form_container">

        <form class="form" method="POST" action="/sectors">
           
            <header class="band">
                <div>
                    <h2>Create sector</h2>                    
                </div>
            </header>

            <div class="form_content">
           
            @csrf()

            <div class="form-field">
                <input type="hidden" value="{{old('id')}}" name="id" id="id"> 
                <label for="sector">Sector</label>
                <input type="text" value="{{old('sector')}}" name="sector" id="sector" required 
                class="@error('sector') is-invalid @enderror" />
            </div>

            <div class="form-field">
                <label for="sub_sector">Sub sector</label>
                <input type="text" value="{{old('sub_sector')}}" name="sub_sector" id="sub_sector" 
                class="@error('sub_sector') is-invalid @enderror" />
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

        function getData(sector_id){
            
            //get record from db
            let request = new XMLHttpRequest();
            request.open('GET', '/sector/detail/' + sector_id, true);

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
            document.querySelector('input#sector').value = data.sector;
            document.querySelector('input#sub_sector').value = data.sub_sector;
        }

        function resetInputFields(){
            document.querySelector('input#id').value = '';
            document.querySelector('input#sector').value = '';
            document.querySelector('input#sub_sector').value = '';
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