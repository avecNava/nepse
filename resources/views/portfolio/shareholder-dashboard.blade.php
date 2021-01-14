@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('header_title')
<h1 class="c_title">Portfolio ({{ $first_name }})</h1>
@endsection

<style>
    .c_score_cards article {
        width: 140px !important;
    }
</style>

@section('js')
    
@endsection

@section('content')

    <div class="sh_dashboard__wrapper">
    
        <div id="loading-message" style="display:none">Loading... Please wait...</div>
        @if(!empty($scorecard))
            <section class="c_score_cards">

                <article>
                    <header>Investment</header>
                    <main>{{number_format($scorecard['investment'])}}</main>
                    <footer></footer>
                </article>

                <article>
                    <header>Net worth</header>
                    <main>{{number_format($scorecard['worth'])}}</main>
                    <footer></footer>
                </article>
                
                <article class="{{$scorecard['gain_class']}}">
                    <header>Net Gain</header>
                    <main>{{number_format($scorecard['gain'])}}</main>
                    <footer>{{$scorecard['gain_per'] ? $scorecard['gain_per'] :''}}</footer>
                </article>

                <article title="Previous worth">
                    <header>Prev. worth</header>
                    <main>{{number_format($scorecard['prev_worth'])}}</main>
                    <footer></footer>
                </article>

                <article title="Difference between Current and Previous worth" class="{{$scorecard['change_class']}}">
                    <header>Difference</header>
                    <main>{{number_format($scorecard['change'])}}</main>
                    <footer>{{$scorecard['change_per'] ? $scorecard['change_per'] :''}}</footer>
                </article>

                <article>
                    <header># Units</header>
                    <main>{{number_format($scorecard['quantity'])}}</main>
                    <footer></footer>
                </article>

                <article>
                    <header># Scripts</header>
                    <main>{{number_format($scorecard['scripts'])}}</main>
                    <footer></footer>
                </article>
                
            </section>
        @endif
            
        @if( !empty($portfolios) )
        
        <section class="main__content">
        
            <header class="info flex together">
                <h2 class="title">{{ $shareholder }}</h2>
                <div class="notification">
                    ({{count($portfolios)}} scripts)
                </div>
            </header>

            <main>
            <table>
                <tr>
                    <th class="c_digit">SN</th>
                    <th style="text-align:left">Symbol</th>
                    <th class="c_digit">Quantity</th>
                    <th class="c_digit">Effective rate</th>
                    <th class="c_digit">Investment</th>
                    <th class="c_digit" title="Last transaction price">LTP</th>
                    <th class="c_digit">Worth</th>
                    <th class="c_digit">Gain</th>
                    <th class="c_digit">Prev. worth</th>
                    <th class="c_digit">Change</th>
                </tr>
                
                @foreach ($portfolios as $key=>$record)

                @php
                    $wacc = $record->wacc;
                    $quantity = $record->quantity;
                    $investment = $quantity * $wacc;
                    $close_price = $record->last_updated_price ?  $record->last_updated_price : $record->close_price;
                    $worth = $quantity * $close_price;
                    $prev_worth = $quantity * $record->previous_day_close_price;
                    $change = $worth - $prev_worth;
                    $gain = $worth - $investment;
                    $change_class = App\Services\UtilityService::gainLossClass1($change);
                    $change_per = App\Services\UtilityService::calculatePercentage($worth, $prev_worth);
                    $gain_class = App\Services\UtilityService::gainLossClass1($gain);
                    $gain_per = App\Services\UtilityService::calculatePercentage($gain, $investment);
                @endphp
                    
                <tr id="row-{{ $record->id }}">
                    <td class="c_digit">{{ $key + 1 }}</td>
                    <td style="text-align:left" title="{{ $record->stock_id }}-{{ $record->security_name }}">
                        <a href="{{ 
                                    url('portfolio',
                                        [
                                            $record->symbol, 
                                            $record->uuid,
                                        ]
                                    )
                                }}">
                                {{ $record->symbol }}
                        </a>

                    </td>
                    <td class="c_digit">{{number_format($quantity)}}</td>
                    <td class="c_digit">{{number_format($wacc,2)}}</td>
                    <td class="c_digit">{{ number_format($investment)}}</td>
                    <td class="c_digit">{{ number_format($close_price)}}</td>
                    <td class="c_digit">{{ number_format($worth)}}</td>
                    <td class="c_digit">
                        <div class="c_change">
                            <div>
                                <span class="change-val">
                                    {{number_format($gain)}}
                                </span>
                                <span class="change-val {{$gain_class}}">
                                ({{$gain_per}})
                                </span>
                            </div>
                            <div class="{{$gain_class}}_icon"></div>
                        </div>                        
                    </td>
                    <td class="c_digit" title="Previous price : {{$record->previous_day_close_price}}">{{ number_format($prev_worth)}}</td>
                    <td>
                        <div class="c_change">
                            <div>
                                <span class="change-val">
                                    {{number_format($change)}}
                                </span>
                                <span class="change-val {{$change_class}}">
                                ({{$change_per}})
                                </span>
                            </div>
                            <div class="{{$change_class}}_icon"></div>
                        </div>
                    </td>
                </tr>

                @endforeach   

            </table>
            
            </main>
            
            <footer>
                <tr><td colspan="10">
                <footer class="flex js-apart al-end">
                    <div></div>
                    <span class="c_info">Last trade date : {{ $transaction_date }} <mark>({{ $transaction_date->diffForHumans() }})</mark></span>
                </footer>
            </td></tr>
        
        </section>
            
        @endif


    </div> 

    <script>
        
       
    </script>

@endsection