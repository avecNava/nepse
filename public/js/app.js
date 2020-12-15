//select an option in a list
function setOption(selectElement, value) {
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

function showForm(form_id) {            
    // let el = document.getElementsByClassName(form_id);
    // el[0].classList.add('show');
    console.log(form_id);
    document.getElementById(form_id).classList.add('show');
}

function hideForm(form_id) {
    // let el = document.getElementsByClassName(form_id);
    // el[0].classList.remove('show');
    document.getElementById(form_id).classList.remove('show');
    // el.classList.add('hide');
}

function showMessage(msg) {
    document.getElementById('message').innerHTML=msg;
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