@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('js')
@endsection

@section('content')

    <section class="c_shareholders">

        <header>
            <h1 class="c_title">Add new Portfolio</h1>            
        </header>

        <main class="form_container">  

            <form method="POST" action="/portfolio/create">

                <div class="c_band @if(session()->has('message')) c_band_success @endif">                    

                    @if(session()->has('message'))
                    <div class="message">
                        {{ session()->get('message') }}
                    </div>
                    @endif                   

                </div>          
                                                                                                                                                        
                <div class="c_portfolio_new">
                    
                    @csrf()
                    <input type="hidden" value="{{old('id')}}" name="id">                     
                    
                    <div class="two-col-form">

                        <div class="block-left">

                            <div class="fields form-field">
                                <label for="symbol" class="@error('symbol') is-invalid @enderror" >Symbol</label>
                                <select name="symbol">
                                    <option value="0">Select</option>
                                    @foreach($stocks as $stock)
                                        <option value="{{ $stock->id }}">
                                            {{$stock->stock_id}} {{$stock->symbol}} - {{$stock->security_name}}
                                        </option>
                                    @endforeach
                                </select> 
                                @error('symbol')
                                    <div class="is-invalid">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="fields form-field">
                                <label for="shareholder" class="@error('shareholder') is-invalid @enderror" >Shareholder</label>
                                <select name="shareholder">
                                <option value="0">Select</option>
                                    @foreach($shareholders as $shareholder)
                                        <option value="{{ $shareholder->id }}">
                                            {{$shareholder->first_name}} {{$shareholder->last_name}}
                                            @if(!empty($shareholder->relation))
                                            ({{ $shareholder->relation }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select> 
                                @error('shareholder')
                                    <div class="is-invalid">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="fields form-field">
                                <label for="quantity" class="@error('quantity') is-invalid @enderror" >Quantity</label>
                                <input type="number" ame="quantity" required  
                                value="{{ old('quantity') }}" />
                                @error('last_name')
                                    <div class="is-invalid">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="fields form-field">
                                <label for="unit_cost" class="@error('unit_cost') is-invalid @enderror">Unit cost</label>
                                <input type="number" name="unit_cost" required
                                value="{{old('unit_cost')}}" />
                                @error('unit_cost')
                                    <div class="is-invalid">{{ $message }}</div>
                                @enderror
                            </div> 

                            <div class="fields form-field">
                                <label for="total_amount" title="bill amount" class="@error('total_amount') is-invalid @enderror">Total amount</label>
                                <input type="number" name="total_amount" required
                                value="{{old('total_amount')}}"/>
                                @error('total_amount')
                                    <div class="is-invalid">{{ $message }}</div>
                                @enderror
                            </div> 

                            <div class="fields form-field">
                                <label for="offer"  class="@error('offer') is-invalid @enderror">Offer type</label>
                                <select name="offer">
                                    <option value="0">Select</option>
                                    @foreach($offers as $offer)
                                        <option value="{{ $offer->id }}"

                                        @if(old('offer_id') == $offer->id )
                                            SELECTED
                                        @endif

                                        >{{$offer->offer_name}}</option>
                                    @endforeach
                                </select> 
                                @error('offer')
                                    <div class="is-invalid">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="fields form-field">
                                <label for="broker" class="@error('broker') is-invalid @enderror">Broker</label>
                                <select name="broker">
                                    <option value="0">Select</option>
                                    @foreach($brokers as $broker)
                                        <option value="{{ $broker['broker_no'] }}">{{$broker['broker_name']}}</option>
                                    @endforeach
                                </select> 
                                @error('broker')
                                    <div class="is-invalid">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            
                            <div class="fields form-field">
                                <label for="receipt_number" class="@error('receipt_number') is-invalid @enderror">Receipt number</label>
                                <input type="text" value="{{old('receipt_number')}}" name="receipt_number"/>
                                @error('receipt_number')
                                    <div class="is-invalid">{{ $message }}</div>
                                @enderror
                            </div> 
                            
                            <div class="fields form-field">
                                <label for="remarks" class="@error('remarks') is-invalid @enderror">Remarks</label>
                                <textarea name="remarks" rows="5" cols="30"> {{old('remarks')}} </textarea>
                                @error('remarks')
                                    <div class="is-invalid">{{ $message }}</div>
                                @enderror
                            </div> 
                    
                        </div>

                        <div class="block-right">

                            <div class="buttons">
                                <button type="submit">Save</button>
                                <button type="reset">Cancel</button>
                            </div>

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

                    </div>
                    
                </div>  

            </form> 

        </main>
        
        <footer></footer>

    </section>
    
    <script></script>

@endsection