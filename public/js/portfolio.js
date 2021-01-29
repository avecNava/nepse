// Select all checkboxes with the name 's_id' using querySelectorAll.
// var checkboxes = document.querySelectorAll("input[type=checkbox][name=s_id]");

// //capture the id of the selected checkbox
// Array.prototype.forEach.call(checkboxes, function(el, i){

//     el.addEventListener('change', function() {
        
//         let s_id = this.id;

//         if(this.checked){
//             document.getElementById('edit').setAttribute('data-id', s_id);
//             document.getElementById('delete').setAttribute('data-id', s_id);
//         }

//         else {
//             document.getElementById('edit').removeAttribute('data-id');
//             document.getElementById('delete').removeAttribute('data-id');
//         }

//         // console.log(this.id, this.checked);
        
//         //uncheck all other checkboxes (one select at a time)
//         // Array.prototype.forEach.call(checkboxes, function(el, i){
//         // if(el.id != s_id)
//         //     el.checked = false;
//         // });

//     })

// });


//check all checkbox is Select all is checked, else unselect all
function checkAll() {
    var select_all = document.getElementById('select_all');
    var flag = select_all.checked;            
    var elements = document.getElementsByName("s_id");
    Array.prototype.forEach.call(elements, function(el, i){
        el.checked=flag;
    });
}
function unCheckAll() {
    var select_all = document.getElementById('select_all');                    
    var elements = document.getElementsByName("s_id");
    Array.prototype.forEach.call(elements, function(el, i){
        el.checked=false;
    });
}

//handle quantity change
document.getElementById('quantity').addEventListener('change',function(){
    updateTotalPrice();
});
//handle dp_amount change
document.getElementById('dp_amount').addEventListener('change',function(){
    updateTotalPrice();
});
//handle quantity blur
document.getElementById('quantity').addEventListener('blur',function(){
    updateTotalPrice();
});
//handle unit_cost change
document.getElementById('unit_cost').addEventListener('change',function(){
    updateTotalPrice();
});
//handle unit_cost blur
document.getElementById('unit_cost').addEventListener('blur',function(){
    updateTotalPrice();
});

//handle offer change
document.getElementById('offer').addEventListener('change',function(){

    let el_offer = document.querySelector('#offer');
    let tag = el_offer.options[ el_offer.selectedIndex ].dataset.tag;
    if(tag==='none') return;

    document.getElementById('secondary').classList.add('hide');
    if(tag === 'IPO' || tag === 'RIGHTS'){
        document.getElementById('unit_cost').value = 100;
    }else if (tag === 'BONUS'){    
        document.getElementById('unit_cost').value = 0;
    }else if (tag === 'SECONDARY'){
        document.getElementById('secondary').classList.remove('hide');
    }
    updateTotalPrice();
});

function updateTotalPrice() {
    
    document.getElementById('broker_commission').value =  '';
    document.getElementById('sebon_commission').value =  '';
    document.getElementById('total_amount').value =  '';
    document.getElementById('base_amount').value =  '';
    document.getElementById('effective_rate').value =  '';

    var quantity = document.getElementById('quantity').value;
    if (!quantity) return;
    if (quantity < 1) return;

    let unit_cost = document.getElementById('unit_cost').value;
    if (!unit_cost) return;
    
    var sub_total = (quantity * unit_cost).toFixed(2);

    var eff_rate = unit_cost;
    var total_amount = sub_total;

    document.getElementById('base_amount').value =  sub_total;
    document.getElementById('total_amount').value =  total_amount;
    document.getElementById('effective_rate').value =  eff_rate;
    
    const el_offer = document.querySelector('#offer');
    const tag = el_offer.options[ el_offer.selectedIndex ].dataset.tag;

    //calculate BROKER COMMISSION & SEBON COMMISSION for purchase via Secondary market
    const url = `${window.location.origin}/commission/${sub_total}`;

    if(tag === 'SECONDARY'){

        let request = new XMLHttpRequest();
        request.open('GET', url, true);
    
        request.onload = function() {

            if (this.status >= 200 && this.status < 400) {
                
                const data = JSON.parse(this.response);

                let broker = parseFloat(data.broker);
                let sebon = parseFloat(data.sebon);
                let broker_commission = ((broker/100) * sub_total);
                let sebon_commission = ((sebon/100) * sub_total);
                const dp_amount = document.getElementById('dp_amount').value;

                total_amount = parseFloat(sub_total) + parseFloat(broker_commission) + parseFloat(sebon_commission) + parseInt(dp_amount);
                eff_rate = (total_amount / quantity);
                if(broker_commission)
                    document.getElementById('broker_commission').value =  broker_commission.toFixed(2);
                if(sebon_commission) 
                    document.getElementById('sebon_commission').value =  sebon_commission.toFixed(2);
                if(total_amount)
                    document.getElementById('total_amount').value =  total_amount.toFixed(2);
                if(eff_rate)
                    document.getElementById('effective_rate').value =  eff_rate.toFixed(2);
            } 

        }
        request.send(); 
    } 

}