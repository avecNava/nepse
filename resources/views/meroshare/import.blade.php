@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('js')
    <script src="{{ URL::to('js/meroshare.js') }}"></script>
@endsection

@section('header_title')
    <h1 class="c_title">Import Stocks</h1>
@endsection

@section('content')

    <div id="loading-message" style="display:none">Working... Please wait...</div>
    <section class="transaction-history">
        
        <div class="import__header">
            <h2 class="c_title">Import (MeroShare)</h2>
        </div>
        
        <section id="meroshare">

            <header class="c_message">

            </header>
            
            <main id="share-import-form" >
                
                <div class="c_instructions">
                    <h3>Instructions</h3>
                    <ul>
                        <li>Login to your <a href="https://meroshare.cdsc.com.np/" target="_blank" rel="noopener noreferrer">Meroshare account</a>.</li>
                        <li>Click on <strong>My Transaction history</strong>. Filter by <strong>Date</strong>.</li>
                        <li>Click on CSV button to download the transaction history.</li>
                        <li>
                            Click on Choose file (below) and browse the CSV file recently downloaded. View
                            <a href="{{ URL::to('templates/sample-meroshare-transaction-history.xlsx')}}" target="_blank">SAMPLE FILE</a> file
                        </li>
                        <li>Choose a Shareholder name.</li>
                        <li>Click on <strong>Import</strong>.</li>
                    </ul>  
                </div>
               
                <div>
                <h2>Import file</h2>

                <form method="POST" action="/import/meroshare/store" enctype="multipart/form-data">


                        <div class="form-field">
                            <div class="c_btn">
                                <button type="submit">Import</button>
                                <button  onClick="closeForm('meroshare-import-form')" type="reset">Cancel</button>
                            </div>
                        </div>

                  
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
                            <label for="shareholder"><strong>Shareholder</strong></label>   <br/>
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
                </div>
            
            </main>
            <footer></footer>
        
        </section>
        <div id="message" class="message error"></div>
        <article class="c_transaction_list">
        
            <header class="info">
                
                <div class="c_band apart">

                    <div id="info">
                        
                        @php
                            $row = $transactions->first();
                            if( !empty($row) ){

                                $shareholder = $row->shareholder;
                                
                                if($shareholder){
                                    echo "<h2 class='title'>$shareholder->first_name $shareholder->last_name</h2>";
                                }
                            }
                        @endphp


                        <div class="notification">
                            @if(count($transactions)>0)
                                ({{count($transactions)}} records)
                            @else
                                No records
                            @endif
                        </div>

                    </div>
                    
                    <div class="c_band__components">
                       
                        <div class="c_shareholder">
                            <!-- <label for="shareholder">Shareholder name</label>    -->
                            <select id="meroshare-shareholder_filter" onChange="meroShareShareholderRefresh()">
                                <option value="">Choose a Shareholder</option>
                                @if(!empty($shareholders))
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
                            <button id="import-meroshare-portfolio" onClick="importMeroShareTransactions()">Save to Portfolio</button>
                            <button id="delete-meroshare"  onClick="deleteMeroShareTransactions()">Delete</button>
                        </div>
                    </div>

                </div>
            </header>

            <main>
                @if( ! empty($transactions) )
                <table>
                    <tr>
                        <th style="text-align:left"><label for="select_all">
                            <input type="checkbox" name="select_all" id="select_all" onClick="checkAll()">&nbsp;Symbol</label>
                        </th>
                        <!-- <th title="Stock ID">ID</th> -->
                        <th class="c_digit">Cr.</th>
                        <th class="c_digit">Dr.</th>
                        <th class="c_digit">Desc</th>
                        <th class="c_digit">Offer</th>
                        <th class="c_digit" title="Transaction date">Transaction date</th>
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
                            <td class="c_digit">{{ $trans->transaction_mode }}</td>
                            <td class="c_digit">{{ $trans->offer_code }}</td>
                            <td class="c_digit">{{ $trans->transaction_date }}</td>
                            <td>
                                @if( !empty($trans->shareholder) )
                                    {{ $trans->shareholder->first_name }} {{ $trans->shareholder->last_name }}
                                @endif
                            </td>
                            <td>{{ $trans->remarks }}</td>
                        </tr>
                    @endforeach            
                </table>
                @endif
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