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
    function openForm($name){
        showForm($name);
    }
    
    //cancel button click, hide the form
    function closeForm($name){
        hideForm($name);
    }
    
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
 * imports the selected transactions from meroshare to the portfolio table
 */
function importMeroShareTransactions(){

        let selected = [];
        let elements = document.getElementsByName("t_id");
        let ele_import = document.getElementById('message');
        
        Array.prototype.forEach.call(elements, function(el, i){
            if(el.checked){
                selected.push(el.id);
            }
        });
    
        if(selected.length <=0 ){
            let message = 'Please select some records to add to the Portfolio 🙏';
            showImportMessage(message);
            return;
        }
        showLoadingMessage();

        //call ajax 
        let _token = document.getElementsByName('_token')[0].value;
        let request = new XMLHttpRequest();
        request.open('POST', '/meroshare/store-portfolio', true);
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

}

function meroShareShareholderRefresh(){

    let url = `${window.location.origin}/meroshare/`;    
    const $shareholder = document.getElementById('meroshare-shareholder_filter');

    //get the value from selectedIndex
    let options = $shareholder.options[$shareholder.selectedIndex];

    //append shareholder_id to the url (ie, /meroshare/import-transaction/1)
    if($shareholder.selectedIndex > 0)
        url = url + options.value;            

    window.location.replace(url);

};

/**
 * deletes the selected records from MeroShare table
 */
function deleteMeroShareTransactions(){

    let selected = [];
    let elements = document.getElementsByName("t_id");
    let ele_import = document.getElementById('message');
    let url = `${window.location.origin}/meroshare/import`;

    Array.prototype.forEach.call(elements, function(el, i){
        if(el.checked){
            selected.push(el.id);
        }
    });

    if(selected.length <=0 ){
        let message = 'Please select records to delete';
        showImportMessage(message);
        return;
    }

    if(confirm('Please confirm the delete operation')){
    
        showLoadingMessage();

        //call ajax 
        let _token = document.getElementsByName('_token')[0].value;
        let request = new XMLHttpRequest();
        request.open('POST', '/meroshare/delete', true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        request.onload = function() {
            if (this.status >= 200 && this.status < 400) {
                $data = JSON.parse(this.response);
                hideLoadingMessage();                
                showImportMessage($data.message);

                setTimeout(function(){ 
                    window.location.replace(url);
                }, 1000);
            }
        }

        request.send(`_token=${_token}&trans_id=${selected.toString()}`);

    }

}

/**
 * imports the selected transactions from myshare to the portfolio table
 */
function importMyShareTransactions(){

    let selected = [];
    let elements = document.getElementsByName("t_id");
    let ele_import = document.getElementById('message');
    

    Array.prototype.forEach.call(elements, function(el, i){
        if(el.checked){
            selected.push(el.id);
        }
    });

    if(selected.length <=0 ){
        let message = 'Please select some records to add to the Portfolio 🙏';
        showImportMessage(message);
        return;
    }
    showLoadingMessage();

    //call ajax 
    let _token = document.getElementsByName('_token')[0].value;
    let request = new XMLHttpRequest();
    request.open('POST', '/share/store-portfolio', true);
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

}

function myShareShareholderRefresh(){

    let url = `${window.location.origin}/share/`;
    const $shareholder = document.getElementById('myshare-shareholder_filter');
    //get the value from selectedIndex
    let options = $shareholder.options[$shareholder.selectedIndex];

    //append shareholder_id to the url (ie, /meroshare/import-transaction/1)
    if($shareholder.selectedIndex > 0)
        url = url + options.value;            

    window.location.replace(url);

}

/**
 * deletes the selected records from MyShare table
 */
function deleteMyShareTransactions(){

    let selected = [];
    let elements = document.getElementsByName("t_id");
    let ele_import = document.getElementById('message');
    let url = `${window.location.origin}/share/import`;

    Array.prototype.forEach.call(elements, function(el, i){
        if(el.checked){
            selected.push(el.id);
        }
    });

    if(selected.length <=0 ){
        let message = 'Please select records to delete';
        showImportMessage(message);
        return;
    }

    if(confirm('Please confirm the delete operation')){

        showLoadingMessage();

        //call ajax 
        let _token = document.getElementsByName('_token')[0].value;
        let request = new XMLHttpRequest();
        request.open('POST', '/meroshare/delete', true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        request.onload = function() {
            if (this.status >= 200 && this.status < 400) {
                $data = JSON.parse(this.response);
                hideLoadingMessage();                
                showImportMessage($data.message);

                setTimeout(function(){ 
                    window.location.replace(url);
                }, 1000);
            }
        }

        request.send(`_token=${_token}&trans_id=${selected.toString()}`);

    }

}