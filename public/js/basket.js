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
$quantity = document.querySelectorAll('[name="quantity"]');
$quantity.forEach(function(el){
    el.addEventListener('change', function(e){

        const id_str = e.target.id;
        const quantity = e.target.value;
        const row = parseID('qty-', id_str);
        
        const id_quantity = `#qty-${row}`;
        const id_wacc = `#wacc-${row}`;
        const id_amount = `#amt-${row}`;

        if(e.target.value < 0) {
            document.querySelector(id_quantity).value = 1;
            return false;
        }

        const wacc = document.querySelector(id_wacc).value;
        const sales_amount = document.querySelector(id_amount).value;

        const $total_amount = parseFloat(wacc) * parseFloat(quantity);
        document.querySelector(id_amount).value = parseFloat($total_amount).toFixed(0);

        // console.log(quantity, wacc, sales_amount);
        // document.querySelector(id_amount).dispatchEvent(new Event("change"));

        calculateGrandTotal();
        
    });
});


/**
 * monitor change in wacc, update wacc and sales_amount as required
 */
$wacc = document.querySelectorAll('[name="wacc"]');
$wacc.forEach(function(el){
    el.addEventListener('change', function(e){

        const id_str = e.target.id;
        const wacc = e.target.value;
        const row = parseID('wacc-', id_str);
        
        const id_quantity = `#qty-${row}`;
        const id_amount = `#amt-${row}`;

        const quantity = document.querySelector(id_quantity).value;
        const sales_amount = document.querySelector(id_amount).value;

        const $total_amount = parseFloat(wacc) * parseFloat(quantity);
        document.querySelector(id_amount).value = parseFloat($total_amount).toFixed(0);

        calculateGrandTotal();
    });
});

/**
 * monitor change in sales amount, calculate grand total
 */

document.querySelectorAll('[name="sales_amount"]')
    .forEach(function(el){
        el.addEventListener('change', function(e){
            calculateGrandTotal();
        });
});    
    
function calculateGrandTotal(){
    var sum = 0;
    document.querySelectorAll('[name="sales_amount"]').forEach( function(el){
        sum = parseFloat(sum) + parseFloat(el.value);        
        // console.log(parseFloat(el.value));
    });
    document.querySelector('#total_sales_amount').value = parseFloat(sum).toFixed(0);
}

function updateBasket(id){
    
    const el_update = `#u-${id}`;
    
    const obj = document.querySelector(el_update);    
    const data = obj.dataset;    
    const shareholder_id = data.shareholder_id;
    const stock_id = data.stock_id;
    
    const quantity = document.querySelector(`#qty-${id}`).value;
    const wacc = document.querySelector(`#wacc-${id}`).value;
    const sales_amount = document.querySelector(`#amt-${id}`).value;
    // console.log(quantity, wacc, sales_amount);
    
    const url = `${window.location.origin}/basket/update`;
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

    request.send(`_token=${_token}&record_id=${id}&stock_id=${stock_id}&shareholder_id=${shareholder_id}&quantity=${quantity}&wacc=${wacc}&sales_amount=${sales_amount}`);
    showLoadingMessage();

}

function deleteBasket(id){

    if(!id){
        __showMessage('Something wrong happened 🐛', false);
        return false;
    }

    if(confirm('Please confirm the delete operation')) {
        
        let request = new XMLHttpRequest();
        request.open('GET', '/basket/delete/'+id, true);
      
        request.onload = function() {

            const data = JSON.parse(this.response);
          
            if (this.status >= 200 && this.status < 400) {
                __showMessage(data.message, true);
    
            }
            else{
    
                __showMessage(data.message, false);     
    
            }
            hideSelectedRow(id);
            hideLoadingMessage();
        }

        request.onerror = function() {
          // There was a connection error of some sort
        //   __showMessage('data.message', false);
          hideLoadingMessage();
        };

        request.send(); 
        showLoadingMessage();
    }

}

function hideSelectedRow(id){
    let rowid = 'row-' + id;
    document.getElementById(rowid).setAttribute('style','display:none');
}

calculateGrandTotal();