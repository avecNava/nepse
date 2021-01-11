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

//-------------------------------------
// handle Cancel button
//-------------------------------------
let btnCancel = document.getElementById("cancel");
btnCancel.addEventListener("click", function() {
  hideForm('shareholder-form');
  resetInputFields();
});

//-------------------------------------
// handle New button
//-------------------------------------
let btnNew = document.getElementById("new");
btnNew.addEventListener("click", function() {
  resetInputFields();   // reset form
  showForm('shareholder-form');
});

//-------------------------------------
// handle Edit button clicked
//-------------------------------------
let btnEdit = document.getElementById("edit");
btnEdit.addEventListener("click", function() {

  //retrieve the data-id attribute from the edit button
  let el = document.getElementById('edit');
  let id = el.getAttribute('data-id');

  if(!id){
    let msg = 'Please select a record to edit';
    showMessage(msg,'message');    return;
  }

  showLoadingMessage(); 
  clearMessage(); 
  showForm('shareholder-form');

  let request = new XMLHttpRequest();
  request.open('GET', '/shareholder/'+id, true);

  request.onload = function() {
      if (this.status >= 200 && this.status < 400) {
          $data = JSON.parse(this.response);
          updateInputFields($data);
          hideLoadingMessage();
      }
  }  
  request.onerror = function() {
    // There was a connection error of some sort
    hideLoadingMessage();
  };
  request.send();
  // request.send(`_token=${_token}&id=${id}`);

});

function resetInputFields() {

  document.getElementById('id').value = '';
  document.getElementById('first_name').value = '';
  document.getElementById('last_name').value = '';
  document.getElementById('email').value = '';
  document.getElementById('date_of_birth').value='';

  let el = document.getElementsByClassName('message');
  el[0].classList.remove('show');
  el[0].classList.innerHTML='';

}
//--------------------------------------------------------------------------------------
// data contains the record being created (first_name, last_name, parent_id, gender etc)
//--------------------------------------------------------------------------------------

function updateInputFields($record) {
  
  document.getElementById('id').value = $record['id'];
  document.getElementById('first_name').value = $record['first_name'];
  document.getElementById('last_name').value = $record['last_name'];
  document.getElementById('email').value = $record['email'];

  if($record['date_of_birth']){
    document.getElementById('date_of_birth').value=$record['date_of_birth'];
  }
  
  if($record['relation']){
    setOption(document. getElementById('relation'), $record['relation']);
  }
  if($record['gender']=="M"){
    document.getElementById("male").checked = true;
  }
  else if($record['gender']=="F"){
      document.getElementById("female").checked = true;
  }
  else if($record['gender']=="O"){
    document.getElementById("other").checked = true;
  }

  let relation = document.getElementsByClassName('c_relation');
  
  //hide relation select box if parent_id = shareholder_id
  if($record['parent'] == true){
    relation[0].setAttribute('style','display:none');
  }
  else{
    relation[0].setAttribute('style','display:block');
  }

}

//-------------------------------------
// handle Delete button clicked
//-------------------------------------
let btnDelete = document.getElementById("delete");
btnDelete.addEventListener("click", function() {
  
  //retrieve the data-id attribute from the delete button
  //the data-id attirbute is the id of the row
  const  el = document.getElementById('delete');
  const id = el.getAttribute('data-id');

  //check if any record is selected for deletion
  if(!id){
    alert('Please select a record to delete');
    return;
  }

  //prevent parent shareholder from deletion
  //get element with the id and read the data-parent  attribute
  //https://developer.mozilla.org/en-US/docs/Learn/HTML/Howto/Use_data_attributes
  let selector = '#row' + id;
  const record = document.querySelector(selector);
  // console.log(record, record.dataset.parent);

  if(record.dataset.parent==true)
  {
    let msg = 'Can not delete a Parent Shareholder 🙄';
    showMessage(msg,'message');
    return;
  }

  if(confirm('Please confirm the delete operation')) {
    
    let request = new XMLHttpRequest();
    request.open('GET', '/shareholder/delete/'+id, true);
  
    request.onload = function(ele_success, ele_loading) {
        if (this.status >= 200 && this.status < 400) {
            data = JSON.parse(this.response);
            showMessage(data.message);
            document.getElementById(data.row).classList.add('hide');
            hideLoadingMessage();
        }
    }  
    request.onerror = function() {
      // There was a connection error of some sort
      hideLoadingMessage();
    };
    request.send(); 

    // request.send(`_token=${_token}&id=${id}`);
    // let _token = document.getElementsByName('_token')[0].value;
    // let request = new XMLHttpRequest();
    // request.open('GET', '/shareholder/delete', true);
    // request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');

    // request.onload = function(ele_success, ele_loading) {
    //     if (this.status >= 200 && this.status < 400) {
    //         $data = JSON.parse(this.response);
    //         var $status = $data.status;
    //         var msg = document.querySelector('#message');
    //         msg.innerHTML= $data.message;
    //         hideSelectedRow(id, $status);
    //         // updateStyle('c_band01', $status);
    //     }
    // }  
    // request.send(`_token=${_token}&id=${id}`);

  }
});

function hideSelectedRow(id){
  let rowid = 'row' + id;
  document.getElementById(rowid).setAttribute('style','display:none');
}