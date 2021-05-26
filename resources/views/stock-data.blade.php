@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('header_title')
    <h1>NEPSE.TODAY</h1>
@endsection

@section('custom_css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
@endsection

@section('custom_js')
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
@endsection

@section('notice')
<?php if(strlen($notice)>0){ ?>
    <div role="notice" class='notice' data-show-notice="yes">
    {!! $notice !!}
    </div>
<?php } ?>
@endsection

@section('content')

<section class="transactions">
    <header">
        <h2>Market data (#NEPSE) </h2>
        <div title="Last transaction time">{{ $last_updated_time }} <mark>({{ $last_updated_time->diffForHumans() }})</mark></div>
    </header>
    @if($transactions)
    <table id="transactions" class="cell-border compact stripe hover">
        <thead>
            <tr>
                <th>Symbol</th>
                <th class="c_digit">Last Price (LTP)</th>
                <th class="c_digit">Change</th>
                <th class="c_digit c_change">% Change</th>
                <th class="optional c_digit">Open price</th>
                <th class="c_digit">High price</th>
                <th class="c_digit">Low Price</th>
                <th class="optional c_digit">Total quantity</th>
                <th class="optional c_digit">Total value</th>
                <th class="optional c_digit">Previous price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $key => $transaction)
            @php
                $ltp = $transaction->last_updated_price;
                $prev_price = $transaction->previous_day_close_price;
                $change = $ltp - $prev_price;
                $change_per = \App\Services\UtilityService::calculatePercentage($change, $prev_price);
                $change_css = \App\Services\UtilityService::gainLossClass1($change);
            @endphp
            <tr>
                <td title="{{$transaction->security_name}}" class="symbol">{{$transaction->symbol}}</td>
                <td class="c_digit">{{ number_format($transaction->last_updated_price) }}</td>
                <td class="{{$change_css}} c_digit">{{number_format($change,2)}}</td>
                <td class="c_digit c_change">
                    <div class="flex apart">
                    <div class="{{$change_css}}">
                        {{$change_per}}
                    </div>
                    <div class="{{$change_css}}_icon">&nbsp;</div>
                    </div>
                </td>
                <td class="c_digit optional">{{ number_format($transaction->open_price) }}</td>
                <td class="c_digit">{{ number_format($transaction->high_price) }}</td>
                <td class="c_digit">{{ number_format($transaction->low_price) }}</td>
                <td class="c_digit optional">{{ number_format($transaction->total_traded_qty) }}</td>
                <td class="c_digit optional">{{ number_format($transaction->total_traded_value) }}</td>
                <td class="c_digit optional">{{ number_format($transaction->previous_day_close_price) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot></tfoot>
    </table>
    @endif
</section>

<script>
    setTimeout(() => {
        $(document).ready( function () {
            $('#transactions').DataTable(
                {"paging":   false,}
                );
        } );
    }, 1000);
</script>

@endsection
