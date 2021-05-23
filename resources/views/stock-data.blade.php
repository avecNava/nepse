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
<?php if(count($notice)>0){ ?>
    <div role="notice" class='notice' data-show-notice="yes">
        <span class='title'>{{$notice['title']}}</span>
        {!!$notice['message']!!}
    </div>
<?php } ?>
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
        
        <article class="turnovers">
            <header>
                <h2>Top Turnover by sectors</h2>
            </header>
            <main>
                <table>
                    <tr>
                        <th>Sector</th>
                        <th class="c_digit">Turnover</th>
                    </tr>
                    @foreach($sectors as $sector)
                    @php
                    //$perTurnover = ($sector['total_value']/$totalTurnover)*100;
                    @endphp
                    <tr>
                        <td title="{{$sector['sector']}}">{{ \Illuminate\Support\Str::limit($sector['sector'], 15) ?: 'Blank'}}</td>
                        <td class="c_digit">
                            {{MyUtility::formatMoney( $sector['total_value'] )}}
                        </td>
                    </tr>
                    @endforeach
                </table>
            </main>
        </article>
        @endif

    </div>
</div>
        
@endsection