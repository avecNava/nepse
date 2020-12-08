@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('header_title')
    <h1 class="c_title">Portfolio details</h1>
@endsection

@section('js')
    
@endsection

@section('content')

    <div class="c_portfolio_container">
    
        <div id="loading-message" style="display:none">Loading... Please wait...</div>

        <section class="c_info_band">

            <div class="info_band_top">
                <div class="block-left">

                    <h2 class="name">{{$shareholder_name}}</h2>
                    <div class="stock">
                        <h3>Symbol : {{$stock_name}}</h3>
                        <h3>Total quantity : {{$total_stocks}}</h3>
                        <h3>Last price (LTP) : NPR {{$last_price}}</h3>
                    </div>

                </div>

                <div class="block-right">

                    <div class="stock">
                        <h3>Total investment : NPR {{$total_investment}}</h3>
                        <h3>Current worth : NPR {{$net_worth}}</h3>
                        <h3>Net Gains : {{$net_gain}}</h3>  
                        <h3>Net Gains per : {{$net_gain}}%</h3>  
                    </div>

                </div>
            </div>
        
            </section>

            <div id="portfolio-form" class="info_band_bottom"  @if (!$errors->any()) hidden @endif>

                <form method="POST" action="/portfolio/edit">
                    
                    @csrf()
                    <input type="hidden" name="id" id="id"  value="{{ old('id') }}"> 
                    <input type="hidden" name="shareholder_id" id="shareholder_id"  value="{{ $shareholder_id }}"> 
                    <section>
                        <div class="form-field">
                            <label for="quantity"
                            class="@error('quantity') is-invalid @enderror">Quantity</label>
                            <input type="number" name="quantity" required id="quantity" required 
                            value="{{ old('quantity') }}"/>
                        </div>

                        <div>
                            <label for="unit_cost"  
                            class="@error('unit_cost') is-invalid @enderror">Unit cost</label>
                            <input type="number" name="unit_cost" require id="unit_cost" required 
                            value="{{ old('unit_cost') }}"/>
                        </div> 
                    </section>
                    <section>
                        <div>
                            <label for="total_amount" title="bill amount"
                            class="@error('total_amount') is-invalid @enderror">Total amount</label>
                            <input type="number" name="total_amount" id="total_amount" required 
                            value="{{ old('total_amount') }}"/>
                        </div> 

                        <div>
                            <label for="effective_rate" title="bill amount"
                            class="@error('effective_rate') is-invalid @enderror">Effective rate</label>
                            <input type="number" name="effective_rate" id="effective_rate" required 
                            value="{{ old('effective_rate') }}"/>
                        </div> 
                    </section>
                    <section>
                        <div>
                            <label for="offer"
                            class="@error('offer') is-invalid @enderror">Offer type</label>
                            <select name="offer" id="offer">
                                @if(!empty(@offers))
                                @foreach($offers as $offer)
                                <option value="0">Select</option>
                                    <option value="{{ $offer->id }}">{{$offer->offer_name}}</option>
                                @endforeach
                                @endif
                            </select> 
                        </div>

                        <div>
                            <label for="broker"
                            class="@error('broker') is-invalid @enderror">Broker</label>
                            <select name="broker" id="broker">
                                @if(!empty(@brokers))
                                <option value="0">Select</option>
                                @foreach($brokers as $broker)
                                    <option value="{{ $broker->broker_no }}">{{$broker->broker_name}}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                    </section>
                    <section>
                        <div>
                            <label for="receipt_number" title="bill amount"
                            class="@error('receipt_number') is-invalid @enderror">Receipt number</label>
                            <input type="text" name="receipt_number" id="receipt_number" 
                            value="{{ old('receipt_number') }}"/>
                        </div> 

                        <div>
                            <button type="submit">Save</button>
                            <button id="cancel" type="reset" onClick="hideForm()">Cancel</button>
                        </div>
                    </section>
                </form> 

                <div class="validation-error">
                @if ($errors->any())
                    <div class="error">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                </div>

            </div>

        

        <section class="portfolio">
        @if( !empty($portfolios) )
           
            <article class="a_portfolio_details">
            
                <header>

                <div class="a_portfolio_main">
  
                    <div class="c_band_right">

                        <div id="message" class="message">                            
                            
                            @if(session()->has('message'))
                                {{ session()->get('message') }}
                            @else
                                @if(count($portfolios)>0)
                                    {{count($portfolios)}} records
                                @else
                                    😟OOpsy! There are not any records to display. Click `New` button to add some.
                                @endif
                            @endif

                        </div>
                        
                        <div class="action-buttons">
                            <button id="new">New</button>
                            <button id="edit">Edit</button>
                            <button id="delete">Delete</button>
                        </div>

                    </div>

                </div>
                </header>

                <main>
                <table>
                    <tr>
                        <th>Symbol</th>
                        <th>Quantity</th>
                        <th>Unit cost</th>
                        <th>Total amount</th>
                        <th>Effective rate</th>
                        <th>Offer</th>
                        <th>Sector</th>
                        <th>Shareholder</th>
                        <th>Purchase date</th>
                    </tr>
                    
                    @foreach ($portfolios as $record)
                        
                        <tr id="row-{{ $record->id }}">
                            
                            <td title="{{ $record->security_name }}">
                                @if( !empty($record))
                                    <input type="checkbox" name="s_id" id="chk-{{ $record->id }}">&nbsp;
                                    <a href="#{{url('portfolio/edit', [$record->id])}}">{{ $record->symbol }}</a>
                                @endif
                            </td>
                            <td>{{$record->quantity}}</td>
                            <td>{{$record->unit_cost}}</td>
                            <td>{{$record->total_amount}}</td>
                            <td>{{$record->effective_rate}}</td>
                            <td title="{{$record->offer_name}}">{{$record->offer_code}}</td>
                            <td>{{$record->sector}}</td>
                            <td>
                                <div id='owner_{{$record->shareholder_id}}'>{{$record->first_name}} {{$record->last_name}}</div>
                            </td>
                            <td>{{$record->purchase_date}}</td>
                        </tr>

                    @endforeach   

                </table>
            </main>
            
            <footer></footer>
            
        </article>
        @endif

        </section>

    </div> <!-- end of portfolio_container -->

    <script>

        // Select all checkboxes with the name 's_id' using querySelectorAll.
        var checkboxes = document.querySelectorAll("input[type=checkbox][name=s_id]");

        //capture the id of the selected checkbox
        Array.prototype.forEach.call(checkboxes, function(el, i){

            el.addEventListener('change', function() {
                
                let s_id = this.id;

                if(this.checked){
                    document.getElementById('edit').setAttribute('data-id', s_id);
                    document.getElementById('delete').setAttribute('data-id', s_id);
                }

                else {
                    document.getElementById('edit').removeAttribute('data-id');
                    document.getElementById('delete').removeAttribute('data-id');
                }

                // console.log(this.id, this.checked);
                
                //uncheck all other checkboxes (one select at a time)
                Array.prototype.forEach.call(checkboxes, function(el, i){
                if(el.id != s_id)
                    el.checked = false;
                });

            })

        });
        
       //-------------------------------------
        // handle New button clicked
        //-------------------------------------
        let btn_new = document.getElementById("new");
        
        btn_new.addEventListener("click", function() {
            const url = `${window.location.origin}/portfolio/new`;
            //redirect to the mian form
            window.location.replace(url);
        });

        
        //-------------------------------------
        // handle Cancel button
        //-------------------------------------
        let btnCancel = document.getElementById("cancel");
        btnCancel.addEventListener("click", function() {
        hideForm('portfolio-form');
        resetInputFields();
        });


        //-------------------------------------
        // handle Edit button clicked
        //-------------------------------------
        let btn = document.getElementById("edit");
        btn.addEventListener("click", function() {

            //retrieve the data-id attribute from the edit button
            let el = document.getElementById('edit');
            let id_string = el.getAttribute('data-id');        //eg, id_string=chk_29
            if(!id_string){
                msg = 'Please select a record to edit';
                showMessage(msg); return;
            }

            showLoadingMessage();
            clearMessage();
            showForm('portfolio-form');

            //parse the id from the given string
            let record_id = parseID('chk_', id_string);

            let request = new XMLHttpRequest();
            const url = `${window.location.origin}/portfolio/get/${record_id}`;
            request.open('GET', url, true);

            request.onload = function() {

                if (this.status >= 200 && this.status < 400) {
                    $data = JSON.parse(this.response);
                    updateInputFields($data);
                    hideLoadingMessage();
                }
            }  

            request.onerror = function() {
                // There was a connection error of some sort
                hideLoadingMessage();
            };
            
            request.send();

        });


        //--------------------------------------------------------------------------------------
        // data contains the record being created (first_name, last_name, parent_id, gender etc)
        //--------------------------------------------------------------------------------------

        function updateInputFields($record) {
        

            document.getElementById('id').value = $record.id;
            // document.getElementById('shareholder_id').value = $record.shareholder_id;
            document.getElementById('quantity').value = $record.quantity;
            document.getElementById('unit_cost').value = $record.unit_cost;
            document.getElementById('total_amount').value = $record.total_amount;
            document.getElementById('effective_rate').value = $record.effective_rate;
            document.getElementById('receipt_number').value = $record.receipt_number;
            setOption(document.getElementById('offer'), $record.offer_id);
            setOption(document.getElementById('broker'), $record.broker_id);

        }
        
        //--------------------------------------------------------------------------------------
        // reset
        //--------------------------------------------------------------------------------------

        function resetInputFields() {

            document.getElementById('id').value = '';
            // document.getElementById('shareholder_id').value = '';
            document.getElementById('quantity').value = '';
            document.getElementById('unit_cost').value = '';
            document.getElementById('total_amount').value = '';
            document.getElementById('effective_rate').value = '';
            document.getElementById('receipt_number').value = '';
            setOption(document.getElementById('offer'), 0);
            setOption(document.getElementById('broker'), 0);

        }

        //-------------------------------------
        // handle Delete button clicked
        //-------------------------------------
        let btn_delete = document.getElementById("delete");
        
        btn_delete.addEventListener("click", function() {
        
            //retrieve the data-id attribute from the delete button
            //the data-id attirbute is the id of the row
            const  el = document.getElementById('delete');
            let id_string = el.getAttribute('data-id');        //eg, id_string=chk_29
            
            if(!id_string){
                msg = 'Please select a record to delete';
                showMessage(msg); return;
            }

            //parse the id from the given string
            let record_id = parseID('chk_', id_string);

            if(confirm('Please confirm the delete operation')) {

                clearMessage();
                showLoadingMessage();

                let request = new XMLHttpRequest();
                const url = `${window.location.origin}/portfolio/delete/${record_id}`;
                request.open('GET', url, true);
            
                request.onload = function(ele_success, ele_loading) {
                    if (this.status >= 200 && this.status < 400) {
                        $data = JSON.parse(this.response);
                        showMessage($data.message);
                        hideLoadingMessage();
                    }
                }  
                request.onerror = function() {
                // There was a connection error of some sort
                hideLoadingMessage();
                };

                request.send(); 

            }

        });
     
    </script>

@endsection