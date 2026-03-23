async function postJSON(url, params){
  const body = new URLSearchParams(params).toString();
  const res = await fetch(url, { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body });
  return await res.json();
}

function get_bookings(search='',page=1)
{
  const monthSelect = document.getElementById('filter_month');
  const yearSelect = document.getElementById('filter_year');
  const statusSelect = document.getElementById('filter_status');

  const month = monthSelect ? monthSelect.value : '';
  const year = yearSelect ? yearSelect.value : '';
  const status = statusSelect ? statusSelect.value : 'all';

  postJSON('ajax/booking_records.php', { get_bookings: 1, search, page, month, year, status })
    .then(data => {
      document.getElementById('table-data').innerHTML = data.table_data;
      document.getElementById('table-pagination').innerHTML = data.pagination;
    });
}

function change_page(page){
  get_bookings(document.getElementById('search_input').value,page);
}

function applyFilters(){
  get_bookings(document.getElementById('search_input').value,1);
}

function download(id){
  window.location.href = 'generate_pdf.php?gen_pdf&id='+id;
}


window.onload = function(){
  get_bookings();
}