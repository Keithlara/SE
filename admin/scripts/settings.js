
let general_data, contacts_data, payment_data;
async function postForm(url, params){
  const body = new URLSearchParams(params).toString();
  const res = await fetch(url, { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body });
  return await res.text();
}

async function postJSON(url, params){
  const body = new URLSearchParams(params).toString();
  const res = await fetch(url, { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body });
  return await res.json();
}

let general_s_form = document.getElementById('general_s_form');
let site_title_inp = document.getElementById('site_title_inp');
let site_about_inp = document.getElementById('site_about_inp');

let contacts_s_form = document.getElementById('contacts_s_form');

let team_s_form = document.getElementById('team_s_form');
let member_name_inp = document.getElementById('member_name_inp');
let member_picture_inp = document.getElementById('member_picture_inp');
let payment_s_form = document.getElementById('payment_s_form');
let payment_gcash_number_inp = document.getElementById('payment_gcash_number_inp');
let payment_maya_number_inp = document.getElementById('payment_maya_number_inp');

function get_general()
{
  let site_title = document.getElementById('site_title');
  let site_about = document.getElementById('site_about');

  let shutdown_toggle = document.getElementById('shutdown-toggle');
  postJSON('ajax/settings_crud.php', { get_general: 1 })
    .then(data => {
      general_data = data;
      site_title.innerText = general_data.site_title;
      site_about.innerText = general_data.site_about;
      site_title_inp.value = general_data.site_title;
      site_about_inp.value = general_data.site_about;
      if(general_data.shutdown == 0){
        shutdown_toggle.checked = false;
        shutdown_toggle.value = 0;
      } else {
        shutdown_toggle.checked = true;
        shutdown_toggle.value = 1;
      }
    });
}

general_s_form.addEventListener('submit',function(e){
  e.preventDefault();
  upd_general(site_title_inp.value,site_about_inp.value);
});

function upd_general(site_title_val,site_about_val)
{
  postForm('ajax/settings_crud.php', { site_title: site_title_val, site_about: site_about_val, upd_general: 1 })
    .then(txt => {
      var myModal = document.getElementById('general-s');
      var modal = bootstrap.Modal.getInstance(myModal);
      modal.hide();
      if(txt == 1){
        alert('success','Changes saved!');
        get_general();
      } else {
        alert('error','No changes made!');
      }
    });
}

function upd_shutdown(val)
{
  postForm('ajax/settings_crud.php', { upd_shutdown: val })
    .then(txt => {
      if(txt == 1 && general_data.shutdown==0){
        alert('success','Site has been shutdown!');
      } else {
        alert('success','Shutdown mode off!');
      }
      get_general();
    });
}

function get_contacts()
{
  let contacts_p_id = ['address','gmap','pn1','pn2','email','fb','insta','tw'];
  let iframe = document.getElementById('iframe');
  postJSON('ajax/settings_crud.php', { get_contacts: 1 })
    .then(json => {
      contacts_data = Object.values(json);
      for(i=0;i<contacts_p_id.length;i++){
        document.getElementById(contacts_p_id[i]).innerText = contacts_data[i+1];
      }
      iframe.src = contacts_data[9];
      contacts_inp(contacts_data);
    });
}

function contacts_inp(data)
{
  let contacts_inp_id = ['address_inp','gmap_inp','pn1_inp','pn2_inp','email_inp','fb_inp','insta_inp','tw_inp','iframe_inp'];

  for(i=0;i<contacts_inp_id.length;i++){
    document.getElementById(contacts_inp_id[i]).value = data[i+1];
  }
}

contacts_s_form.addEventListener('submit',function(e){
  e.preventDefault();
  upd_contacts();
});

function upd_contacts()
{
  let index = ['address','gmap','pn1','pn2','email','fb','insta','tw','iframe'];
  let contacts_inp_id = ['address_inp','gmap_inp','pn1_inp','pn2_inp','email_inp','fb_inp','insta_inp','tw_inp','iframe_inp'];
  
  let data_str="";

  for(i=0;i<index.length;i++){
    data_str += index[i] + "=" + document.getElementById(contacts_inp_id[i]).value + '&';
  }
  data_str += "upd_contacts";
  fetch('ajax/settings_crud.php', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:data_str })
    .then(r=>r.text())
    .then(txt=>{
      var myModal = document.getElementById('contacts-s');
      var modal = bootstrap.Modal.getInstance(myModal);
      modal.hide();
      if(txt == 1){
        alert('success','Changes saved!');
        get_contacts();
      } else {
        alert('error','No changes made!');
      }
    });
}

team_s_form.addEventListener('submit',function(e){
  e.preventDefault();
  add_member();
});

payment_s_form.addEventListener('submit', function(e){
  e.preventDefault();
  upd_payment_settings();
});

function get_payment_settings(){
  postJSON('ajax/settings_crud.php', { get_payment_settings: 1 })
    .then(data => {
      payment_data = data;
      document.getElementById('payment_gcash_number').innerText = data.payment_gcash_number || 'Not set';
      document.getElementById('payment_maya_number').innerText = data.payment_maya_number || 'Not set';

      const gcashPreview = document.getElementById('payment_gcash_qr_preview');
      const mayaPreview = document.getElementById('payment_maya_qr_preview');

      gcashPreview.src = data.payment_gcash_qr || '';
      gcashPreview.style.display = data.payment_gcash_qr ? 'block' : 'none';
      mayaPreview.src = data.payment_maya_qr || '';
      mayaPreview.style.display = data.payment_maya_qr ? 'block' : 'none';

      payment_gcash_number_inp.value = data.payment_gcash_number || '';
      payment_maya_number_inp.value = data.payment_maya_number || '';
    });
}

function upd_payment_settings(){
  const formData = new FormData(payment_s_form);
  formData.append('upd_payment_settings', '');

  fetch('ajax/settings_crud.php', { method:'POST', body: formData })
    .then(r=>r.json())
    .then(res=>{
      var myModal = document.getElementById('payment-s');
      var modal = bootstrap.Modal.getInstance(myModal);
      modal.hide();
      if(res.status === 'success'){
        alert('success','Payment settings updated!');
        payment_s_form.reset();
        get_payment_settings();
      } else {
        alert('error', res.message || 'Failed to update payment settings');
      }
    });
}

function add_member()
{
  let data = new FormData();
  data.append('name',member_name_inp.value);
  data.append('picture',member_picture_inp.files[0]);
  data.append('add_member','');
  fetch('ajax/settings_crud.php', { method:'POST', body:data })
    .then(r=>r.text())
    .then(txt=>{
      var myModal = document.getElementById('team-s');
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
        alert('success','New member added!');
        member_name_inp.value='';
        member_picture_inp.value='';
        get_members();
      }
    });
}

function get_members()
{
  postForm('ajax/settings_crud.php', { get_members: 1 })
    .then(html => { document.getElementById('team-data').innerHTML = html; });
}

function rem_member(val)
{
  confirmDelete('Remove this team member?', ()=>{
    fetch('ajax/settings_crud.php', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'rem_member='+encodeURIComponent(val) })
      .then(r=>r.text())
      .then(txt=>{
        if(txt==1){
          toastSuccess('Member removed');
          get_members();
        } else {
          toastError('Server down');
        }
      });
  });
}

window.onload = function(){
  get_general();
  get_contacts();
  get_payment_settings();
  get_members();
}
