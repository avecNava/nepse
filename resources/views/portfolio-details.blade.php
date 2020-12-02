@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('header_title')
    <h1 class="c_title">Portfolio details</h1>
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
           
            <article class="a_portfolio_details">
            
                <header>

                <div class="a_portfolio_main">
  
                    <div class="c_band">

                        <div id="message" class="message">
                            @if(count($portfolios)>0)
                                {{count($portfolios)}} records
                            @else
                                No records found for the selected Shareholder. Use the form above to import.
                            @endif
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

                        <div class="buttons">
                            <button id="delete" onClick="deletePortfolios()">Delete</button>
                        </div>

                    </div>

                </div>
                </header>

                <main>
                <table>
                    <tr>
                        <th>Symbol</th>
                        <th>Quantity</th>
                        <th>Unit cost</th>
                        <th>Total</th>
                        <th>Effective rate</th>
                        <th>Offer</th>
                        <th>Sector</th>
                        <th>Shareholder</th>
                        <th>Purchase date</th>
                    </tr>
                    
                    @foreach ($portfolios as $record)
                        
                        <tr>
                            
                            <td title="{{ $record->security_name }}">
                                @if( !empty($record))
                                    <input type="checkbox" name="chk_{{ $record->id }}" id="chk-{{ $record->id }}">&nbsp;
                                    <a href="{{url('portfolio/edit', [$record->id])}}">{{ $record->symbol }}</a>
                                @endif
                            </td>
                            <td>{{ $record->quantity }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td title="{{$record->offer_name}}">{{$record->offer_code}}</td>
                            <td></td>
                            <td>{{$record->first_name}} {{$record->last_name}}</td>
                            <td>{{$record->purchase_date}}</td>
                        </tr>

                    @endforeach   

                </table>
            </main>
            
            <footer></footer>
            
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