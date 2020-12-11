@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('js')
    <script src="{{ URL::to('js/meroshare.js') }}"></script>
@endsection

@section('content')

    <div id="loading-message" style="display:none">Importing... Please wait...</div>
    <section class="transaction-history">

        <h1 class="c_title">Import transaction</h1>

        <article>

            <header class="c_message">

            </header>
            @php
                $hidden = 'hidden';
                if ($errors->any() || session()->has('error') || session()->has('message')  || session()->has('success')) {
                    $hidden = '';
                } 
            @endphp 
            <main id="meroshare-import-form" class="meroshare-import-form" {{$hidden}}>
                
                <h2>Instructions</h2>
               
                <div class="c_instructions">
                    <ul>
                        <li>Login to your<a href="https://meroshare.cdsc.com.np/" target="_blank" rel="noopener noreferrer">Meroshare account</a>.</li>
                        <li>Click on <strong>My Transaction history</strong>. Filter by <strong>Date</strong>.</li>
                        <li>Click on CSV button to download the transaction history.</li>
                        <li>
                            Click on Choose file (below) and browse the CSV file recently downloaded.
                            <br>Click here to see
                            <a href="{{ URL::to('templates/sample-meroshare-transaction-history.xlsx')}}" target="_blank">SAMPLE FILE</a> file
                        </li>
                        <li>Choose a Shareholder name.</li>
                        <li>Click on <strong>Import</strong>.</li>
                    </ul>  
                </div>
               

                <div class="c_band">                    
                    <h2>Import transaction from Meroshare account.</h2>
                </div>

                <form method="POST" action="/meroshare/transaction" enctype="multipart/form-data">

                    <div class="block-left">

                        <div class="form-field">
                            <button type="submit">Import</button>
                            <button id="cancel" type="reset">Cancel</button>
                        </div>

                        <div class="context-link">
                            <a href="{{url('shareholders')}}">+ Add a new shareholder</a>
                        </div>
                        
                    </div>

                    <div class="block-right">
                        
                        <div class="form-message">

                            @if (\Session::has('success'))
                                <div class="message success">
                                    {!! \Session::get('success') !!}
                                </div>
                                @endif

                                @if (\Session::has('error'))
                                <div class="message error">
                                    <!-- {!! \Session::get('error') !!}</li> -->
                                </div>
                            @endif
                        </div>

                        @csrf()

                        <div class="form-field">
                            <label for="file">Select a transaction file to import. <br>
                                Click on <strong>Choose file</strong> or 
                                <strong> drag and drop</strong> a transaction file inside the box below.
                                <br><mark>CSV or Excel files only.</mark> <br>
                            </label>
                            <input type="file" name="file" required class="@error('file') is-invalid @enderror" />
                            @error('file')
                                <div class="is-invalid">
                                    {{ $message }}
                                </div>
                            @enderror
                            @if (\Session::has('error'))
                            <div class="is-invalid">
                                {!! \Session::get('error') !!}</li>
                            </div>
                            @endif
                            
                        </div>

                        <div class="form-field" title="Choose a shareholder under whom the file will be imported.">
                            <label for="shareholder">Shareholder name</label>   
                            <select name="shareholder" id="shareholder">
                                <option value="">Shareholder name</option>
                                @if (!empty($shareholders))
                                    @foreach($shareholders as $member)
                                        <option value="{{ $member->id }}" @if( old('shareholder') == $member->id ) SELECTED @endif>
                                            {{ $member->first_name }} {{ $member->last_name }} 
                                            @if (!empty($member->relation))
                                                ({{ $member->relation }})
                                            @endif
                                        </option>
                                    @endforeach
                                @endif
                            </select> 

                            @error('shareholder')
                                <div class="is-invalid">{{ $message }}</div>
                            @enderror

                        </div>

                    </div>

                </form>
            
            </main>
            <footer></footer>
        
        </article>

        <article class="c_transaction_list">
        
            <header>

                @if( $transactions->count() > 0 )                
                <div class="instruction">
                    Following are the data from  MeroShare account from your last import. You can import again to refresh the list. 
                    <br/>Select the transactions and click on "Import to <strong>My Portfolio</strong>"
                </div>
                @endif

                <div class="c_band_right">

                    <div id="message" class="message">
                        @if(count($transactions)>0)
                            {{count($transactions)}} records
                        @else
                            No records
                        @endif
                    </div>
                    
                    <div class="c_band__components">
                    <button id="new">Import Shares</button>
                        <div class="c_shareholder">
                            <!-- <label for="shareholder">Shareholder name</label>    -->
                            <select id="shareholder_filter">
                                <option value="">Shareholder name</option>
                                @if (!empty($shareholders))
                                    @foreach($shareholders as $member)
                                        <option value="{{ $member->id }}"
                                        @if($shareholder_id == $member->id) SELECTED @endif>
                                        {{ $member->first_name }} {{ $member->last_name }} 
                                            @if (!empty($member->relation))
                                                ({{ $member->relation }})
                                            @endif
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <button id="saveToPortfolio">Save to Portfolio</button>
                    </div>

                </div>
            </header>

            <main>
            <table>
                <tr>
                    <th><label for="select_all">
                        <input type="checkbox" name="select_all" id="select_all" onClick="checkAll()">&nbsp;Symbol</label>
                    </th>
                    <!-- <th title="Stock ID">ID</th> -->
                    <th class="c_digit">Cr.</th>
                    <th class="c_digit">Dr.</th>
                    <th>Desc</th>
                    <th>Offer</th>
                    <th>Trans. date</th>
                    <th>Shareholder</th>
                    <th>Remarks</th>
                </tr>
                
                @foreach ($transactions as $trans)
                
                @php 
                    $security_name = $trans->symbol;
                    $stock_id = '';

                    if( !empty($trans->share)){
                        $security_name = $trans->share->security_name;
                        $stock_id = $trans->share->id;
                    }
                   
                @endphp

                    <tr>
                        
                        <td>
                            @if ( !empty($stock_id) )
                                <input type="checkbox" name="t_id" id="{{ $trans->id }}">
                            @endif
                            &nbsp;
                            <label for="{{ $trans->id }}" title="{{ $security_name }}">
                                {{ $trans->symbol }}
                            </label>
                        </td>

                        <!-- <td>{{ $stock_id }}</td> -->
                        <td class="c_digit">{{ $trans->credit_quantity }}</td>
                        <td class="c_digit">{{ $trans->debit_quantity }}</td>
                        <td>{{ $trans->transaction_mode }}</td>
                        <td>{{ $trans->offer_code }}</td>
                        <td>{{ $trans->transaction_date }}</td>
                        <td>
                            @if( !empty($trans->shareholder) )
                                {{ $trans->shareholder->first_name }} {{ $trans->shareholder->last_name }}
                            @endif
                        </td>
                        <td>{{ $trans->remarks }}</td>
                    </tr>
                @endforeach            
            </table>
        </main>
        
        <footer>
            <p class="note">
                <strong>Note : </strong>If you do not see a checkbox to select some of the transactions, chances are that they might be new and have not been updated into our system yet.
            </p>
            <p>
                If you wish to notify us of this incident, you can do it via the 
                <a href="{{url('contact-us')}}">Contact us</a> page.
            </p>
        </footer>
        
    </article>

    
    </section>
    
    

@endsection