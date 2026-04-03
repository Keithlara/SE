function bytesToSize(bytes){
  if(bytes === 0) return '0 B';
  const k = 1024; const sizes = ['B','KB','MB','GB','TB'];
  const i = Math.floor(Math.log(bytes)/Math.log(k));
  return parseFloat((bytes/Math.pow(k,i)).toFixed(2))+' '+sizes[i];
}

async function postJSON(url, params){
  const body = new URLSearchParams(params).toString();
  const res = await fetch(url, { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body });
  return await res.json();
}

function load_backups(){
  postJSON('ajax/backup_restore.php', { list_backups: 1 })
    .then(res => {
    if(res.dir){ document.getElementById('storage-path').innerText = res.dir; }
    let rows = '';
    if(res.files && res.files.length){
      res.files.forEach((f,idx)=>{
        let d = new Date(f.mtime*1000);
        rows += `<tr>
          <td>${idx+1}</td>
          <td>${f.name}</td>
          <td>${bytesToSize(f.size)}</td>
          <td>${d.toLocaleString()}</td>
          <td>
            <a href="ajax/backup_restore.php?download=${encodeURIComponent(f.name)}" class="btn btn-sm btn-outline-success me-2">Download</a>
            <button class="btn btn-sm btn-outline-danger" onclick="restore_backup('${f.name}')">Restore</button>
          </td>
        </tr>`;
      });
    } else {
      rows = '<tr><td colspan="5"><b>No backups found</b></td></tr>';
    }
    document.getElementById('table-data').innerHTML = rows;
    });
}

function create_backup(){
  const button = document.getElementById('create-backup-btn');
  if(button){
    button.disabled = true;
    button.innerText = 'Creating...';
  }

  fetch('ajax/backup_restore.php', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'create_backup=1' })
    .then(res => res.json())
    .then(res => {
      if(res.status == 1){
        const fileName = res.file ? ` (${res.file})` : '';
        alert('success', 'Backup created successfully' + fileName, 'backup-toast-area');
        load_backups();
      } else {
        alert('error', res.msg || 'Backup failed.', 'backup-toast-area');
      }
    })
    .catch(() => {
      alert('error', 'Could not create backup right now.', 'backup-toast-area');
    })
    .finally(() => {
      if(button){
        button.disabled = false;
        button.innerText = 'Create Backup';
      }
    });
}

function restore_backup(name){
  if(!confirm('This will overwrite the current database. Type OK to proceed.')) return;
  fetch('ajax/backup_restore.php', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'restore_backup=1&file='+encodeURIComponent(name)+'&confirm=YES' })
    .then(res => res.json())
    .then(res => {
      if(res.status == 1){
        alert('success', 'Backup restored successfully.', 'backup-toast-area');
        load_backups();
      } else {
        alert('error', res.msg || 'Restore failed.', 'backup-toast-area');
      }
    })
    .catch(() => {
      alert('error', 'Could not restore backup right now.', 'backup-toast-area');
    });
}

window.addEventListener('DOMContentLoaded', load_backups);

