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

<div class="main__wrapper">

    <section class="transactions" id="trade_summary" style="display:block">
        
        <div class="trade_summary__wrapper">
        
        <div>
            <div class="flex js-apart al-cntr">
                <div>
                    <h2>NEPSE Today</h2>
                    <div>{{$currentIndex->transactionDate}}</div>
                </div>
                <h3><a class="market_open" href="{{url('stock-data')}}">Market data</a></h3>
            </div>
            <div id="area_chart" style="width: 100%; min-height: 300px;"></div>

        </div>


        <div class="trade_summary">
            
            @php
                $index_change = 0;
                if($currentIndex){
                    $index_change = $currentIndex->closingIndex - $prevIndex->closingIndex;
                }
                $change_css = \App\Services\UtilityService::gainLossClass1($index_change);
                $change_per = \App\Services\UtilityService::calculatePercentage($index_change, $prevIndex->closingIndex);
            @endphp
            
            <div class="item">
                <label>Index </label>
                <div class="value" id="current_index">{{number_format(optional($currentIndex)->closingIndex,2)}}</div>
                <div class="sm-text {{$change_css}}">{{ number_format( $index_change,2)}} &nbsp;({{ $change_per }})</div>
            </div>

            <div class="item">
                <label>Previous index</label>
                <div class="value" id="prev_index">{{number_format(optional($prevIndex)->closingIndex,2)}}</div>
            </div>    

            <div class="item">
                <label># Scrips</label>
                <div class="value">{{ number_format($totalScrips) }}</div>
            </div>

            <div class="item" title="{{ number_format( $totalTurnover) }}">
                <label>Turnover</label>
                <div class="value" id="current_over">{{ MyUtility::formatMoney($totalTurnover) }}</div>
            </div>

        </div>
    </div>
    </section>

    <section id="articles">

        <article class="turnovers">
            <header>
                <h2>Top turnovers</h2>
            </header>
            <main>
                <table>
                    <tr>
                        <th>Symbol</th>
                        <th class="c_digit">Turnover</th>
                        <th class="c_digit optional">LTP</th>
                    </tr>
                    @foreach($turnovers as $turnover)
                    <tr>
                        <td title="{{ $turnover->security_name }}">{{$turnover->symbol}}</td>
                        <td class="c_digit">{{number_format($turnover->total_traded_value)}}</td>
                        <td class="c_digit optional">{{number_format($turnover->last_updated_price)}}</td>
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

    <section id="sectors">
        <article>
            <h2>Top Turnover by sectors</h2>
        </article>
        <div>
            <div id="pie_chart" style="min-width:59.2rem;min-height: 600px;" hidden></div>
            <article class="sectors">            
                <main>
                    <table>
                        <tr>
                            <th>Sector</th>
                            <th class="c_digit">Turnover</th>
                            <th class="c_digit">%</th>
                        </tr>
                        @foreach($sectors as $sector)
                        @php
                        $perTurnover = ($sector['total_value']/$totalTurnover)*100;
                        @endphp
                        <tr>
                            <td title="{{$sector['sector']}}">{{ \Illuminate\Support\Str::limit($sector['sector'], 15) ?: '***'}}</td>
                            <td class="c_digit">
                                {{MyUtility::formatMoney( $sector['total_value'] )}} 
                            </td>
                            <td class="c_digit">
                            {{ number_format($perTurnover, 2)}}%
                            </td>
                        </tr>
                        @endforeach
                    </table>
                </main>
            </article>
        </div>        
    </section>
    
    <section class="footer-date">
        <div title="Last transaction time">{{ $last_updated_time }} <mark style="display:inline-block">({{ $last_updated_time->diffForHumans() }})</mark></div>
    </section>
    
</div>


<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">

    // Load the Visualization API and the piechart package.
    google.charts.load('current', {'packages':['corechart','table']});

    // Set a callback to run when the Google Visualization API is loaded.
    google.charts.setOnLoadCallback(drawChart);    
    // google.charts.setOnLoadCallback(drawChart1);    
    google.charts.setOnLoadCallback(drawChartTradesBySector);    

    //line chart for current index
    function drawChart() {
        let request = new XMLHttpRequest();
        const url = `${window.location.origin}/chart/current-index`;
        request.open('GET', url, true);
        request.onload = function() {

            if (this.status >= 200 && this.status < 400) {
                json_data = JSON.parse(this.response);
                
                const epoch = json_data.epoch*1000;
                const dt = new Date(json_data.dateString);
                

                const month = '0' + (dt.getMonth() + 1);
                const date = '0' + dt.getDate();
                const date_str = `${dt.getFullYear()}-${ month.substring(month.length-2)}-${ date.substring(date.length-2)} ${dt.getHours()}:${dt.getMinutes()}`;
                // console.table(json_data.indexHistory.cols);
                // console.table(json_data.indexHistory.rows);
                var data = new google.visualization.DataTable(json_data.indexHistory);
                var options = {
                    legend:'none',
                    title: `NEPSE index: ${json_data.index} (${date_str})`,
                    titleTextStyle:{ 
                                    color: '#000',
                                    fontSize: '15px',
                                    bold: true,
                                },
                    hAxis: {
                        
                        viewWindow: {
                            min: new Date(dt.getFullYear(), dt.getMonth()+1, dt.getDate(), 11 ),
                            max: new Date(dt.getFullYear(), dt.getMonth()+1, dt.getDate(), dt.getHours(), dt.getMinutes())
                        },
                        gridlines: {
                            count: -1,
                            units: {
                            days: {format: ['MMM dd']},
                            hours: {format: ['HH:mm', 'ha']},
                            }
                        },
                        minorGridlines: {
                            units: {
                            hours: {format: ['hh:mm:ss a', 'ha']},
                            minutes: {format: ['HH:mm a Z', ':mm']}
                            }
                        }
                        
                    },
                    vAxis: {title:"Index"},
                    hAxis: {title:"Time"},
                    
                };

                var chart = new google.visualization.AreaChart(document.getElementById('area_chart'));
                document.getElementById('area_chart').style.display="block";
                document.getElementById('trade_summary').style.display="block";
                chart.draw(data, options);
            
            }
        }  
        request.send();
    }

   
    function drawChartTradesBySector() {
        let request = new XMLHttpRequest();
        const url = `${window.location.origin}/chart/sector-turnover`;
        request.open('GET', url, true);
        request.onload = function() {

            if (this.status >= 200 && this.status < 400) {
                const json = JSON.parse(this.response);
                
                // console.table(json.turnover.cols);
                console.table(json.turnover.rows);
                
                var pie_data = new google.visualization.DataTable(json.turnover);
                var options = {
                    title: 'Sectorwise Turnover',
                    is3D: true,
                    pieHole: 0.4,
                    pieSliceText: 'none',
                    sliceVisibilityThreshold: .1,
                    legend: {position: 'labeled', textStyle: {color: 'blue', fontSize: 16}},
                    // chartArea:{left:50,top:20,width:'80%',height:'75%'},

                };

                var chart = new google.visualization.PieChart(document.getElementById('pie_chart'));
                chart.draw(pie_data, options);                
                document.getElementById('pie_chart').style.display="block";
            
            }
        }
        request.send();
    }
    
</script>
@endsection