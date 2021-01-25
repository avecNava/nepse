@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('js')
@endsection

@section('header_title')
<h1 class="c_title">Sales basket</h1>
@endsection

@section('content')

<style>
    tr.basket-header {
        background: unset;
    }
    tr.basket-header h2 {padding: 0 ; background:revert;}
    tr.basket-header td {
        padding: 0 !important;
        height: 40px !important;
    }
    article footer {
        text-align: right;
        margin: 5px 0;
    }
    article h2 {
        background: beige;
        padding: 10px;
    }
   
    main#carts input[type='checkbox'] {
        width:20px;
    }
    main#carts input {
        font-family: 'Cutive';
        width: 100px;
        text-align: right;
        font-size: 12px;
    }
    main#carts .wide input {
        width: 80px;
    }
    section#basket td {
        padding: 10px 5px;
        height:50px;
    }
 
    button.sell {
        background: #efefef;
        color: black;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 12px;
        display: flex;
        align-items: center;
        min-width: 50px;
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
    
    input#net_receivable {
        outline: 2px solid #FF9800;
        font-weight: bold;
    }
</style>

<div id="loading-message" style="display:none">Working... Please wait...</div>

<section id="top-nav"  class="optional">
    <div></div>
    <div class="links">
        <div class="link">
            <a href="{{url('sales/new')}}" title="New sales">New sales</a>
        </div>
        <div class="link">
            <a href="{{url('sales')}}" title="See Sales">View Sales</a>
        </div>
        <div class="link selected">
            <a href="{{url('cart')}}" title="See what's inside the cart">View Cart</a>
        </div>
    </div>
</section>

<section id="basket">

    <!-- message -->
    <div class="message">
        <h3>
            <div id="sell_message"></div>
        </h3>
    </div>
    
    <!-- basket -->
    <article>
            
        <main id="carts">
          
            <table>
                <thead>
                    <tr class="basket-header">
                       
                        <td colspan="14">
                            
                            <div class="flex js-apart al-cntr">
                            <div class="flex js-start al-cntr">
                                
                                    <h2 class="title">
                                        @if($selected) 
                                            {{ Str::title($selected->first_name)}} {{Str::title($selected->last_name)}}
                                        @else
                                            ALL
                                        @endif
                                    </h2>
                                    <div class="notification">
                                        {{count($baskets)}} record(s)
                                    </div> 

                            </div>

                            <div class="flex al-cntr">

                                <select name="shareholders" id="shareholders" style="margin:2px 5px">
                                    <option value="">Everyone</option>
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

                                <div class="buttons">
                                    <button type="button"  id="edit" onClick="updateBasket(); return false;" title="update records">💾</button>
                                    <button type="button" id="delete" onClick="deleteBasket(); return false;" title="delete records">❌</button>
                                </div>

                            </div>
                        </div>
                        </td>
                    </tr>
                    
                    <tr>
                        <th>&nbsp;Symbol</th>
                        <th class="c_digit">Quantity</th>
                        <th class="c_digit"><abbr title="Weighted average (Effective rate)">WACC</abbr></th>
                        <th class="c_digit">Cost Price</th>
                        <th class="c_digit"><abbr title="Last trade price">LTP</abbr></th>
                        <th class="c_digit">Sales amount</th>
                        <th class="c_digit">Gain</th>
                        <th class="c_digit"><abbr title="Capital Gain Tax">CGT</abbr></th>
                        <th class="c_digit" title="Broker commission">Comm.</th>
                        <th class="c_digit" title="SEBON commission">SEBON</th>
                        <th class="c_digit">Effective rate</th>
                        <th class="c_digit" title="Sell Price">Net amount</th>
                        <th>Shareholder</th>
                        <th>Sell</th>
                    </tr>
                    @if(count($baskets)<=0)
                    <tr>
                        <td colspan="14">
                            <div class="info center-box error-box" style="text-align:center">
                                <h2 class="message error">The cart is empty<h2>
                                <h3 class="message success">💡 The records will show up here once you make some sales.</h3>
                            </div>
                        </td>
                    </tr>
                    @endif
                </thead>
                <tbody>

                    @foreach ($baskets as $index => $row)                    
                    @php
                        $quantity = $row->quantity;
                        $ltp = 0;
                        if( !empty($row->price)){
                            $ltp = $row->price->close_price ?: $row->price->last_updated_price;
                        }
                        $wacc = $row->wacc;
                        $cost_price = $wacc * $quantity;
                        $worth = $ltp * $quantity;
                        $sales_amount = $ltp * $quantity;
                        $gain = $worth - $cost_price;
                        $gain_pc = '';
                        if($cost_price>0)
                            $gain_pc = round(($gain/$cost_price)*100, 2);
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
                        <!-- <input value="{{ number_format($cost_price) }}" type="text"> -->
                        <div name="cost_price" id="cost-{{$row->id}}">{{ ($cost_price) }}
                        </div>
                    </td>
                    
                    <td class="c_digit">
                        <div name="ltp" id="ltp-{{$row->id}}">{{ $ltp }}</div>
                    </td>
                    <td class="c_digit wide">
                        <input type="text" name="sell_price" id="sell-{{$row->id}}" value="{{ $sales_amount }}">
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
                    <td class="c_digit"><div id="net_amount-{{$row->id}}"></td>
                    <td>{{ $row->shareholder->first_name }}</td>
                    <td>
                        <button class="sell" title="Mark Sold" onClick="fnSell({{$row->id}})">
                            <span class="cart">🛒</button>
                    </td>
                </tr>

                @endforeach  
                </tbody>
                </table>
         

        </main>
            
    </article>

    <!-- basket summary -->
    @if(count($baskets)>0)
    <article class="summary">
        <header>
            <h2>Summary</h2>
        </header>
        <main>

            <div class="col">
                <div class="form-field">
                    <label for="total_quantity">Total quantity</label>
                    <input type="text" name="total_quantity" id="total_quantity" readonly>
                </div>
                <div class="form-field">
                    <label for="total_investment">Total Investment</label>
                    <input type="text" name="total_investment" id="total_investment" readonly>
                </div>
                <div class="form-field">
                    <label for="total_amount">Total sales </label>
                    <input type="text" name="total_amount" id="total_amount" readonly>
                </div>
                <div class="form-field">
                    <label for="total_gain">Net Gain</label>
                    <input type="text" name="total_gain" id="total_gain" readonly>
                </div>
                <div class="form-field">
                    <label for="total_gain_tax">Gain tax</label>
                    <input type="text" name="total_gain_tax" id="total_gain_tax" readonly>
                </div>

            </div>

            <div class="col">

                <div class="form-field">
                    <label for="total_sebon_comm">SEBON Commission</label>                
                    <input type="text" name="total_sebon_comm" id="total_sebon_comm" readonly>
                </div>
                <div class="form-field">
                    <label for="total_broker_comm">Broker Commission</label>       
                    <input type="text" name="total_broker_comm" id="total_broker_comm" readonly>
                </div>
                
                <div class="form-field">
                    <label for="dp_amount">DP amount</label>
                    <input type="text" name="dp_amount" id="dp_amount" readonly>
                </div>
                <div class="form-field net_pay">
                    <label for="net_receivable">Net Receivable </label>
                    <input type="text" name="net_receivable" id="net_receivable" readonly>
                </div>

            </div>

        </main>
        <footer></footer>
    </article>
    @endif

</section>
<script src="{{ URL::to('js/cart.js') }}"></script>
<script>
        
    const el = document.querySelector('select#shareholders').addEventListener('change',function(e){
        const uuid = e.target.value;
        const url = `/cart/${uuid}`;
        window.location.replace(url);
    });
</script>
@endsection