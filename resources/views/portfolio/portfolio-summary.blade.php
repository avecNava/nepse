@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('header_title')
    <h1 class="c_title">My portfolio</h1>
    <span class="c_info"><label>as of</label> {{ $transaction_date }}</span>
@endsection

<style>

</style>
@section('js')
    
@endsection

@section('content')

    <div class="c_portfolio_container">
    
        <div id="loading-message" style="display:none">Loading... Please wait...</div>
        @if(!empty($scorecard))
        <section class="c_score_cards">
            <article>
                <header>Total investment</header>
                <main>{{number_format($scorecard['total_investment'])}}</main>
                <footer></footer>
            </article>
            <article>
                <header>Net worth</header>
                <main>{{number_format($scorecard['net_worth'])}}</main>
                <footer></footer>
            </article>
            <article class="{{$scorecard['net_gain_css']}}">
                <header>Net Gain</header>
                <main>{{number_format($scorecard['net_gain'])}}</main>
                <footer>{{$scorecard['net_gain_per'] ? number_format($scorecard['net_gain_per']) :''}}%</footer>
            </article>
            <article>
                <header># Shareholders</header>
                <main>{{number_format($scorecard['shareholders'])}}</main>
                <footer></footer>
            </article>
            <article>
                <header># Scripts</header>
                <main>{{number_format($scorecard['total_scripts'])}}</main>
                <footer></footer>
            </article>
            
        </section>
        @endif
            
        @if( !empty($portfolio_summary) )
        
        <section class="a_portfolio">
        
            <header>
                @if(count($portfolio_summary)>1)
                    <div class="portfolio__message">
                        {{count($portfolio_summary)}} members
                    </div>
            @endif
            </header>

            <main>
                <section class='header-row'>
                    <div></div>
                    <div class='header-labels header-items'>
                        <div class="col1"># Scripts</div>
                        <div class="col2"># Units</div>
                        <div class="col3">Investment</div>
                        <div class="col4">Current worth</div>
                        <div class="col5">Previous worth</div>
                        <div class="col6">Difference</div>
                        <div class="col7">Gain</div>
                    </div>
                </section>

                @foreach ($portfolio_summary as $row)
                
                <details id="row-{{$row['shareholder_id']}}" class='summary'>

                    <summary>
                    <section class='shareholder-group'>
                        
                        @php
                            $shareholder = App\Services\UtilityService::serializeString($row['shareholder'],'-');
                        @endphp
                        <div class="shareholder" title="Click to see portfolio for '{{$row['shareholder']}}'">
                            <h3><a href={{url("portfolio/$shareholder", [ $row['shareholder_id'] ]) }}>
                                {{ $row['shareholder']}} 
                                </a>
                            </h3>
                        </div>
                        <div class='header-labels'>

                            <div class="col1">                                    
                                {{ number_format($row['total_scripts']) }}
                            </div>
                            <div class="col2">
                                {{ number_format($row['total_units']) }}
                            </div>                                    
                            <div class="col3">
                                {{number_format($row['total_investment'])}}
                            </div>
                            <div class="col4">
                                {{number_format( $row['current_worth'] ) }}
                            </div>
                            <div class="col5">{{number_format($row['prev_worth'])}}</div>

                            <div class="col6">
                                <div class="c_change">
                                    <div>
                                        <div>{{ number_format( $row['change'] ) }} </div>
                                        <span class="{{ $row["change_css"] }}"">
                                            @if($row['change_pc'])
                                                {{ $row['change_pc'] }}%
                                            @endif
                                        </span>
                                    </div>
                                    <div class="{{ $row["change_css"]  }}_icon"></div>
                                </div>
                            </div>

                            <div class="col7">
                                <div class="c_change">
                                    <div>
                                        <div>{{ number_format( $row['gain'] ) }} </div>
                                        <span class={{ $row["gain_css"] }}>
                                            @if($row['gain_pc'])
                                                {{ $row['gain_pc'] }}%
                                            @endif
                                        </span>
                                    </div>
                                    <div class="{{ $row["gain_css"]  }}_icon"></div>
                                </div>

                            </div>

                        </div>
                        
                    </section>

                    </summary>

                    <div id="detail-{{$row['shareholder_id']}}"></div> 

                </details>

                @endforeach   
            
            </main>
            
            <footer><span class="c_info">Last transaction date : {{ $transaction_date }}</span></footer>
        
        </section>
            
        @endif


    </div> <!-- end of portfolio_container -->

    <script>
        
        var elements = document.getElementsByClassName("summary");

        var getUserStocks = function() {

            
            var attribute = this.getAttribute("id");
            const id = parseID('row-',attribute);
            
            //ignore if details already open
            const isOpen = document.getElementById(attribute).hasAttribute('open');
            if(isOpen){
                return;
            }

            showLoadingMessage();
            

            let request = new XMLHttpRequest();

            //todo: get symbols by shareholder and display
            request.open('GET', '/summary/stocks/'+ id, true);

            request.onload = function() {
                if (this.status >= 200 && this.status < 400) {
                    const result = JSON.parse(this.response);
                    if(result.status === 'success'){
                        const stocks = JSON.parse(result.data);
                        showUserStocks(stocks, id);
                    }else{
                        showUserStocksError(data.message);
                    }
                    hideLoadingMessage();
                }
            }  
            request.onerror = function() {
                hideLoadingMessage();
            };
            request.send();

        };

        Array.from(elements).forEach(function(element) {
            element.addEventListener('click', getUserStocks);
        });

        function showUserStocksError(msg){
            document.getElementById('message').innerHTML = msg;
        }

        function showUserStocks(stocks, id){
            const html_head = `
                <table>
                    <tr>
                        <th>SN</th>
                        <th>Symbol</th>
                        <th>Qty</th>
                        <th title="Effective rate">Eff. rate</th>
                        <th>Investment</th>
                        <th>LTP</th>
                        <th>Net worth</th>
                        <th>Gain</th>
                        <th title="Previous price">Prev. price</th>
                        <th title="Previous worth">Prev. worth</th>
                        <th>Change</th>
                    </tr>`;

            const html_foot = 
                `<tr class="separator">
                    <td colspan="11"><hr/></td>
                </tr>
                </table>`;

            var html_body ='';
            var nf = Intl.NumberFormat();
            var $row = 0;

            stocks.forEach(item => {
                
                let close_price = 0;

                if(!item.close_price) {
                    close_price = item.last_updated_price;
                 }else{
                    close_price = item.close_price;
                 }

                var quantity = item.quantity;
                const worth = quantity * close_price;
                const prev_worth = item.previous_day_close_price * quantity;
                let change = worth - prev_worth;
                let rate = item.wacc ? item.wacc : 0;
                const investment = item.investment ? item.investment : 0;
                const investment_f = investment > 0 ? nf.format(investment) : '-';
                const gain = worth - investment;
                
                let change_pc = '';
                let gain_pc = '';
                let change_pc_f = '';
                let gain_pc_f = '';
                let gain_css = '';
                let change_css='';

                if(prev_worth > 0){
                    change_pc = (change / prev_worth)*100;
                    if(change_pc != 0)
                        change_pc_f = ` (${ change_pc.toFixed(1) })%`;
                }
                if(investment > 0){ 
                    gain_pc = (gain/investment)*100;
                    if(gain_pc != 0)
                        gain_pc_f = ` (${ gain_pc.toFixed(1) })%`;
                }
                
                if(gain > 0){ gain_css = 'increase'; } else if(gain < 0){ gain_css = 'decrease'; }
                if(change > 0){ change_css = 'increase'; }  else if(change < 0) { change_css = 'decrease'; }
                
                const effective_rate = item.effective_rate ? item.effective_rate : '';
                const l_name = item.last_name.length>0 ? `-${item.last_name}` :'';
                const full_name = `${item.first_name}${l_name}`;
                const shareholder_name = serializeString(full_name);
                
                const url = window.location.origin;
                html_body += 
                `<tr>
                    <td class="c_digit">${ ++$row }</td>
                    <td title="${ item.stock_id }-${ item.security_name }">
                        <a href="${url}/portfolio/${shareholder_name}/${item.symbol}/${item.shareholder_id }">
                            ${ item.symbol }
                        </a>
                    </td>
                    <td class="c_digit"> ${ quantity }</td>
                    <td class="c_digit"> ${ rate } </td>
                    <td class="c_digit"> ${ investment_f }</td>
                    <td class="c_digit"> ${ close_price ? close_price : '-' } </td>
                    <td class="c_digit"> ${ nf.format(worth) }</td>

                    <td>
                        <div class="c_change">
                            <div>
                                <span class='change-val'>
                                    ${nf.format(gain)}
                                </span>
                                <span class="change-val ${ gain_css }">
                                    ${gain_pc_f}
                                </span>
                            </div>
                            <div class="${ gain_css }_icon"></div>
                        </div>
                    </td>
                    
                    <td class="c_digit"> ${ item.previous_day_close_price ? item.previous_day_close_price : '-' }</td>
                    <td class="c_digit"> ${ nf.format(prev_worth) }</td>
                    <td>
                        <div class="c_change">
                            <div>
                                <span class='change-val'>
                                    ${nf.format(change)}
                                </span>
                                <span class="change-val ${ change_css }">
                                    ${change_pc_f}
                                </span>
                            </div>
                            <div class="${ change_css }_icon"></div>
                        </div>
                    </td>
                </tr>
                `
            });

        var html = `${html_head}${html_body}${html_foot}`;
        // console.log(html);
        const detail_id = `detail-${id}`;
        document.getElementById(detail_id).innerHTML = html;

        }

        // redirect the user to the selected sharehodler's poftfolio (ie, /portfolio/7)
        function loadShareholder(){
            
            let url = "{{url('portfolio')}}";
            const shareholder = document.getElementById('shareholder');
            const options = shareholder.options[shareholder.selectedIndex];
            let username = options.text.split(" ")[0];
            username = username.toLowerCase();
            //append shareholder_id to the url (ie, /portfolio/username/7)
            if(shareholder.selectedIndex > 0)
                url = `${url}/${username}/${options.value}`;
            
            window.location.replace(url);
        }

    </script>

@endsection