@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('js')
    
@endsection

@section('content')

    <div class="c_portfolio_container">
    
        <div id="loading-message" style="display:none">Loading... Please wait...</div>

        <section class="c_score_cards">
        
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
                    <div class="a_portfolio_header">
                        <h1 class="c_title">My portfolio</h1>
                        <span class="c_info">{{ $last_transaction_date }}</span>
                    </div>
                    <div class="c_shareholder">
                        @if( !empty($shareholders) )
                      
                            Shareholder 
                            <select id="shareholder" onChange="loadShareholder()">
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
                        <!-- <th>Shareholder</th> -->
                    </tr>
                    
                    @foreach (json_decode($portfolios) as $record)
                        @php
                            //dd($record->quantity);
                            $qty = $record->quantity;
                            $ltp = $record->stock_price->close_price;
                            $ltp_prev = $record->stock_price->previous_day_close_price;
                            $worth_ltp = round($qty * $ltp ,2);
                            $worth_prev_ltp = round($qty * $ltp_prev ,2);

                            $change = $ltp_prev - $ltp;
                            $change_per = round($change/$ltp_prev,2);

                            //up or down
                            if($change == 0){
                                $upordown = 'no-change';
                            }elseif($change>0){
                                $upordown = 'increase';
                            }else{
                                $change = 'decrease';
                            }

                        @endphp
                        <tr>
                            
                            <td>
                            @if( !empty($record->share))
                                <input type="checkbox" name="chk_{{ $record->id }}" id="{{ $record->id }}">
                                &nbsp;
                                <label for="{{ $record->id }}"></label>
                                <a href="{{ url('portfolio/details', [ $record->share->symbol ]) }}" title="{{ $record->share->security_name }}" }}>
                                    {{ $record->share->symbol }}
                                </a> 
                                
                            @endif
                            </td>

                            <td>{{ $record->quantity }}</td>
                            <td>{{ number_format($ltp) }}</td>
                            <td>{{ number_format( $worth_ltp) }}</td>
                            <td>{{ number_format($ltp_prev) }}</td>
                            <td>{{ number_format( $worth_prev_ltp ) }}</td>
                            <td>
                                <div class="c_change {{ $upordown }}">
                                    {{ $change }} 
                                    <span class="c_change_per">
                                        ({{$change_per}}%)
                                    </span>
                                </div>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <!-- <td>
                            @if( !empty($record->shareholder))
                                {{ $record->shareholder->first_name }} {{ $record->shareholder->last_name }}
                            @endif
                            </td> -->
                        </tr>

                    @endforeach   

                </table>
            </main>
            
            <footer><span class="c_info">Last transaction date : {{ $last_transaction_date }}</span></footer>
            
        </article>
        @endif

        </section>

    </div> <!-- end of portfolio_container -->
    <script>

        // redirect the user to the selected sharehodler's poftfolio (ie, /portfolio/7)
        function loadShareholder(){
            
            let url = "{{url('portfolio')}}";
            let shareholder = document.getElementById('shareholder');
            let options = shareholder.options[shareholder.selectedIndex];
            
            
            //append shareholder_id to the url (ie, /portfolio/7)
            if(shareholder.selectedIndex > 0)
                url = url + "/"+ options.value;            
            
            window.location.replace(url);
        }
    </script>

@endsection