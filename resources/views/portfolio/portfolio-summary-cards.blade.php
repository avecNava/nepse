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

    <div class="summary__wrapper">
       
        <article>
                
            <div class="profile__wrapper">
                    <h2>Summary</h2>
                    <div>
                    {{number_format($scorecard['total_scrips'])}} scrips
                    @if($scorecard['shareholders'] > 1)
                        ;&nbsp;{{number_format($scorecard['shareholders'])}} shareholders
                    @endif
                    </div>
            </div>

            @php
                $changeIndex = $index->closingIndex - $prevIndex->closingIndex;
                $increase = $changeIndex > 0 ? true : false;
                $changeIndexColor = $increase == true ? 'increase' : 'decrease';
            @endphp

            <section class="score_card__wapper">

                <article>
                    <header>Index</header>
                    <main class="value">
                        {{number_format(optional($index)->closingIndex,2)}}
                    </main>
                    <footer>
                        <span class="{{$changeIndexColor}}">{{number_format($changeIndex,2)}}</span>
                        <!-- as of @if($index){{optional($index)->transactionDate}}@endif -->
                    </footer>
                </article>

                <article>
                    <header>Turnover</header>
                    <main class="value">
                    {{ MyUtility::formatMoney($totalTurnover) }}
                    </main>
                    <footer>
                        ≪  {{ MyUtility::formatMoney($prevTurnover) }} ≫
                    </footer>
                </article>

                <article>
                    <header>Total investment</header>
                    <main class="value">{{number_format($scorecard['total_investment'])}}</main>
                    <footer></footer>
                </article>

                <article>
                    <header>Net worth</header>
                    <main class="value">{{number_format($scorecard['net_worth'])}}</main>
                    <footer></footer>
                </article>

                <article>
                    <header>Net Gain</header>
                    <main class="value {{$scorecard['net_gain_css']}}">{{number_format($scorecard['net_gain'])}}</main>
                    <footer>{{$scorecard['net_gain_per'] ? number_format($scorecard['net_gain_per']) :''}}%</footer>
                </article>
                
            </section>

        </article>

        @if( !empty($portfolio_summary) )
        
            @foreach ($portfolio_summary as $key => $row)
            
            <article id="row-{{$row['uuid']}}">

                <div class="profile__wrapper">
                    
                    @php
                        $gender = Str::lower($row['gender']) == "f" ? "female" : "male";
                    @endphp

                    <!-- <a href="{{url('portfolio',[ $row['uuid'] ]) }}">
                        <div class="dp dp-{{ $gender}}">
                        </div>
                    </a> -->
                    
                    <div class="name" title="{{ $row['shareholder'] }}">
                        <h2><a href="{{url('portfolio',[ $row['uuid'] ]) }}">
                            {{ $row['shareholder'] }}
                            </a>
                        </h2>
                    </div>
                </div>

                <section class="score_card__wapper">

                    <article>
                        <header>Total scrips</header>
                        <main class="value">
                            {{ number_format($row['total_scrips']) }}
                        </main>
                        <footer></footer>
                    </article>

                    <article>
                        <header>Investment</header>
                        <main class="value">रु {{number_format($row['total_investment'])}}</main>
                        <footer></footer>
                    </article>

                    <article>
                        <header>Net worth</header>
                        <main class="value">रु {{number_format( $row['current_worth'] ) }}</main>
                        <footer title="Previous worth">
                        ≪ {{number_format( $row['prev_worth'] ) }} ≫
                        </footer>
                    </article>
                        
                    <article>
                        <header>Day gain</header>
                        <main class="value {{$row['day_gain_css']}}">रु {{ number_format( $row['day_gain'] ) }}</main>
                        <footer>
                            <span>
                                @if($row['day_gain_pc'])
                                    {{ $row['day_gain_pc'] }}%
                                @endif
                            </span>
                        </footer>
                    </article>
                    
                    <article>
                        <header>Net gain</header>
                        <main class="value {{$row['gain_css']}}">रु {{ number_format( $row['gain'] ) }}</main>
                        <footer>
                            <span>
                                @if($row['gain_pc'])
                                    {{ $row['gain_pc'] }}%
                                @endif
                            </span>
                        </footer>
                    </article>

                </section>
                
            </article>

            @endforeach   
            
        @endif

    </div> <!-- end of summary__wrapper -->

@endsection