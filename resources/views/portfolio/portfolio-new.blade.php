@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('js')
<!-- <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script> -->
    <script src="{{ URL::to('js/portfolio.js') }}"></script>
@endsection

@section('header_title')
    <h1 class="c_title">Add new Portfolio</h1>
@endsection

@section('content')

    <style>
        textarea{
            width:100%;
        }
    </style>
    <section id="top-nav">
        <div class="label">Import shares in bulk</div>
        <div class="links">
            <div class="link">
                <a href="{{url('import/share')}}" title="Import Share from Excel file">Import (Spreadsheet)</a>
            </div>
            <div class="link">
                <a href="{{url('import/meroshare')}}" title="Import Share from MeroShare">Import from MeroShare</a>
            </div>
        </div>
    </section>

    <section class="message">
        <div class="message">
            @if(session()->has('message'))
            <div class="success">
                {{ session()->get('message') }}
            </div>
            @endif     

            @if(session()->has('error'))
            <div class="error">
                {{ session()->get('error') }}
            </div>
            @endif
        </div>
        <div class="validation-error">
            @if ($errors->any())
            <div class="error">
                <h3>Validation errors</h3>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </section>

    <section class="form">

    <form method="POST" action="/portfolio/create">

        <header>
            <div>
                <h2>Add new stock</h2>
                <h3>Enter the following details</h3>
            </div>
            <section class="buttons">
                <button type="submit">Save</button>
                <button type="reset">Reset</button>
            </section>
        </header>

        <main class="form_container">  

            <div class="c_portfolio_new">
 
                    @csrf()
                    <input type="hidden" value="{{old('id')}}" name="id">  
                    
                    <div class="two-col-form">

                        <div class="block-left">

                        <div class="fields form-field">
                                <label for="shareholder" class="@error('shareholder') is-invalid @enderror" >Shareholder</label>
                                <select name="shareholder">
                                <option value="0">Select</option>
                                    @foreach($shareholders as $shareholder)
                                        <option value="{{ $shareholder->id }}"
                                            
                                        @if(old('shareholder') == $shareholder->id )
                                            SELECTED
                                        @endif
                                        >
                                            {{$shareholder->first_name}} {{$shareholder->last_name}}
                                            @if(!empty($shareholder->relation))
                                            ({{ $shareholder->relation }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="fields form-field">
                                <label for="stock" class="@error('stock') is-invalid @enderror" >Symbol</label>
                                <select name="stock">
                                    <option value="0">Select</option>
                                    @foreach($stocks as $stock)
                                        <option value="{{ $stock->id }}"

                                        @if(old('stock') == $stock->id )
                                            SELECTED
                                        @endif
                                        >
                                            {{$stock->stock_id}} {{$stock->symbol}} - {{$stock->security_name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="fields form-field">
                                <label for="offer"  class="@error('offer') is-invalid @enderror">Offering type</label>
                                <select name="offer" id="offer">
                                    <option data-tag="none" value="0">Select</option>
                                    @foreach($offers as $offer)
                                        <option data-tag="{{ $offer->offer_code }}" value="{{ $offer->id }}"

                                            @if(old('offer') == $offer->id )
                                                SELECTED
                                            @endif

                                        >{{ $offer->offer_code }} ({{$offer->offer_name}})</option>
                                    @endforeach
                                </select> 
                            </div>
                            
                            <div class="fields form-field">
                                <label for="unit_cost" class="@error('unit_cost') is-invalid @enderror">Unit cost</label>
                                <input type="number" name="unit_cost" id="unit_cost" required value="{{old('unit_cost')}}" />
                                <span id="unit_cost_label"></span>
                            </div> 

                            <div class="fields form-field">
                                <label for="quantity" class="@error('quantity') is-invalid @enderror" >Quantity</label>
                                <input type="number" name="quantity" id='quantity' required value="{{ old('quantity') }}" />
                            </div> 

                            <div class="fields form-field">
                                <label for="base_amount" class="@error('base_amount') is-invalid @enderror" >Base amount</label>
                                <input type="number" name="base_amount" id='base_amount' required value="{{ old('base_amount') }}" />
                            </div> 

                            <section id="secondary">

                            <div>
                                <label for="broker_commission">Broker Commission</label>
                                <input type="text" name="broker_commission" id="broker_commission" value="{{old('broker_commission')}}" />
                            </div>
                            <div>
                                <label for="sebon_commission">Sebon Commission</label>
                                <input type="text" name="sebon_commission" id="sebon_commission" value="{{old('sebon_commission')}}" />
                            </div>
                            <div>
                                <label for="dp_amount">DP amount</label>
                                <input type="text" name="dp_amount" id="dp_amount" value="{{old('dp_amount')}}" />
                            </div>

                            <div class="fields form-field">
                                    <label for="broker" class="@error('broker') is-invalid @enderror">Broker</label>
                                    <select name="broker">
                                        <option value="0">Select</option>
                                    @foreach($brokers as $broker)
                                    <option value="{{ $broker['broker_no'] }}" 
                                    @if(old('broker') == $broker['broker_no'] )
                                    SELECTED
                                    @endif
                                    >{{ $broker['broker_no'] }}-{{$broker['broker_name']}}</option>
                                    @endforeach
                                </select>
                                <span id="broker_label"></span>
                            </div>

                            </section>

                            <div class="fields form-field" title="Bill amount">
                                <label for="total_amount" title="bill amount" class="@error('total_amount') is-invalid @enderror">Total amount</label>
                                <input type="text" name="total_amount" id="total_amount" required value="{{old('total_amount')}}"/>
                                <span id="total_amount_label"></span>
                            </div>      
                            
                            <div class="fields form-field">
                                <label for="effective_rate" class="@error('effective_rate') is-invalid @enderror">Effective rate <em>(per share)</em></label>
                                <input type="text" name="effective_rate" id="effective_rate" required value="{{old('effective_rate')}}" />
                                <span id="effective_rate_label"></span>
                            </div> 

                        </div>

                        <div class="block-right">

                            <div class="fields form-field">
                                <label for="purchase_date" class="@error('purchase_date') is-invalid @enderror">Purchase date</label>
                                <input type="date" value="{{old('purchase_date')}}" name="purchase_date"/>
                            </div> 
                            
                            <div class="fields form-field">
                                <label for="receipt_number" class="@error('receipt_number') is-invalid @enderror">Receipt number</label>
                                <input type="text" value="{{old('receipt_number')}}" name="receipt_number"/>
                            </div> 
                            
                            <div class="fields form-field">
                                <label for="tags" class="@error('tags') is-invalid @enderror">Tags</label>
                                <input type="text" value="{{old('tags')}}" name="tags"/>
                            </div> 
                            
                            
                            <div class="fields form-field remarks">
                                <label for="remarks" class="@error('remarks') is-invalid @enderror">Remarks</label>
                            </div> 
                            <div>
                                <textarea name="remarks" rows="5" cols="30"> {{old('remarks')}} </textarea>
                            </div>

                        </div>

                    </div>
                    
                </div>  

        </main>
        
        <footer></footer>

    </form> 

    </section>

@endsection
