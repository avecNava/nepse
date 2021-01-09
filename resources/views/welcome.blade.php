@extends('layouts.app')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('header_title')
    Stock today
@endsection
@section('js')
    <link rel="stylesheet" href="{{ URL::to('css/welcome.css') }}">    
@endsection
@section('custom_css')
    <link rel="stylesheet" href="{{ URL::to('css/welcome.css') }}">    
@endsection
@section('notice')
@if($notice)
    <div class="message_wrapper" data-title="{{$notice['title']}}">
        <p class='title'>{{$notice['title']}}</p>
        <p class='notice'>{!!$notice['message']!!}</p>
    </div>
@endif
@endsection

@section('content')
<div class="welcome__wrapper">

    <section class="transactions">
        <header class="flex js-apart al-end">
            <h2>NEPSE stock data</h2>
            <div class="c_info" title="Last transaction time">{{ $last_updated_time }} <mark>({{ $last_updated_time->diffForHumans() }})</mark></div>
        </header>
        @if($transactions)
        <table width="100%">
            <thead>
                <tr>
                    <th class="optional">SN</th>
                    <th>Symbol</th>
                    <th>LTP</th>
                    <th>Change</th>
                    <th>% change</th>
                    <th class="optional">Open price</th>
                    <th>High price</th>
                    <th>Low Price</th>
                    <th class="optional">Total quantity</th>
                    <th class="optional">Toal value</th>
                    <th class="optional">Previous price</th>
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
                    <td class="optional">{{$key+1}}</td>
                    <td title="{{$transaction->security_name}}" class="symbol">{{$transaction->symbol}}</td>
                    <td>{{$transaction->last_updated_price}}</td>
                    <td class="{{$change_css}}">{{$change}}</td>
                    <td>
                        <div class="flex apart">
                        <div class="{{$change_css}}">
                            {{$change_per}}
                        </div>
                        <div class="{{$change_css}}_icon">&nbsp;</div>
                        </div>
                    </td>
                    <td class="optional">{{$transaction->open_price}}</td>
                    <td>{{$transaction->high_price}}</td>
                    <td>{{$transaction->low_price}}</td>
                    <td class="optional">{{$transaction->total_traded_qty}}</td>
                    <td class="optional">{{$transaction->total_traded_value}}</td>
                    <td class="optional">{{$transaction->previous_day_close_price}}</td>
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
                            <th>Turnover</th>
                            <th>LTP</th>
                        </tr>
                        @foreach($turnovers as $turnover)
                        <tr>
                            <td title="{{ $turnover->security_name }}">{{$turnover->symbol}}</td>
                            <td>{{number_format($turnover->total_traded_value)}}</td>
                            <td>{{number_format($turnover->last_updated_price)}}</td>
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
                            <th>LTP</th>
                            <th>Change</th>
                        </tr>
                        @foreach($gainers as $turnover)
                        <tr>
                            <td title="{{$turnover['security_name']}}">{{$turnover['symbol']}}</td>
                            <td>{{number_format($turnover['ltp'])}}</td>
                            <td title="{{number_format($turnover['change'])}}">{{number_format($turnover['change_per'],2)}}%</td>
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
                            <th>LTP</th>
                            <th>Change</th>
                        </tr>
                        @foreach($loosers as $turnover)
                        <tr>
                            <td title="{{$turnover['security_name']}}">{{$turnover['symbol']}}</td>
                            <td>{{number_format($turnover['ltp'])}}</td>
                            <td title="{{number_format($turnover['change'])}}">{{number_format($turnover['change_per'],2)}}%</td>
                        </tr>
                        @endforeach
                    </table>
                </main>
            </article>

        </section>

        @if(!empty($sectors))
        <section id="sectors">
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