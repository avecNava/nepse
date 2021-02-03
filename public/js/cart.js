function fnRefreshBasket(){
    const msg = document.querySelector('#sell_message');
    // msg.innerHTML ='Refreshing the basket ⌚ ... ';
    url = `${window.location.origin}/cart`;
    // setTimeout(function(){ 
    //     window.location.replace(url);
    // }, 1000);
}

function __showMessage(message, error = false,  clear_message = false, refresh = false){

    const msg = document.querySelector('#sell_message');
    msg.innerHTML = message;
    if(error){ msg.classList.add('error'); }else{ msg.classList.add('success'); }
    //reset message that are marked clear_message
    if(clear_message){ setTimeout(function () {  msg.innerHTML=''; }, 2000 ); }
    if(refresh){ fnRefreshBasket(); }

}

/**
 * monitor change in quantity, update wacc and sales_amount as required
 */

document.querySelectorAll('[name="quantity"]').forEach(function(el){

        el.addEventListener('change', function(e){

            const id = parseID('qty-', e.target.id);
            
            const wacc = document.querySelector(`#wacc-${id}`).value;
            const ltp = document.querySelector(`#ltp-${id}`).innerText;
            const quantity = e.target.value;
            if(e.target.value < 0) {
                document.querySelector(`#qty-${id}`).value = 1;
                return false;
            }
            
            const sales_amount = parseFloat(quantity) * parseFloat(ltp);
            const investment_amount = parseFloat(quantity) * parseFloat(wacc);
            
            //update sales amount
            document.querySelector(`#sell-${id}`).value = (sales_amount.toFixed(1));
            
            //update investment amount
            document.querySelector(`#cost-${id}`).innerText = (investment_amount.toFixed(1));
            
            calculateOthers(id);
        
    });

});

/**
 * monitor change in wacc, update wacc and sales_amount as required
 */
document.querySelectorAll('[name="wacc"]').forEach(function(el){
    el.addEventListener('change', function(e){

        const wacc = e.target.value;
        const id = parseID('wacc-', e.target.id);
        
        const quantity = document.querySelector(`#qty-${id}`).value;
        const $total_investment_amount = parseFloat(wacc) * parseFloat(quantity);
        document.querySelector(`#cost-${id}`).innerText = $total_investment_amount.toFixed(1);
        
        calculateOthers(id);

    });

});
/**
 * monitor change in wacc, update wacc and sales_amount as required
 */
document.querySelectorAll('[name="sell_price"]').forEach(function(el){
    el.addEventListener('change', function(e){

        const id = parseID('sell-', e.target.id);        
        calculateOthers(id);

    });

});
  
//POST the records to the appropriate routes
function fnPOST(action_url, querystring){

    let request = new XMLHttpRequest();
    request.open('POST', action_url, true);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    request.onload = function() {

        hideLoadingMessage();
        const data = JSON.parse(this.response);
        console.log(this.status);

        if (this.status >= 200 && this.status < 400) {
            __showMessage(data.message, false, false, true);
            return true;
        }
        else{
            __showMessage(data.message, true, false);
            return false;
        }
    }

    request.send(querystring);
    showLoadingMessage();

}

function updateBasket(){
    
    let selected = [];
    document.querySelectorAll('input[name=s_id').forEach(element => {
        if(element.checked){
            const id = parseID('chk-', element.id);
            selected.push(id);
        }
    });

    if(!selected.length){
        __showMessage('🍺 Please select some records', true, true);
        return false;
    }
    
    const url = `${window.location.origin}/cart/update`;
    //for each ids, get the quantity, sales_amount, wacc, shareholder/stock id, 
    //then process ajax record to update record
    selected.forEach(function(id){

        const obj = document.querySelector(`#chk-${id}`).dataset;    
        const shareholder_id = obj.user;
        const stock_id = obj.stock;        
        const quantity = document.querySelector(`#qty-${id}`).value;
        const wacc = document.querySelector(`#wacc-${id}`).value;
        const broker_comm = document.querySelector(`#comm-${id}`).textContent;
        const sebon_comm = document.querySelector(`#sebon-${id}`).textContent;
        const cgt = document.querySelector(`#cgt-${id}`).textContent;
        const sell_price = document.querySelector(`#sell-${id}`).value;
        const cost_price = document.querySelector(`#cost-${id}`).textContent;
        const net_receivable = document.querySelector(`#net_amount-${id}`).textContent;

        let _token = document.getElementsByName('_token')[0].value;
        const querystring = `_token=${_token}
            &record_id=${id}
            &stock_id=${stock_id}
            &shareholder_id=${shareholder_id}
            &quantity=${quantity}
            &wacc=${wacc}
            &broker=${broker_comm}
            &sebon=${sebon_comm}
            &cgt=${cgt}
            &cost_price=${cost_price}
            &sell_price=${sell_price}
            &net_receivable=${net_receivable}`;
        
        fnPOST(url, querystring);
        
    });
}

function fnSell(id){
    
    const url = `${window.location.origin}/sales/mark-sold`;

    if(confirm(`Ths will mark the current record as SOLD. Please confirm.`)) {
        
        const obj = document.querySelector(`#chk-${id}`).dataset;    
        const shareholder_id = obj.user;
        const portfolio_id = obj.portfolioId;
        const stock_id = obj.stock;
        const quantity = document.querySelector(`#qty-${id}`).value;
        const wacc = document.querySelector(`#wacc-${id}`).value;
        const broker_comm = document.querySelector(`#comm-${id}`).textContent.replaceAll(',','');
        const sebon_comm = document.querySelector(`#sebon-${id}`).textContent.replaceAll(',','');
        const cgt = document.querySelector(`#cgt-${id}`).textContent.replaceAll(',','');
        const sell_price = document.querySelector(`#sell-${id}`).value;
        const cost_price = document.querySelector(`#cost-${id}`).textContent.replaceAll(',','');
        const net_receivable = document.querySelector(`#net_amount-${id}`).textContent.replaceAll(',','');

        let _token = document.getElementsByName('_token')[0].value;
        const querystring = `_token=${_token}
            &record_id=${id}
            &portfolio_id=${portfolio_id}
            &stock_id=${stock_id}
            &shareholder_id=${shareholder_id}
            &quantity=${quantity}
            &wacc=${wacc}
            &broker=${broker_comm}
            &sebon=${sebon_comm}
            &cgt=${cgt}
            &cost_price=${cost_price}
            &sell_price=${sell_price}
            &net_receivable=${net_receivable}`;

        if(fnPOST(url, querystring)){
            console.log('refreshing');
            fnRefreshBasket();
        }

    }
}

function deleteBasket(){

    let selected = [];
    document.querySelectorAll('input[name=s_id').forEach(element => {
        if(element.checked){
            const id = parseID('chk-', element.id);
            selected.push(id);
        }
    });

    if(!selected.length){
        __showMessage('🍺 Please select some records', true);
        return false;
    }

    let url = `${window.location.origin}/cart/delete`;

    if(confirm(`Ths will delete ${selected.length} records. Please confirm.`)) {
        
        showLoadingMessage();

        //call ajax 
        let _token = document.getElementsByName('_token')[0].value;
        let request = new XMLHttpRequest();
        request.open('POST', url, true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');

        request.onload = function() {

            if (this.status >= 200 && this.status < 400) {

                const data = JSON.parse(this.response);
            
                if (this.status >= 200 && this.status < 400) {
                    __showMessage(data.message,false,false,true);    
                }
                else{    
                    __showMessage(data.message, true);    
                }
                fnRefreshBasket();
                // hideLoadingMessage();
            }
        }

        request.onerror = function() {
          hideLoadingMessage();
        }

        request.send(`_token=${_token}&ids=${selected.toString()}`);
        showLoadingMessage();
    }
}

//calculaet commission
function calculateOthers(id)
{
    const CGT = 0.05;
    const DP = 25;

    const sales_amount = document.querySelector(`#sell-${id}`).value;
    const investment_amount = document.querySelector(`#cost-${id}`).innerText;
    const quantity = document.querySelector(`#qty-${id}`).value;

    const num_scripts = 1;
    const dp_amount = parseFloat(num_scripts) * parseFloat(DP);
    // console.log('sales:', sales_amount,'investment:', investment_amount, 'quantity:', quantity, 'dp:', dp_amount);

    let gain_class = '';
    const gain = parseFloat(sales_amount) - parseFloat(investment_amount);
    const gain_tax = gain > 0 ? (parseFloat(CGT) * parseFloat(gain)) : 0;
    gain_class = gain > 0 ? 'increase' : 'decrease';
    let gain_per = '-';
    if(parseFloat(investment_amount)>0){
        gain_per = parseFloat(gain)/parseFloat(investment_amount)*100;
    }
    // console.info(id, '( invest:', investment_amount,' sales: ', sales_amount,') = ', gain,'(', gain_per,')');
    
    const sebon_comm = 0;
    const broker_comm = 0;
    let payable_amount = 0;
    let broker_commission = 0;
    let sebon_commission = 0;
    let effective_rate = 0;

    document.querySelector(`#cgt-${id}`).innerText =  gain_tax.toFixed(1);
    // console.log(gain);
    document.querySelector(`#gain-${id}`).innerHTML =  gain.toFixed(1);

    document.querySelector(`#g_per-${id}`).innerHTML =  `&nbsp;(${gain_per.toFixed(1)}%)`;
    document.querySelector(`#g_per-${id}`).classList.remove('increase','decrease');
    document.querySelector(`#g_per-${id}`).classList.add(gain_class);

    document.querySelector(`#g_img-${id}`).classList.remove('increase','decrease');
    document.querySelector(`#g_img-${id}`).classList.add(`${gain_class}_icon`);

    //calculate BROKER COMMISSION & SEBON COMMISSION for purchase via Secondary market
    const url = `${window.location.origin}/commission/${sales_amount}`;

    let request = new XMLHttpRequest();
    request.open('GET', url, true);

    request.onload = function() {

        if (this.status >= 200 && this.status < 400) {
            const data = JSON.parse(this.response);

            const broker_rate = parseFloat(data.broker);
            const sebon_rate = parseFloat(data.sebon);
            broker_commission = (broker_rate/100) * sales_amount;
            sebon_commission = (sebon_rate/100) * sales_amount;
            
            payable_amount = parseFloat(sales_amount) - (parseFloat(broker_commission) + parseFloat(sebon_commission) + parseFloat(gain_tax) + parseInt(dp_amount) );
            document.querySelector(`#net_amount-${id}`).innerText =  (payable_amount.toFixed(1));
            effective_rate = parseFloat(payable_amount)/parseInt(quantity);
            
            document.querySelector(`#rate-${id}`).innerText =  formatNumber(effective_rate.toFixed(1));

            if(broker_commission){
                document.querySelector(`#comm-${id}`).innerText =  formatNumber(broker_commission.toFixed(1));
                document.querySelector(`#comm-${id}`).setAttribute('data-rate', broker_rate);
            }
            
            if(sebon_commission) {
                document.querySelector(`#sebon-${id}`).innerText =  formatNumber(sebon_commission.toFixed(1));
                document.querySelector(`#sebon-${id}`).setAttribute('data-rate', sebon_rate);
            }
            
            setTimeout(
                'calculateSummary()',
                1000 
            );

        } 

    }
    request.send(); 
} 

//trigger change in quantity for all records
document.querySelectorAll('input[name=quantity').forEach(element => {
    // console.log('qty change envent global');
    document.querySelector(`#${element.id}`).dispatchEvent(new Event('change'));
});

//trigger change in wacc for all records
document.querySelectorAll('input[name=wacc').forEach(element => {
    // console.log('wacc change envent global');
    document.querySelector(`#${element.id}`).dispatchEvent(new Event('change'));
});

function calculateSummary(){
    const DP = 25;
    var sum_quantity = 0;
    var sum_amount = 0;
    var sum_investment = 0;
    var sum_gain = 0;
    var sum_gain_tax = 0;
    var sum_broker_comm = 0;
    var sum_sebon_comm = 0;
    var sum_net_receivable= 0;
    var unique_scripts= new Set();
    
    document.querySelectorAll('[name="s_id"]').forEach( function(el){
        const id = el.dataset.id;
        const user_symbol = el.dataset.userSymbol;
        unique_scripts.add(user_symbol);
        sum_quantity += parseFloat(document.querySelector(`#qty-${id}`).value);
        sum_amount += parseFloat(document.querySelector(`#sell-${id}`).value);
        sum_investment += parseFloat(document.querySelector(`#cost-${id}`).textContent);
        sum_gain += parseFloat(document.querySelector(`#gain-${id}`).textContent);
        sum_gain_tax += parseFloat(document.querySelector(`#cgt-${id}`).textContent);
        sum_broker_comm += parseFloat(document.querySelector(`#comm-${id}`).textContent);
        sum_sebon_comm += parseFloat(document.querySelector(`#sebon-${id}`).textContent);
        sum_net_receivable += parseFloat(document.querySelector(`#net_amount-${id}`).textContent);
        // console.log(sum_gain_tax, sum_broker_comm, sum_sebon_comm);
    });

    const dp_amount = unique_scripts.size * DP;
    const total_payable = parseFloat(sum_net_receivable)-dp_amount;
    document.querySelector('#total_quantity').value = formatNumber(sum_quantity.toFixed(2));
    document.querySelector('#total_investment').value = formatNumber(sum_investment.toFixed(2));
    document.querySelector('#total_amount').value = formatNumber(sum_amount.toFixed(2));
    document.querySelector('#total_gain').value = formatNumber(sum_gain.toFixed(2));
    document.querySelector('#total_gain_tax').value = formatNumber(sum_gain_tax.toFixed(2));
    document.querySelector('#total_broker_comm').value = formatNumber(sum_broker_comm.toFixed(2));
    document.querySelector('#total_sebon_comm').value = formatNumber(sum_sebon_comm.toFixed(2));
    document.querySelector('#dp_amount').value = formatNumber(dp_amount);
    document.querySelector('#net_receivable').value = formatNumber(total_payable.toFixed(2));
}
