@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('header_title')
    <h1 class="c_title">My portfolio</h1>
    <span class="c_info"><label>as of</label> {{ $transaction_date }}</span>
@endsection

@section('js')
    
@endsection

@section('content')

    <div class="c_portfolio_container">
    
        <div id="loading-message" style="display:none">Loading... Please wait...</div>

        <section class="c_score_cards">
            <article>
                <header>
                    cost_price
                </header>
                <main>
                    6900
                </main>
                <footer>
                    NPR
                </footer>
            </article>
            <article>
                <header>
                    Investment
                </header>
                <main>
                    6900
                </main>
                <footer>
                    NPR
                </footer>
            </article>
            <article>
                <header>
                    Investment
                </header>
                <main>
                    6900
                </main>
                <footer>
                    NPR
                </footer>
            </article>
            <article>
                <header>
                    Investment
                </header>
                <main>
                    6900
                </main>
                <footer>
                    NPR
                </footer>
            </article>
            <article>
                <header>
                    Investment
                </header>
                <main>
                    6900
                </main>
                <footer>
                    NPR
                </footer>
            </article>
        </section>

            
        @if( !empty($portfolio_summary) )
        
        <section class="a_portfolio">
        
            <header>

                <div class="a_portfolio_main">
            
                    <div class="c_band_right">

                        <div id="message" class="message">
                            {{count($portfolio_summary)}} members
                        </div>

                    </div>

                </div>

            </header>

            <main>
                <section class='shareholder-group header-row'>
                    <div class="shareholder" style="margin-left:6px">Shareholders</div>
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
                    <section class='shareholder-group inset'>

                        <div title="{{$row['relation']}}" class="shareholder">
                            <h3>{{$row['shareholder']}}</h3> 
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
                                        <span>{{ number_format( $row['change'] ) }} </span>
                                        <span class={{ $row["change_class"] }}>
                                            @if($row['change_pc'])
                                                ({{ $row['change_pc'] }}%)
                                            @endif
                                        </span>
                                    </div>
                                    <div class="{{ $row["change_class"]  }}_icon"></div>
                                </div>
                            </div>

                            <div class="col7">
                                <div class="c_change">
                                    <div>
                                        <span>{{ number_format( $row['gain'] ) }} </span>
                                        <span class={{ $row["gain_class"] }}>
                                            @if($row['gain_pc'])
                                                ({{ $row['gain_pc'] }}%)
                                            @endif
                                        </span>
                                    </div>
                                    <div class="{{ $row["gain_class"]  }}_icon"></div>
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
            request.open('GET', '/portfolio/user/'+ id, true);

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

        // for (var i = 0; i < elements.length; i++) {
        //     elements[i].addEventListener('click', getUserStocks, false);
        // }

        Array.from(elements).forEach(function(element) {
            element.addEventListener('click', getUserStocks);
        });

        function showUserStocksError(msg){
            document.getElementById('message').innerHTML = msg;
        }

        function showUserStocks(stocks, id){
            console.log(stocks);
            const html_head = `
                <table>
                    <tr>
                        <th>Symbol</th>
                        <th>Qty</th>
                        <th title="Effective rate">Eff. rate</th>
                        <th>Cost price</th>
                        <th>LTP</th>
                        <th>Current worth</th>
                        <th title="Previous price">*Price</th>
                        <th title="Previous worth">*Worth</th>
                        <th>Change</th>
                        <th>Net Worth</th>
                        <th>Profit</th>
                        <th>Profit %</th>

                    </tr>`;

            const html_foot = `</table>`;
            var html_body ='';
            var nf = Intl.NumberFormat();

            stocks.forEach(item => {

                let up_or_down = '';
                let close_price = '';

                if(!item.close_price) {
                    close_price = item.last_updated_price;
                 }else{
                    close_price = item.close_price;
                 }
                var quantity = item.total_quantity;
                const worth = quantity * close_price;
                const prev_worth = item.previous_day_close_price * quantity;
                const change = worth - prev_worth;
                change_css='';
                if(change > 0){
                    change_css = 'increase';
                } 
                else if(change < 0) {
                    change_css = 'decrease';
                }
                let change_pc = '';
                if(prev_worth>0){
                    change_pc = `(${ ((change / prev_worth)*100).toFixed(1) })%`;
                }
                let cost_price = '-';
                let gain = '-';
                let net_worth = '';
                let gain_per = '';
                if(item.effective_rate){
                    cost_price = (quantity * item.effective_rate).toFixed(1);
                    net_worth = worth - cost_price;
                    gain = net_worth - cost_price;
                    if(cost_price > 0){
                        gain_per = `${((gain/cost_price)*100).toFixed(1)}%`;
                    }
                    gain = nf.format(net_worth - cost_price);
                }
                const url = window.location.origin;
                const effective_rate = item.effective_rate ? item.effective_rate : '';
                const full_name = `${item.first_name}-${item.last_name}`;
                const shareholder_name = serializeString(full_name);

                html_body += 
                `<tr>
                    <td title="${ item.stock_id }-${ item.security_name }">
                        <a href="${url}/portfolio/${shareholder_name}/${item.symbol}/${item.shareholder_id }">
                            ${ item.symbol }
                        </a>
                    </td>
                    <td> ${ quantity }</td>
                    <td> ${ effective_rate } </td>
                    <td>${cost_price}</td>
                    <td> ${ close_price } </td>
                    <td> ${ nf.format(worth) }</td>
                    <td> ${ item.previous_day_close_price }</td>
                    <td> ${ nf.format(prev_worth) }</td>
                    <td>
                        <div class="c_change">
                            <div>
                                <span class='change-val'>
                                    ${nf.format(change)}
                                </span>
                                <span class="change-val ${ change_css }">
                                    ${change_pc}
                                </span>
                            </div>
                            <div class="${ change_css }_icon"></div>
                        </div>
                    </td>
                    <td>${net_worth}</td>
                    <td>${gain}</td>
                    <td>${gain_per}</td>
                </tr>
                `
            });

        var html = `${html_head}${html_body}${html_foot}`;
        // console.log(html);
        const detail_id = `detail-${id}`;
        console.log(detail_id);
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