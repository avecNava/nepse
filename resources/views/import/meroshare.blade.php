@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('js')
    <script src="{{ URL::to('js/meroshare.js') }}"></script>
@endsection

@section('header_title')
    <h1 class="c_title">Import Stocks (MeroShare)</h1>
@endsection

@section('content')
<section class="share_import__wrapper">

    <section id="top-nav"  class="optional">
        <div class="message">
            @if (\Session::has('success'))
                <div class="success">
                    {!! \Session::get('success') !!}
                </div>
                @endif

                @if (\Session::has('error'))
                <div class="error">
                    {!! \Session::get('error') !!}</li>
                </div>
            @endif
        </div>

        <div class="links">
            <div class="link selected">
                <a href="{{url('import/meroshare')}}" title="Import Share from Meroshare account">Import from MeroShare</a>
            </div>
            <div class="link">
                <a href="{{url('import/share')}}" title="Import Share from Excel file">Import using Spreadsheet</a>
            </div>
        </div>
    </section>

    <!-- Trigger/Open The Modal -->
    <!-- <button id="myBtn">Open Modal</button> -->

    <!-- The Modal -->
    <div id="myModal" class="modal">

    <!-- Modal content -->
    <div class="modal-content">
        <span id="close" class="close">&times;</span>
        <p id="modal-message"></p>
        <div class="notice">
            <h2><mark>Please pay attention to the following points:</mark></h2>
            <p>
                <ul>
                    <li>Cost price for IPOs have been recorded as Rs 100 and Bonus shares updated to 0.</li>
                    <li>
                        Stocks purchased via FPO and/or Secondary market (or brokers) does not have purchase price updated. You will need to update them manually. 
                    </li>
                    <li>
                        <mark>Stocks without cost price (as mentioned above) will not be displayed in the summary page and shareholder's dashboard.  As a result you may see shares with less quantity than what it appears in your Meroshare account.</mark>
                    </li>
                    <li>Such shares will re-appear once the cost prices are updated updated.</li>
                    <li>If you have any SalesBasket record, they will be imported to the Sales section. However, those records will also appear under your portfolio. You'll need to identify such records and delete from your portfolio manually. Or you can identify such records in the excel file and delete them beofre importing.</li>
                    <li>The imported records will be preserved until they are manually deleted.</li>
                    <li>During future imports (if any), old records will be preserved. Only new stocks will be imported.</li>
                    <li>Instructions on updating stocks are available at
                    <a href="{{url('guidelines#update-stocks')}}" target="_blank" rel="noopener noreferrer">Update stocks</a> section in the 
                    <a href="{{url('guidelines')}}" target="_blank" rel="noopener noreferrer">Guidelines</a></li>
                </ul>
            </p>
        </div>
    </div>

    </div>

    <details class="form_details">
        <summary><h3>To begin importing, click here</h3></summary>
        <section id="share-import-form">
            <main>

                <div class="import__instructions">
                    <h3>Instructions</h3>
                    <ul>
                        <li>Login to your <a href="https://meroshare.cdsc.com.np/" target="_blank" rel="noopener noreferrer">Meroshare account</a>.</li>
                        <li>Click on <strong>My Transaction history</strong>. Filter by <strong>Date</strong>.</li>
                        <li>Click on CSV button to download the transaction history.</li>
                        <li>
                            Click on Choose file (below) and browse the CSV file recently downloaded. View
                            <a href="{{ URL::to('templates/sample-meroshare-transaction-history.xlsx')}}" target="_blank">SAMPLE FILE</a>
                        </li>
                        <li>Choose a Shareholder name.</li>
                        <li>Click on <strong>Import</strong>.</li>
                    </ul>  
                </div>
            
                <div class="form">
                    <h3>Import Form</h3>
                    <form method="POST" action="/import/meroshare/store" enctype="multipart/form-data">

                        @csrf()

                        <div class="form-field">
                            <label for="file">
                                <mark>only CSV and excel files</mark>
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
                            <label for="shareholder"><strong>Shareholder</strong></label> 
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

                        <div class="form-field">
                            <div class="c_btn">
                                <button type="submit" class="focus">Import</button>
                                <button  onClick="closeForm()" type="reset">Cancel</button>
                            </div>
                        </div>

                    </form>
                </div>
            </main>
            <footer></footer>
        </section>
    </details>

    <section class="nav">
        <h2>Shareholders</h2>
        <!-- shareholder filter -->   
        @if(count($shareholders)>0)
        <article id="shareholders"  class="center-box">
            <header>
                <ul class="shareholder-nav">
                    @foreach($shareholders as $record)
                    <li>
                        <a href="{{ url('import/meroshare', [ $record->uuid ]) }}" 
                            title="{{ $record->relation }}">
                            {{ $record->first_name }} {{ $record->last_name }}
                        </a>
                    </li>                    
                    @endforeach
                </ul>
            </header>
        </article>
        @endif
    </section>

    <article class="import-list">
    
        <div id="message" class="message"></div>

        <header class="info">
            
            <div class="flex js-apart al-end">

                <div class="flex js-start al-cntr">
                    
                    @php
                        $row = $transactions->first();
                        if( !empty($row) ){
                            $shareholder = $row->shareholder;
                            if($shareholder){
                                echo "<h2 class='title'>$shareholder->first_name $shareholder->last_name</h2>";
                            }
                        }
                    @endphp
                        
                    @if( count($transactions)>0 )
                    <div class="notification optional">
                        ({{count($transactions)}} records)
                    </div>
                    @endif

                </div>

                <div class="buttons">
                    <button id="import-meroshare-portfolio" onClick="importMeroShareTransactions()">Send to Portfolio</button>
                    <button id="delete-meroshare"  onClick="deleteMeroShareTransactions()">Delete</button>
                </div>
               
            </div>
        </header>

        <main class="transactions">
          
            <table>
                <tr>
                    <th>

                        <div class="flex al-cntr">
                            <input type="checkbox" name="select_all" id="select_all" onClick="checkAll()">
                            <label for="select_all">&nbsp;Symbol</label>                            
                        </div>

                    </th>
                    <!-- <th title="Stock ID">ID</th> -->
                    <th class="c_digit">Cr.</th>
                    <th class="c_digit">Dr.</th>
                    <th class="c_digit">Desc</th>
                    <th>Offer</th>
                    <th class="c_digit optional" title="Transaction date">Transaction date</th>
                    <th> <div class="td-clip">Shareholder</div></th>
                    <th class="optional td-clip">Remarks</th>
                </tr>
                @if(count($transactions)<=0)
                    <tr>
                        <td colspan="8">

                            <div class="info center-box">
                                <h2 class="message error">No records<h2>
                                <h3 class="message success">💡 Please click on the shareholder above to view records</h3>
                            </div>

                        </td>
                    </tr>
                @endif
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
                    <td class="flex al-cntr">
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
                    <td class="c_digit">{{ $trans->transaction_mode }}</td>
                    <td style="margin-left:10px" class="td-clip-75" title="{{ $trans->offer_code }}">{{ $trans->offer_code }}</td>
                    <td class="c_digit optional">{{ $trans->transaction_date }}</td>
                    <td>
                    <div style="margin-left:10px" class="td-clip" title="{{ $trans->shareholder->first_name }} {{ $trans->shareholder->last_name }}">
                        @if( !empty($trans->shareholder) )
                            {{ $trans->shareholder->first_name }} {{ $trans->shareholder->last_name }}
                        @endif
                    </div>
                    </td>
                    <td class="optional"><div title="{{ $trans->remarks }}" style="margin-left:10px" class="td-clip">{{ $trans->remarks }}</div></td>
                </tr>
                @endforeach            
            </table>

        </main>

        @if( count($transactions)>0 )
        <footer class="flex">
            <p class="strong">Note : </p>
            <div>
                <p class="note">
                    If you do not see a checkbox to select some of the transactions, chances are that they might be new and have not been updated into our system yet.
                    <strong>If you wish to notify us of this incident, you can do it via the <a href="{{url('feedbacks')}}">Contact us</a> page.</strong>
                </p>
            </div>
        </footer>
        @endif

    </article>
    <script>

        // Get the modal
        var modal = document.getElementById("myModal");

        var el_close = document.getElementById("close");
        el_close.onclick = function() {
            modal.style.display = "none";
        }

    </script>
</section>
@endsection