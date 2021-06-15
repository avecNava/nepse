@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('header_title')
    Stock detail
@endsection

@section('custom_js')
    <script src="{{ URL::to('js/portfolio.js') }}"></script>
@endsection

@section('content')

<style>
    section.info_band div {
    line-height: 26px;
}
tfoot td {
    background: #fff;
}
</style>

    <div id="portfolio-detail">
   
        @php
            $ltp=100;
            $ltp_prev=100;
            $price_high=0;
            $price_low=0;
            $price_high_52=0;
            $price_low_52=0;
            $qty = $info['quantity'];
            $worth= $qty * $ltp;
            $investment = $info['investment'];
            $wacc = ($investment > 0 ) ? $investment / $qty : 0;
            if(!empty($price)){
                $ltp = $price->last_updated_price ? $price->last_updated_price : $price->close_price;
                if(!$ltp) $ltp = 100; 
                $ltp_prev = $price->previous_day_close_price;
                if(!$ltp_prev) $$ltp_prev = 100; 
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
      
        <section>
            <div class="ps__wrapper">
                <div class="shareholder-info">
                    <h2>
                        <a href="{{ url('portfolio', [ $shareholder['uuid'] ]) }}">
                            {{ $shareholder['name'] }}
                        </a>
                    </h2> 
                    @if($stock)
                    <h3 class='highlight'>{{$stock['security_name']}} ({{$stock['symbol']}})</h3>
                    <h3>{{optional($stock->sector)->sector}}</h3>
                    @endif
                    <section id="basket" class="item basket" style="display:none">
                    <header>
                        <h3>Add to Sales basket</h3>
                        @csrf()                    
                    </header>
                    <div class="flex al-cntr">
                        @if($stock)
                        <label for="sell_quantity" style="padding:4px">Quantity &nbsp;&nbsp;
                            <input type="number" name="sell_quantity" id="sell_quantity" 
                            data-uuid="{{  $shareholder['uuid'] }}"
                            data-stock-id="{{  $stock['id']  }}">
                        </label>
                        @endif
                        <div class="flex al-cntr">
                        <button onClick="addToBasket()">Add to basket</button>
                        <span class='button'><a href="{{url('cart')}}">View basket</a></span>
                        </div>
                    </div>
                    <div id="basket_message"></div>
                </section>
                
                </div>
                <div class="flex js-start">
                    <div class="stock left">
                    
                        <div class="item">
                            <label>Total quantity </label>
                            <span class="value" data-quantity="{{$qty}}" id="total_quantity">
                                {{number_format($qty)}}
                            </span>
                        </div>
                        <div class="item">
                            <label>WACC (Weighted avg.) </label>
                            <span class="value" id="wacc" data-rate="{{$wacc}}">
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
                            @if($change != 0)
                            <span class="value {{$change_class}}">
                                {{number_format($change)}} ({{number_format($change_per,2)}}%)
                            </span>
                            @else
                            -
                            @endif
                        </div>  
                    </div>
                    <div class="stock right">
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
                            @if($gain != 0)
                            <span class="value {{$gain_class}}">
                            {{number_format($gain)}} ({{number_format($gain_per,2)}}%)
                            </span>
                            @endif
                        </div>  
                        <div class="item">
                        @if($price_high != 0)
                            <label>High/Low (LTP) </label>
                            <span class="value"> 
                                {{number_format($price_high)}}/{{number_format($price_low)}}
                            </span>
                        @endif
                        </div>
                        <div class="item">
                            <label>High/low (52 weeks)</label>
                            @if($price_high_52 != 0)
                            <span class="value">
                                {{number_format($price_high_52)}}/{{number_format($price_low_52)}}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
      

            <div style="position:relative">

            @php 
                $hidden = 'hidden';
                if($errors->any()){
                    $hidden = '';
                }
            @endphp

            <div id="portfolio-form__wrapper" {{$hidden}}>

                <header>
                    <h2>Edit Stock</h2>
                </header>

                <form method="POST" action="/portfolio/edit">
                    
                    @csrf()
                    <input type="hidden" name="id" id="id"  value="{{ old('id') }}"> 
                    <input type="hidden" name="shareholder_id" id="shareholder_id"  value="{{ old('shareholder_id', $shareholder['uuid']) }}">
                    <!-- @if($stock)
                    <input type="hidden" name="stock_id" value="{{ old('stock_id', $stock['id']) }}">
                    @endif
                    @if($stock)
                    <div class="form-field">
                        <label>Script</label><div><strong>{{$stock['security_name']}}</strong></div>
                    </div>
                    @endif -->

                    <section>
                        
                        <div class="form-field">
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

                        <div class="form-field">
                            <label for="unit_cost"  
                            class="@error('unit_cost') is-invalid @enderror">Unit cost</label>
                            <input type="number" name="unit_cost" id="unit_cost" step=".01" required  class="input-sm" 
                            value="{{ old('unit_cost') }}"/>
                        </div>
                        <div class="form-field">
                            <label for="quantity"
                            class="@error('quantity') is-invalid @enderror">Quantity</label>
                            <input type="number" name="quantity" id="quantity" required  class="input-sm" 
                            value="{{ old('quantity') }}"/>
                        </div>
                        <div class="form-field">
                            <label for="" title="Base price * Quantity"
                            class="@error('base_amount') is-invalid @enderror">Base amount</label>
                            <input type="number" step=".01" name="base_amount" id="base_amount" required  class="input-sm" 
                            value="{{ old('base_amount') }}"/>
                        </div>
                        <div class="form-field">
                            <label for="total_amount" title="Bill amount inclusive commissions"
                            class="@error('total_amount') is-invalid @enderror">Total amount</label>
                            <input type="number" step=".01" name="total_amount" id="total_amount" required  class="input-sm" 
                            value="{{ old('total_amount') }}"/>
                        </div>
                        <div class="form-field">
                            <label for="effective_rate"
                            class="@error('effective_rate') is-invalid @enderror">Effective rate</label>
                            <input type="number" step=".01" name="effective_rate" id="effective_rate" required  class="input-sm" 
                            value="{{ old('effective_rate') }}"/>
                        </div>

                    </section>

                    <section id='secondary' class='hide'>

                        <div class="form-field">
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
                        <div class="form-field">
                            <label for="sebon_commission">Broker commission<label>
                            <input type="number" step=".01" name="broker_commission" id="broker_commission"  value="{{ old('broker_commission','') }}"  class="input-sm" > 
                        </div>
                        <div class="form-field">
                            <label for="sebon_commission">SEBON commission<label>
                            <input type="number" step=".01" name="sebon_commission" id="sebon_commission"  value="{{ old('sebon_commission','') }}"  class="input-sm" > 
                        </div> 
                        <div class="form-field">
                            <label for="dp_amount">DP amount<label>
                            <input type="number" step=".01" name="dp_amount" id="dp_amount"  value="{{ old('dp_amount','') }}" class="input-sm"  class="input-sm" > 
                        </div> 
                    </section>

                    <section>
                        
                        <div class="form-field">
                            <label for="receipt_number"
                            class="@error('receipt_number') is-invalid @enderror">Receipt number</label>
                            <input type="text" name="receipt_number" id="receipt_number" 
                            value="{{ old('receipt_number') }}"/>
                        </div>
                        <div class="form-field optional">
                            <label for="tags"
                            class="@error('tags') is-invalid @enderror">Tags</label><br/>
                            <input type="text" name="tags" id="tags" 
                            value="{{ old('tags') }}"/>
                        </div>
                        <div class="form-field optional">
                            <label for="purchase_date"
                            class="@error('purchase_date') is-invalid @enderror">Purchase date</label><br/>
                            <input type="date" name="purchase_date" id="purchase_date"  class="input-sm" 
                            value="{{ old('purchase_date') }}"/>
                        </div>
                        <div class='flex'>
                            <button type="submit" class="focus">Save</button>
                            <button id="cancel" type="reset">Cancel</button>
                        </div>

                    </section>
        
                </form> 

            </div>

        </section>
        
        
            <header class="info js-apart flex">
                @php
                    $count = count($portfolios);
                    $quantity = $portfolios->sum('quantity');
                    $count_str = ($count <= 1) ? ' record' :' records';
                @endphp

                <section class="message">

                <div id="message">
                    
                <h3>{{$count}} {{$count_str}} ({{$quantity}} units)</h3>

                    @if ($errors->any())
                    <div class="error">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if(session()->has('message'))
                    <div class="success">                            
                        {{ session()->get('message') }}
                    </div>
                    @endif

                </div>
                </section>
                
                
                <div class="flex al-cntr">
                    <button id="new">New</button>
                    <button id="edit">Edit</button>
                    <button id="delete">Delete</button>
                </div>

            </header>
            <main>
                
                <table>
                    <thead>
     
                    <tr>
                        <th class="flex al-cntr">
                            <input type="checkbox" name="select_all" id="select_all" onClick="checkAll()">
                            <label for="select_all">&nbsp;Symbol</label>                            
                        </th>
                        <th class="optional">Offering type</th>
                        <th class="c_digit">Quantity</th>
                        <th class="c_digit">Unit cost</th>
                        <th class="c_digit" title="Effective rate">Eff. rate</th>
                        <th class="c_digit optional">Total amount</th>
                        <th class="c_digit">LTP</th>
                        <th class="c_digit">Worth</th>
                        <th class="c_digit">Gain</th>
                        <th class="c_digit optional">Purchase date</th>
                        <th class="optional">Tags</th>
                    </tr>
                    </thead>
                    <tbody>
                        
                    @if( count($portfolios)==0 )
                    <tr>
                        <td colspan="11">
                            <div class="center-box error-box">
                                <h2 class="message error">Nothing in here<h2>
                                <h3 class="message success">💡 You can add some by clicking the `New` button.</h3>
                            </div>
                        </td>
                    </tr>
                    @endif

                    @foreach ($portfolios as $record)
                        @php
                            $ltp = $record->last_updated_price?: $record->close_price;
                            if(!$ltp) $ltp = 100;
                            $qty = $record->quantity;
                            $worth = $qty * $ltp;
                            $investment = $record->total_amount;
                            $gain = $worth - $investment;
                            $gain_class = \App\Services\UtilityService::gainLossClass1($gain);
                            $gain_per = \App\Services\UtilityService::calculatePercentage($gain, $investment);
                        @endphp
                        
                        <tr id="row-{{ $record->id }}"  class="@if(empty($record->wacc_updated_at)) strike @endif">
                            
                            <td title="{{ $record->stock_id }}-{{ $record->security_name }}" >
                                <div style="display:flex;flex-wrap:nowrap;align-items:center">
                                @if( !empty($record))
                                    <input type="checkbox" name="s_id" id="{{ $record->id }}">
                                    <label for="{{ $record->id }}" style="padding:5px">
                                        {{ $record->symbol }}
                                    </label>
                                @endif
                                </div>
                            </td>
                            <td title="{{$record->offer_name}}" class="optional">
                                <label for="{{ $record->id }}" style="padding:5px">
                                    {{$record->offer_code}}
                                </label>
                            </td>
                            <td class="c_digit">{{ $qty }}</td>
                            <td class="c_digit">{{ number_format($record->unit_cost) }}</td>
                            <td class="c_digit">{{ number_format($record->effective_rate, 2) }}</td>
                            <td class="c_digit optional">{{ number_format($record->total_amount) }}</td>
                            <td class="c_digit">{{ number_format($ltp) }}</td>
                            <td class="c_digit">{{ number_format($worth) }}</td>
                            <td class="c_digit">
                                @if($gain !=0)
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
                                @endif
                            </td>
                            <td class="c_digit optional">{{$record->purchase_date}}</td>
                            <td>{{$record->tags}}</td>
                        </tr>
                        @endforeach   
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="11">
                                <div class="flex js-apart al-end">
                                        <div>
                                            <strong>📢 </strong>Stocks marked with 
                                            <span style="text-decoration:line-through;color:red;"> strikethrough needs to be udpated for Purchase price </span>  
                                        </div>
                                </div>
                            </td>
                        </tr>
                    </tfoot>

                </table>

            </main>

            </div>
        
        <section id="sales-list" class="sales"> 
            @php
                $count = count($sales);
                $quantity = $sales->sum('quantity');
                $count_str = ($count <= 2) ? ' record' :' records';
            @endphp
            @if($count)
            <details>
                <summary><h2>Sales</h2> - {{$count}} {{$count_str}} [{{$quantity}} units] </summary>                
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

    document.getElementById('select_all').addEventListener('change', function(e){
        checkAll();
    });


    // handle New button clicked
    document.getElementById("new").addEventListener("click", function() {
        const url = `${window.location.origin}/portfolio/new`;
        //redirect to the mian form
        window.location.replace(url);
    });

    // handle Cancel button
    document.getElementById("cancel").addEventListener("click", function() {
        hideForm('portfolio-form__wrapper');
        resetInputFields();
    });

    // handle Edit button clicked
    document.getElementById("edit").addEventListener("click", function() {

        var chk = document.querySelector('input[name=s_id]:checked');
        
        if(chk === null){
            msg = 'Please select a record';
            showMessage(msg, 'message', 'error'); return;
        }

        showLoadingMessage();
        showForm('portfolio-form__wrapper');
        
        document.querySelector('.message').innerHTML='';


        let request = new XMLHttpRequest();
        const url = `${window.location.origin}/portfolio/get/${chk.id}`;
        console.log(url);
        request.open('GET', url, true);

        request.onload = function() {

            if (this.status >= 200 && this.status < 400) {
                data = JSON.parse(this.response);
                updateInputFields(data);
                //shows fields related to broker
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
        document.getElementById('base_amount').value = $record.base_amount;
        document.getElementById('effective_rate').value = $record.effective_rate;
        document.getElementById('receipt_number').value = $record.receipt_number;
        document.getElementById('tags').value = $record.tags;
        document.getElementById('purchase_date').value = $record.purchase_date;
        document.getElementById('broker_commission').value = ($record.broker_commission) ? $record.broker_commission : '';
        document.getElementById('sebon_commission').value = ($record.sebon_commission) ? $record.sebon_commission : '';
        document.getElementById('dp_amount').value = ($record.dp_amount) ? $record.dp_amount : '';
        setOption(document.getElementById('broker'), $record.broker_no);
        setOption(document.getElementById('offer'), $record.offer_id);
        setOption(document.getElementById('broker'), $record.broker_id);

    }

    function resetInputFields() {
        const MyDate = new Date();
        // MyDate.setDate(MyDate.getDate() + 20);

        const date_str = MyDate.getFullYear() + '-' + ('0' + MyDate.getMonth() + 1).slice(-2) + '-'
             + ('0' + (MyDate.getDate()+1)).slice(-2);

        document.getElementById('id').value = '';
        document.getElementById('quantity').value = '';
        document.getElementById('unit_cost').value = '';
        document.getElementById('total_amount').value = '';
        document.getElementById('base_amount').value = '';
        document.getElementById('effective_rate').value = '';
        document.getElementById('broker_commission').value = '';
        document.getElementById('sebon_commission').value = '';
        document.getElementById('dp_amount').value = '';
        document.getElementById('receipt_number').value = '';
        document.getElementById('tags').value = '';
        document.getElementById('purchase_date').value = date_str;
        setOption(document.getElementById('broker'), 0);
        setOption(document.getElementById('offer'), 0);
        setOption(document.getElementById('broker'), 0);

    }

    // handle Delete button clicked
    document.getElementById("delete").addEventListener("click", function() {
    let selected = [];
    let elements = document.getElementsByName("s_id");
    let ele_import = document.getElementById('message');
    let url = `${window.location.origin}/portfolio/delete`;

    Array.prototype.forEach.call(elements, function(el, i){
        if(el.checked){
            selected.push(el.id);
        }
    });

    if(selected.length <= 0 ){
        let message = 'Please select records to delete';
        showMessage(message,'message','error');
        return;
    }

    if(confirm('Please confirm the delete operation')){

            showLoadingMessage();
            let _token = document.getElementsByName('_token')[0].value;
            let request = new XMLHttpRequest();            
            request.open('POST', url, true);
            request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
            request.onload = function() {                
                data = JSON.parse(this.response);                
                if (this.status >= 200 && this.status < 400) {
                    showMessage(data.message,'message');

                    //if no records remain, redirect to main page
                    if(data.quantity == 0){
                        url = `${window.location.origin}/portfolio`;
                        window.location.replace(url);
                    }
                    window.location.reload();   //refresh the page
                }
            }  
            request.onerror = function() {
            // There was a connection error of some sort
            hideLoadingMessage();
            };

            request.send(`_token=${_token}&rows=${selected.toString()}`);

        }
    
    });

    // function hideDeletedRow($id) {
    //     const tag = document.getElementById('delete').dataset.id;
    //     const id = parseID('chk-', tag);
    //     document.getElementById('row-'+id).classList.add('hide');
    // }

    function resetSellError(){
        const msg = document.querySelector('#basket_message')
        msg.classList.remove('error');
        msg.classList.remove('success');
        msg.innerHTML = '';
    }
    
    function saveToBasket(sell_quantity, shareholder_id, stock_id){
        
        showLoadingMessage();
        const url = `${window.location.origin}/cart/store`;
        let _token = document.getElementsByName('_token')[0].value;
        let request = new XMLHttpRequest();
        request.open('POST', url, true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        request.onload = function() {
            const data = JSON.parse(this.response);
            if (this.status >= 200 && this.status < 400) {
                showMessage(data.message, 'basket_message');
            }
            else{
                showMessage(data.message,'basket_message', 'error');            
            }
            hideLoadingMessage();
        }
        request.send(`_token=${_token}&stock_id=${stock_id}&uuid=${shareholder_id}&quantity=${sell_quantity}`);
    }

    function addToBasket(){

        const ele = document.querySelector('#sell_quantity');
        const uuid = ele.dataset.uuid;
        const stock_id = ele.dataset.stockId;
        const sell_quantity = ele.value;

        if(!sell_quantity){
            showMessage( 'Enter sell quantity','basket_message', 'error');
            return false;
        }

        const total_quantity = document.querySelector('#total_quantity').dataset.quantity;

        const diff = parseInt(total_quantity) - parseInt(sell_quantity);

        if( parseInt(sell_quantity) >  (total_quantity)){
            showMessage(`Sell quantity exceeds the total quantity (${total_quantity})`,'basket_message', 'error');
            return false;
        }

        const wacc = document.querySelector('#wacc').dataset.rate;
        if(!wacc){
            showMessage('Weighted average not updated for this stock.','basket_message', 'error');
            return false;
        }

        resetSellError();
        saveToBasket(sell_quantity, uuid, stock_id);

    }

</script>
@endsection