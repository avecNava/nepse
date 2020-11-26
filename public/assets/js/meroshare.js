function showLoadingMessage() {
    let ele_loading = document.getElementById('loading-message');
    ele_loading.classList.add('loading');
}
function hideLoadingMessage() {
    let ele_loading = document.getElementById('loading-message');
    ele_loading.classList.remove('loading');
}
function showImportMessage($t=5000) {
    let ele_loading = document.getElementById('import-message');
    ele_loading.classList.add('success');
    setTimeout(function(){ 
        ele_loading.classList.remove('success');
     }, $t);
}
function hideImportMessage() {
    let ele_loading = document.getElementById('import-message');
    ele_loading.classList.remove('success');
}
function checkAll() {
    var select_all = document.getElementById('select_all');
    var flag = select_all.checked;            
    var elements = document.getElementsByName("t_id");
    Array.prototype.forEach.call(elements, function(el, i){
        el.checked=flag;
    });
}
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
    request.open('POST', '/meroshare/import-transaction', true);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    request.onload = function(ele_success, ele_loading) {
        if (this.status >= 200 && this.status < 400) {
            $msg = JSON.parse(this.response);
            // console.log($msg);
            hideLoadingMessage();
            showImportMessage(5000*2);
        }
    }
    request.send(`_token=${_token}&trans_id=${selected.toString()}`);

}