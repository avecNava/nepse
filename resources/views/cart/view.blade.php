@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('js')
    <!-- <script src="{{ URL::to('js/meroshare.js') }}"></script> -->
@endsection

@section('content')

<style>

    main#cart input {
        width: 50px;
        text-align:right;
    }
    main#cart .wide input {
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

</style>

<div id="loading-message" style="display:none">Working... Please wait...</div>

<section id="basket">

    <h1 class="c_title">My Cart ({{ optional(Auth::user())->name }})</h1>
 
        <article>
        
            <header>
                <div id="message" class="message">
                    @if(count($basket)>0)
                        {{count($basket)}} records
                    @else
                        No records
                    @endif
                </div>
            </header>

            <main id="cart">

                <table>
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
                    
                    @foreach ($basket as $index => $row)

                    <tr>
                        <td class="c_digit">{{ $index + 1 }}</td>
                        <td>
                            <label for="{{ $row->id }}" title="{{ $row->share->security_name }}">
                                {{ $row->share->symbol }}
                            </label>
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
                        <td class="c_digit">
                            <button>Update</button>
                            <button>Remove</button>
                        </td>
                    </tr>

                    @endforeach  
                    <tr>
                        <td class="c_digit"></td>
                        <td></td>
                        <td class="c_digit"></td>
                        <td class="c_digit"><strong>Grand total</strong></td>
                        <td class="c_digit wide">
                            <input style="font-weight:bold" type="number" id="total_sales_amount" readonly>
                        </td>
                        <td></td>
                        <td class="c_digit"></td>
                        <td class="c_digit">
                            <button class="focus">Sell</button>
                        </td>
                    </tr>          
                </table>
            </main>
        
            <footer></footer>
        
    </article>

</section>
        
@endsection