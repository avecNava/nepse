@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('header_title')
Frequently asked questions
@endsection

@section('content')
<style>
    h2 {
        display: inline-block;
        color: var(--form-band);
    }
    summary:focus {
        outline: none;
        background: beige;
    }
</style>

    <section class="faq">
    <header>
        <h2>Frequently asked questions</h2>
    </header>
    <main>
        <details>
            <summary>
                <h3> Share quantities in the summary page and detail pages are different.</h3>
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
                <h3> What is basket (or cart) ?</h3>
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
                <h3> Can not add stocks to the basket (or cart)</h3>
            </summary>
            <div class="desc">
                <p>
                    For the similar reason to #1, stocks need to be updated properly before it can be added to basket.
                </p>
            </div>
        </details>
        <details>
        <summary>
                <h3> What is WACC ?</h3>
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