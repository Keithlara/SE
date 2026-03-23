async function postForm(url, params){
  const body = new URLSearchParams(params).toString();
  const res = await fetch(url, { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body });
  return await res.text();
}

async function get_users()
{
  try{
    const html = await postForm('ajax/users.php', { get_users: 1 });
    document.getElementById('users-data').innerHTML = html;
  }catch(e){ /* ignore */ }
}


function toggle_status(id,val)
{
  fetch('ajax/users.php', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'toggle_status='+encodeURIComponent(id)+'&value='+encodeURIComponent(val) })
    .then(r=>r.text())
    .then(txt=>{
      if(txt==1){
        alert('success','Status toggled!');
        get_users();
      } else {
        alert('error','Server Down!');
      }
    });
}

function remove_user(user_id)
{
  confirmDelete('Remove this user?', ()=>{
    let data = new FormData();
    data.append('user_id',user_id);
    data.append('remove_user','');
    fetch('ajax/users.php', { method:'POST', body:data })
      .then(r=>r.text())
      .then(txt=>{
        if(txt==1){
          toastSuccess('User removed');
          get_users();
        } else {
          toastError('User removal failed');
        }
      });
  });
}

async function search_user(username){
  try{
    const html = await postForm('ajax/users.php', { search_user: 1, name: username });
    document.getElementById('users-data').innerHTML = html;
  }catch(e){ /* ignore */ }
}

window.onload = function(){
  get_users();
}