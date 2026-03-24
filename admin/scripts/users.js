async function postForm(url, params) {
  const body = new URLSearchParams(params).toString();
  const res = await fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body });
  return await res.text();
}

async function get_users() {
  try {
    const html = await postForm('ajax/users.php', { get_users: 1 });
    document.getElementById('users-data').innerHTML = html;
  } catch (e) { /* ignore */ }
}

function toggle_status(id, val) {
  fetch('ajax/users.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'toggle_status=' + encodeURIComponent(id) + '&value=' + encodeURIComponent(val)
  })
    .then(r => r.text())
    .then(txt => {
      if (txt == 1) {
        toastSuccess('Status updated!');
        get_users();
      } else {
        toastError('Could not update status.');
      }
    });
}

function archive_user(user_id) {
  Swal.fire({
    title: 'Archive this user?',
    text: 'The user will be moved to the archive and can be restored later.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#f59e0b',
    confirmButtonText: '<i class="bi bi-archive-fill me-1"></i> Yes, archive',
    cancelButtonText: 'Cancel',
    reverseButtons: true
  }).then((result) => {
    if (!result.isConfirmed) return;

    Swal.fire({
      title: 'Archiving...',
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading()
    });

    fetch('ajax/users.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'archive_user=1&user_id=' + encodeURIComponent(user_id)
    })
      .then(r => r.text())
      .then(txt => {
        Swal.close();
        if (txt == 1) {
          Swal.fire({
            icon: 'success',
            title: 'User Archived',
            text: 'The user has been moved to the archive.',
            timer: 1800,
            showConfirmButton: false
          });
          get_users();
        } else {
          toastError('Failed to archive user. Please try again.');
        }
      })
      .catch(() => {
        Swal.close();
        toastError('Connection error. Please try again.');
      });
  });
}

// Legacy: keep remove_user alias working
function remove_user(user_id) {
  archive_user(user_id);
}

async function search_user(username) {
  try {
    const html = await postForm('ajax/users.php', { search_user: 1, name: username });
    document.getElementById('users-data').innerHTML = html;
  } catch (e) { /* ignore */ }
}

window.onload = function () {
  get_users();
};
