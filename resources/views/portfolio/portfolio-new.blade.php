@extends('layouts.default')

@section('title')
    Your stock portfolio management application over the browser
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
@endsection

@section('content')

    <section class="c_shareholders">

        <header>
            <h1 class="c_title">Add new Portfolio</h1>            
        </header>

        <main class="form_container">  

            <form method="POST" action="/portfolio/create">

                <div class="c_band @if(session()->has('message')) c_band_success @endif">                    

                    @if(session()->has('message'))
                    <div class="message">
                        {{ session()->get('message') }}
                    </div>
                    @endif                   

                </div>          
                                                                                                                                                        
                <div class="c_portfolio_new">
                    
                    @csrf()
                    <input type="hidden" value="{{old('id')}}" name="id">                     
                    
                    <div class="two-col-form">

                        <div class="block-left">

                        <div class="fields form-field">
                                <label for="shareholder" class="@error('shareholder') is-invalid @enderror" >Shareholder</label>
                                <select name="shareholder">
                                <option value="0">Select</option>
                                    @foreach($shareholders as $shareholder)
                                        <option value="{{ $shareholder->id }}"
                                            
                                        @if(old('shareholder') == $shareholder->id )
                                            SELECTED
                                        @endif
                                        >
                                            {{$shareholder->first_name}} {{$shareholder->last_name}}
                                            @if(!empty($shareholder->relation))
                                            ({{ $shareholder->relation }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="fields form-field">
                                <label for="stock" class="@error('stock') is-invalid @enderror" >Symbol</label>
                                <select name="stock">
                                    <option value="0">Select</option>
                                    @foreach($stocks as $stock)
                                        <option value="{{ $stock->id }}"

                                        @if(old('stock') == $stock->id )
                                            SELECTED
                                        @endif
                                        >
                                            {{$stock->stock_id}} {{$stock->symbol}} - {{$stock->security_name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="fields form-field">
                                <label for="offer"  class="@error('offer') is-invalid @enderror">Offering type</label>
                                <select name="offer" id="offer">
                                    <option data-tag="none" value="0">Select</option>
                                    @foreach($offers as $offer)
                                        <option data-tag="{{ $offer->offer_code }}" value="{{ $offer->id }}"

                                            @if(old('offer') == $offer->id )
                                                SELECTED
                                            @endif

                                        >{{ $offer->offer_code }} ({{$offer->offer_name}})</option>
                                    @endforeach
                                </select> 
                            </div>
                            
                            <div class="fields form-field">
                                <label for="unit_cost" class="@error('unit_cost') is-invalid @enderror">Unit cost</label>
                                <input type="number" name="unit_cost" id="unit_cost" required value="{{old('unit_cost')}}" />
                                <span id="unit_cost_label"></span>
                            </div> 

                            <div class="fields form-field">
                                <label for="quantity" class="@error('quantity') is-invalid @enderror" >Quantity</label>
                                <input type="number" name="quantity" id='quantity' required value="{{ old('quantity') }}" />
                            </div>

                            <div class="fields form-field" title="Bill amount">
                                <label for="total_amount" title="bill amount" class="@error('total_amount') is-invalid @enderror">Total amount</label>
                                <input type="number" name="total_amount" id="total_amount" required value="{{old('total_amount')}}"/>
                                <span id="total_amount_label"></span>
                            </div>

                            <div class="fields form-field">
                                <label for="effective_rate" class="@error('effective_rate') is-invalid @enderror">Effective rate <em>(per share)</em></label>
                                <input type="number" name="effective_rate" id="effective_rate" required value="{{old('effective_rate')}}" />
                                <span id="effective_rate_label"></span>
                            </div> 

                            <div class="fields form-field">
                                <label for="broker" class="@error('broker') is-invalid @enderror">Broker</label>
                                <select name="broker">
                                    <option value="0">Select</option>
                                    @foreach($brokers as $broker)
                                        <option value="{{ $broker['broker_no'] }}" 
                                        @if(old('broker') == $broker['broker_no'] )
                                            SELECTED
                                        @endif
                                        >{{$broker['broker_name']}}</option>
                                    @endforeach
                                </select>
                                <span id="broker_label"></span>
                            </div>
                            
                            <div class="fields form-field">
                                <label for="purchase_date" class="@error('purchase_date') is-invalid @enderror">Purchase date</label>
                                <input type="date" value="{{old('purchase_date')}}" name="purchase_date"/>
                            </div> 
                            
                            <div class="fields form-field">
                                <label for="receipt_number" class="@error('receipt_number') is-invalid @enderror">Receipt number</label>
                                <input type="text" value="{{old('receipt_number')}}" name="receipt_number"/>
                            </div> 
                            
                            
                            <div class="fields form-field remarks">
                                <label for="remarks" class="@error('remarks') is-invalid @enderror">Remarks</label>
                                <textarea name="remarks" rows="5" cols="30"> {{old('remarks')}} </textarea>
                            </div> 
                    
                        </div>

                        <div class="block-right">

                            <div class="buttons">
                                <button type="submit">Save</button>
                                <button type="reset">Cancel</button>
                            </div>

                            <div class="validation-error">
                                @if ($errors->any())
                                    <div class="error">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>

                        </div>

                    </div>
                    
                </div>  

            </form> 

        </main>
        
        <footer></footer>

    </section>
    
    <script>

        document.getElementById('offer').addEventListener('change',function(){

            let el_unit_cost = document.getElementById('unit_cost_label');
            el_unit_cost.innerHTML = '';
            let el_broker = document.getElementById('broker_label');
            el_broker.innerHTML = '';

            let el_offer = document.querySelector('#offer');
            const i = el_offer.selectedIndex;            
            let tag = el_offer.options[i].dataset.tag;
            let unit_cost = '';

            if(tag==='none') return;
            
            if(tag === 'IPO'){
                unit_cost = 100;
            }
            else if (tag === 'BONUS'){
                unit_cost = 0;
            }
            else if (tag === 'SECONDARY'){
                el_broker.innerHTML =`<mark>Choose a broker</mark>`;
                el_unit_cost.innerHTML =`<mark>Enter unit cost for ${tag} share</mark>`;
            }
            else{
                el_unit_cost.innerHTML =`<mark>Enter unit cost for ${tag}</mark>`;
                document.getElementById('unit_cost').value='';
                document.getElementById('unit_cost').focus();
                return;
            }
            document.getElementById('unit_cost').value = unit_cost;
            updateTotalPrice();
        });

        document.getElementById('quantity').addEventListener('change',function(){
            updateTotalPrice();
        });
        document.getElementById('unit_cost').addEventListener('change',function(){
            updateTotalPrice();
        });

        function updateTotalPrice() {

            let quantity = document.getElementById('quantity').value;
            if (!quantity) return;

            let unit_cost = document.getElementById('unit_cost').value;
            if (!unit_cost) return;
            
            let total = (quantity * unit_cost).toFixed(2);
            
            const url = `${window.location.origin}/portfolio/commission/${total}`;

            // const sendGetRequest = async () => {
            //     try {
            //         const resp = await axios.get(url);
            //         console.log(resp.data);
            //     } catch (error) {
            //         console.log(error);
            //     }
            // };

            total_amount = total;
            eff_rate = (total_amount/quantity).toFixed(2);

            //calculate BROKER COMMISSION & SEBON COMMISSION for purchase via Secondary market
            let el_offer = document.querySelector('#offer');
            const i = el_offer.selectedIndex;            
            let tag = el_offer.options[i].dataset.tag;

            if(tag==='SECONDARY'){

                let request = new XMLHttpRequest();
                request.open('GET', url, true);
            
                request.onload = function() {
                    if (this.status >= 200 && this.status < 400) {
                        const data = JSON.parse(this.response);
                        let broker = parseFloat(data.broker);
                        let sebon = parseFloat(data.sebon);
                        let broker_commission = ((broker/100) * total).toFixed(2);
                        let sebon_commission = ((sebon/100) * total).toFixed(2);
                        const total_amount = parseFloat(total) 
                                            + parseFloat(broker_commission) 
                                            + parseFloat(sebon_commission);
                        const eff_rate = (total_amount / quantity).toFixed(2);
                        let x = `total: ${total} + broker: ${broker_commission} + SEBON : ${sebon_commission}`;
                        document.getElementById('total_amount_label').innerHTML = x;
                    }            
                }
                request.send(); 
            }
            
            document.getElementById('total_amount').value =  total_amount;
            document.getElementById('effective_rate').value =  eff_rate;

        }
    </script>

@endsection
