@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('header_title')
<h1 class="c_title">Portfolio</h1>
@endsection

@section('js')
    
@endsection

@section('content')

    <div class="shareholder_dashboard__wrapper">
    
        @if(($scorecard['scripts']>0))
            <section class="score_card__wapper">

                <article>
                    <header>Investment</header>
                    <main class="value">{{number_format($scorecard['investment'])}}</main>
                    <footer></footer>
                </article>

                <article>
                    <header>Net worth</header>
                    <main class="value">{{number_format($scorecard['worth'])}}</main>
                    <footer></footer>
                </article>
                
                <article class="{{$scorecard['gain_class']}}">
                    <header>Net Gain</header>
                    <main class="value">{{number_format($scorecard['gain'])}}</main>
                    <footer>{{$scorecard['gain_per'] ? $scorecard['gain_per'] :''}}</footer>
                </article>

                <article title="Previous worth">
                    <header>Prev. worth</header>
                    <main class="value">{{number_format($scorecard['prev_worth'])}}</main>
                    <footer></footer>
                </article>

                <article title="Difference between Current and Previous worth" class="{{$scorecard['change_class']}}">
                    <header>Difference</header>
                    <main class="value">{{number_format($scorecard['change'])}}</main>
                    <footer>{{$scorecard['change_per'] ? $scorecard['change_per'] :''}}</footer>
                </article>

                <article  hidden>
                    <header># Units</header>
                    <main class="value">{{number_format($scorecard['quantity'])}}</main>
                    <footer></footer>
                </article>

                <article hidden>
                    <header># Scripts</header>
                    &nbsp;<main class="value">{{number_format($scorecard['scripts'])}}</main>
                    <footer></footer>
                </article>
                
            </section>
        @endif
    

        @if( !empty($portfolios) )
        
        <section class="main__content">
        
            <header class="info">

                <div class="flex js-apart al-end">

                <div class="flex js-start al-cntr">
                    <h2 class="title">{{ $shareholder }}</h2>
                    @if(count($portfolios)>0)
                    <div class="notification">
                    &nbsp;({{count($portfolios)}} scripts)
                    </div>
                    @endif
                </div>

                <div class="flex js-start al-cntr">
                <select name="shareholders" id="shareholders" onchange="refresh()">
                    @foreach($shareholders as $row)
                    <option value="{{ $row->uuid }}" @if($uuid == $row->uuid) SELECTED @endif>
                        {{$row->first_name}} {{$row->last_name}} 
                    </option>
                    @endforeach
                </select>    
                &nbsp;
                    <div class="message" id="message">                    
                        @if(session()->has('message'))
                            <span class="success">{{ session()->get('message') }}</span>
                        @endif
                        @if(session()->has('error'))
                            <span class="error">{{ session()->get('error') }}</span>
                        @endif
                    </div>

                    @php
                        $row = $portfolios->first();
                    @endphp
                    <form  method="POST" action="/portfolio/export" style="margin:0" class="optional">
                        @csrf()
                        <input name="id" type="hidden" value="{{ optional($row)->shareholder_id }}">
                        <button style="margin:0">Export</button>
                    </form>

                </div>

            </header>

            <main>
            <table>
                <tr>
                    <th class="c_digit optional">SN</th>
                    <th style="text-align:left">Symbol</th>
                    <th class="c_digit">Quantity</th>
                    <th class="c_digit optional">Effective rate</th>
                    <th class="c_digit optional">Investment</th>
                    <th class="c_digit" title="Last transaction price">LTP</th>
                    <th class="c_digit">Worth</th>
                    <th class="c_digit">Gain</th>
                    <th class="c_digit optional">Prev. worth</th>
                    <th class="c_digit optional">Change</th>
                </tr>
                @if(count($portfolios)==0)
                <tr>
                    <td colspan="10">
                    <div class="center-box error-box">
                        <h2 class="message error">Nothing in here<h2>
                        <h3 class="message success">💡 You can add some by clicking the `New` button.</h3>
                    </div>
                    </td>
                </tr>
                @endif
                
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
                    <td class="c_digit optional">{{ $key + 1 }}</td>
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
                    <td class="c_digit optional">{{number_format($wacc,2)}}</td>
                    <td class="c_digit optional">{{ number_format($investment)}}</td>
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
                    <td class="c_digit optional" title="Previous price : {{$record->previous_day_close_price}}">{{ number_format($prev_worth)}}</td>
                    <td class="optional">
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
                    @if(count($portfolios)>0)
                    <span class="c_info">Last trade date : {{ $transaction_date }} <mark>({{ $transaction_date->diffForHumans() }})</mark></span>
                    @endif
                </footer>
            </td></tr>
        
        </section>
            
        @endif


    </div> 

    <script>
        
        function refresh() {
            const el = document.querySelector('select#shareholders');
            const url = `/portfolio/${el.value}`;
            window.location.replace(url);
        }
       
    </script>

@endsection