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

                        <div class="c_shareholder">
                            @if( !empty($shareholders) )
                        
                                <!-- <label for="shareholder">Shareholder</label> -->
                                <select id="shareholder" name="shareholder" onChange="loadShareholder()">
                                    <option value="0">Shareholder (All)</option>
                                    @foreach ($shareholders as $shareholder)
                                
                                    <option 
                                    @php                                
                                    
                                    if( $shareholder_id == $shareholder->id){
                                        echo "SELECTED";
                                    }                                
                                    
                                    @endphp
                                    value="{{ $shareholder->id }}">
                                        {{ $shareholder->first_name }} {{ $shareholder->last_name }}
                                        @if (!empty($shareholder->relation))
                                            ({{ $shareholder->relation }})
                                        @endif
                                    </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                    </div>

                </div>

            </header>

            <main>

                @foreach ($portfolio_summary as $row)

                <details id="row-{{$row['id']}}" class='summary'>

                    <summary>
                        <section class='shareholder-group'>
                            <ul>
                                <li title="{{$row['relation']}}">
                                   <h3>{{$row['shareholder']}}</h3> 
                                </li>

                                <li>
                                    <div class='summary-label'># scripts :</div>
                                    {{ $row['stocks'] }}
                                </li>
                                <li>
                                    <div class='summary-label'># units :</div>
                                    {{ $row['quantity'] }}
                                </li>
                                
                                <li>
                                    <div class='summary-label'>Current worth :</div>
                                    {{round($row['current_worth'],2)}}
                                    @if($row['total_amount'])
                                    ({{$row['total_amount']}})
                                    @endif
                                </li>
                                <li>
                                    <div class='summary-label'>Previous worth :</div>
                                    {{round($row['prev_worth'],2)}}
                                </li>
                                <li>
                                    <div class='summary-label'>Gain :</div>
                                    {{ round($row['gain'], 2 )}}
                                    ({{ round($row['change']/100, 2 )}}%)
                                </li>

                            </ul>
                        </section>
                    </summary>

                    <div id="detail-{{$row['id']}}"></div> 

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
            
            showLoadingMessage();
            
            var attribute = this.getAttribute("id");
            const id = parseID('row-',attribute);

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
            const html_head = `
                <table>
                    <tr>
                        <th>Symbol</th>
                        <th>Quantity</th>
                        <th>LTP</th>
                        <th>Worth (LTP)</th>
                        <th>Prev Price</th>
                        <th>Worth(Prev)</th>
                        <th>Change</th>
                        <th>Cost Price</th>
                        <th>Net Worth</th>
                        <th>Profit</th>
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
                const worth = item.total_quantity * close_price;
                const prev_worth = item.previous_day_close_price * item.total_quantity;
                const change = worth - prev_worth;
                change_css='';
                if(change > 0){
                    change_css = 'increase';
                } 
                else if(change < 0) {
                    change_css = 'decrease';
                }
                const change_pc = ((change / prev_worth)*100).toFixed(2);
                const investment = '';
                const gain = '';
                if(item.effective_rate){
                    investment = (item.total_quantity * item.effective_rate).toFixed(2);
                    const gain = worth - investment;
                }
                html_body += 
                `<tr>
                    <td title="${ item.security_name }"> ${ item.symbol }</td>
                    <td> ${ item.total_quantity }</td>
                    <td> ${ close_price } </td>
                    <td> ${ nf.format(worth) }</td>
                    <td> ${ item.previous_day_close_price }</td>
                    <td> ${ nf.format(prev_worth) }</td>
                    <td>
                        <div class="c_change  ${ change_css }">
                            <span class="c_change_val">
                             ${nf.format(change)}
                            </span>
                            <span class="c_change_per">
                                ( ${change_pc}%)
                            </span>
                        </div>
                    </td>
                    <td>${investment}</td>
                    <td>${investment - worth}</td>
                    <td>${gain}</td>
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