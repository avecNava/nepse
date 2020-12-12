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

                    <p> {{$row['shareholder']}}</p> 

                </details>

                @endforeach   
            
            </main>
            
            <footer><span class="c_info">Last transaction date : {{ $transaction_date }}</span></footer>
        
        </section>
            
        @endif


    </div> <!-- end of portfolio_container -->
    <script>
        
        var elements = document.getElementsByClassName("summary");

        var myFunction = function() {

            var attribute = this.getAttribute("id");
            const id = parseID('row-',attribute);

            let request = new XMLHttpRequest();

            //todo: get symbols by shareholder and display
            request.open('GET', '/portfolio/details/'+ id, true);

            request.onload = function(ele_success, ele_loading) {
                if (this.status >= 200 && this.status < 400) {
                    $data = JSON.parse(this.response);
                    updateInputFields($data);
                    hideLoadingMessage();
                }
            }  
            request.onerror = function() {
                // There was a connection error of some sort
                hideLoadingMessage();
            };
            request.send();
            // request.send(`_token=${_token}&id=${id}`);

            });

        };

        for (var i = 0; i < elements.length; i++) {
            elements[i].addEventListener('click', myFunction, false);
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