@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('js')
    <script src="{{asset('assets/js/meroshare.js')}}"></script>
@endsection

@section('content')

    <div id="loading-message" style="display:none">Importing... Please wait...</div>
    <section class="transaction-history">

        <h1 class="c_title">Import transaction</h1>

        <article>

            <header class="c_message">

                <h2>Import transaction from Meroshare account.</h2>
                <div class="c_instructions">
                    <ul>
                        <li>Login to your<a href="https://meroshare.cdsc.com.np/" target="_blank" rel="noopener noreferrer">Meroshare account</a>.</li>
                        <li>Click on <strong>My Transaction history</strong>. Filter by <strong>Date</strong>.</li>
                        <li>Click on CSV button to download the transaction history.</li>
                        <li>Click on Choose file (below) and browse the CSV file recently downloaded.</li>
                        <li>Choose a Shareholder name.</li>
                        <li>Click on <strong>Import</strong>.</li>
                    </ul>  
                </div>
            
                <div class="c_band c_band_left">
                    @if (\Session::has('success'))
                    <div class="message message_success">
                        {!! \Session::get('success') !!}. <a href="{{ url('meroshare/transaction') }}">Refresh</a> the page to see them.
                    </div>
                    @endif

                    @if (\Session::has('error'))
                    <div class="message message_error">
                        {!! \Session::get('error') !!}</li>
                    </div>
                    @endif

                    <!-- @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif -->
                </div>

            </header>

            <main class="c_trans_form">
                
                <form method="POST" action="/meroshare/transaction" enctype="multipart/form-data">
                    
                    <div class="form-field">
                        <button type="submit">Import</button>
                    </div>
                    
                    @csrf()

                    <div class="form-field">
                        <label for="shareholder">Shareholder name</label>   
                        <select name="shareholder" id="shareholder">
                            <option value="">Shareholder name</option>
                            @if (!empty($shareholders))
                                @foreach($shareholders as $member)
                                    <option value="{{ $member->id }}">{{ $member->first_name }} {{ $member->last_name }} 
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

                    <div class="form-field">
                        <label for="file">Select or drag and drop a transaction file (<mark>CSV or Excel files only</mark>) <br></label>
                        <input type="file" name="file" required class="@error('file') is-invalid @enderror" />
                        @error('file')
                            <div class="is-invalid">{{ $message }}</div>
                        @enderror
                    </div>

                </form>
            
            </main>
            <footer></footer>
        
        </article>

        <article class="c_transaction_list">
        
            <header>
                <div>
                    Following are the data from  MeroShare account from your last import. You can import again to refresh the list. 
                    <br/>Select the transactions and click on "Import to <strong>My Portfolio</strong>"
                </div>
                <div class="c_band">
                    <div id="message" class="message">
                    </div>
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
                    <button id="import_all" onClick="importToMyPortfolio()">Import to <strong>My Portfolio</strong></button>
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