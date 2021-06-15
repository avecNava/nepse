@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('header_title')
    Sales record
@endsection


@section('notice')
<?php if(strlen($notice)>0){ ?>
    <div role="notice" class='notice' data-show-notice="yes">
        {!! $notice !!}
    </div>
<?php } ?>
@endsection

@section('content')
<style>
    
    tr.basket-header {
        background: unset;
    }
    tr.basket-header h2 {padding: 0 }
    tr.basket-header td {
        padding: 0 !important;
        height: 40px !important;
    }
    .icon-buttons button {
        background: unset;
        border-radius: unset;
    }
   
</style>

<section id="top-nav" class="optional">
    <div></div>
    <div class="links">
        <div class="link">
            <a href="{{url('sales/new')}}" title="New sales">New sales</a>
        </div>
        <div class="link selected">
            <a href="{{url('sales')}}" title="See Sales" class="selected">View Sales</a>
        </div>
        <div class="link">
            <a href="{{url('cart')}}" title="See what's inside the cart">View Cart</a>
        </div>
    </div>
</section>

<section id="sales">
              
    <!-- message -->
    <div class="info">
        <h3>
            <div id="sell_message"></div>
        </h3>
    </div>

    <!-- update form  -->
    <article id="sales_crud" class="overlay">    
        <form class="form" method="POST" action="/sales/edit">
            
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
                <div class="flex">
                    <h2>Edit Sales</h2> 
                    <div class="flex">
                        <h2 id="symbol">{{old('stock_name')}}</h2>
                            <h2 id="shareholder-name">{{old('shareholder_name')}}</h2>
                    </div>
                </div>
            </header>

            <div class="form_content">
            
            @csrf()

                <input type="hidden" value="{{old('id')}}" name="id" id="id"> 
                <input type="hidden" value="{{old('shareholder_id')}}" name="shareholder_id" id="shareholder_id"> 
                <input type="hidden" value="{{old('stock_id')}}" name="stock_id" id="stock_id"> 
                <input type="hidden" value="{{old('shareholder_name')}}" name="shareholder_name" id="shareholder_name"> 
                <input type="hidden" value="{{old('stock_name')}}" name="stock_name" id="stock_name"> 

            <div class="form-field"></div>
            <div class="form-field"></div>
            <div class="form-field"></div>
            <div class="form-field">
                <label for="sales_date">Sales date</label>
                <input type="date" value="{{old('sales_date')}}" name="sales_date" id="sales_date" 
                class="@error('sales_date') is-invalid @enderror" />
            </div>

            <div class="form-field">
                <label for="quantity">Quantity</label>
                <input type="text" value="{{old('quantity')}}" name="quantity" id="quantity" 
                class="@error('quantity') is-invalid @enderror" />
            </div>

            <div class="form-field">
                <label for="wacc"><abbr title="Weighted average cost of capital">WACC</abbr></label>
                <input type="text" value="{{old('wacc')}}" name="wacc" id="wacc"
                class="@error('wacc') is-invalid @enderror" />
            </div> 
            
            <div class="form-field">
                <label for="cost_price">Cost price</label>
                <input type="text" value="{{old('cost_price')}}" name="cost_price" id="cost_price"
                class="@error('cost_price') is-invalid @enderror" />
            </div> 
            
            <div class="form-field">
                <label for="sell_price">Sell price</label>
                <input type="text" value="{{old('sell_price')}}" name="sell_price" id="sell_price"
                class="@error('sell_price') is-invalid @enderror" />
            </div> 

            <div class="form-field">
                <label for="gain">Gain</label>
                <input type="text" value="{{old('gain')}}" name="gain" id="gain"
                class="@error('gain') is-invalid @enderror" />
            </div> 

                <div class="form-field">
                <label for="broker_commission">Broker commission</label>
                <input type="text" value="{{old('broker_commission')}}" name="broker_commission" id="broker_commission"
                class="@error('broker_commission') is-invalid @enderror tax" />
            </div> 

                <div class="form-field">
                <label for="sebon_commission">SEBON commission</label>
                <input type="text" value="{{old('sebon_commission')}}" name="sebon_commission" id="sebon_commission"
                class="@error('sebon_commission') is-invalid @enderror tax" />
            </div> 
            
            
            <div class="form-field">
                <label for="capital_gain_tax">Capital gain tax</label>
                <input type="text" value="{{old('capital_gain_tax')}}" name="capital_gain_tax" id="capital_gain_tax"
                class="@error('capital_gain_tax') is-invalid @enderror tax" />
            </div> 
            
            <div class="form-field">
                <label for="dp_amount">DP amount</label>
                <input type="text" value="{{old('dp_amount')}}" name="dp_amount" id="dp_amount"
                class="@error('dp_amount') is-invalid @enderror tax" />
            </div> 
            
            <div class="form-field">
                <label for="name_transfer">Name transfer</label>
                <input type="text" value="{{old('name_transfer')}}" name="name_transfer" id="name_transfer"
                class="@error('name_transfer') is-invalid @enderror tax" />
            </div> 
            <div class="form-field">
                <label for="net_receivable">Net receivable</label>
                <input type="text" value="{{old('net_receivable')}}" name="net_receivable" id="net_receivable"
                class="@error('net_receivable') is-invalid @enderror" />
            </div> 
            
            <div class="form-field">
                <label for="payment_date">Payment received on</label>
                <input type="date" value="{{old('payment_date')}}" name="payment_date" id="payment_date"
                class="@error('payment_date') is-invalid @enderror" />
            </div> 

            <div class="form-field">
                    <label for="broker">Broker</label>
                    <select name="broker_no" id="broker_no">
                        @if (!empty($brokers))
                            @foreach($brokers as $record)
                                <option value="{{ $record->broker_no }}"
                                @if(strcasecmp( old("broker_no"), $record->broker_no ) == 0)
                                    SELECTED
                                @endif
                                >{{$record->broker_no}} - {{$record->broker_name}}</option>
                            @endforeach
                        @endif
                    </select> 
                </div>

            <div class="form-field">
                <label for="receipt_number">Receipt number</label>
                <input type="text" value="{{old('receipt_number')}}" name="receipt_number" id="receipt_number"
                class="@error('receipt_number') is-invalid @enderror" />
            </div> 

            <div class="form-field" style="grid-column:1/3">
                <label for="remarks">Remarks</label>
                <textarea name="remarks" id="remarks" class="@error('remarks') is-invalid @enderror">{{old('remarks')}}</textarea>
            </div> 
            
            <div >
                <button type="submit" class="focus">Update</button>
                <button id="cancel" type="button" >Cancel</button>
            </div>

        </form>
    </article>

    <!-- sales -->  
    <article class="sales_list">
    
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

        <header class="band info flex js-apart">

           <div></div>
            
            <div class="flex">
                <form  method="POST" action="/sales/export" style="margin:0" class="optional">
                    @csrf()
                    <button style="margin:0">Export</button>
                &nbsp;
                <select name="shareholders" id="shareholders">
                    <option value="">ALL</option>
                    @foreach($shareholders as $record)
                    <option value="{{ $record['uuid'] }}" 
                    @IF($selected)
                        @if($selected->uuid == $record['uuid']) SELECTED @endif
                    @endif
                    >
                        {{$record['name']}}
                    </option>
                    @endforeach
                </select> 
                </form>
            </div>
        </header>
        <main>
        @foreach ($sales_grouped as $sales)
            <hr>
            <table>
            <thead>
                <tr>
                    <td colspan="13">
                        <div class="flex js-start">
                            <h2 class="title">
                                @php $temp = $sales->first(); @endphp                             
                                {{ Str::title($temp->shareholder->first_name)}} {{Str::title($temp->shareholder->last_name)}}                           
                            </h2>
                            <div class="notification">
                                @if(count($sales)>0)
                                    ({{count($sales)}} records)
                                @endif
                            </div> 
                        </div>
                    </td>
                </tr>
                <tr>
                    <th style="text-align:left" class="optional">&nbsp;Sales date</th>
                    <th style="text-align:left">Symbol</th>
                    <th>Qty</th>
                    <th class="optional">Cost Price</th>
                    <th>Sell Price</th>
                    <th class="optional" title="Broker commission">Broker</th>
                    <th class="optional" title="SEBON commission">SEBON</th>
                    <th>Gain</th>
                    <th class="optional">Capital gain tax</th>
                    <th class="optional">WACC</th>
                    <th><span class="td-clip-75" title="Net Receiveable">Net Receiveable</span></th>
                    <th><span class="td-clip-75" title="Amount received">Amount received</span></th>
                    <th style="text-align:center"></th>
                </tr>
               
            </thead>
            <tbody>
                
                
                @foreach ($sales as $record)
                    @if(count($sales)<=0)
                    <tr>
                        <td colspan="14">
                            <div class="info center-box error-box" style="text-align:center">
                                <h2 class="message error">No Sales record yet<h2>
                                <h3 class="message success">💡 The records will show up here once you make some sales.</h3>
                            </div>
                        </td>
                    </tr>
                    @else
                    <tr>                        
                        <td style="text-align:left"  class="optional">{{ $record->sales_date }}</td>
                        <td style="text-align:left" title="{{ $record->share->id }}-{{ $record->share->security_name }}">
                            {{ $record->share->symbol }}
                        </td>
                        <td>{{ number_format($record->quantity) }}</td>
                        <td class="optional">{{ number_format($record->cost_price) }}</td>
                        <td>{{ number_format($record->sell_price, 2) }}</td>
                        <td class="optional">{{ number_format($record->broker_commission) }}</td>
                        <td class="optional">{{ number_format($record->sebon_commission) }}</td>
                        <td>{{ number_format($record->gain) }}</td>
                        <td class="optional">{{ number_format($record->capital_gain_tax) }}</td>
                        <td class="optional">{{ number_format($record->wacc) }}</td>
                        <td>{{ number_format($record->net_receivable) }}</td>
                        <td></td>
                        <td><button class="small-btn edit" data-id="{{ $record->id }}">📝</button></td>
                    </tr>
                    @endif

                @endforeach   
            </tbody>
            </table>
            @endforeach
        </main>

    </article>

</section>

<script>


    //sales shareholder filter     
    document.querySelector('select#shareholders').addEventListener('change',function(e){
        const uuid = e.target.value;
        const url = `${window.location.origin}/sales/${uuid}`;
        window.location.replace(url);
    });

    //cancel button hanlder
    document.querySelector('button#cancel').onclick=function(e){
        document.querySelector('article#sales_crud').classList.toggle('show');
    }

    //edit button handler
    document.querySelectorAll('button.edit').forEach(function(el){
        el.addEventListener('click', function(e){
            display_data(e.target.dataset.id);
            const form = document.querySelector('article#sales_crud');
            // form.style.height = "100vh";
            form.style.width = "100%";
            form.classList.toggle('show');
        });
    });

    //display data
    function display_data(id) {
        //get record 
        const url = `${window.location.origin}/sales/get/${id}`;
        let request = new XMLHttpRequest();
        request.open('GET', url, true);
        request.onload = function() {
            if (this.status >= 200 && this.status < 400) {
                data = JSON.parse(this.response);
                updateInputFields(data);
                // document.querySelector('#offer').dispatchEvent(new Event("change"));
                hideLoadingMessage();
            }
        }  
        request.onerror = function() {
            hideLoadingMessage();
        };
        request.send();
    }

    function updateInputFields(data) {
        document.querySelector('h2#shareholder-name').innerHTML = `<strong>(${data.shareholder.first_name} ${data.shareholder.last_name})</strong>`;
        document.querySelector('input#shareholder_name').value = `${data.shareholder.first_name} ${data.shareholder.last_name}`;
        document.querySelector('h2#symbol').innerHTML = `<strong> - ${data.share.symbol}</strong>`;
        document.querySelector('input#stock_name').value = data.share.symbol;
        document.querySelector('#id').value = data.id;
        document.querySelector('#shareholder_id').value = data.shareholder_id;
        document.querySelector('#stock_id').value = data.stock_id;
        document.querySelector('#quantity').value = data.quantity;
        document.querySelector('#wacc').value = data.wacc;
        document.querySelector('#cost_price').value = data.cost_price;
        document.querySelector('#sell_price').value = data.sell_price;
        document.querySelector('#net_receivable').value = data.net_receivable;
        document.querySelector('#sales_date').value = data.sales_date;
        document.querySelector('#payment_date').value = data.payment_date;
        document.querySelector('#broker_commission').value = data.broker_commission;
        document.querySelector('#sebon_commission').value = data.sebon_commission;
        document.querySelector('#capital_gain_tax').value = data.capital_gain_tax;
        document.querySelector('#gain').value = data.gain;
        document.querySelector('#dp_amount').value = data.dp_amount;
        document.querySelector('#name_transfer').value = data.name_transfer;
        document.querySelector('#receipt_number').value = data.receipt_number;
        document.querySelector('#remarks').value = data.remarks;
        if(data.broker_no) { 
            setOption(document.getElementById('broker_no'), data.broker_no);
        }
    }

</script>
@endsection