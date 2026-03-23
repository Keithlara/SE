
let carousel_s_form = document.getElementById('carousel_s_form');
let carousel_picture_inp = document.getElementById('carousel_picture_inp');

async function postForm(url, params){
  const body = new URLSearchParams(params).toString();
  const res = await fetch(url, { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body });
  return await res.text();
}
carousel_s_form.addEventListener('submit',function(e){
  e.preventDefault();
  add_image();
});

function add_image()
{
  let data = new FormData();
  data.append('picture',carousel_picture_inp.files[0]);
  data.append('add_image','');
  fetch('ajax/carousel_crud.php', { method:'POST', body:data })
    .then(r=>r.text())
    .then(txt=>{
      var myModal = document.getElementById('carousel-s');
      var modal = bootstrap.Modal.getInstance(myModal);
      modal.hide();
      if(txt == 'inv_img'){
        alert('error','Only JPG and PNG images are allowed!');
      }
      else if(txt == 'inv_size'){
        alert('error','Image should be less than 2MB!');
      }
      else if(txt == 'upd_failed'){
        alert('error','Image upload failed. Server Down!');
      }
      else{
        alert('success','New image added!');
        carousel_picture_inp.value='';
        get_carousel();
      }
    });
}

function get_carousel()
{
  postForm('ajax/carousel_crud.php', { get_carousel: 1 })
    .then(html => { document.getElementById('carousel-data').innerHTML = html; });
}

function rem_image(val)
{
  confirmDelete('Remove this image?', ()=>{
    postForm('ajax/carousel_crud.php', { rem_image: val })
      .then(txt => {
        if(txt==1){
          toastSuccess('Image removed');
          get_carousel();
        } else {
          toastError('Server down');
        }
      });
  });
}

window.onload = function(){
  get_carousel();
}
