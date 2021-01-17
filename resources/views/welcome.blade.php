@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('header_title')
    <h1>NEPSE.TODAY</h1>
@endsection
@section('js')
@endsection
@section('custom_css')
    
@endsection
@section('notice')
@if($notice)
    <div class="message_wrapper" data-title="{{$notice['title']}}">
        <p class='notice'>
            <span class='title'>{{$notice['title']}}</span>
            {!!$notice['message']!!}
        </p>
    </div>
@endif
@endsection

@section('content')

<div class="welcome__wrapper">

    <section class="transactions">
        <header class="flex js-apart al-end">
            <h2 style="display:inline-block;min-width:50%">NEPSE stock data</h2>
            <div title="Last transaction time" style="text-align:right">{{ $last_updated_time }} <mark style="display:inline-block">({{ $last_updated_time->diffForHumans() }})</mark></div>
        </header>
        @if($transactions)
        <table>
            <thead>
                <tr>
                    <th class="optional c_digit">&nbsp;SN</th>
                    <th>Symbol</th>
                    <th class="c_digit">LTP</th>
                    <th class="c_digit">Change</th>
                    <th class="c_digit c_change">% Change</th>
                    <th class="optional c_digit">Open price</th>
                    <th class="c_digit">High price</th>
                    <th class="c_digit">Low Price</th>
                    <th class="optional c_digit">Total quantity</th>
                    <th class="optional c_digit">Toal value</th>
                    <th class="optional c_digit">Previous price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $key => $transaction)
                @php
                    $ltp = $transaction->last_updated_price;
                    $prev_price = $transaction->previous_day_close_price;
                    $change = $ltp - $prev_price;
                    $change_per = \App\Services\UtilityService::calculatePercentage($change, $prev_price);
                    $change_css = \App\Services\UtilityService::gainLossClass1($change);
                @endphp
                <tr>
                    <td class="optional c_digit">{{$key+1}}</td>
                    <td title="{{$transaction->security_name}}" class="symbol">{{$transaction->symbol}}</td>
                    <td class="c_digit">{{ number_format($transaction->last_updated_price) }}</td>
                    <td class="{{$change_css}} c_digit">{{$change}}</td>
                    <td class="c_digit c_change">
                        <div class="flex apart">
                        <div class="{{$change_css}}">
                            {{$change_per}}
                        </div>
                        <div class="{{$change_css}}_icon">&nbsp;</div>
                        </div>
                    </td>
                    <td class="c_digit optional">{{ number_format($transaction->open_price) }}</td>
                    <td class="c_digit">{{ number_format($transaction->high_price) }}</td>
                    <td class="c_digit">{{ number_format($transaction->low_price) }}</td>
                    <td class="c_digit optional">{{ number_format($transaction->total_traded_qty) }}</td>
                    <td class="c_digit optional">{{ number_format($transaction->total_traded_value) }}</td>
                    <td class="c_digit optional">{{ number_format($transaction->previous_day_close_price) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot></tfoot>
        </table>
        @endif
    </section>

    <div class="aside">

        <section id="top10items">

            <article class="turnovers">
                <header>
                    <h2>Top turnovers</h2>
                </header>
                <main>
                    <table>
                        <tr>
                            <th>Symbol</th>
                            <th class="c_digit">Turnover</th>
                            <th class="c_digit">LTP</th>
                        </tr>
                        @foreach($turnovers as $turnover)
                        <tr>
                            <td title="{{ $turnover->security_name }}">{{$turnover->symbol}}</td>
                            <td class="c_digit">{{number_format($turnover->total_traded_value)}}</td>
                            <td class="c_digit">{{number_format($turnover->last_updated_price)}}</td>
                        </tr>
                        @endforeach
                    </table>
                </main>
            </article>

            <article class="gainers">
                <header>
                    <h2>Top gainers</h2>
                </header>
                <main>
                    <table>
                        <tr>
                            <th>Symbol</th>
                            <th class="c_digit">LTP</th>
                            <th class="c_digit">Change</th>
                        </tr>
                        @foreach($gainers as $turnover)
                        <tr>
                            <td title="{{$turnover['security_name']}}">{{$turnover['symbol']}}</td>
                            <td class="c_digit">{{number_format($turnover['ltp'])}}</td>
                            <td class="c_digit" title="{{number_format($turnover['change'])}}">{{number_format($turnover['change_per'],2)}}%</td>
                        </tr>
                        @endforeach
                    </table>
                </main>
            </article>

            <article class="loosers">
                <header>
                    <h2>Top loosers</h2>
                </header>
                <main>
                    <table>
                        <tr>
                            <th>Symbol</th>
                            <th class="c_digit">LTP</th>
                            <th class="c_digit">Change</th>
                        </tr>
                        @foreach($loosers as $turnover)
                        <tr>
                            <td title="{{$turnover['security_name']}}">{{$turnover['symbol']}}</td>
                            <td class="c_digit">{{number_format($turnover['ltp'])}}</td>
                            <td class="c_digit" title="{{number_format($turnover['change'])}}">{{number_format($turnover['change_per'],2)}}%</td>
                        </tr>
                        @endforeach
                    </table>
                </main>
            </article>

        </section>

        @if(!empty($sectors))
        <section class="sectors">
            <header>
            <h2>Sectorwise turnover</h2>
            </header>
            <main>
                @foreach($sectors as $sector)
                <div class="sector">
                    <h3 class='sector-name' title="{{$sector['sector']}}">{{$sector['sector']}}</h3>
                    <!-- <div class="quantity">{{$sector['total_qty']}}</div> -->
                    <div class="volume"><label>Turnover</label> {{ number_format($sector['total_value'])}}</div>
                </div>
                @endforeach
            </main>
            <footer></footer>
        </section>
        @endif

    </div>
</div>
        
@endsection