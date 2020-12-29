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
                        No records
                    </div>

                @else
                @csrf()
                

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
                                        ({{count($basket)}} entries)
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
                                <th class="c_digit">Added on</th>
                                <th>Shareholder</th>
                                <th class="c_digit">Update</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        
                        
                        @foreach ($basket as $index => $row)

                        <tr id="row-{{$row->id}}">
                            <td class="c_digit">{{ $index + 1 }}</td>
                            <td>
                                <abbr for="{{ $row->id }}" title="{{$row->share->id}}-{{ $row->share->security_name }}">
                                    {{ $row->share->symbol }}
                                </abbr>
                            </td>

                            <td class="c_digit">
                                <input type="number" name="quantity" id="qty-{{$row->id}}" value="{{ $row->quantity }}">
                            </td>
                            <td class="c_digit wide">
                                <input type="number" name="wacc" id="wacc-{{$row->id}}" value="{{ $row->wacc }}">
                            </td>
                            <td class="c_digit wide">
                                <input type="number" name="sales_amount" id="amt-{{$row->id}}" value="{{ $row->quantity * $row->wacc }}">
                            </td>
                            <td class="c_digit">{{ $row->basket_date }}</td>
                            <td>
                                @if( !empty($row->shareholder) )
                                    <div id="{{$row->shareholder->id}}">
                                        {{ $row->shareholder->first_name }} {{ $row->shareholder->last_name }}
                                    </div>
                                @endif
                            </td>
                            <td class="c_digit small-buttons">
                                <button id="u-{{$row->id}}" onClick="updateBasket('{{$row->id}}')" data-shareholder_id="{{$row->shareholder->id}}" data-stock_id="{{$row->share->id}}">Update</button>
                                <button d="d-{{$row->id}}" onClick="deleteBasket('{{$row->id}}')">Remove</button>
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
                
                @endif

            </main>
        
            <footer></footer>
        
        </article>

</section>
        
@endsection