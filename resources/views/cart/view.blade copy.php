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

    article {
        margin: 30px 0;
    }
    article footer {
        text-align: right;
        margin: 5px 0;
    }
    article h2 {
        background: beige;
        padding: 10px;
        text-align: center;
    }
    .info h2 {
        background: unset;
    }
    td{
        line-height:unset;
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
    .c_change{max-width: 100px;}
    .gain_label {
        max-width: 70px;
        display: flex;
        flex-direction: column;
        font-size: 15px;
        text-align: right;
    }
    td.symbol {
        min-width: 100px;
    }

    article.summary main {
        display: flex;
        justify-content: space-around;
    }
    article.summary .block>label {
        display: inline-block;
        width: 150px;
        font-weight: bold;
        padding: 5px;
    }
    
    article.summary input {
        width: 130px;
        background: #f7f7f7;
        text-align: right;
        font-size: 16px;
        height: 1.3em;
        font-family: 'Cutive';
    }
    input#net_payable {
        outline: 2px solid #FF9800;
        font-weight: bold;
    }
</style>

<div id="loading-message" style="display:none">Working... Please wait...</div>

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
        </article>

        <article>
            
        <form action="{{url('sales/store')}}" method="POST">

            <header>
                @if(count($basket)<=0)
                    <div class="info" style="text-align:center">
                        <h2 class="message error">The cart is empty.<h2>
                        <h3 class="message success">💡 You can to to <strong>Portfolio details</strong> and add items to the cart.</h3>
                    </div>
                @endif
            </header>
            <main id="carts">

                @if(count($basket) > 0)
                    <table>
                        <thead>
                            <tr>
                                @php
                                    $data = $basket->first();
                                @endphp
                                <th colspan="11" class="info">
                                    <div style="display:flex;justify-content:flex-start;">
                                        <div>
                                            <h2 class="title">{{$data->shareholder->first_name}} {{$data->shareholder->last_name}}</h2>
                                            <div class="notification">
                                                @if(count($basket)>0)
                                                ({{count($basket)}} entries)
                                                @endif
                                            </div> 
                                        </div>
                                        <div id="sell_message" style="margin-left:15px;align-self:center"></div>
                                    </div>
                                </th>
                                <th colspan="2" class="info icon-buttons" style="text-align:right">
                                    <button id="edit" onClick="updateBasket()" title="update records">💾</button>
                                    <button id="delete" onClick="deleteBasket()" title="delete records">❌</button>
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
                                    data-id="{{ $row->id }}" 
                                    data-stock="{{$row->share->id}}" 
                                    data-user="{{$row->shareholder->id}}"
                                    data-user-symbol="{{$row->shareholder->first_name}}-{{$row->share->symbol}}">
                                    <label for="chk-{{ $row->id }}">
                                        <abbr for="{{ $row->id }}" title="{{$row->share->id}}-{{ $row->share->security_name }}">
                                            {{ $row->share->symbol }}
                                        </abbr>
                                    </label>     
                                <input type="hidden" name="cart_id" value="{{$row->id}}">                           
                            </td>
                            <td class="c_digit">
                                <input type="number" name="quantity" id="qty-{{$row->id}}" value="{{ $row->quantity }}">
                            </td>
                            <td class="c_digit">
                                <input type="number" name="wacc" id="wacc-{{$row->id}}" value="{{ $wacc }}">
                            </td>
                         
                            <td class="c_digit">
                                <!-- <input value="{{ number_format($investment) }}" type="text"> -->
                                <div name="investment" id="invest-{{$row->id}}">{{ ($investment) }}
                                </div>
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
                                        <div id="gain-{{ $row->id }}">{{ $gain }}</div>
                                        <div id="g_per-{{ $row->id }}" class="{{$gain_class}}">&nbsp;({{$gain_pc}}%)</div>
                                    </div>
                                    <div id="g_img-{{ $row->id }}" class="{{$gain_class}}_icon"></div>
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
                        </tbody>

                    </table>
                @endif

            </main>
        
            <footer>
            @if(count($basket) > 0)
                <button class="focus">Mark as SOLD</button>
            @endif
            </footer>
            
        </form>        
        </article>

        @if(count($basket)>0)
        <article class="summary">
            <header>
                <h2>Summary</h2>
            </header>
            <main>

                <div class="col">
                    <div class="block">
                        <label for="total_quantity">Total quantity</label>
                        <input type="text" name="total_quantity" id="total_quantity" readonly>
                    </div>
                    <div class="block">
                        <label for="total_investment">Total Investment</label>
                        <input type="text" name="total_investment" id="total_investment" readonly>
                    </div>
                    <div class="block">
                        <label for="total_amount">Total sales </label>
                        <input type="text" name="total_amount" id="total_amount" readonly>
                    </div>
                    <div class="block">
                        <label for="total_gain">Net Gain</label>
                        <input type="text" name="total_gain" id="total_gain" readonly>
                    </div>
                    <div class="block">
                        <label for="total_gain_tax">Gain tax</label>
                        <input type="text" name="total_gain_tax" id="total_gain_tax" readonly>
                    </div>

                </div>

                <div class="col">

                    <div class="block">
                        <label for="total_sebon_comm">SEBON Commission</label>                
                        <input type="text" name="total_sebon_comm" id="total_sebon_comm" readonly>
                    </div>
                    <div class="block">
                        <label for="total_broker_comm">Broker Commission</label>       
                        <input type="text" name="total_broker_comm" id="total_broker_comm" readonly>
                    </div>
                    
                    <div class="block">
                        <label for="dp_amount">DP amount</label>
                        <input type="text" name="dp_amount" id="dp_amount" readonly>
                    </div>
                    <div class="block net_pay">
                        <label for="net_payable">Net Receivable </label>
                        <input type="text" name="net_payable" id="net_payable" readonly>
                    </div>

                </div>

            </main>
            <footer>
                
            </footer>
        </article>
        @endif

</section>
        
@endsection