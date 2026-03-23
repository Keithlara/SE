let page=1, limit=10;

async function postJSON(url, params){
  const body = new URLSearchParams(params).toString();
  const res = await fetch(url, { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body });
  return await res.json();
}

function params(){
  return {
    list: 1,
    page, limit,
    from: document.getElementById('t_from').value,
    to: document.getElementById('t_to').value,
    method: document.getElementById('t_method').value,
    status: document.getElementById('t_status').value,
    search: document.getElementById('t_search').value
  };
}

function get_trans(){
  postJSON('ajax/transactions.php', params())
    .then(res => {
      document.getElementById('table-data').innerHTML = res.table || '<tr><td colspan="7" class="text-center py-4"><b>No Data</b></td></tr>';
      document.getElementById('table-pagination').innerHTML = res.pagination || '';
    });

  const q = new URLSearchParams({from:params().from,to:params().to,method:params().method,status:params().status,search:params().search}).toString();
  document.getElementById('t_export_csv').href = 'ajax/transactions.php?export=csv&'+q;
  document.getElementById('t_export_pdf').href = 'ajax/transactions.php?export=pdf&'+q;
}

function change_page(p){ page = p; get_trans(); }

['t_from','t_to','t_method','t_status','t_search'].forEach(id=>{
  const el = document.getElementById(id); if(el){ el.addEventListener('input', ()=>{ page=1; get_trans(); }); }
});

window.addEventListener('DOMContentLoaded', get_trans);


