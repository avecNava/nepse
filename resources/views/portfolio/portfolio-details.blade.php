@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('header_title')
    <h1 class="c_title">Portfolio details</h1>
@endsection

@section('js')
    <script src="{{ URL::to('js/portfolio.js') }}"></script>
@endsection

@section('content')

    <div class="c_portfolio_container">
    
        <div id="loading-message" style="display:none">Loading... Please wait...</div>

        @php
            $ltp=0;
            $ltp_prev=0;
            $worth=0;
            $price_high=0;
            $price_low=0;
            $price_high_52=0;
            $price_low_52=0;
            $qty = $info['quantity'];
            $investment = $info['investment'];
            $wacc = ($investment > 0 ) ? $investment / $qty : 0;
            if(!empty($price)){
                $ltp = $price->last_updated_price ? $price->last_updated_price : $price->close_price;
                $ltp_prev = $price->previous_day_close_price;
                $worth = $qty * $ltp;
                $worth_prev = $qty * $ltp_prev;
                $price_high = $price->high_price;
                $price_low = $price->low_price;
                $price_high_52 = $price->fifty_two_week_high_price;
                $price_low_52 = $price->fifty_two_week_low_price;
            } 
            $change = $ltp - $ltp_prev;
            $gain = $worth - $investment;
            $change_per = 0; $gain_per=0;
            if($ltp_prev > 0){
                $change_per = ($change/$ltp_prev)*100;
            }
            if($investment > 0){
                $gain_per = ($gain/$investment)*100;
            }
            $gain_class =''; $change_class = '';
            if($change > 0) { $change_class='increase'; } else if($change < 0) { $change_class='decrease'; }
            if($gain > 0) { $gain_class='increase'; } else if($gain < 0) { $gain_class='decrease'; }
        @endphp
            
        <section class="info_band_top apart">

                <div class="block-left">

                    <section class="shareholder nav">
                        <h2>
                            <a href="{{ url('portfolio',[ $info['shareholder_str'], $info['shareholder_id'] ]) }}">
                                {{ $info['shareholder'] }}
                            </a>
                        </h2>
                    </section>

                    <div class="stock">
                        <h2 class='highlight'>{{$info['security_name']}}</h2>
                        <h3>{{$info['sector']}}</h3>
                        <div class="item">
                            <label>Total quantity </label>
                            <span class="value" id="total_quantity">
                                {{number_format($qty)}}
                            </span>
                        </div>
                        <div class="item">
                            <label>WACC (Weighted avg.) </label>
                            <span class="value" id="wacc">
                                {{number_format($wacc,2)}}
                            </span>
                        </div>
                        <div class="item">
                            <label>Last price (LTP) </label>
                            <span class="value">
                                {{number_format($ltp)}}
                            </span>
                        </div>
                        <div class="item">
                            <label>Previous day price </label>
                            <span class="value">
                                {{number_format($ltp_prev)}}
                            </span>
                        </div>
                        <div class="item">
                            <label>Change </label>
                            <span class="value {{$change_class}}">
                                {{number_format($change)}} ({{number_format($change_per,2)}}%)
                            </span>
                        </div>  
                    </div>

                </div>

                <div class="block-right">
                   
                    <div class="stock">
                        <div class="item">
                            <label>Total investment </label>
                            <span class="value">
                                {{ number_format($investment)}}
                            </span>
                        </div>
                        <div class="item">
                            <label>Current worth </label>
                            <span class="value">
                                {{number_format($worth)}}
                            </span>
                        </div>
                        <div class="item">
                            <label>Gain </label>
                            <span class="value {{$gain_class}}">
                            {{number_format($gain)}} ({{number_format($gain_per,2)}}%)
                            </span>
                        </div>  
                        <div class="item">
                            <label>High </label>
                            <span class="value"> 
                                {{number_format($price_high)}}
                            </span>
                        </div>
                        <div class="item">
                            <label>Low </label>
                            <span class="value"> 
                                {{number_format($price_low)}}
                            </span>
                        </div>
                        <div class="item">
                            <label>52 weeks high</label>
                            <span class="value">
                                {{number_format($price_high_52)}}
                            </span>
                        </div>
                        <div class="item">
                            <label>52 weeks low</label>
                            <span class="value">
                                {{number_format($price_low_52)}}
                            </span>
                        </div>
                        <div class="item basket">
                            <h3>Sales</h3>
                            <label for="sell_quantity">Quantity &nbsp;&nbsp;
                                <input type="number" name="sell_quantity" id="sell_quantity" 
                                data-shareholder-id="{{  $info['shareholder_id'] }}"
                                data-stock-id="{{  $info['stock_id'] }}">
                            </label>
                            <button onClick="addToBasket()">Add to basket</button>
                            @csrf()
                            <div id="sell_message"></div>
                        </div>
                    </div>

                </div>

        </section>

        @php 
            $hidden = 'hidden';
            if($errors->any()){
                $hidden = '';
            }
        @endphp

        <div id="portfolio-form" class="info_band_bottom" {{$hidden}}>

            <form method="POST" action="/portfolio/edit">
                
                @csrf()
                <input type="hidden" name="id" id="id"  value="{{ old('id') }}"> 
                <input type="hidden" name="shareholder_id" id="shareholder_id"  value="{{ old('shareholder_id', $info['shareholder_id']) }}">
                <input type="hidden" name="stock_id" value="{{ old('stock_id', $info['stock_id']) }}">

                <section>
                    <div class="display-label">
                        <label>Shareholder</label><div title="{{$info['relation']}}"><strong>{{$info['shareholder']}}</strong></div>
                    </div>
                    <div class="display-label">
                        <label>Script</label><div><strong>{{$info['security_name']}}</strong></div>
                    </div>
                    <div>
                        <label for="offer" class="@error('offer') is-invalid @enderror">Offering type</label>
                        <select name="offer" id="offer">
                            @if(!empty(@offers))
                                <option data-tag="none" value="0">Select</option>
                                @foreach($offers as $offer)
                                    <option data-tag="{{ $offer->offer_code }}" value="{{ $offer->id }}"

                                        @if(old('offer') == $offer->id )
                                            SELECTED
                                        @endif

                                    >{{ $offer->offer_code }} ({{$offer->offer_name}})</option>
                                @endforeach
                            @endif
                        </select> 
                    </div>
                </section>

                <section>
                    <div>
                        <label for="unit_cost"  
                        class="@error('unit_cost') is-invalid @enderror">Unit cost</label>
                        <input type="number" name="unit_cost" require id="unit_cost" required 
                        value="{{ old('unit_cost') }}"/>
                    </div>
                    <div class="form-field">
                        <label for="quantity"
                        class="@error('quantity') is-invalid @enderror">Quantity</label>
                        <input type="number" name="quantity" required id="quantity" required 
                        value="{{ old('quantity') }}"/>
                    </div>
                    <div>
                        <label for="total_amount" title="bill amount"
                        class="@error('total_amount') is-invalid @enderror">Total amount</label>
                        <input type="text" name="total_amount" id="total_amount" required 
                        value="{{ old('total_amount') }}"/>
                    </div>
                    <div>
                        <label for="effective_rate"
                        class="@error('effective_rate') is-invalid @enderror">Effective rate</label>
                        <input type="text" name="effective_rate" id="effective_rate" required 
                        value="{{ old('effective_rate') }}"/>
                    </div>

                </section>

                <section id='secondary' class='hide'>

                    <div>
                        <label for="broker"
                        class="@error('broker') is-invalid @enderror">Broker</label>
                        <select name="broker" id="broker">
                            @if(!empty(@brokers))
                            <option value="0">Select</option>
                            @foreach($brokers as $broker)
                                <option value="{{ $broker->broker_no }}">{{ $broker->broker_no }} - {{$broker->broker_name}}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <div>
                        <label for="sebon_commission">Broker commission<label>
                        <input type="text" name="broker_commission" id="broker_commission"  value="{{ old('broker_commission','') }}"> 
                    </div>
                    <div>
                        <label for="sebon_commission">SEBON commission<label>
                        <input type="text" name="sebon_commission" id="sebon_commission"  value="{{ old('sebon_commission','') }}"> 
                    </div> 
                </section>

                <section>
                    <div>
                        <label for="receipt_number"
                        class="@error('receipt_number') is-invalid @enderror">Receipt number</label>
                        <input type="text" name="receipt_number" id="receipt_number" 
                        value="{{ old('receipt_number') }}"/>
                    </div>
                    <div>
                        <label for="tags"
                        class="@error('tags') is-invalid @enderror">Tags</label>
                        <input type="text" name="tags" id="tags" 
                        value="{{ old('tags') }}"/>
                    </div>
                    <div>
                        <label for="purchase_date"
                        class="@error('purchase_date') is-invalid @enderror">Purchase date</label>
                        <input type="date" name="purchase_date" id="purchase_date" 
                        value="{{ old('purchase_date') }}"/>
                    </div>
                    <div class='action-buttons'>
                        <button type="submit">Save</button>
                        <button id="cancel" type="reset" onClick="hideForm()">Cancel</button>
                    </div>
                </section>
            </form> 

        </div>

        <div class="message error">
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

        <section class="portfolio">
            
            @if( !empty($portfolios) )
        
            <article class="a_portfolio_details">
         
                <header>

                    <div class="a_portfolio_main">
    
                        <div class="c_band apart">

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
                            <th>Offering type</th>
                            <th class="c_digit">Quantity</th>
                            <th class="c_digit">Unit cost</th>
                            <th class="c_digit" title="Effective rate">Eff. rate</th>
                            <th class="c_digit">Total amount</th>
                            <th class="c_digit">LTP</th>
                            <th class="c_digit">Worth</th>
                            <th class="c_digit">Gain</th>
                            <th class="c_digit">Purchase date</th>
                            <th>Tags</th>
                        </tr>
                        
                        @foreach ($portfolios as $record)
                            @php
                                $ltp = $record->last_updated_price?: $record->close_price;
                                $qty = $record->quantity;
                                $worth = $qty * $ltp;
                                $investment = $record->total_amount;
                                $gain = $worth - $investment;
                                $gain_class = \App\Services\UtilityService::gainLossClass1($gain);
                                $gain_per = \App\Services\UtilityService::calculatePercentage($gain, $investment);
                            @endphp
                            
                            <tr id="row-{{ $record->id }}">
                                
                                <td title="{{ $record->stock_id }}-{{ $record->security_name }}">
                                    @if( !empty($record))
                                        <input type="checkbox" name="s_id" id="chk-{{ $record->id }}">&nbsp;
                                        <label for="chk-{{ $record->id }}">{{ $record->symbol }}</label>
                                    @endif
                                </td>
                                <td title="{{$record->offer_name}}">{{$record->offer_code}}</td>
                                <td class="c_digit">{{ $qty }}</td>
                                <td class="c_digit">{{ number_format($record->unit_cost) }}</td>
                                <td class="c_digit">{{ number_format($record->effective_rate, 2) }}</td>
                                <td class="c_digit">{{ number_format($record->total_amount) }}</td>
                                <td class="c_digit">{{ number_format($ltp) }}</td>
                                <td class="c_digit">{{ number_format($worth) }}</td>
                                <td class="c_digit">
                                    <div class="c_change">
                                        <div>
                                            <span class="change-val">
                                            {{ number_format($gain, 1) }}
                                            </span>
                                            <span class="change-val {{$gain_class}}">
                                            ({{ $gain_per }})
                                            </span>
                                        </div>
                                        <div class="{{$gain_class}}_icon"></div>
                                    </div>
                                </td>
                                <td class="c_digit">{{$record->purchase_date}}</td>
                                <td>{{$record->tags}}</td>
                            </tr>

                        @endforeach   

                    </table>

                </main>
            
                <footer></footer>
            
            </article>

            @endif

        </section>
        
        <section class="sales">
            @php
                $count = count($sales);
                $quantity = $sales->sum('quantity');
                $count_str = ($count <= 2) ? ' record' :' records';
            @endphp
            @if($count)
            <details>
                <summary><h2>Sales</h2> - {{$count}} {{$count_str}} {{$quantity}} units </summary>                
                <table>
                    <tr>
                        <th>Symbol</th>
                        <th>Quantity</th>
                        <th>Sales amount</th>
                        <th>Net gain</th>
                        <th>Receipt #</th>
                        <th>Sales date</th>
                    </tr>
                    
                    @foreach ($sales as $record)
                        <tr>
                            <td data-id="stock-{{$record->share->id}}">{{$record->share->symbol}}</td>
                            <td>{{$record->quantity}}</td>
                            <td>{{$record->sales_amount}}</td>
                            <td>{{$record->net_gain}}</td>
                            <td>{{$record->receipt_number}}</td>
                            <td>{{$record->sales_date}}</td>
                        </tr>
                    @endforeach
                </table>

            </details>
            @endif
        </section>

    </div> <!-- end of portfolio_container -->

    <script>

        // handle New button clicked
        document.getElementById("new").addEventListener("click", function() {
            const url = `${window.location.origin}/portfolio/new`;
            //redirect to the mian form
            window.location.replace(url);
        });

        // handle Cancel button
        document.getElementById("cancel").addEventListener("click", function() {
            hideForm('portfolio-form');
            resetInputFields();
        });

        // handle Edit button clicked
        document.getElementById("edit").addEventListener("click", function() {

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
            
            document.querySelector('.message').innerHTML='';


            //parse the id from the given string
            let record_id = parseID('chk_', id_string);

            let request = new XMLHttpRequest();
            const url = `${window.location.origin}/portfolio/get/${record_id}`;
            request.open('GET', url, true);

            request.onload = function() {

                if (this.status >= 200 && this.status < 400) {
                    data = JSON.parse(this.response);
                    console.log(data);
                    updateInputFields(data);
                    document.querySelector('#offer').dispatchEvent(new Event("change"));
                    hideLoadingMessage();
                }
            }  

            request.onerror = function() {
                // There was a connection error of some sort
                hideLoadingMessage();
            };

            request.send();

        });

        function updateInputFields($record) {
            document.getElementById('id').value = $record.id;
            // document.getElementById('shareholder_id').value = $record.shareholder_id;
            document.getElementById('quantity').value = $record.quantity;
            document.getElementById('unit_cost').value = $record.unit_cost;
            document.getElementById('total_amount').value = $record.total_amount;
            document.getElementById('effective_rate').value = $record.effective_rate;
            document.getElementById('receipt_number').value = $record.receipt_number;
            document.getElementById('tags').value = $record.tags;
            document.getElementById('purchase_date').value = $record.purchase_date;
            document.getElementById('broker_commission').value = ($record.broker_commission) ? $record.broker_commission : '';
            document.getElementById('sebon_commission').value = ($record.sebon_commission) ? $record.sebon_commission : '';
            setOption(document.getElementById('broker'), $record.broker_no);
            setOption(document.getElementById('offer'), $record.offer_id);
            setOption(document.getElementById('broker'), $record.broker_id);

        }

        function resetInputFields() {
            let date = Date.now();
            let date_str = date.getFullYear() + '-' + date.getMonth() + 1 + '-' + getDate();

            document.getElementById('id').value = '';
            document.getElementById('quantity').value = '';
            document.getElementById('unit_cost').value = '';
            document.getElementById('total_amount').value = '';
            document.getElementById('effective_rate').value = '';
            document.getElementById('broker_commission').value = '';
            document.getElementById('sebon_commission').value = '';
            document.getElementById('receipt_number').value = '';
            document.getElementById('tags').value = '';
            document.getElementById('purchase_date').value = date_str;
            setOption(document.getElementById('broker'), 0);
            setOption(document.getElementById('offer'), 0);
            setOption(document.getElementById('broker'), 0);

        }

        // handle Delete button clicked
        document.getElementById("delete").addEventListener("click", function() {

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
                        hideDeletedRow();
                    }
                }  
                request.onerror = function() {
                // There was a connection error of some sort
                hideLoadingMessage();
                };

                request.send(); 

            }
        
        });

        function hideDeletedRow($id) {
            const tag = document.getElementById('delete').dataset.id;
            const id = parseID('chk-', tag);
            document.getElementById('row-'+id).classList.add('hide');
        }

    </script>

@endsection