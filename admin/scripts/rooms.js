let add_room_form = document.getElementById('add_room_form');
    
add_room_form.addEventListener('submit',function(e){
  e.preventDefault();
  add_room();
});

async function postForm(url, params){
  const body = new URLSearchParams(params).toString();
  const res = await fetch(url, { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body });
  return await res.text();
}

function add_room()
{
  let data = new FormData();
  data.append('add_room','');
  data.append('name',add_room_form.elements['name'].value);
  data.append('area',add_room_form.elements['area'].value);
  data.append('price',add_room_form.elements['price'].value);
  data.append('quantity',add_room_form.elements['quantity'].value);
  data.append('adult',add_room_form.elements['adult'].value);
  data.append('children',add_room_form.elements['children'].value);
  data.append('desc',add_room_form.elements['desc'].value);

  let features = [];
  add_room_form.elements['features'].forEach(el =>{
    if(el.checked){
      features.push(el.value);
    }
  });

  let facilities = [];
  add_room_form.elements['facilities'].forEach(el =>{
    if(el.checked){
      facilities.push(el.value);
    }
  });

  data.append('features',JSON.stringify(features));
  data.append('facilities',JSON.stringify(facilities));

  fetch('ajax/rooms.php', { method:'POST', body:data })
    .then(r=>r.text())
    .then(txt=>{
      var myModal = document.getElementById('add-room');
      var modal = bootstrap.Modal.getInstance(myModal);
      modal.hide();
      if(txt == 1){
        alert('success','New room added!');
        add_room_form.reset();
        get_all_rooms();
      } else {
        alert('error','Server Down!');
      }
    });
}

function get_all_rooms()
{
  postForm('ajax/rooms.php', { get_all_rooms: 1 })
    .then(html=>{ document.getElementById('room-data').innerHTML = html; });
}

let edit_room_form = document.getElementById('edit_room_form');

function edit_details(id)
{
  fetch('ajax/rooms.php', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'get_room='+encodeURIComponent(id) })
    .then(r=>r.json())
    .then(data=>{

    edit_room_form.elements['name'].value = data.roomdata.name;
    edit_room_form.elements['area'].value = data.roomdata.area;
    edit_room_form.elements['price'].value = data.roomdata.price;
    edit_room_form.elements['quantity'].value = data.roomdata.quantity;
    edit_room_form.elements['adult'].value = data.roomdata.adult;
    edit_room_form.elements['children'].value = data.roomdata.children;
    edit_room_form.elements['desc'].value = data.roomdata.description;
    edit_room_form.elements['room_id'].value = data.roomdata.id;

    edit_room_form.elements['features'].forEach(el =>{
      if(data.features.includes(Number(el.value))){
        el.checked = true;
      }
    });

    edit_room_form.elements['facilities'].forEach(el =>{
      if(data.facilities.includes(Number(el.value))){
        el.checked = true;
      }
    });
  });
}

edit_room_form.addEventListener('submit',function(e){
  e.preventDefault();
  submit_edit_room();
});

function submit_edit_room()
{
  let data = new FormData();
  data.append('edit_room','');
  data.append('room_id',edit_room_form.elements['room_id'].value);
  data.append('name',edit_room_form.elements['name'].value);
  data.append('area',edit_room_form.elements['area'].value);
  data.append('price',edit_room_form.elements['price'].value);
  data.append('quantity',edit_room_form.elements['quantity'].value);
  data.append('adult',edit_room_form.elements['adult'].value);
  data.append('children',edit_room_form.elements['children'].value);
  data.append('desc',edit_room_form.elements['desc'].value);

  let features = [];
  edit_room_form.elements['features'].forEach(el =>{
    if(el.checked){
      features.push(el.value);
    }
  });

  let facilities = [];
  edit_room_form.elements['facilities'].forEach(el =>{
    if(el.checked){
      facilities.push(el.value);
    }
  });

  data.append('features',JSON.stringify(features));
  data.append('facilities',JSON.stringify(facilities));

  fetch('ajax/rooms.php', { method:'POST', body:data })
    .then(r=>r.text())
    .then(txt=>{
      var myModal = document.getElementById('edit-room');
      var modal = bootstrap.Modal.getInstance(myModal);
      modal.hide();
      if(txt == 1){
        alert('success','Room data edited!');
        edit_room_form.reset();
        get_all_rooms();
      } else {
        alert('error','Server Down!');
      }
    });
}

function toggle_status(id,val)
{
  fetch('ajax/rooms.php', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'toggle_status='+encodeURIComponent(id)+'&value='+encodeURIComponent(val) })
    .then(r=>r.text())
    .then(txt=>{
      if(txt==1){
        alert('success','Status toggled!');
        get_all_rooms();
      } else {
        alert('error','Server Down!');
      }
    });
}

let add_image_form = document.getElementById('add_image_form');

add_image_form.addEventListener('submit',function(e){
  e.preventDefault();
  add_image();
});

function add_image()
{
  let data = new FormData();
  data.append('image',add_image_form.elements['image'].files[0]);
  data.append('room_id',add_image_form.elements['room_id'].value);
  data.append('add_image','');

  fetch('ajax/rooms.php', { method:'POST', body:data })
    .then(r=>r.text())
    .then(txt=>{
    if(txt == 'inv_img'){
      alert('error','Only JPG, WEBP or PNG images are allowed!','image-alert');
    }
    else if(txt == 'inv_size'){
      alert('error','Image should be less than 2MB!','image-alert');
    }
    else if(txt == 'upd_failed'){
      alert('error','Image upload failed. Server Down!','image-alert');
    }
    else{
      alert('success','New image added!','image-alert');
      room_images(add_image_form.elements['room_id'].value,document.querySelector("#room-images .modal-title").innerText)
      add_image_form.reset();
    }
  });
}

function room_images(id,rname)
{
  document.querySelector("#room-images .modal-title").innerText = rname;
  add_image_form.elements['room_id'].value = id;
  add_image_form.elements['image'].value = '';
  postForm('ajax/rooms.php', { get_room_images: id })
    .then(html=>{ document.getElementById('room-image-data').innerHTML = html; });
}

function rem_image(img_id,room_id)
{
  let data = new FormData();
  data.append('image_id',img_id);
  data.append('room_id',room_id);
  data.append('rem_image','');
  fetch('ajax/rooms.php', { method:'POST', body:data })
    .then(r=>r.text())
    .then(txt=>{
    if(txt == 1){
      alert('success','Image Removed!','image-alert');
      room_images(room_id,document.querySelector("#room-images .modal-title").innerText);
    }
    else{
      alert('error','Image removal failed!','image-alert');
    }
  });
}

function thumb_image(img_id,room_id)
{
  let data = new FormData();
  data.append('image_id',img_id);
  data.append('room_id',room_id);
  data.append('thumb_image','');
  fetch('ajax/rooms.php', { method:'POST', body:data })
    .then(r=>r.text())
    .then(txt=>{
    if(txt == 1){
      alert('success','Image Thumbnail Changed!','image-alert');
      room_images(room_id,document.querySelector("#room-images .modal-title").innerText);
    }
    else{
      alert('error','Thumbnail update failed!','image-alert');
    }
  });
}

function remove_room(room_id) {
  Swal.fire({
    title: 'Archive Room',
    text: 'Are you sure you want to archive this room?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, archive it!',
    cancelButtonText: 'Cancel',
    reverseButtons: true
  }).then((result) => {
    if (result.isConfirmed) {
      // Show loading state
      Swal.fire({
        title: 'Archiving...',
        text: 'Please wait while we archive the room',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });
      
      // Use the new archive_room_fix.php endpoint
      const formData = new FormData();
      formData.append('room_id', room_id);
      
      fetch('archive_room_fix.php', { 
        method: 'POST',
        body: formData
      })
      .then(response => {
        if (!response.ok) {
          return response.text().then(text => {
            throw new Error(text || 'Network response was not ok');
          });
        }
        return response.json();
      })
      .then(data => {
        Swal.close();
        if (data.success) {
          Swal.fire('Success!', data.message || 'Room has been archived successfully.', 'success');
          get_all_rooms();
        } else {
          throw new Error(data.error || 'Failed to archive room');
        }
      })
      .catch(error => {
        Swal.close();
        console.error('Error:', error);
        // Try to parse error message as JSON
        try {
          const errorData = JSON.parse(error.message);
          Swal.fire('Error!', errorData.error || 'Failed to archive room', 'error');
        } catch (e) {
          Swal.fire('Error!', error.message || 'An error occurred while archiving the room', 'error');
        }
      });
    }
  });
}

window.onload = function(){
  get_all_rooms();
}