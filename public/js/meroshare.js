    function showImportMessage($msg, $t=5000) {
        let ele = document.getElementById('message');
        ele.innerHTML = `${$msg}`;
        setTimeout(function(){ 
            ele.innerHTML = ' ';
        }, $t);
    }
    function hideImportMessage() {
        let ele = document.getElementById('import-message');
        ele.classList.remove('success');
    }
    function checkAll() {
        var select_all = document.getElementById('select_all');
        var flag = select_all.checked;            
        var elements = document.getElementsByName("t_id");
        Array.prototype.forEach.call(elements, function(el, i){
            el.checked=flag;
        });
    }
    function unCheckAll() {
        var select_all = document.getElementById('select_all');                    
        var elements = document.getElementsByName("t_id");
        Array.prototype.forEach.call(elements, function(el, i){
            el.checked=false;
        });
    }
    
    //new button click, show the form
    let btnNew = document.getElementById("new");
        btnNew.addEventListener("click", function() {
        showForm('meroshare-import-form');
    });
    
    //cancel button click, hide the form
    let btnCancel = document.getElementById("cancel");
        btnCancel.addEventListener("click", function() {
        hideForm('meroshare-import-form');
        resetInputFields();
    });

    
    //display selected count (select all)    
    const container = document.getElementById('message');
    document.querySelector('input[name=select_all]').addEventListener("change", function() {
        
        let selected = document.querySelectorAll('input[name=t_id]:checked').length;
        let total = document.querySelectorAll('input[name=t_id]').length;
        if(this.checked){
            container.innerHTML = `${selected} records selected`;
        }
        else{
            container.innerHTML = `${total} records`;
        }
        
    });
  
    //display selected count
    let count = 0;
    // const container = document.getElementById('message');
    const checkboxes = document.querySelectorAll('input[name=t_id]');
    checkboxes.forEach(checkbox=>{
        checkbox.addEventListener("change", function() {
            if(this.checked){
                count = count + 1;
            }else{
                count = count - 1;
            }
            // let count = document.querySelectorAll("input[name=t_id]:checked").length;
            if(count > 0){
                container.innerHTML = `${count} records selected`;
            }
            else{
                container.innerHTML = `${checkboxes.length} records`;
            }
            
        });
    });

/**
 * imports the selected transactions to the portfolio table
 */
document.getElementById('saveToPortfolio')
        .addEventListener('click',function(){

        let selected = [];
        let elements = document.getElementsByName("t_id");
        let ele_import = document.getElementById('message');
        

        Array.prototype.forEach.call(elements, function(el, i){
            if(el.checked){
                selected.push(el.id);
            }
        });
    
        if(selected.length <=0 ){
            let message = 'Please select some records for Portfolio 🙏';
            showImportMessage(message);
            return;
        }
        showLoadingMessage();

        //call ajax 
        let _token = document.getElementsByName('_token')[0].value;
        let request = new XMLHttpRequest();
        request.open('POST', '/meroshare/import-portfolio', true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        request.onload = function() {
            if (this.status >= 200 && this.status < 400) {
                $data = JSON.parse(this.response);
                hideLoadingMessage();
                unCheckAll();
                showImportMessage($data.message);
            }
        }

        request.send(`_token=${_token}&trans_id=${selected.toString()}`);

});

let select = document.getElementById("shareholder_filter");
select.addEventListener("change", function() {

    let url = window.location.origin + "/meroshare/transaction/";
    
    //get the value from selectedIndex
    let options = this.options[this.selectedIndex];

    //append shareholder_id to the url (ie, /meroshare/import-transaction/1)
    if(this.selectedIndex > 0)
        url = url + options.value;            
    
    window.location.replace(url);

});