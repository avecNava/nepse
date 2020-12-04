@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('js')
@endsection

@section('content')

    <section class="c_shareholders">

        <header>
            <h1 class="c_title">Edit Portfolio</h1>            
        </header>

        <main class="c_shareholder_form">  

            <form method="POST" action="/shareholders">

                <div class="c_band @if(session()->has('message')) c_band_success @endif">                    

                    @if(session()->has('message'))
                    <div class="message">
                        {{ session()->get('message') }}
                    </div>
                    @endif

                    <div class="form-field button">
                        <button type="submit">Save</button>
                    </div>

                </div>          
                                                                                                                                                        
                <div class="c_layout_portfolio_form">
                    
                    @csrf()

                        <div class="block_right">

                            <div data-id="{{$portfolio->shareholder_id}}">
                                <h2>
                                    {{$portfolio->shareholder->first_name}} {{$portfolio->shareholder->last_name}}

                                    @if(!empty($portfolio->shareholder->relation))
                                        ({{$portfolio->shareholder->relation}})
                                    @endif
                                </h2>
                            </div>

                        </div>

                        <div class="block-left">
                            <h2>{{$portfolio->share->security_name}} ({{$portfolio->share->symbol}})</h2>
                        </div>
                        
                    </div>
                    
                    <div class="fields form-field">
                        <label for="quantity">Quantity</label>
                        <input type="hidden" value="{{$portfolio->id}}" name="id"> 
                        <input type="number"l 
                        value="{{ old('quantity', $portfolio->quantity) }}" 
                        name="quantity" required 
                        class="@error('quantity') is-invalid @enderror" />
                        @error('last_name')
                            <div class="is-invalid">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="fields form-field">
                        <label for="unit_cost">Unit cost</label>
                        <input type="text" value="{{old('unit_cost', $portfolio->unit_cost)}}" name="unit_cost" required
                        class="@error('unit_cost') is-invalid @enderror" />
                        @error('unit_cost')
                            <div class="is-invalid">{{ $message }}</div>
                        @enderror
                    </div> 

                    <div class="fields form-field">
                        <label for="total_amount" title="bill amount">Total amount</label>
                        <input type="text" value="{{old('total_amount', $portfolio->total_amount)}}" name="total_amount" 
                        class="@error('total_amount') is-invalid @enderror" />
                        @error('total_amount')
                            <div class="is-invalid">{{ $message }}</div>
                        @enderror
                    </div> 

                    <div class="fields form-field">
                        <label for="offer">Offer type</label>
                        <select name="offer">
                            @foreach($offers as $offer)
                                <option value="{{ $portfolio->id }}"

                                @if($portfolio->offer_id == $offer->id )
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
                        <label for="broker">Broker</label>
                        <select name="broker">
                            @foreach($brokers as $broker)
                                <option value="{{ $portfolio->id }}"

                                @if($portfolio->broker_number == $broker->number )
                                    SELECTED
                                @endif
                                >{{$broker->broker_name}}</option>
                            @endforeach
                        </select> 
                        @error('broker')
                            <div class="is-invalid">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="fields form-field">
                        <label for="receipt_number" title="bill amount">Receipt number</label>
                        <input type="text" value="{{old('receipt_number', $portfolio->receipt_number)}}" name="receipt_number" 
                        class="@error('receipt_number') is-invalid @enderror" />
                        @error('receipt_number')
                            <div class="is-invalid">{{ $message }}</div>
                        @enderror
                    </div> 

                </div>  

            </form> 

        </main>
        
        <footer></footer>

    </section>
    
    <script></script>

@endsection