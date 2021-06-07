@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('header_title')
    Guidelines
@endsection


@section('content')

<style>
    .scroll-top {scroll-margin-top: .8em;}
    .note {
        margin: 30px;
    }
}
</style>
    <section class="guidelines">
    <h2>Guidelines</h2>
        
    <nav role="navitation">
        <ul class="navbar">
            <li><a href="#add-stocks">Add Stocks</a></li>
            <li><a href="#add-shareholder">Add Shareholder</a></li>
            <li><a href="#dashboard">Dashboard</a></li>
            <li><a href="#portfolio">Portfolio</a></li>
            <li><a href="#update-stocks">Update stocks</a></li>
            <li><a href="#delete-stocks">Delete stocks</a></li>
            <li><a href="#sales">Sales</a></li>
            <li><a href="#stocks">Stocks</a></li>
            <li><a href="#brokers">Brokers</a></li>
            <li><a href="#feedbacks">Feedbacks</a></li>
        </ul>
    </nav>
   
    <div class="col">

        <div>
            
            <article class="scroll-top" id="add-stocks">
                
                <h2>Add Stocks</h2>
                <p>You can add stocks in multiple ways.</p>

                <section id="manual">
                    <h3>01. Add stocks manually </h3>
                    <p>
                        Add new stocks manually one by one by using webform. The form has validations and will calculate effective rate as you enter data.
                    </p>
                    <ul>
                            <li>
                                <a href="{{ url('portfolio/new') }}" title="Add new share"  target="_blank" rel="noopener noreferrer">Add new share</a>
                            </li>
                    </ul>
                </section>

                <section id="excel">
                    <h3>02. Bulk Import (Spreadsheet) </h3>
                    <p>
                        To import share using excel file, click the link below. Please <a href="{{'templates/my-shares-template.xlsx'}}">download a sample excel file</a>, update with your stocks and import.
                    </p>
                    <ul>
                            <li>
                                <a href="{{ url('import/share') }}" title="Import share"  target="_blank" rel="noopener noreferrer">Import shares (excel file)</a>
                            </li>
                    </ul>
                </section>

                <section class="meroshare">
                    <h3>03. Bulk Import (Meroshare CSV dump)</h3>
                    <p>We understand manually entering huge transactions can be error prone, time taking and confusing. That is the reason why we added a feature for bulk importing stocks. You can download your portfolio from your <a href="https://meroshare.cdsc.com.np" target="_blank" rel="noopener noreferrer">MeroShare account</a> as csv/excel file and upload at one shot.</p>
                    <p>
                        Stocks downloaded should have a fixed structure/format. You can <a href="{{ url('templates/sample-meroshare-transaction-history.xlsx') }}">download a sample file</a> and see how it looks.
                    </p>
                    <ul>
                        <li>
                            <a href="{{ url('import/meroshare') }}" title="Import share"  target="_blank" rel="noopener noreferrer">Import shares (Meroshare)</a>
                        </li>
                    </ul>
                    
                    <div class="note">
                        <strong>Note :</strong>
                        <ul>
                            <li>
                                Use this method of bulk importing shares for the first time only. You can edit and delete shares manually afterwards. 
                            </li>
                            <li>
                                The system will update IPO and Rights shares with unit cost of 100 and Bonus shares with unit cost of zero (0) during bulk import. 
                            </li>
                            <li>All other offering types like FPO, Bonds, Mutual Funds, Secondary market purchases etc will have unit cost as Zero. <mark>You are required to verify/update unit price/effective price for shares imported via Meroshare manually.</mark>
                            </li>
                            <li>
                                If the unit cost price is not entered, the net worth and gain may not be calculated correctly.
                            </li>
                            <li>The summary page and shareholders dashboard page only shows stocks with cost price updated. Hence,<mark>the quanities of shares may appear to be less than what actaully is from your Meroshare account. Such shares will re-appear once cost price is updated.</mark></li>
                        </ul>
                    </div>
                    
                </section>
            </article>

            <article class="scroll-top" id="add-shareholder">
                
                <h2>Add Shareholder</h2>
                <p>
                    You can add your family members, friends circle etc as Shareholder and add stocks under their names. Stocks for different shareholders will be visible in the <a href="{{ url('portfolio') }}" title="dashboard"  target="_blank" rel="noopener noreferrer">dashboard</a>
                </p>
                <ul>
                    <li>
                        <a href="{{ url('shareholders') }}" title="shareholders"  target="_blank" rel="noopener noreferrer">Add shareholder</a>
                    </li>
                </ul>
                                
            </article>
        
        </div>

        <div>

            <article class="scroll-top" id="dashboard">
                
                <h2>Dashboard</h2>
                <p>
                    The dashboard will show the summary of the stocks for each family member in one shot. This may keep changing as we add new features.
                </p>
                <ul>
                        <li>
                        <a href="{{ url('dashboard') }}" title="dashboard"  target="_blank" rel="noopener noreferrer">Dashboard</a>
                        </li>
                </ul>
                
            </article>

            <article class="scroll-top" id="portfolio">
                
                <h2>Portfolio</h2>
                <p>
                    When you click on any Shareholder name in the dashboard it will take you to the portfolio page for that person.
                </p>
                <p>
                    Click on the individual symbols in the list to view details for each share. The details view will show shares by purchase date, offering type and quantities.
                </p>
                <ul>
                        <li>
                        <a href="{{ url('dashboard') }}" title="dashboard"  target="_blank" rel="noopener noreferrer">Dashboard</a>
                        </li>
                </ul>
                
            </article>

            <article class="scroll-top" id="update-stocks">
                
                <h2>Update stocks</h2>
                <p>
                    Goto <a href="#portfolio">shareholder portfolio</a>. Click on the symbol you would like to edit and click on <strong>Edit</strong> button.
                </p>
            </article>
            
            <article class="scroll-top" id="delete-stocks">
                
                <h2>Delete stocks</h2>
                <p>
                    Similar like <a href="#update-stocks">update stocks</a> but this time click on <strong>Delete</strong> button.
                </p>
            </article>

            <article class="scroll-top" id="sales">
                
                <h2>Sales</h2>
                <p>
                    You can keep a record of Sales of the stocks you sold. <mark>Please note that this feature is evolving and not yet complete</mark>
                </p>
                <ul>
                        <li>
                            <a href="{{url('portfolio/sales')}}" title="Add sales" target="_blank" rel="noopener noreferrer">Add sales record</a> <mark>Under development</mark>
                        </li>
                        <li>
                            <a href="{{url('sales')}}" title="Sales" target="_blank" rel="noopener noreferrer">View Sales</a>
                        </li>
                </ul>
            </article>

            <article class="scroll-top" id="stocks">
                
                <h2>Stocks</h2>
                <p>
                    If you do not see any stock name in the list of stocks, please let us know.
                </p>
                <ul>
                        <li>
                            <a href="{{url('feedbacks')}}" target="_blank" rel="noopener noreferrer">Contact us</a>
                        </li>
                </ul>
            </article>

            <article class="scroll-top" id="brokers">
                
                <h2>Brokers</h2>
                <p>
                    If you do not see any broker name in the list of brokers, please let us know.
                </p>
                <ul>
                        <li>
                            <a href="{{url('feedbacks')}}" target="_blank" rel="noopener noreferrer">Contact us</a>
                        </li>
                </ul>
            </article>

        </div>

        <div style="grid-column:1/3;background:#f7f7f7;margin-top:20px;padding:20px;" class="box-shadow">

            <article class="scroll-top" id="feedbacks">
                
                <h2>Ideas, bugs, suggestions</h2>
                <p>
                    No application is ever complete or is ever free from errors (or bugs 🐛). You may have a creative head than we do and have interesting ideas we could apply to help manage stocks more easily and beautifully.
                </p>
                <p>
                    We are open for feedbacks, suggestions or complaints. If you care and have time to do so, you can do so using the following link.
                    <ul>
                        <li>
                            <a href="{{url('feedbacks')}}" target="_blank" rel="noopener noreferrer">Contact us</a>
                        </li>
                </ul>
            </article>
            
        </div>

    </div>
    </section>    

    <script>

        function fnHighlightText(hash) {
            
            fnClearHighlights();

            if(hash){
                document.querySelector(hash + '>h2')
                    .setAttribute('class','highlighted');
            }

        }
        
        function fnClearHighlights() {
            
            var ele = document.querySelectorAll('.guidelines article h2')

            ele.forEach(function(i){
                var j = `${i.tagName}.highlighted`;
                if( document.querySelector(j) ){
                    document.querySelector(j).removeAttribute('class');
                }
            });

        }

        // document.querySelectorAll('.guidelines ul.navbar a').forEach(element => {
        //     console.log(element.length);
        //     element.forEach(temp=>{
        //         console.log(temp);
        //     });
        // });

        document.querySelectorAll('.guidelines ul.navbar a').forEach(element => {

            const article = element.getAttribute('href');
            var temp = `article${article}>h2`;

            element.addEventListener('click', function(e){                
                var id = `article${this.getAttribute('href')}`;
                fnHighlightText(id);
            });
        });

        
    </script>

@endsection