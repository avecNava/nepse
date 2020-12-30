function __showMessage(message, flag){
    const msg = document.querySelector('#sell_message')
    if(flag){
        msg.classList.add('success');
    }else{
        msg.classList.add('error');        
    }
    msg.innerHTML = message;
}
function resetSellError(){
    const msg = document.querySelector('#sell_message')
    msg.classList.remove('error');
    msg.classList.remove('success');
    msg.innerHTML = '';
}
function parseString(input_str, delim=","){
    return input_str.replaceAll(delim, "");
}

function addToBasket(){

    const ele = document.querySelector('#sell_quantity');
    const shareholder_id = ele.dataset.shareholderId;
    const stock_id = ele.dataset.stockId;
    const sell_quantity = ele.value;

    if(!sell_quantity){
        __showMessage('Enter sell quantity', false);
        return false;
    }

    const quantity_str = document.getElementById('total_quantity');
    const total_quantity = parseString(quantity_str.innerText);
    
    const diff = parseInt(total_quantity) - parseInt(sell_quantity);

    if( parseInt(sell_quantity) >   (total_quantity)){
        __showMessage('Sell quantity can not exceed the total quantity', false);
        return false;
    }

    const wacc_str = document.getElementById('wacc');
    const wacc = parseString(wacc_str.innerText);
    if(!wacc){
        __showMessage('Weighted average not found', false);
        return false;
    }

    resetSellError();
    
    saveToBasket(sell_quantity, shareholder_id, stock_id);
}

function saveToBasket(sell_quantity, shareholder_id, stock_id){
    
    showLoadingMessage();
    const url = `${window.location.origin}/basket/store`;
    let _token = document.getElementsByName('_token')[0].value;
    let request = new XMLHttpRequest();
    request.open('POST', url, true);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    request.onload = function() {
        const data = JSON.parse(this.response);
        if (this.status >= 200 && this.status < 400) {
            __showMessage(data.message, true);
        }
        else{
            __showMessage(data.message, false);            
        }
        hideLoadingMessage();
    }
    request.send(`_token=${_token}&stock_id=${stock_id}&shareholder_id=${shareholder_id}&quantity=${sell_quantity}`);
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
        document.querySelector(`#amt-${id}`).value = parseFloat(sales_amount).toFixed(0);
        //update investment amount
        document.querySelector(`#invest-${id}`).innerText = parseFloat(investment_amount).toFixed(0);
        
        // document.querySelector(`#wacc-${id}`).dispatchEvent(new Event("change"));
        
        calculateSalesGrandTotal();
        calculateInvestmentGrandTotal();
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
        document.querySelector(`#invest-${id}`).innerText = parseFloat($total_investment_amount).toFixed(0);

        calculateInvestmentGrandTotal();
        calculateOthers(id);

    });
});
  
    
function updateBasket(){
    
    let selected = [];
    document.querySelectorAll('input[name=s_id').forEach(element => {
        if(element.checked){
            const id = parseID('chk-', element.id);
            selected.push(id);
        }
    });

    if(!selected.length){
        __showMessage('You did not select any records');
        return false;
    }

    //for each ids, get the quantity, sales_amount, wacc, shareholder/stock id, 
    //then process ajax record to update record
    selected.forEach(function(id){

        const obj = document.querySelector(`#chk-${id}`).dataset;    
        const shareholder_id = obj.user;
        const stock_id = obj.stock;
        
        const quantity = document.querySelector(`#qty-${id}`).value;
        const wacc = document.querySelector(`#wacc-${id}`).value;
        const sales_amount = document.querySelector(`#amt-${id}`).value;
        console.log(quantity, wacc, sales_amount);

        let _token = document.getElementsByName('_token')[0].value;
        // const querystring = `_token=${_token}
        //                         &record_id=${id}
        //                         &stock_id=${stock_id}
        //                         &shareholder_id=${shareholder_id}
        //                         &quantity=${quantity}
        //                         &wacc=${wacc}
        //                         &sales_amount=${sales_amount}`;
        // saveBasket(querystring);
    });
}
    
function saveBasket(querystring){

    const url = `${window.location.origin}/basket/update`;

    let request = new XMLHttpRequest();
    request.open('POST', url, true);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    request.onload = function() {

        const data = JSON.parse(this.response);
        if (this.status >= 200 && this.status < 400) {
            __showMessage(data.message, true);

        }
        else{

            __showMessage(data.message, false);     

        }
        hideLoadingMessage();
    }
    request.send(querystring);

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
        __showMessage('You did not select any records');
        return false;
    }

    let url = `${url}/basket/delete`;

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
                    __showMessage(data.message, true);    
                }
                else{    
                    __showMessage(data.message, false);    
                }
                hideLoadingMessage();
                
                //refresh the page
                url = `${url}/basket`;
                setTimeout(function(){ 
                    window.location.replace(url);
                }, 1000);

            }
        }

        request.onerror = function() {
          hideLoadingMessage();
        }

        request.send(`_token=${_token}&ids=${selected.toString()}`);
        showLoadingMessage();
    }
}



// function hideSelectedRow(id){
//     let rowid = 'row-' + id;
//     document.getElementById(rowid).setAttribute('style','display:none');
// }

function calculateSalesGrandTotal(){
    let sum_sales = 0;
    document.querySelectorAll('[name="sales_amount"]').forEach( function(el){
        const sales = el.value;
        sum_sales += parseFloat(sales);        
    });
    document.querySelector('#total_sales_amount').value = parseFloat(sum_sales).toFixed(0);
}

function calculateInvestmentGrandTotal(){
    let sum_investment = 0;
    document.querySelectorAll('[name="investment"]').forEach( function(el){
        const investment = el.innerText;
        sum_investment += parseFloat(investment);        
    });
    document.querySelector('#total_investment_amount').value = parseFloat(sum_investment).toFixed(0);
}

function calculateTotalQuantity(){
    var sum = 0;
    document.querySelectorAll('[name="quantity"]').forEach( function(el){
        sum = parseFloat(sum) + parseFloat(el.value);        
    });
    document.querySelector('#total_quantity').value = parseFloat(sum).toFixed(0);
}


//calculaet commission
function calculateOthers(id)
{
    const CGT = 0.05;
    const DP = 25;

    const sales_amount = document.querySelector(`#amt-${id}`).value;
    const investment_amount = document.querySelector(`#invest-${id}`).value;
    const quantity = document.querySelector(`#qty-${id}`).value;

    const no_scripts = 1;
    const dp_amount = parseFloat(no_scripts) * parseFloat(DP);
    // console.log('sales:', sales_amount,'investment:', investment_amount, 'quantity:', quantity, 'dp:', dp_amount);

    const gain = parseFloat(sales_amount) - parseFloat(investment_amount);
    const gain_tax = (parseFloat(gain) > 0) ? (parseFloat(CGT) * parseFloat(gain)) : 0;
    const sebon_comm = 0;
    const broker_comm = 0;
    let payable_amount = 0;
    let broker_commission = 0;
    let sebon_commission = 0;
    let effective_rate = 0;

    document.querySelector(`#cgt-${id}`).innerText =  gain_tax.toFixed(1);

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
            document.querySelector(`#net_pay-${id}`).innerText =  payable_amount.toFixed(0);
            effective_rate = parseFloat(payable_amount)/parseInt(quantity);
            
            document.querySelector(`#rate-${id}`).innerText =  effective_rate.toFixed(1);

            if(broker_commission)
                document.querySelector(`#comm-${id}`).innerText =  broker_commission.toFixed(0);
            if(sebon_commission) 
                document.querySelector(`#sebon-${id}`).innerText =  sebon_commission.toFixed(0);
            

        } 

    }
    request.send(); 
} 

calculateSalesGrandTotal();
calculateInvestmentGrandTotal();
calculateTotalQuantity();