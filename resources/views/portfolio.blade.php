@extends('default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('js')
    
@endsection

@section('content')

    <div id="loading-message" style="display:none">Loading... Please wait...</div>
    <section class="transaction-history">

        <h1 class="c_title">My portfolio</h1>

        @if( $portfolios->isNotEmpty() )
        <article class="c_portfolio_list">
        
            <header>
                <button id="delete" onClick="deletePortfolios()" hidden>Delete</button>
                <div id="delete-message" style="display:none">
                    The selected scripts have been deleted successfully.
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
                    <th>Quantity</th>
                    <th>LTP</th>
                    <th>Worth(LTP)</th>
                    <th>Trans. date</th>
                    <th>Cost Price</th>
                    <th>Net Worth</th>
                    <th>Profit</th>
                    <th>Shareholder</th>
                </tr>
                
                @foreach ($portfolios as $trans)
                    <tr>
                        <td><input type="checkbox" name="t_id" id="{{ $trans->id }}"></td>
                        <td>
                            <label for="{{ $trans->id }}">
                                <a href="{{ url('portfolio/details', [ $trans->symbol ]) }}"
                                title="{{ $trans->symbol }}" }}>
                                    {{ $trans->symbol }}
                                </a> 
                            </label>
                        </td>
                        <td>{{ $trans->quantity }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ $trans->shareholder_id }}</td>
                    </tr>
                @endforeach            
            </table>
        </main>
        
        <footer></footer>
        
    </article>
    @endif

    </section>

@endsection