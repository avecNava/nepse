function showLoadingMessage() {
    let ele_loading = document.getElementById('loading-message');
    ele_loading.classList.add('loading');
}
function hideLoadingMessage() {
    let ele_loading = document.getElementById('loading-message');
    ele_loading.classList.remove('loading');
}
function showImportMessage($msg, $t=5000) {
    let ele = document.getElementById('message');
    ele.innerHTML = $msg;
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

/**
 * imports the selected transactions to the portfolio table
 */
function importToMyPortfolio() {
    let selected = [];
    let elements = document.getElementsByName("t_id");
    let ele_import = document.getElementById('import-message');
    
    showLoadingMessage();

    Array.prototype.forEach.call(elements, function(el, i){
        if(el.checked){
            selected.push(el.id);
        }
    });

    //call ajax 
    let _token = document.getElementsByName('_token')[0].value;
    let request = new XMLHttpRequest();
    request.open('POST', '/meroshare/import-portfolio', true);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    request.onload = function(ele_success, ele_loading) {
        if (this.status >= 200 && this.status < 400) {
            $data = JSON.parse(this.response);
            hideLoadingMessage();
            showImportMessage($data.message);
        }
    }
    request.send(`_token=${_token}&trans_id=${selected.toString()}`);

}

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