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
                        &nbsp;
                        <label for="select_all">Symbol</label>
                    </th>
                    <th>Quantity</th>
                    <th>LTP</th>
                    <th>Worth(LTP)</th>
                    <th>Trans. date</th>
                    <th>Cost Price</th>
                    <th>Net Worth</th>
                    <th>Profit</th>
                    <th>Shareholder</th>
                </tr>
                
                @foreach ($portfolios as $record)

                    <tr>
                        
                        <td>
                        @if( !empty($record->share))
                            <input type="checkbox" name="chk_{{ $record->id }}" id="{{ $record->id }}">
                            &nbsp;
                            <label for="{{ $record->id }}"></label>
                            <a href="{{ url('portfolio/details', [ $record->share->symbol ]) }}" title="{{ $record->share->symbol }}" }}>
                                {{ $record->share->symbol }}
                            </a> 
                            
                        @endif
                        </td>

                        <td>{{ $record->quantity }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                        @if( !empty($record->shareholder))
                            {{ $record->shareholder->first_name }} {{ $record->shareholder->last_name }}
                        @endif
                        </td>
                    </tr>

                @endforeach   

            </table>
        </main>
        
        <footer></footer>
        
    </article>
    @endif

    </section>

@endsection