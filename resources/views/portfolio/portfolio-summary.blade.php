
@extends('layouts.default')


@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('header_title')
    Dashboard
@endsection

@section('notice')
<?php if(strlen($notice)>0){ ?>
    <div role="notice" class='notice' data-show-notice="yes">
        {!! $notice !!}
    </div>
<?php } ?>
@endsection

@section('content')



<div class="main__wrapper">
    
    <div class="dsh_layout__wrapper">
        
        <div class="box box-summary" style="grid-column:1/2">
            
            <div style="display:flex;justify-content:center">
                <h2>Summary</h2> &nbsp;as of&nbsp;
                <div>{{optional($index)->transactionDate}}</div>
            </div>
                
            <div role="summary" class="card dsh__card">
                    
                @php
                    $changeIndex = optional($index)->closingIndex - optional($previousIndex)->closingIndex;
                    $increase = $changeIndex > 0 ? true : false;
                    $changeIndexColor = $increase == true ? 'increase' : 'decrease';
                @endphp
                <article>
                    <header>&nbsp;</header>
                    <main>
                        <div>{{number_format($scorecard['total_scrips'])}} scrips</div>
                        <div>{{number_format($scorecard['shareholders'])}} shareholders</div>
                    </main>
                    <footer></footer>
                </article>

                <article>
                    <header>Index</header>
                    <main>
                        {{number_format(optional($index)->closingIndex,2)}}<br/>
                        <span class="{{$changeIndexColor}} tiny-txt">
                            <!-- @php echo $changeIndex>0 ? '+' : '-' @endphp -->
                                {{number_format($changeIndex,2)}}
                        </span>
                    </main>
                    <footer>
                        ≪{{number_format(optional($previousIndex)->closingIndex,2)}}≫
                        <!-- as of @if($index){{optional($index)->transactionDate}}@endif -->
                    </footer>
                </article>

                <article>
                    <header>Turnover</header>
                    <main>
                    {{ MyUtility::formatMoney($totalTurnover) }}
                    </main>
                    <footer>
                        ≪{{ MyUtility::formatMoney($prevTurnover) }}≫
                    </footer>
                </article>

                <article>
                    <header>Investment</header>
                    <main>{{number_format($scorecard['total_investment'])}}</main>
                    <footer></footer>
                </article>

                <article>
                    <header>Net worth</header>
                    <main>{{number_format($scorecard['net_worth'])}}</main>
                    <footer></footer>
                </article>

                <article>
                    <header>Net Gain</header>
                    <main class="value {{$scorecard['net_gain_css']}}">{{number_format($scorecard['net_gain'])}}</main>
                    <footer>{{$scorecard['net_gain_per'] ? number_format($scorecard['net_gain_per']) :''}}%</footer>
                </article>
                    
            </div>

        </div>

        <div class="box" style="grid-column:2/4">
        
            <h2>Details by Shareholders</h2>

            @if( !empty($portfolio_summary) )

            <section role="summary by shareholders">

                <table>
                    <thead>
                        <tr>
                            <th>Shareholder</th>
                            <th class="optional"># scrips</th>
                            <th>Day gain</th>
                            <th>Investment</th>
                            <th>Net worth</th>
                            <th class="optional">Prev worth</th>
                            <th>Net gain</th>
                            <th>Gain %</th>
                        </tr>    
                    </thead>
                    <tbody>
                    
                        @foreach ($portfolio_summary as $key => $row)

                        <tr>
                            <td><strong><a href="{{url('portfolio',[ $row['uuid'] ]) }}">{{ $row['shareholder'] }}</a></strong></td>
                            <td class="optional">{{ number_format($row['total_scrips']) }}</td>

                            <td>
                                <div class="data_wrapper">
                                    <div class="{{$row['day_gain_css']}}">{{ number_format( $row['day_gain'] ) }}</div>
                                    <span class="optional">
                                        @if($row['day_gain_pc'])
                                            &nbsp;({{ $row['day_gain_pc'] }}%)
                                        @endif
                                    </span>
                                </div>
                            </td>

                            <td>{{number_format($row['total_investment'])}}</td>
                            <td>{{number_format( $row['current_worth'] ) }}</td>
                            <td class="optional">{{number_format( $row['prev_worth'] ) }}</td>
                            <td>{{ number_format( $row['gain'] ) }}</td>
                            <td>{{ $row['gain_pc'] }}%</td>
                        </tr>

                        @endforeach

                    </tbody>
                </table>

            </section>

            @endif

        </div>

        <div class="box" style="grid-column:1/2">
            <h2>Your top gains</h2>

            @if( !empty($top_gains) )

            <section role="Top gains">

                <table>
                    <thead>
                        <tr>
                            <th>Symbol</th>
                            <th>LTP</th>
                            <th style="text-align:right">Change %</th>
                            <th style="text-align:right">Net worth</th>
                        </tr>    
                    </thead>
                    <tbody>
                        @foreach ($top_gains as $key => $row)
                        <tr>
                            <td><a href="https://nepsealpha.com/trading/chart?symbol={{ $row['symbol'] }}" target="_blank" rel="noopener noreferrer" title="{{ $row['name'] }}">{{ $row['symbol'] }}</a></td>
                            <td>{{ number_format($row['ltp']) }}</td>
                            <td align="right"><span class="{{$row['change_css']}}">{{ number_format($row['change_per'],2) }}%</span></td>
                            <td align="right">{{ number_format($row['worth']) }}</td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>

            </section>

            @endif
        </div>

        <div class="box" style="grid-column:2/3">
            <h2>Your top losses</h2>
            @if( !empty($top_losses) )

            <section role="Top gains">

                <table>
                    <thead>
                        <tr>
                            <th>Symbol</th>
                            <th>LTP</th>
                            <th style="text-align:right">Change %</th>
                            <th style="text-align:right">Net worth</th>
                        </tr>    
                    </thead>
                    <tbody>
                        @foreach ($top_losses as $key => $row)
                        <tr>
                            <td><a href="https://nepsealpha.com/trading/chart?symbol={{ $row['symbol'] }}" target="_blank" rel="noopener noreferrer" title="{{ $row['name'] }}">{{ $row['symbol'] }}</a></td>
                            <td>{{ number_format($row['ltp']) }}</td>
                            <td align="right"><span class="{{$row['change_css']}}">{{ number_format($row['change_per'],2) }}%</span></td>
                            <td align="right">{{ number_format($row['worth']) }}</td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>

            </section>

            @endif
        </div>

        <div class="box" style="grid-column:3/4">
            <h2>Your top grossing</h2>
            @if( !empty($top_grossing) )

            <section role="Top grossing">

                <table>
                    <thead>
                        <tr>
                            <th>Symbol</th>
                            <th style="text-align:right">LTP</th>
                            <th style="text-align:right">Net worth</th>
                        </tr>    
                    </thead>
                    <tbody>
                    
                        @foreach ($top_grossing as $key => $row)
                        <tr>
                            <td><a href="https://nepsealpha.com/trading/chart?symbol={{ $row['symbol'] }}" target="_blank" rel="noopener noreferrer" title="{{ $row['name'] }}">{{ $row['symbol'] }}</a></td>
                            <td align="right">{{ number_format($row['ltp']) }}</td>
                            <td align="right">{{ number_format($row['worth']) }}</td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>

            </section>

            @endif

        </div>

    </div>

</div> <!-- end of summary__wrapper -->

@endsection