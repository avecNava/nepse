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

        <section class="portfolio">
        @if( !empty($portfolios) )
           
            <article class="a_portfolio">
            
                <header>
                <div class="a_portfolio_msg">
                    <button id="edit" onClick="editPortfolios()" hidden>Edit</button>
                    <button id="delete" onClick="deletePortfolios()" hidden>Delete</button>
                    <div id="delete-message" style="display:none">
                        The selected scripts have been deleted successfully.
                    </div>
                    
                </div>

                <div class="a_portfolio_main">
                    <!-- <div class="a_portfolio_header">
                        <h1 class="c_title">My portfolio</h1>
                        <span class="c_info">{{ $transaction_date }}</span>
                    </div> -->
                    <div class="c_band">
                        <div id="message" class="message">
                            {{count($portfolios)}} records
                        </div>
                        <div class="c_shareholder">
                            @if( !empty($shareholders) )
                        
                                <label for="shareholder">Shareholder</label>
                                <select id="shareholder" name="shareholder" onChange="loadShareholder()">
                                    <option value="0">All</option>
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
                <table>
                    <tr>
                        <th>
                            <input type="checkbox" name="select_all" id="select_all" onClick="checkAll()">
                            &nbsp;
                            <label for="select_all">Symbol</label>
                        </th>
                        <th>Quantity</th>
                        <th>LTP</th>
                        <th>Worth (LTP)</th>
                        <th>Prev Price</th>
                        <th>Worth(Prev)</th>
                        <th>Change</th>
                        <th>Cost Price</th>
                        <th>Net Worth</th>
                        <th>Profit</th>
                        @if($shareholder_id==0)
                            <th style="text-align:center" title="Shareholder">Initial</th>
                        @endif
                    </tr>
                    @php 
                    //$objPortfolios = json_decode($portfolios);
                    $objPortfolios = $portfolios;
                    
                    @endphp
                    @foreach ($objPortfolios as $record)
                        @php
                            
                            $ltp = $record->close_price;
                            if(empty($record->close_price)){
                                $ltp = $record->last_updated_price;
                            }
                            $quantity = $record->quantity;
                            $ltp_prev = $record->previous_day_close_price;
                            
                            $worth_ltp = round($quantity * $ltp ,2);
                            $worth_prev_ltp = round($quantity * $ltp_prev ,2);

                            $change = $ltp - $ltp_prev;
                            $change_per = round(($change/$ltp_prev)*100,2);

                            //up or down
                            if($change == 0){
                                $upordown = 'no-change';
                            }elseif($change>0){
                                $upordown = 'increase';
                            }else{
                                $upordown = 'decrease';
                            }

                        @endphp
                        <tr>
                            
                            <td>
                            @if( !empty($record))
                                <input type="checkbox" name="chk_{{ $record->id }}" id="{{ $record->id }}">
                                &nbsp;
                                <label for="{{ $record->id }}"></label>
                                <a href="{{ url('portfolio', 
                                            [
                                                Str::lower($record->first_name), 
                                                Str::lower($record->symbol), 
                                                $record->shareholder_id 
                                            ]) 
                                        }}" 
                                    title="{{ $record->security_name }}">
                                    {{ $record->symbol }}
                                </a> 
                                
                            @endif
                            </td>

                            <td>{{ $record->quantity }}</td>
                            <td title="Last updated at : {{$record->last_updated_time}}">{{ number_format($ltp) }}</td>
                            <td>{{ number_format( $worth_ltp) }}</td>
                            <td>{{ number_format($ltp_prev) }}</td>
                            <td>{{ number_format( $worth_prev_ltp ) }}</td>
                            <td>
                                <div class="c_change {{ $upordown }}">
                                    <span class="c_change_val">
                                    {{ $change }} 
                                    </span>
                                    <span class="c_change_per">
                                        ({{$change_per}}%)
                                    </span>
                                </div>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            @if($shareholder_id==0)                                
                                <td style="text-align:center" title="{{ $record->first_name }} {{ $record->last_name }}">                            
                                    {{Str::substr($record->first_name,0,1)}}{{Str::substr($record->last_name,0,1)}}                                    
                                </td>
                            @endif
                        </tr>

                    @endforeach   

                </table>
            </main>
            
            <footer><span class="c_info">Last transaction date : {{ $transaction_date }}</span></footer>
            
        </article>
        @endif

        </section>

    </div> <!-- end of portfolio_container -->
    <script>

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