<section id="menu-upload">

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
                <label for="category">Category</label>   
                <select name="category" id="category">
                    <option value="Food">Food</option>
                    <option value="Drink">Drink</option>
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