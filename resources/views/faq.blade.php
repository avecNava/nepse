@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('header_title')
<h1 class="c_title">FAQ</h1>
@endsection

@section('js')
   
@endsection

@section('content')
<style>
    details {
        margin: 2px 0;
    }
    summary {
        padding: 10px;
        background:#f7f7f7;
    }
    h2 {
        display: inline-block;
        color: var(--form-band);
    }
    summary:focus {
        outline: none;
        background: beige;
    }

</style>
    <section id="top-nav">
        <div class="label">See how to use the application in guidelines</div>
        <div class="links">
            <div class="link">
                <a href="{{url('guidelines')}}" title="Guidelines">Guidelines</a>
            </div>
        </div>
    </section>

    <section class="faq">
    <header>
        <h2>Frequently asked questions</h2>
    </header>
    <main>
        <details>
            <summary>
                <h2>#1 Share quantities in the summary page and detail pages are different.</h2>
            </summary>
            <div class="desc">
                <p>
                    The summary page aggregates and displays the stocks from the Portfolio. However, if for some stocks the cost price is not entered, they are not aggregated. 
                </p>
                <p>
                    You need to edit such stocks and enter the cost price. Once done, the Summary page should show the correct quantities.
                </p>
            </div>
        </details>
        <details>
        <summary>
                <h2>#2 What is basket (or cart) ?</h2>
            </summary>
            <div class="desc">
                <p>
                    A basket or a cart is a way of collecting shares which you want to sell. 
                </p>
                <p>You collect stocks into a basket (or cart) and mark them as sold.</p>
            </div>
        </details>
        <details>
        <summary>
                <h2>#3 Can not add stocks to the basket (or cart)</h2>
            </summary>
            <div class="desc">
                <p>
                    For the similar reason to #1, stocks need to be updated properly before it can be added to basket.
                </p>
            </div>
        </details>
        <details>
        <summary>
                <h2>#4 What is WACC ?</h2>
            </summary>
            <div class="desc">
                <p>
                    WACC (or weighted average cost of capital) is the average cost of the share. Sometimes, it is also called as Effective rate.
                </p>
                <p>
                    It is a calculation of investments in which investments are proportionately weighted.
                </p>
            </div>
        </details>
    </main>
    <footer></footer>    
    </section>

@endsection