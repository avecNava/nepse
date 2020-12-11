// Select all checkboxes with the name 's_id' using querySelectorAll.
var checkboxes = document.querySelectorAll("input[type=checkbox][name=s_id]");

//capture the id of the selected checkbox
Array.prototype.forEach.call(checkboxes, function(el, i){

    el.addEventListener('change', function() {
        
        let s_id = this.id;

        if(this.checked){
            document.getElementById('edit').setAttribute('data-id', s_id);
            document.getElementById('delete').setAttribute('data-id', s_id);
        }

        else {
            document.getElementById('edit').removeAttribute('data-id');
            document.getElementById('delete').removeAttribute('data-id');
        }

        // console.log(this.id, this.checked);
        
        //uncheck all other checkboxes (one select at a time)
        Array.prototype.forEach.call(checkboxes, function(el, i){
        if(el.id != s_id)
            el.checked = false;
        });

    })

});


//handle quantity change
document.getElementById('quantity').addEventListener('change',function(){
    updateTotalPrice();
});
//handle unit_cost change
document.getElementById('unit_cost').addEventListener('change',function(){
    updateTotalPrice();
});
//handle offer change
document.getElementById('offer').addEventListener('change',function(){

    // let el_unit_cost = document.getElementById('unit_cost_label');
    // el_unit_cost.innerHTML = '';
    // let el_broker = document.getElementById('broker_label');
    // el_broker.innerHTML = '';

    let el_offer = document.querySelector('#offer');
    const i = el_offer.selectedIndex;            
    let tag = el_offer.options[i].dataset.tag;
    let unit_cost = 0;

    if(tag==='none') return;

    document.getElementById('secondary').classList.add('hide');
    if(tag === 'IPO'){
        unit_cost = 100;
        document.getElementById('unit_cost').value = unit_cost;
    }
    else if (tag === 'BONUS'){
        unit_cost = 0;
        document.getElementById('unit_cost').value = unit_cost;
    }
    else if (tag === 'SECONDARY'){
        document.getElementById('secondary').classList.remove('hide');
        // el_broker.innerHTML =`<mark>Choose a broker</mark>`;
        // el_unit_cost.innerHTML =`<mark>Enter unit cost for ${tag} share</mark>`;
    }
    // else{
    //     el_unit_cost.innerHTML =`<mark>Enter unit cost for ${tag}</mark>`;
    //     document.getElementById('unit_cost').value='';
    //     document.getElementById('unit_cost').focus();
    //     return;
    // }
    // document.getElementById('unit_cost').value = unit_cost;
    document.getElementById('unit_cost').focus();
    updateTotalPrice();
});

function updateTotalPrice() {

    let quantity = document.getElementById('quantity').value;
    if (!quantity) return;

    let unit_cost = document.getElementById('unit_cost').value;
    if (!unit_cost) return;
    
    let total = (quantity * unit_cost).toFixed(2);
    
    const url = `${window.location.origin}/portfolio/commission/${total}`;

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
                // let x = `total: ${total} + broker: ${broker_commission} + SEBON : ${sebon_commission}`;
                // document.getElementById('total_amount_label').innerHTML = x;
                document.getElementById('broker_commission').value =  broker_commission;
                document.getElementById('sebon_commission').value =  sebon_commission;
            }            
        }
        request.send(); 
    }
    else{
            document.getElementById('broker_commission').value =  '';
            document.getElementById('sebon_commission').value =  '';
    }
    
    document.getElementById('total_amount').value =  total_amount;
    document.getElementById('effective_rate').value =  eff_rate;

}