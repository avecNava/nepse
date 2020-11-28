// Select all checkboxes with the name 'settings' using querySelectorAll.
var checkboxes = document.querySelectorAll("input[type=checkbox][name=s_id]");

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
// handle Edit button clicked
//-------------------------------------
let btn = document.getElementById("edit");
btn.addEventListener("click", function() {

  //retrieve the data-id attribute from the edit button
  let el = document.getElementById('edit');
  let id = el.getAttribute('data-id');

  if(!id){
    alert('Please select a record to edit');
    return;
  }

  showLoadingMessage();  

  let request = new XMLHttpRequest();
  request.open('GET', '/shareholder/'+id, true);

  request.onload = function(ele_success, ele_loading) {
      if (this.status >= 200 && this.status < 400) {
          $data = JSON.parse(this.response);
          updateFormFields($data);
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


//-------------------------------------
// handle Delete button clicked
//-------------------------------------
let btn_delete = document.getElementById("delete");
btn_delete.addEventListener("click", function() {
  
  //retrieve the data-id attribute from the delete button
  let  el = document.getElementById('delete');
  let id = el.getAttribute('data-id');

  if(!id){
    alert('Please select a record to delete');
    return;
  }

  if(confirm('Please confirm the delete operation')) {
    let _token = document.getElementsByName('_token')[0].value;
    let request = new XMLHttpRequest();
    request.open('POST', '/shareholder/delete', true);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');

    request.onload = function(ele_success, ele_loading) {
        if (this.status >= 200 && this.status < 400) {
            $data = JSON.parse(this.response);

            if($data.message=='deleted'){
              hideSelectedRow(id);
            }
        }
    }  
    request.send(`_token=${_token}&id=${id}`);
  }
});

function hideSelectedRow(id){
  let rowid = 'row' + id;
  document.getElementById(rowid).setAttribute('style','display:none');
}
//--------------------------------------------------------------------------------------
// data contains the record being created (first_name, last_name, parent_id, gender etc)
//--------------------------------------------------------------------------------------

function updateFormFields($record) {
  
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