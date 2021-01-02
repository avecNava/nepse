@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('header_title')
    <h1 class="c_title">Sales record</h1>
@endsection

@section('js')
    <!-- <script src="{{ URL::to('js/portfolio.js') }}"></script> -->
@endsection

@section('content')
<style>
    .icon-buttons button {
        background: unset;
        border-radius: unset;
    }
    ul.shareholders li::marker {
        content: '🧑🏻';
    }
    ul.shareholders {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        padding: 5px;
        background: var(--color-black-light95);
        border-radius: 5px;
    }
    ul.shareholders li {
        margin-right: 25px;
        padding: 5px;
    }
    ul.shareholders li:hover {
        background:beige;
    }
    ul.shareholders li a{
        font-weight: bold;
        color: #3F51B5;
    }
</style>

    <div class="c__sales">
    
        <div id="loading-message" style="display:none">Loading... Please wait...</div>

        <section id="top-nav">
            <div class="label">See what's inside the cart</div>
            <div class="links">
                <div class="link">
                    <a href="{{url('basket')}}" title="See what's inside the cart">View Cart</a>
                </div>
            </div>
        </section>

            <article id="shareholders">

            <header class="">
                <ul class="shareholders">
                    <li>
                        <a href="{{url('basket') }}" title="All records">Everyone</a>
                    </li>
                    @foreach($shareholders as $shareholder)
                        <li>
                            <a href="{{url('sales', [$shareholder['_name'], $shareholder['id']]) }}" title="{{$shareholder['relation']}}">
                                {{$shareholder['name']}}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </header>

            </article>
                
            <div id="message" style="margin-left:15px;align-self:center"></div>
            @if( !empty($sales) )
            <article class="sales_list">
         
            <header>
                @if(count($sales)<=0)
                    <div class="info" style="text-align:center">
                        <h2 class="message error">No Sales record yet<h2>
                        <h3 class="message success">💡 The records will show up here once you make some sales.</h3>
                    </div>
                @endif
            </header>
            @if(count($sales)>0)
                
                <header class="info">
                @php
                    $data = $sales->first();
                @endphp
                <div class="flex together">
                    <h2 class="title">{{$data->shareholder->first_name}} {{$data->shareholder->last_name}}</h2>
                    <div class="notification">
                        @if(count($sales)>0)
                        ({{count($sales)}} entries)
                        @endif
                    </div> 
                </div>
                </header>

                <main>
                    <table>
                    <thead>
                        
                        <tr>
                            <th>Sales date</th>
                            <th>Symbol</th>
                            <th class="c_digit">Qty</th>
                            <th class="c_digit">Cost Price</th>
                            <th class="c_digit">Sell Price</th>
                            <th class="c_digit" title="Broker commission">Broker</th>
                            <th class="c_digit" title="SEBON commission">SEBON</th>
                            <th class="c_digit">Gain</th>
                            <th class="c_digit">Gain Tax</th>
                            <th class="c_digit">WACC</th>
                            <th class="c_digit">Net Receiveable</th>
                            <th class="c_digit">Amount received</th>
                            <th>Shareholder</th>
                            <th>Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        @foreach ($sales as $record)
                            
                            <tr>
                                
                                <td class="c_digit">{{ $record->sales_date }}</td>
                                <td title="{{ $record->share->id }}-{{ $record->share->security_name }}">
                                    {{ $record->share->symbol }}
                                </td>
                                <td class="c_digit">{{ number_format($record->quantity) }}</td>
                                <td class="c_digit">{{ number_format($record->cost_price) }}</td>
                                <td class="c_digit">{{ number_format($record->sell_price, 2) }}</td>
                                <td class="c_digit">{{ number_format($record->broker_commission) }}</td>
                                <td class="c_digit">{{ number_format($record->sebon_commission) }}</td>
                                <td class="c_digit">{{ number_format($record->gain) }}</td>
                                <td class="c_digit">{{ number_format($record->capital_gain_tax) }}</td>
                                <td class="c_digit">{{ number_format($record->wacc) }}</td>
                                <td class="c_digit">{{ number_format($record->net_receivable) }}</td>
                                <td></td>
                                <td>{{$record->shareholder->first_name}}</td>
                                <td title="Update" class="icon-buttons button">
                                    <button>💾</button>
                                </td>
                            </tr>

                        @endforeach   
                    </tbody>
                    </table>
                @endif
                </main>
            
                <footer></footer>
            
            </article>

            @endif

        </section>
        
@endsection