@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('js')
    <!-- <script src="{{ URL::to('js/meroshare.js') }}"></script> -->
@endsection

@section('header_title')
    Sales Cart
@endsection

@section('content')

<style>

    main#carts input {
        width: 50px;
        text-align:right;
    }
    main#carts .wide input {
        width: 100px;
    }

    button.focus {
        background: #305063;
        color: #fff;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 2px;
        font-size: 15px;
    }
    input#total_sales_amount {
        background: #ebebeb;
        border: 0px;
        font-weight: bold;
        font-size: 15px;
    }
</style>

<div id="loading-message" style="display:none">Working... Please wait...</div>

<section class="message">
    <div id="sell_message"></div> 
</section>

<section id="basket">

        <article>
        
            <header class="info">
                <h2 class="title">Carts</h2>
                <div class="notification">
                    @if(count($baskets)>0)
                        ({{count($baskets)}} shareholders)
                    @endif
                </div>
            </header>

            <main id="carts">

                @csrf()
                @foreach($baskets as $index => $basket)
                <section id="user-{{$index}}" class="carts__cart">
                
                    <table>
                        <thead>
                            <tr>
                                @php
                                    $data = $basket->first();
                                @endphp
                                <th colspan="8" class="info">
                                    <h2 class="title">{{$data->shareholder->first_name}} {{$data->shareholder->last_name}}</h2>
                                    <div class="notification">
                                        @if(count($basket)>0)
                                            ({{count($basket)}} records)
                                        @endif
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <th class="c_digit">SN</th>
                                <th>Symbol</th>
                                <th class="c_digit">Quantity</th>
                                <th class="c_digit">Weighted average</th>
                                <th class="c_digit">Sales amount</th>
                                <th>Shareholder</th>
                                <th class="c_digit">Added on</th>
                                <th class="c_digit">Update</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        
                        
                        @foreach ($basket as $index => $row)

                        <tr>
                            <td class="c_digit">{{ $index + 1 }}</td>
                            <td>
                                <abbr for="{{ $row->id }}" title="{{ $row->share->security_name }}">
                                    {{ $row->share->symbol }}
                                </abbr>
                            </td>

                            <td class="c_digit">
                                <input type="number" name="quantity" id="quantity-{{$row->id}}" value="{{ $row->quantity }}">
                            </td>
                            <td class="c_digit wide">
                                <input type="number" name="wacc" id="wacc-{{$row->id}}" value="{{ $row->wacc }}">
                            </td>
                            <td class="c_digit wide">
                                <input type="number" name="sales_amount" id="amount-{{$row->id}}" value="{{ $row->quantity * $row->wacc }}">
                            </td>
                            <td>
                                @if( !empty($row->shareholder) )
                                {{ $row->shareholder->first_name }} {{ $row->shareholder->last_name }}
                                @endif
                            </td>
                            <td class="c_digit">{{ $row->basket_date }}</td>
                            <td class="c_digit small-buttons">
                                <button onClick="updateBasket()">Update</button>
                                <button onClick="deleteBasket()">Remove</button>
                            </td>
                        </tr>

                        @endforeach  
                        <tr>
                            <td class="c_digit"></td>
                            <td></td>
                            <td class="c_digit"></td>
                            <td class="c_digit"><strong>Grand total</strong></td>
                            <td class="c_digit wide">
                                <input type="number" id="total_sales_amount" readonly>
                            </td>
                            <td></td>
                            <td class="c_digit"></td>
                            <td class="c_digit">
                                <button class="focus">Sell</button>
                            </td>
                        </tr>          
                    </table>
                    
                </section>
                @endforeach
            </main>
        
            <footer></footer>
        
        </article>

</section>
        
@endsection