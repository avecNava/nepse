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

        <div class="c_band @if(session()->has('message')) c_band_success @endif">                    

            @if(session()->has('message'))
            <div class="message">
                {{ session()->get('message') }}
            </div>
            @endif

        </div>   

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

            <div class="info_band_bottom" hidden>

                <form method="POST" action="/portfolio/edit">
                    
                    @csrf()

                    <div class="form-field">
                        <input type="hidden" name="id"> 
                        <label for="quantity">Quantity</label>
                        <input type="number" name="quantity" required 
                        class="@error('quantity') is-invalid @enderror" />
                    </div>

                    <div class="fields form-field">
                        <label for="unit_cost">Unit cost</label>
                        <input type="text" name="unit_cost" required
                        class="@error('unit_cost') is-invalid @enderror" />
                    </div> 

                    <div class="fields form-field">
                        <label for="total_amount" title="bill amount">Total amount</label>
                        <input type="text" name="total_amount" 
                        class="@error('total_amount') is-invalid @enderror" />
                    </div> 

                    <div class="fields form-field" class="@error('offer') is-invalid @enderror">
                        <label for="offer">Offer type</label>
                        <select name="offer">
                            @if(!empty(@offers))
                            @foreach($offers as $offer)
                                <option value="{{ $portfolio->id }}">{{$offer->offer_name}}</option>
                            @endforeach
                            @endif
                        </select> 
                    </div>

                    <div class="fields form-field" class="@error('broker') is-invalid @enderror">
                        <label for="broker">Broker</label>
                        <select name="broker">
                            @if(!empty(@brokers))
                            @foreach($brokers as $broker)
                                <option value="{{ $portfolio->id }}">{{$broker->broker_name}}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    
                    <div class="fields form-field">
                        <label for="receipt_number" title="bill amount">Receipt number</label>
                        <input type="text" name="receipt_number" 
                        class="@error('receipt_number') is-invalid @enderror" />
                    </div> 

                    <div class="button">
                        <button type="submit">Save</button>
                    </div>
                </form> 

            </div>

        </section>

        <section class="portfolio">
        @if( !empty($portfolios) )
           
            <article class="a_portfolio_details">
            
                <header>

                <div class="a_portfolio_main">
  
                    <div class="c_band">

                        <div id="message" class="message">
                            @if(count($portfolios)>0)
                                {{count($portfolios)}} records
                            @else
                                You will need to import before we can display something. See instructions above.
                            @endif
                        </div>

                        <div class="buttons">
                            <button id="delete" onClick="deletePortfolios()">Delete</button>
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
                        <th>Total</th>
                        <th>Effective rate</th>
                        <th>Offer</th>
                        <th>Sector</th>
                        <th>Shareholder</th>
                        <th>Purchase date</th>
                    </tr>
                    
                    @foreach ($portfolios as $record)
                        
                        <tr>
                            
                            <td title="{{ $record->security_name }}">
                                @if( !empty($record))
                                    <input type="checkbox" name="chk_{{ $record->id }}" id="chk-{{ $record->id }}">&nbsp;
                                    <a href="{{url('portfolio/edit', [$record->id])}}">{{ $record->symbol }}</a>
                                @endif
                            </td>
                            <td>{{ $record->quantity }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td title="{{$record->offer_name}}">{{$record->offer_code}}</td>
                            <td></td>
                            <td>{{$record->first_name}} {{$record->last_name}}</td>
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

     
    </script>

@endsection