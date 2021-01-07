@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('js')
    <script src="{{ URL::to('js/meroshare.js') }}"></script>
@endsection

@section('header_title')
    <h1 class="c_title">Import Stocks</h1>
    <h2>Spreadsheet</h2>
@endsection

@section('content')

<style>
    section#import-shares ul>li {
        padding: 5px 0;
        font-size: 18px;
    }
    section#import-shares ul {
        padding: 0 20px;
}
</style>

<div id="loading-message" style="display:none">Working... Please wait...</div>
<section class="share_import__wrapper">
    
    <section id="top-nav">
        <div class="label">Import shares from MeroShare instead?</div>
        <div class="links">
            <div class="link">
                <a href="{{url('import/share')}}" title="Import share from MeroShare">Import Share (MeroShare)</a>
            </div>
        </div>
    </section>

    <details>
    <summary><h2>Click here to import new data</h2></summary>
    <section id="share-import-form">
        <main>
            <div class="c_instructions">
                <h3>Instructions : </h3>
                <ul>
                    <li>
                        Download the
                        <a href="{{ URL::to('templates/my-shares-template.xlsx')}}" target="_blank">SAMPLE EXCEL FILE</a>.
                    </li>
                    <li>Open and update the file with your stocks. <mark>Stock symbol, quantity and offering type are mandatory.</mark></li>
                    <li>Use valid and standard names for stock symbol. Use only symbol not the full name.</li>
                    <li>For offering types, ONLY USE THE CODES DEFINED IN THE DOWNLOADED FILE.</li>
                    <li>Upload the updated file using the form below.</li>
                    <li>Choose a shareholder. Use separate file for different shareholder.</li>
                    <li>Click on Import.</li>
                    <li>Once imported, you can save the stocks to your portfolio.</li>
                </ul>  
            </div>
                
            <div class="form">
                <h2>Import file</h2>
                <form method="POST" action="/import/share/store" enctype="multipart/form-data">
                        <div class="form-field">
                            <div class="c_btn">
                                <button type="submit">Import</button>
                                <button onClick="closeForm('myshare-import-form')" type="reset">Cancel</button>
                            </div>
                        </div>
                        
                        @csrf()

                        <div class="form-field">
                            
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
                            <label for="shareholder"><strong>Shareholder</strong></label><br/>
                            <select name="shareholder" id="shareholder" onChange="">
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

            </div>
        </main>
    </section>
    </details>

    <div id="message" class="message error">
        @if (\Session::has('success'))
            <div class="message success">
                {!! \Session::get('success') !!}
            </div>
            @endif

            @if (\Session::has('error'))
            <div class="message error">
                {!! \Session::get('error') !!}</li>
            </div>
        @endif
    </div>

    <section class="nav">
    <!-- shareholder filter -->   
    @if(count($shareholders)>0)
    <article id="shareholders"  class="center-box">
        <header>
            <ul class="shareholders">
                @foreach($shareholders as $record)
                <li>
                    <a href="{{ url('import/share', [ $record->uuid ]) }}" 
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

    <article>
        
       <header class="info">
            
            <div class="flex js-apart al-cntr">

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
                    <div class="notification">
                        ({{count($transactions)}} records)
                    </div>
                    @endif

                </div>
                
                <div class="buttons">
                    <button id="import-myshare-portfolio" onClick="importMyShareTransactions()">Save to Portfolio</button>
                    <button id="delete-myshare" onClick="deleteMyShareTransactions()">Delete</button>
                </div>
                
            </div>

            </div>
    </header>

    <main class="transactions">
        <table>
            <tr>
                <th style="text-align:left"><label for="select_all">
                    <input type="checkbox" name="select_all" id="select_all" onClick="checkAll()">&nbsp;Symbol</label>
                </th>
                <!-- <th title="Stock ID">ID</th> -->
                <th class="c_digit">Quantity</th>
                <th class="c_digit">Unit cost</th>
                <th class="c_digit">Effective rate</th>
                <th class="c_digit">Total Cost</th>
                <th class="c_digit">Offering type</th>
                <th class="c_digit">Purchase date</th>
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
                <td class="c_digit">{{ $trans->quantity }}</td>
                <td class="c_digit">{{ $trans->unit_cost }}</td>
                <td class="c_digit">{{ number_format($trans->effective_rate, 2) }}</td>
                <td class="c_digit">{{ number_format($trans->effective_rate * $trans->quantity, 1) }}</td>
                <td class="c_digit">{{ $trans->offer_code }}</td>
                <td class="c_digit">{{ $trans->purchase_date }}</td>
                <td>
                    @if( !empty($trans->shareholder) )
                        {{ $trans->shareholder->first_name }} {{ $trans->shareholder->last_name }}
                    @endif
                </td>
                <td>{{ $trans->description }}</td>
            </tr>
            @endforeach            
        </table>
    </main>    

    </article>

</section>
        
@endsection