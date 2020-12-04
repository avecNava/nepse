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
                {{dd($portfolio)}}
                <div class="fields">
                    
                    @csrf()
                    
                    <div class="form-field">
                        <input type="hidden" value="{{$portfolio->id}}" name="id"> 
                        <label for="shareholder">Symbol</label>
                        <div data-id="{{$portfolio->id}}">
                            {{$portfolio->share->symbol}}
                        </div>
                    </div>
                    
                    <div class="form-field">
                        <label for="shareholder">Shareholder</label>
                        <div data-id="{{$portfolio->shareholder_id}}">
                            {{$portfolio->shareholder->first_name}} {{$portfolio->shareholder->last_name}}
                            if(!empty($portfolio->shareholder->relation)){
                                ({{$portfolio->shareholder->relation}})
                            }
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="quantity">Quantity</label>
                        <input type="number"l 
                        value="{{ old('quantity', $portfolio->quantity) }}" 
                        name="quantity" required 
                        class="@error('quantity') is-invalid @enderror" />
                        @error('last_name')
                            <div class="is-invalid">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label for="unit_cost">Unit cost</label>
                        <input type="date" value="{{old('unit_cost', $record->unit_cost)}}" name="unit_cost" required
                        class="@error('unit_cost') is-invalid @enderror" />
                        @error('unit_cost')
                            <div class="is-invalid">{{ $message }}</div>
                        @enderror
                    </div> 

                    <div class="form-field">
                        <label for="total_amount" title="bill amount">total_amount</label>
                        <input type="date" value="{{old('total_amount', $record->total_amount)}}" name="total_amount" 
                        class="@error('total_amount') is-invalid @enderror" />
                        @error('total_amount')
                            <div class="is-invalid">{{ $message }}</div>
                        @enderror
                    </div> 
                    
                    <div class="form-field">
                        <label for="receipt_number" title="bill amount">receipt_number</label>
                        <input type="date" value="{{old('receipt_number', $record->receipt_number)}}" name="receipt_number" 
                        class="@error('receipt_number') is-invalid @enderror" />
                        @error('receipt_number')
                            <div class="is-invalid">{{ $message }}</div>
                        @enderror
                    </div> 

                    <div class="form-field">
                        <label for="offer">Offer type</label>
                        <select name="offer">
                            @foreach($offers as $offer)
                                <option value="{{ $record->id }}"

                                @if($record->offer_id == $offer->id )
                                    SELECTED
                                @endif
                                >{{$offer->offer_name}}</option>
                            @endforeach
                        </select> 
                        @error('offer')
                            <div class="is-invalid">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label for="sector">Sector</label>
                        <select name="sector">
                            @foreach($sectors as $sector)
                                <option value="{{ $record->id }}"

                                @if($record->sector->sector_id == $sector->id )
                                    SELECTED
                                @endif
                                >{{$sector->name}}</option>
                            @endforeach
                        </select> 
                        @error('offer')
                            <div class="is-invalid">{{ $message }}</div>
                        @enderror
                    </div>

                          

                    <div class="form-field">

                        <label>Gender</label>

                        <input type="radio" name="gender" value="male" id="male" {{ old('gender') == "male" ? 'checked' : '' }}>
                        <label for="male">Male</label>

                        <input type="radio" name="gender" value="female" id="female" {{ old('gender') == "female" ? 'checked' : '' }}
                        <label for="female">Female</label>

                        <input type="radio" name="gender" value="other" id="other" {{ old('gender') == "other" ? 'checked' : '' }}
                        <label for="other">Other</label>

                        @error('gender')
                            <div class="is-invalid">{{ $message }}</div>
                        @enderror

                    </div>
                   
                    <div class="form-field c_relation">

                        <label for="relation">Relation</label>
                        <select name="relation" id="relation">
                            @if (!empty($relationships))
                                @foreach($relationships as $record)
                                    <option value="{{ $record->relation }}"
                                    @if(strcasecmp( old('relation'), $record->relation ) == 0)
                                        SELECTED
                                    @endif
                                    >{{$record->relation}}</option>
                                @endforeach
                            @endif
                        </select> 
                        @error('relation')
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