let lineChart, pieChart;

function load_report(){
  let params = new URLSearchParams({
    get_metrics: 1,
    granularity: document.getElementById('granularity').value,
    from: document.getElementById('from').value,
    to: document.getElementById('to').value
  }).toString();

  let xhr = new XMLHttpRequest();
  xhr.open('POST','ajax/reports.php',true);
  xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
  xhr.onload = function(){
    let res = JSON.parse(this.responseText);
    const m = res.metrics;
    document.getElementById('metric-reservations').innerText = m.reservations;
    document.getElementById('metric-cancelled').innerText = m.cancelled;
    document.getElementById('metric-revenue').innerText = '₱'+m.revenue;
    document.getElementById('metric-occupancy').innerText = m.occupancy+'%';

    const ctxL = document.getElementById('lineChart').getContext('2d');
    if(lineChart) lineChart.destroy();
    lineChart = new Chart(ctxL, {
      type: 'line', data: { labels: res.labels, datasets: [{ label: 'Bookings', data: res.series, borderColor: '#0d6efd', fill: false }] }, options: { responsive: true }
    });

    const ctxP = document.getElementById('pieChart').getContext('2d');
    if(pieChart) pieChart.destroy();
    const pb = m.payment_breakdown || {};
    const labels = Object.keys(pb);
    const data = Object.values(pb);
    pieChart = new Chart(ctxP, { type: 'pie', data: { labels, datasets: [{ data, backgroundColor: ['#198754','#dc3545','#ffc107','#0dcaf0','#6f42c1'] }] } });
  }
  xhr.send(params);

  document.getElementById('export-csv').href = 'ajax/reports.php?export=csv&'+new URLSearchParams({granularity: document.getElementById('granularity').value, from: document.getElementById('from').value, to: document.getElementById('to').value}).toString();
  document.getElementById('export-pdf').href = 'ajax/reports.php?export=pdf&'+new URLSearchParams({granularity: document.getElementById('granularity').value, from: document.getElementById('from').value, to: document.getElementById('to').value}).toString();
}

window.addEventListener('DOMContentLoaded', load_report);



function render_occupancy(containerId, payload){
  const wrap = document.getElementById(containerId);
  if(!wrap) return;
  wrap.innerHTML = '';
  const rooms = (payload && payload.rooms) ? payload.rooms : [];
  if(rooms.length === 0){
    const empty = document.createElement('div');
    empty.className = 'text-muted';
    empty.textContent = 'No room data available for this date.';
    wrap.appendChild(empty);
    return;
  }
  // Parent card/title/legend are in HTML; only render rows and seats here
  const grid = document.createElement('div');
  grid.className = 'seat-grid';

  rooms.forEach(r => {
    const row = document.createElement('div');
    row.className = 'seat-row';
    const left = document.createElement('div');
    left.className = 'seat-row-label';
    const lname = (r.name || '').toLowerCase();
    left.textContent = lname.includes('couple') ? 'CR' : (lname.includes('deluxe') ? 'DR' : (lname.includes('family') ? 'FR' : 'R'));
    row.appendChild(left);

    (r.seats || []).forEach((s, idx) => {
      const seat = document.createElement('div');
      const status = s.status || (s.occupied ? 'occupied' : 'available');
      const cls = status==='occupied' ? 'occupied' : (status==='pending' ? 'pending' : 'available');
      seat.className = 'seat ' + cls;
      seat.title = (r.name || 'Room') + ' • Room #' + String(idx+1);
      seat.textContent = String(idx+1);
      row.appendChild(seat);
    });

    grid.appendChild(row);
  });

  wrap.appendChild(grid);
}

function load_occupancy_for(dateStr){
  const xhr = new XMLHttpRequest();
  xhr.open('POST','ajax/reports.php',true);
  xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
  xhr.onload = function(){
    try {
      const res = JSON.parse(this.responseText);
      render_occupancy('occ-grid-reports', res);
    } catch(e){
      render_occupancy('occ-grid-reports', { rooms: [] });
    }
  }
  xhr.send('get_occupancy_map=1&date='+encodeURIComponent(dateStr));
}

function trigger_occupancy_load(){
  const el = document.getElementById('occ-date');
  const v = el.value || new Date().toISOString().slice(0,10);
  load_occupancy_for(v);
}

window.addEventListener('DOMContentLoaded', function(){
  const el = document.getElementById('occ-date');
  if(el){ el.value = new Date().toISOString().slice(0,10); }
  trigger_occupancy_load();
});