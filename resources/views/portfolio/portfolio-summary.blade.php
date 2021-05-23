@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('header_title')
    <h1 class="c_title">Dashboard</h1>
@endsection

<style>

</style>
@section('js')
    
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

    <div class="summary__wrapper">
    
        @if(!empty($scorecard))
        <section class="score_card__wapper">
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
            <article class="{{$scorecard['net_gain_css']}}">
                <header>Net Gain</header>
                <main class="value">{{number_format($scorecard['net_gain'])}}</main>
                <footer>{{$scorecard['net_gain_per'] ? number_format($scorecard['net_gain_per']) :''}}%</footer>
            </article>
            <article class="optional">
                <header>Index</header>
                <main class="value">{{number_format(optional($index)->closingIndex ,2)}}</main>
                <footer>
                    as of @if($index){{optional($index)->transactionDate}}@endif
                </footer>
            </article>
            <article>
                <header># Scrips</header>
                <main class="value">{{number_format($scorecard['total_scrips'])}}</main>
                <footer>
                    @if($scorecard['shareholders'] > 1)
                        {{number_format($scorecard['shareholders'])}} shareholders
                    @endif
                </footer>
            </article>
            
        </section>
        @endif
            
        @if( !empty($portfolio_summary) )
        
            <main class="showcase__wrapper">
        
                @foreach ($portfolio_summary as $key => $row)
                
                <article id="row-{{$row['uuid']}}" class='showcase'>

                    <div class="left_section">

                        <div class="profile__wrapper">
                            
                            @php
                                $gender = Str::lower($row['gender']) == "f" ? "female" : "male";
                            @endphp
                            <a href="{{url('portfolio',[ $row['uuid'] ]) }}">
                                <div class="dp dp-{{ $gender}}">
                                </div>
                            </a>
                            
                            <div class="name" title="{{ $row['shareholder'] }}">
                                <h2><a href="{{url('portfolio',[ $row['uuid'] ]) }}">
                                    {{ $row['shareholder'] }}
                                    </a>
                                </h2>
                                <div class="relation">{{ $row['relation'] ?: 'You' }}</div>
                            </div>
                        </div>

                        <div class="summary">

                            <div>
                                <span>Total</span>
                                {{ number_format($row['total_scrips']) }} scrips
                            </div>
                            <div>
                                <span>Investment</span>
                                रु {{number_format($row['total_investment'])}}
                            </div>
                            <div>
                                <span>Net worth</span>
                                रु {{number_format( $row['current_worth'] ) }}
                            </div>

                            <div>
                                <span>Change</span>
                                <div class="change">
                                    <div>रु {{ number_format( $row['change'] ) }} </div>
                                    <div class="c_change_per">
                                        <span class="{{ $row['change_css'] }}">
                                            @if($row['change_pc'])
                                                {{ $row['change_pc'] }}%
                                            @endif
                                        </span>
                                    </div>
                                    <div class="{{ $row['change_css']  }}_icon"></div>
                                </div>
                            </div>

                            <div>
                                <span>Net Gain</span>
                                <div class="c_change">
                                    <div>रु {{ number_format( $row['gain'] ) }} </div>
                                    <div class="c_change_per">
                                        <span class="{{ $row['gain_css'] }}">
                                            @if($row['gain_pc'])
                                                {{ $row['gain_pc'] }}%
                                            @endif
                                        </span>
                                    </div>
                                    <div class="{{ $row['gain_css']  }}_icon"></div>
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="right_section">
                        <div class="top5grossing">
                            <h3>Top grossing</h3>
                            <table>
                                <tr>
                                    <th>Symbol</th>
                                    <th title="Quantity">Qty.</th>
                                    <th>LTP</th>
                                    <th>Worth</th>
                                </tr>
                                @php
                                    $top_grossing = $arr_grossing[$key];
                                    $uuid = $row['uuid'];
                                @endphp
                                @foreach ($top_grossing as $record)
                                <tr>
                                    
                                    <td>
                                        <a href="{{ url('portfolio',[ $record['symbol'], $uuid ]) }}">
                                            <abbr title="{{ $record['name'] }}">{{ $record['symbol'] }}</abbr>
                                        </a>
                                    </td>
                                    <td class="c_digit">{{ $record['quantity'] }}</td>
                                    <td class="c_digit">{{ number_format($record['ltp']) }}</td>
                                    <td class="c_digit">{{ number_format($record['worth']) }}</td>
                                </tr>
                                @endforeach

                            </table>
                        </div>
                        <div class="top5gains">
                            <h3>Top Gains</h3>
                            <table>
                                <tr>
                                    <th>Symbol</th>
                                    <th>Gains</th>
                                </tr>
                                @php
                                    $top_gains = $arr_gainloss[$key]['gain'];
                                    $uuid = $row['uuid'];
                                @endphp
                                @foreach ($top_gains as $record)
                                @if($record['gain'] > 0)
                                <tr>
                                    <td>
                                        <a href="{{ url('portfolio',[ $record['symbol'], $uuid ]) }}">
                                            <abbr title="{{ $record['name'] }}">{{ $record['symbol'] }}</abbr>
                                        </a>
                                    </td>
                                    <td class="c_digit increase">{{ number_format($record['gain']) }}</td>
                                </tr>
                                @endif
                                @endforeach
                                
                            </table>
                        </div>
                        @php
                            $top_loss = $arr_gainloss[$key]['loss'];
                            $uuid = $row['uuid'];
                        @endphp
                        @if(count($top_loss) > 0)
                        <div class="top5loss">
                            <h3>Top Losses</h3>
                            <table>
                                <tr>
                                    <th>Symbol</th>
                                    <th>Losses</th>
                                </tr>
                                
                                @foreach ($top_loss as $record)
                                @if($record['gain'] < 0)
                                <tr>
                                    <td>
                                        <a href="{{ url('portfolio',[ $record['symbol'], $uuid ]) }}">
                                            <abbr title="{{ $record['name'] }}">{{ $record['symbol'] }}</abbr>
                                        </a>
                                    </td>
                                    <td class="c_digit decrease">{{ number_format($record['gain']) }}</td>
                                </tr>
                                @endif
                                @endforeach
                                
                            </table>
                        </div>
                        @endif
                        <!-- <div class="events">
                            <h3>Events</h3>
                            <div class="event">
                                <p class="event">Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                            </div>
                            <div class="event">
                                <p class="event">Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                            </div>
                            <div class="event">
                                <p class="event">Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                            </div>
                            <div class="event">
                                <p class="event">Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                            </div>
                        </div> -->
                    </div>

                </article>

                @endforeach   
            
            </main>
            
        @endif


    </div> <!-- end of portfolio_container -->

    <script>
        
        // var elements = document.getElementsByClassName("summary");

        // var getUserStocks = function() {
            
        //     var attribute = this.getAttribute("id");
        //     const id = parseID('row-',attribute);
            
        //     //ignore if details already open
        //     const isOpen = document.getElementById(attribute).hasAttribute('open');
        //     if(isOpen){
        //         return;
        //     }

        //     showLoadingMessage();
            

        //     let request = new XMLHttpRequest();

        //     //todo: get symbols by shareholder and display
        //     request.open('GET', '/summary/'+ id, true);

        //     request.onload = function() {
        //         if (this.status >= 200 && this.status < 400) {
        //             const result = JSON.parse(this.response);
        //             if(result.status === 'success'){
        //                 const stocks = JSON.parse(result.data);
        //                 showUserStocks(stocks, id);
        //             }else{
        //                 showUserStocksError(data.message);
        //             }
        //             hideLoadingMessage();
        //         }
        //     }  
        //     request.onerror = function() {
        //         hideLoadingMessage();
        //     };
        //     request.send();

        // };

        // Array.from(elements).forEach(function(element) {
        //     element.addEventListener('click', getUserStocks);
        // });

        // function showUserStocksError(msg){
        //     document.getElementById('message').innerHTML = msg;
        // }

        // function showUserStocks(stocks, id){
        //     const html_head = `
        //         <table>
        //             <tr>
        //                 <th class="c_digit">SN</th>
        //                 <th>Symbol</th>
        //                 <th class="c_digit">Qty</th>
        //                 <th class="c_digit" title="Effective rate">Eff. rate</th>
        //                 <th class="c_digit">Investment</th>
        //                 <th class="c_digit">LTP</th>
        //                 <th class="c_digit">Net worth</th>
        //                 <th class="c_digit">Gain</th>
        //                 <th class="c_digit" title="Previous price">Prev. price</th>
        //                 <th class="c_digit" title="Previous worth">Prev. worth</th>
        //                 <th class="c_digit">Change</th>
        //             </tr>`;

        //     const html_foot = 
        //         `<tr class="separator">
        //             <td colspan="11"></td>
        //         </tr>
        //         </table>`;

        //     var html_body ='';
        //     var nf = Intl.NumberFormat();
        //     var $row = 0;

        //     stocks.forEach(item => {
                
        //         let close_price = 0;

        //         if(!item.close_price) {
        //             close_price = item.last_updated_price;
        //          }else{
        //             close_price = item.close_price;
        //          }

        //         var quantity = item.quantity;
        //         const worth = quantity * close_price;
        //         const prev_worth = item.previous_day_close_price * quantity;
        //         let change = worth - prev_worth;
        //         let rate = item.wacc ? item.wacc : 0;
        //         const investment = item.investment ? item.investment : 0;
        //         const investment_f = investment > 0 ? nf.format(investment) : '-';
        //         const gain = worth - investment;
                
        //         let change_pc = '';
        //         let gain_pc = '';
        //         let change_pc_f = '';
        //         let gain_pc_f = '';
        //         let gain_css = '';
        //         let change_css='';

        //         if(prev_worth > 0){
        //             change_pc = (change / prev_worth)*100;
        //             if(change_pc != 0)
        //                 change_pc_f = ` (${ change_pc.toFixed(1) })%`;
        //         }
        //         if(investment > 0){ 
        //             gain_pc = (gain/investment)*100;
        //             if(gain_pc != 0)
        //                 gain_pc_f = ` (${ gain_pc.toFixed(1) })%`;
        //         }
                
        //         if(gain > 0){ gain_css = 'increase'; } else if(gain < 0){ gain_css = 'decrease'; }
        //         if(change > 0){ change_css = 'increase'; }  else if(change < 0) { change_css = 'decrease'; }
                
        //         const effective_rate = item.effective_rate ? item.effective_rate : '';
        //         const l_name = item.last_name.length>0 ? `-${item.last_name}` :'';
        //         // const full_name = `${item.first_name}${l_name}`;
        //         // const shareholder_name = serializeString(full_name);
                
        //         const url = window.location.origin;
        //         html_body += 
        //         `<tr>
        //             <td class="c_digit">${ ++$row }</td>
        //             <td title="${ item.stock_id }-${ item.security_name }">
        //                 <a href="${url}/portfolio/${item.symbol}/${item.uuid }">
        //                     ${ item.symbol }
        //                 </a>
        //             </td>
        //             <td class="c_digit"> ${ quantity }</td>
        //             <td class="c_digit"> ${ rate } </td>
        //             <td class="c_digit"> ${ investment_f }</td>
        //             <td class="c_digit"> ${ close_price ? close_price : '-' } </td>
        //             <td class="c_digit"> ${ nf.format(worth) }</td>

        //             <td>
        //                 <div class="c_change">
        //                     <div>
        //                         <span class='change-val'>
        //                             ${nf.format(gain)}
        //                         </span>
        //                         <span class="change-val ${ gain_css }">
        //                             ${gain_pc_f}
        //                         </span>
        //                     </div>
        //                     <div class="${ gain_css }_icon"></div>
        //                 </div>
        //             </td>
                    
        //             <td class="c_digit"> ${ item.previous_day_close_price ? item.previous_day_close_price : '-' }</td>
        //             <td class="c_digit"> ${ nf.format(prev_worth) }</td>
        //             <td>
        //                 <div class="c_change">
        //                     <div>
        //                         <span class='change-val'>
        //                             ${nf.format(change)}
        //                         </span>
        //                         <span class="change-val ${ change_css }">
        //                             ${change_pc_f}
        //                         </span>
        //                     </div>
        //                     <div class="${ change_css }_icon"></div>
        //                 </div>
        //             </td>
        //         </tr>
        //         `
        //     });

        // var html = `${html_head}${html_body}${html_foot}`;
        // const detail_id = `detail-${id}`;
        // document.getElementById(detail_id).innerHTML = html;

        // }
    </script>

@endsection