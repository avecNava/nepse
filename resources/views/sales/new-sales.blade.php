@extends('layouts.default')

@section('title')
    New Sales Entry
@endsection

@section('header_title')
    <h1 class="c_title">New Sales</h1>
@endsection

@section('js')
   
@endsection

@section('content')

<section id="top-nav" class="optional">
    <div></div>
    <div class="links">
        <div class="link selected">
            <a href="{{url('sales/new')}}" title="New sales" class="selected">New sales</a>
        </div>
        <div class="link">
            <a href="{{url('sales')}}" title="See Sales">View Sales</a>
        </div>
        <div class="link">
            <a href="{{url('cart')}}" title="See what's inside the cart">View Cart</a>
        </div>
    </div>
</section>

<section id="sales">
    <!-- message -->
    @if(session()->has('message'))
    <div class="info">
        <h3>
            <div id="sell_message"> {!! session()->get('message') !!}</div>
        </h3>
    </div>
    @endif

    <!-- update form  -->
    <article id="form_sales_new">
    
    <form method="POST" action="/sales/store">
           
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
                   <h2>New Sales</h2> 
                   <div class="flex al-cntr">
                       <h2 id="symbol">{{old('stock_name')}}</h2>
                        <h2 id="shareholder-name">{{old('shareholder_name')}}</h2>
                   </div>
               </div>
           </header>

           <div class="form_content">
           
           @csrf()

            <div class="form-field">
                <label for="stock_id">Share</label>
                <select name="stock_id" class="@error('stock_id') is-invalid @enderror" >
                <option value="0">Choose</option>
                    @if (!empty($stocks))
                        @foreach($stocks as $record)
                            <option value="{{ $record->id }}"
                            @if(strcasecmp( old("stock_id"), $record->id ) == 0)
                                SELECTED
                            @endif
                            >{{$record->symbol}} - {{$record->security_name}}</option>
                        @endforeach
                    @endif
                </select> 
            </div>

            <div class="form-field">
                <label for="shareholder_id">Shareholder</label>
                <select name="shareholder_id" class="@error('shareholder_id') is-invalid @enderror" >
                <option value="0">Choose</option>
                    @if (!empty($shareholders))
                        @foreach($shareholders as $record)
                            <option value="{{ $record->id }}"
                            @if(strcasecmp( old("shareholder_id"), $record->id ) == 0)
                                SELECTED
                            @endif
                            >{{$record->first_name}} {{$record->last_name}}</option>
                        @endforeach
                    @endif
                </select> 
            </div>

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
           <div class="form-field"></div>
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
                <label for="broker_no">Broker</label>
                <select name="broker_no" class="@error('brokder_no') is-invalid @enderror" >
                    <option value="0">Choose</option>
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

           <div class="form-field">
               <label for="remarks">Remarks</label>
               <textarea name="remarks" id="remarks" class="@error('remarks') is-invalid @enderror">{{old('remarks')}}</textarea>
           </div> 
           
            <div>
                <button class="focus">Save</button>
                <button  type="reset">Reset</button>
            </div>
            <div class="form-field"></div>

    </form>

    </article>

</section>

<script>


</script>
@endsection