<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script src="scripts/admin_theme.js?v=<?php echo filemtime('scripts/admin_theme.js'); ?>"></script>

<script>

  function alert(type,msg,position='body')
  {
    let bs_class = (type == 'success') ? 'alert-success' : 'alert-danger';
    let element = document.createElement('div');
    element.innerHTML = `
      <div class="alert ${bs_class} alert-dismissible fade show" role="alert">
        <strong class="me-3">${msg}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    `;

    if(position=='body'){
      document.body.append(element);
      element.classList.add('custom-alert');
    }
    else{
      document.getElementById(position).appendChild(element);
    }
    setTimeout(remAlert, 2000);
  }

  function remAlert(){
    const firstAlert = document.getElementsByClassName('alert')[0];
    if(firstAlert){ firstAlert.remove(); }
  }

    
  function setActive()
  {
    let navbar = document.getElementById('dashboard-menu');
    if (!navbar) return;
    let a_tags = navbar.getElementsByTagName('a');
    let currentPage = window.location.pathname.split('/').pop().toLowerCase();

    for(let i=0; i<a_tags.length; i++)
    {
      let file = a_tags[i].href.split('/').pop().split('?')[0].toLowerCase();

      if(file && file === currentPage){
        a_tags[i].classList.add('active');
      }

    }
  }
  setActive();

  function getSwalSafe(){
    return (typeof window.Swal !== 'undefined') ? window.Swal : null;
  }

  // admin logout confirmation (delegated)
  document.addEventListener('click', function(e){
    const link = e.target.closest('#admin-logout');
    if(link){
      e.preventDefault();
      const swal = getSwalSafe();
      if(!swal){
        if(window.confirm('Are you sure you want to logout?')){
          window.location.href = link.getAttribute('href');
        }
        return;
      }
      swal.fire({
        title: 'Logout?',
        text: 'Are you sure you want to logout?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, logout',
        cancelButtonText: 'Cancel'
      }).then((res)=>{
        if(res.isConfirmed){
          window.location.href = link.getAttribute('href');
        }
      });
    }
  });

  // SweetAlert helpers for reuse
  function confirmDelete(message='Are you sure you want to delete this?', onConfirm){
    const swal = getSwalSafe();
    if(!swal){
      if(window.confirm(message) && typeof onConfirm==='function'){ onConfirm(); }
      return;
    }
    swal.fire({
      title: 'Confirm Delete',
      text: message,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete',
      cancelButtonText: 'Cancel'
    }).then((res)=>{ if(res.isConfirmed && typeof onConfirm==='function'){ onConfirm(); } });
  }

  function confirmAdd(message='Proceed with this action?', onConfirm){
    const swal = getSwalSafe();
    if(!swal){
      if(window.confirm(message) && typeof onConfirm==='function'){ onConfirm(); }
      return;
    }
    swal.fire({
      title: 'Confirm',
      text: message,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Yes',
      cancelButtonText: 'No'
    }).then((res)=>{ if(res.isConfirmed && typeof onConfirm==='function'){ onConfirm(); } });
  }

  function toastSuccess(message='Done'){
    const swal = getSwalSafe();
    if(!swal){ alert('success', message); return; }
    swal.fire({icon:'success',title:message,timer:1200,showConfirmButton:false});
  }
  function toastError(message='Something went wrong'){
    const swal = getSwalSafe();
    if(!swal){ alert('error', message); return; }
    swal.fire({icon:'error',title:message,timer:1500,showConfirmButton:false});
  }
</script>
