//show notice 

const el = document.querySelector('#notice');
const notice  = el.querySelector('.notice'); 

if(typeof(notice) != 'undefined' && notice != null){
    if(notice.dataset.showNotice === "yes"){
        el.classList.remove('hide');
        el.classList.add('show');
    }
}

function hide_notice(){
    // document.querySelector('.notice_wrapper>.btn>a').addEventListener('click',function(e){
    //     e.preventDefault();
    // });
    const el = document.querySelector('#notice');
    el.classList.add('d-none');
}

function convertTZ(date, tzString) {
    return new Date((typeof date === "string" ? new Date(date) : date).toLocaleString("en-US", {timeZone: tzString}));   
}

//select an option in a list
function setOption(selectElement, value) {
    // console.log(selectElement, value);
    var options = selectElement.options;
    for (var i = 0, optionsLength = options.length; i < optionsLength; i++) {
        if (options[i].value == value) {
            selectElement.selectedIndex = i;
            return true;
        }
    }
    return false;
}

function showLoadingMessage() {
    let ele_loading = document.getElementById('loading-message');
    ele_loading.classList.add('loading');
}

function hideLoadingMessage() {
    let ele_loading = document.getElementById('loading-message');
    ele_loading.classList.remove('loading');
}

function getDateTime(){
    var date = new Date();
    var temp = `${date.getFullYear()}-${date.getMonth()+1}-${date.getDate()} ${date.getHours()}:${date.getMinutes()}:${date.getSeconds()}`;
    return temp;
}
function showDate(){
    document.querySelector('input#email_verified_at').value= getDateTime();
}

function showForm(form_id) {            
    document.getElementById(form_id).classList.add('show');
}

function hideForm(form_id) {
    // let el = document.getElementsByClassName(form_id);
    // el[0].classList.remove('show');
    document.getElementById(form_id).classList.remove('show');
    // el.classList.add('hide');
}

function showMessage(message, el = 'message', flag = 'success') {
    const msg = document.getElementById(el);
    msg.innerHTML=message;
    msg.classList.add(flag);
}

function clearMessage() {
    document.getElementById('message').innerHTML='';
}

//parse record_id returns "28" from string "chk-28"
function parseID(prefix, id_string) {
    const string_len = id_string.length;
    return id_string.substr(prefix.length, string_len - prefix.length);
}

//serialize name (replace spaces with dashes)
function serializeString(name){
    return name.replace(/\s+/g, '-').toLowerCase();
}

function formatNumber(num) {
    return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
  }

function addCommas(nStr){
    nStr += '';
    var x = nStr.split('.');
    var x1 = x[0];
    var x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
     x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}