@extends('default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('js')
    
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
                        <label for="shareholder_id">Shareholder</label>   
                        <select name="shareholder_id" id="shareholder_id">
                            @if (!empty($shareholders))
                                @foreach($shareholders as $member)
                                    <option value="{{ $member->id }}">{{ $member->first_name }} {{ $member->last_name }} ({{$member->relation}})</option>
                                @endforeach
                            @endif
                        </select> 
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

        @if( $transactions->isNotEmpty() )
        <article class="c_transaction_list">
        
            <header>
                <button id="import_all" onClick="importToMyPortfolio()">Import to <strong>My Portfolio</strong></button>
                <div id="import-message" style="display:none">
                    The selected transactions have been successfully imported to your <a href="{{url('portfolio')}}"> Portfolio</a>.
                </div>
            </header>

            <main>
            <table>
                <tr>
                    <th>
                        <input type="checkbox" name="select_all" id="select_all" onClick="checkAll()">
                        <label for="select_all" hidden>Select all</label>
                    </th>
                    <th><label for="select_all">Symbol</label></th>
                    <th>Cr qty</th>
                    <th>Dr qty</th>
                    <th>Dr/Cr</th>
                    <th>Offer</th>
                    <th>Trans. date</th>
                    <th>Shareholder</th>
                    <th>Remarks</th>
                </tr>
                
                @foreach ($transactions as $trans)
                    <tr>
                        <td><input type="checkbox" name="t_id" id="{{ $trans->id }}"></td>
                        <td><label for="{{ $trans->id }}">{{ $trans->symbol }}</label></td>
                        <td>{{ $trans->credit_quantity }}</td>
                        <td>{{ $trans->debit_quantity }}</td>
                        <td>{{ $trans->transaction_mode }}</td>
                        <td>{{ $trans->offer_type }}</td>
                        <td>{{ $trans->transaction_date }}</td>
                        <td>{{ $trans->shareholder_id }}</td>
                        <td>{{ $trans->remarks }}</td>
                    </tr>
                @endforeach            
            </table>
        </main>
        
        <footer></footer>
        
    </article>
    @endif
    
    </section>
    
    <script>
        function showLoadingMessage() {
            let ele_loading = document.getElementById('loading-message');
            ele_loading.classList.add('loading');
        }
        function hideLoadingMessage() {
            let ele_loading = document.getElementById('loading-message');
            ele_loading.classList.remove('loading');
        }
        function showImportMessage($t=5000) {
            let ele_loading = document.getElementById('import-message');
            ele_loading.classList.add('success');
            setTimeout(function(){ 
                ele_loading.classList.remove('success');
             }, $t);
        }
        function hideImportMessage() {
            let ele_loading = document.getElementById('import-message');
            ele_loading.classList.remove('success');
        }
        function checkAll() {
            var select_all = document.getElementById('select_all');
            var flag = select_all.checked;            
            var elements = document.getElementsByName("t_id");
            Array.prototype.forEach.call(elements, function(el, i){
                el.checked=flag;
            });
        }

        function importToMyPortfolio() {
            let selected = [];
            let elements = document.getElementsByName("t_id");
            let ele_import = document.getElementById('import-message');
            
            showLoadingMessage();

            Array.prototype.forEach.call(elements, functionel, i){
                if(el.checked){
                    selected.push(el.id);
                }
            });

            //call ajax 
            let e = document.getElementById('shareholder_id');
            let _token = document.getElementsByName('_token')[0].value;
            let shareholder_id =e.options[e.selectedIndex].value;

            let request = new XMLHttpRequest();
            request.open('POST', '/meroshare/import-transaction', true);
            request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
            request.onload = function(ele_success, ele_loading) {
                if (this.status >= 200 && this.status < 400) {
                    $msg = JSON.parse(this.response);
                    console.log($msg);
                    hideLoadingMessage();
                    showImportMessage(5000*2);
                }
            }
            request.send(`_token=${_token}&trans_id=${selected.toString()}&shareholder_id=${shareholder_id}`);

        }
    </script>

@endsection