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

//handle Edit button clicked
let btn = document.getElementById("edit");
btn.addEventListener("click", function() {
  showLoadingMessage();
  //retrieve the data-id attribute from the edit button
  let  el = document.getElementById('edit');
  let id = el.getAttribute('data-id');

  //call ajax 
  let _token = document.getElementsByName('_token')[0].value;

  let request = new XMLHttpRequest();
  request.open('GET', '/shareholder/'+id, true);

  // request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
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
  request.send(`_token=${_token}&id=${id}`);

});

function updateFormFields($data) {
  $record = $data['data'];
  // console.log($record);
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

  //hide relation select box if parent_id = shareholder_id
  let parent_id = document.getElementById('parent_id').value;
  let relation = document.getElementsByClassName('c_relation');

  // console.log(parent_id, $record['parent_id']);

  if(parent_id == $record['id']){
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