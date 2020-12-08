// import axios from 'axios';

// const BASE_URL = 'https://jsonplaceholder.typicode.com';

// const getTodos = async () => {
//   try {
//     const res = await axios.get(`${BASE_URL}/todos`);

//     const todos = res.data;

//     console.log(`GET: Here's the list of todos`, todos);

//     return todos;
//   } catch (e) {
//     console.error(e);
//   }
// };

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
    let el = document.getElementById(form_id);
    el.classList.add('show');
}

function hideForm(form_id) {
    // let el = document.getElementsByClassName(form_id);
    // el[0].classList.remove('show');
    let el = document.getElementById(form_id);
    el.classList.remove('show');
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