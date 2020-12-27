@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('js')
    <script src="{{ URL::to('js/meroshare.js') }}"></script>
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

<section id="import-my-share">

    <section class="transaction-history">

        <div class="import__header">
            <div>
                <h1 class="c_title">Import Stocks</h1>
                <a class="link_menu"  onClick="openForm('myshare-import-form')" href="#">+ Import data</a>
            </div>
            <div id="import-shares-links">
                <a class="link_menu" href="/import/meroshare" title="Import share from meroshare">
                    Import from MeroShare account</li>
                </a>
            </div>
        </div>
 

        <section id="myshare">

            <header class="c_message">

            </header>
            @php
                $hidden = 'hidden';
                if ($errors->any() || session()->has('error') || session()->has('message')  || session()->has('success')) {
                    $hidden = '';
                } 
            @endphp 
            <main id="myshare-import-form" class="meroshare-import-form" {{$hidden}}>
                
                <h2>Instructions : </h2>
               
                <div class="c_instructions">
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
               

                <div class="c_band">                    
                    <h2>Import file</h2>
                </div>

                <form method="POST" action="/import/share/store" enctype="multipart/form-data">

                    <div class="block-left">

                        <div class="form-field">
                            <button type="submit">Import</button>
                            <button onClick="closeForm('myshare-import-form')" type="reset">Cancel</button>
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
                            <label for="file"><strong>Select a file</strong> <br>
                            Please update the file downloaded above as per the instructions.<br>
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
            
            </main>
            <footer></footer>
        
        </section>

        <article class="c_transaction_list">
        
            <header>

                @php
                    $row = $transactions->first();
                    if( !empty($row) ){

                        $shareholder = $row->shareholder;
                        
                        if($shareholder){
                            echo "<h2>$shareholder->first_name $shareholder->last_name</h2>";
                        }
                    }
                @endphp

                <div class="c_band_right">

                    <div id="message" class="message">
                        @if(count($transactions)>0)
                            {{count($transactions)}} records
                        @else
                            No records
                        @endif
                    </div>
                    
                    <div class="c_band__components">
                    
                        <div class="c_shareholder">
                            <!-- <label for="shareholder">Shareholder name</label>    -->
                            <select id="myshare-shareholder_filter" onChange="myShareShareholderRefresh()">
                                <option value="">Choose a Shareholder</option>
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
                        <div class="buttons">
                            <button id="import-myshare-portfolio" onClick="importMyShareTransactions()">Save to Portfolio</button>
                            <button id="delete-myshare" onClick="deleteMyShareTransactions()">Delete</button>
                        </div>
                    </div>

                </div>
            </header>

            <main>
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