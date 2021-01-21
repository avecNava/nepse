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
    
    tr.basket-header {
        background: unset;
    }
    tr.basket-header h2 {padding: 0 }
    tr.basket-header td {
        padding: 0 !important;
        height: 40px !important;
    }
    .icon-buttons button {
        background: unset;
        border-radius: unset;
    }
   
</style>

<div id="loading-message" style="display:none">Loading... Please wait...</div>


<section id="top-nav" class="optional">
    <div></div>
    <div class="links">
        <div class="link selected">
            <a href="{{url('sales')}}" title="See Sales" class="selected">View Sales</a>
        </div>
        <div class="link">
            <a href="{{url('cart')}}" title="See what's inside the cart">View Cart</a>
        </div>
    </div>
</section>

<section id="basket">
              
    <!-- message -->
    <div class="info">
        <h3>
            <div id="sell_message"></div>
        </h3>
    </div>

    <!-- sales -->
  
    <article class="sales_list">
    
        <header class="info flex js-apart al-end">

            <div class="flex js-start ">
                <h2 class="title">
                    @if($selected) 
                        {{ Str::title($selected->first_name)}} {{Str::title($selected->last_name)}}
                    @else
                        ALL
                    @endif
                </h2>
                <div class="notification">
                    @if(count($sales)>0)
                        ({{count($sales)}} records)
                    @endif
                </div> 
            </div>

            <select name="shareholders" id="shareholders">
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

        </header>
        <main>
            <table>
            <thead>
                <tr>
                    <th class="optional">Sales date</th>
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
                @if(count($sales)<=0)
                <tr>
                    <td colspan="14">
                        <div class="info center-box error-box" style="text-align:center">
                            <h2 class="message error">No Sales record yet<h2>
                            <h3 class="message success">💡 The records will show up here once you make some sales.</h3>
                        </div>
                    </td>
                </tr>
                @endif
            </thead>
            <tbody>
                
                @foreach ($sales as $record)
                    
                    <tr>
                        
                        <td  class="optional">{{ $record->sales_date }}</td>
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
                        <td title="Update">
                            <button class="small-btn">Save</button>
                        </td>
                    </tr>

                @endforeach   
            </tbody>
            </table>
        </main>

    </article>

</section>

<script>
        
    const el = document.querySelector('select#shareholders').addEventListener('change',function(e){
        const uuid = e.target.value;
        const url = `/sales/${uuid}`;
        window.location.replace(url);
    });
</script>
@endsection