<section class="transaction-history">

    <article class="form">
    
        <header>
            <h2>Import transaction from Meroshare account.</h2>
            <div>Login to your Meroshare account. Download the transaction history and upload the CSV file here.</div>
        
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

        </div>

        <main>
            
            <form method="POST" action="/meroshare/transaction" enctype="multipart/form-data">
                
                @csrf()

                <div class="form-field">
                    <label for="shareholder_id">Shareholder</label>   
                    <select name="shareholder_id" id="category">
                        <option value="100">Nava</option>
                        <option value="101">Juna</option>
                    </select> 
                </div>

                <div class="form-field">
                    <label for="menu">Select a transaction file : (CSV or Excel files only) <br></label>
                    <input type="file" name="file" required class="@error('file') is-invalid @enderror" />
                    @error('file')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-field">
                    <button type="submit">Upload menu</button>
                </div>

            </form>
        
        </main>
        <footer></footer>

    </article>

    <div class="list">

        <table>
            <tr>
                <th>Transaction ID</th>
                <th>Symbol</th>
                <th>Credit quantity</th>
                <th>Debit quantity</th>
                <th>Dr. Cr.</th>
                <th>Offer type</th>
                <th>Transacton date</th>
                <th>Remarks</th>
                <th>Shareholder</th>
                <th><button id="import_all">Import all</button></th>
            </tr>
            
            @foreach ($transactions as $trans)
                <tr>
                    <td>{{ $trans->id }}</td>
                    <td>{{ $trans->symbol }}</td>
                    <td>{{ $trans->credit_quantity }}</td>
                    <td>{{ $trans->debit_quantity }}</td>
                    <td>{{ $trans->transaction_mode }}</td>
                    <td>{{ $trans->offer_type }}</td>
                    <td>{{ $trans->transaction_date }}</td>
                    <td>{{ $trans->remarks }}</td>
                    <td>{{ $trans->shareholder_id }}</td>
                    <td><button>Import</button></td>
                </tr>
            @endforeach            
        </table>

    </div>

</section>