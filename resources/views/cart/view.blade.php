@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('js')
    <!-- <script src="{{ URL::to('js/meroshare.js') }}"></script> -->
@endsection

@section('header_title')
<!-- <h1 class="c_title">My Cart ({{ optional(Auth::user())->name }})</h1> -->
<h1 class="c_title">Sales Cart</h1>
@endsection

@section('content')

<style>
    td{
        line-height:unset;
    }
    .grand-total {
        font-weight: bold;
        font-size: 15px;
        font-family: 'Cutive';
    }
    main#carts input[type='checkbox'] {
        width:20px;
    }
    main#carts input {
        font-family: 'Cutive';
        width: 60px;
        text-align: right;
        font-size: 12px;
    }
    main#carts .wide input {
        width: 80px;
    }
    section#basket td {
        padding: 0 3px;
    }
    button.focus {
        background: #305063;
        color: #fff;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 2px;
        font-size: 15px;
    }
    li::marker {
        content: '🧑🏻';
    }
    ul.shareholders {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        padding: 30px 15px;
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
    .c_change{max-width: 100px;}
    .gain_label {
        max-width: 70px;
        display: flex;
        flex-direction: column;
        font-size: 15px;
        text-align: right;
    }
    article.summary {
        margin: 25px 0;
    }

    article.summary th {
        font-weight: bold;
        text-align: right;
        background:unset;
    }
    article.summary input {
        width: 130px;
        background: #f7f7f7;
        text-align: right;
        font-size: 16px;
        height: 1.3em;
        font-family: 'Cutive';
    }
    article.summary tr:nth-child(odd),
    article.summary tr:nth-child(even) {
        background: unset;
    }  
    article.summary h2 {
        text-align: center;
    }
        td.symbol {
        min-width: 100px;
    }
</style>

<div id="loading-message" style="display:none">Working... Please wait...</div>

<section class="message">
    <div id="sell_message"></div> 
</section>

<section id="basket">

        <article>
        
            <header class="">
                <ul class="shareholders">
                    <li>
                        <a href="{{url('basket') }}" title="All records">Everyone</a>
                    </li>
                    @foreach($shareholders as $shareholder)
                        <li>
                            <a href="{{url('basket', [$shareholder['_name'], $shareholder['id']]) }}" title="{{$shareholder['relation']}}">
                                {{$shareholder['name']}}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </header>

            <main id="carts">

                @if(count($basket)<=0)
                    
                    <div class="message error">
                        The cart is empty.
                    </div>

                @else
                @csrf()
                

                    <table>
                        <thead>
                            <tr>
                                @php
                                    $data = $basket->first();
                                @endphp
                                <th colspan="10" class="info">
                                    <h2 class="title">{{$data->shareholder->first_name}} {{$data->shareholder->last_name}}</h2>
                                    <div class="notification">
                                        @if(count($basket)>0)
                                            ({{count($basket)}} entries)
                                        @endif
                                    </div> 
                                </th>
                                <th colspan="4" class="info small-buttons" style="text-align:right">
                                    <button id="edit" onClick="updateBasket()">✔</button>
                                    <button id="delete" onClick="deleteBasket()">❌</button>
                                </th>
                            </tr>
                            <tr>
                                <th>&nbsp;Symbol</th>
                                <th class="c_digit">Quantity</th>
                                <th class="c_digit"><abbr title="Weighted average (Effective rate)">WACC</abbr></th>
                                <th class="c_digit">Investment</th>
                                <th class="c_digit"><abbr title="Last trade price">LTP</abbr></th>
                                <th class="c_digit">Sales amount</th>
                                <th class="c_digit">Gain</th>
                                <th class="c_digit"><abbr title="Capital Gain Tax">CGT</abbr></th>
                                <th class="c_digit" title="Broker commission">Comm.</th>
                                <th class="c_digit" title="SEBON commission">SEBON</th>
                                <th class="c_digit">Effective rate</th>
                                <th class="c_digit">Net amount</th>
                                <th>Shareholder</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        
                        
                        @foreach ($basket as $index => $row)
                        
                        @php
                            $quantity = $row->quantity;
                            $ltp = $row->price->close_price ?: $row->price->last_updated_price;
                            $wacc = $row->wacc;
                            $investment = $wacc * $quantity;
                            $worth = $ltp * $quantity;
                            $sales_amount = $ltp * $quantity;
                            $gain = $worth - $investment;
                            $gain_pc = '';
                            if($investment>0)
                                $gain_pc = round(($gain/$investment)*100, 2);
                            $gain_class = '';
                            if($gain > 0){
                                $gain_class = 'increase';
                            }elseif($gain < 0){
                                $gain_class = 'decrease';
                            }
                        @endphp
                        
                        <tr id="row-{{$row->id}}">
                            <td class="symbol">
                                <input type="checkbox" name="s_id" 
                                    id="chk-{{ $row->id }}" 
                                    data-stock="{{$row->share->id}}" 
                                    data-user="{{$row->shareholder->id}}">
                                    <label for="chk-{{ $row->id }}">
                                        <abbr for="{{ $row->id }}" title="{{$row->share->id}}-{{ $row->share->security_name }}">
                                            {{ $row->share->symbol }}
                                        </abbr>
                                    </label>                                
                            </td>
                            <td class="c_digit">
                                <input type="number" name="quantity" id="qty-{{$row->id}}" value="{{ $row->quantity }}">
                            </td>
                            <td class="c_digit">
                                <input type="number" name="wacc" id="wacc-{{$row->id}}" value="{{ $wacc }}">
                            </td>
                         
                            <td class="c_digit">
                                <div name="investment" id="invest-{{$row->id}}">{{$investment}}</div>
                            </td>
                            
                            <td class="c_digit">
                                <div name="ltp" id="ltp-{{$row->id}}">{{ $ltp }}</div>
                            </td>
                            <td class="c_digit wide">
                                <input type="number" name="sales_amount" id="amt-{{$row->id}}" value="{{ $sales_amount }}">
                            </td>
                            <td>
                                <div class="c_change">
                                    <div class="gain_label">
                                        <div>{{ $gain }}</div>
                                        <div class="{{$gain_class}}">&nbsp;({{$gain_pc}}%)</div>
                                    </div>
                                    <div class="{{$gain_class}}_icon"></div>
                                </div>
                            </td>
                            <td class="c_digit"><div id="cgt-{{$row->id}}"></div></td>
                            <td class="c_digit"><div id="comm-{{$row->id}}"></td>
                            <td class="c_digit"><div id="sebon-{{$row->id}}"></td>
                            <td class="c_digit"><div id="rate-{{$row->id}}"></td>
                            <td class="c_digit"><div id="net_pay-{{$row->id}}"></td>
                            <td>{{ $row->shareholder->first_name }}</td>
                        </tr>

                        @endforeach  
                        <tr>
                            <td></td>
                            <td colspan="2" class="c_digit"><strong>Grand total</strong></td>
                            <td class="c_digit wide">
                                <input class="grand-total" type="number" id="total_investment_amount" readonly>
                            </td>
                            <td></td>
                            <td class="c_digit wide">
                                <input class="grand-total" type="number" id="total_sales_amount" readonly>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="padding:5px 10px">
                                <button class="focus">Sell</button>
                            </td>
                        </tr>          
                    </table>
                
                @endif

            </main>
        
            <footer></footer>
        
        </article>

        @if(count($basket)>0)
        <article class="summary">
            <header>
                <h2>Summary</h2>
            </header>
            <main>
                <table>
                    <tr>
                        <th>
                            Total quantity
                        </th>
                        <td>
                            <input type="number" name="total_quantity" id="total_quantity" readonly>
                        </td>
                        <th>
                            Total share amount
                        </th>
                        <td>
                            <input type="number" name="total_amount" id="total_amount" readonly>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            SEBON Commission
                        </th>
                        <td>
                          <input type="number" name="sebon_commission" id="sebon_commission" readonly>
                        </td>
                        <th>
                            Broker Commission
                        </th>
                        <td>
                            <input type="number" name="borker_commission" id="borker_commission" readonly>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Net Gain
                        </th>
                        <td>
                            <input type="number" name="net_gain" id="net_gain" readonly>
                        </td>
                        <th>
                            Gain tax
                        </th>
                        <td>
                            <input type="number" name="gain_tax" id="gain_tax" readonly>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            DP amount
                        </th>
                        <td>
                            <input type="number" name="dp_amount" id="dp_amount" readonly>
                        </td>
                        <th>
                            Net Payable amount
                        </th>
                        <td>
                            <input type="number" name="net_payable" id="net_payable" readonly>
                        </td>
                    </tr>
                </table>
            </main>
            <footer>

            </footer>
        </article>
        @endif

</section>
        
@endsection