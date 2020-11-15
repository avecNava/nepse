<div class="transaction-history">

<section class="upload">

    <section id="header-box">
        <h2>Import transaction from Meroshare account.</h2>
        <div>Login to your Meroshare account. Download the transaction history and upload the CSV file here.</div>
    </section>

    <section id="message">
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
    </section>

    <section id="form">
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
    </section>

</section>

<section class="list">

    <table>
        <tr>
            <th>Symbol</th>
            <th>Quantity</th>
            <th>Dr. Cr.</th>
            <th>Offering type</th>
            <th>Transacton date</th>
            <th>Remarks</th>
            <th>Shareholder</th>
        </tr>
    </table>

</section>

</div>