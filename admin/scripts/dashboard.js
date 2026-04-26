// Simple helper to POST form data and get JSON back
async function postForm(url, params){
  const body = new URLSearchParams(params).toString();
  const res = await fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body
  });
  return await res.json();
}

async function booking_analytics(period=1)
{
  try{
    const data = await postForm('ajax/dashboard.php', { booking_analytics: 1, period });
    document.getElementById('total_bookings').textContent = data.total_bookings;
    document.getElementById('total_amt').textContent = '₱'+data.total_amt;
    document.getElementById('active_bookings').textContent = data.active_bookings;
    document.getElementById('active_amt').textContent = '₱'+data.active_amt;
    document.getElementById('cancelled_bookings').textContent = data.cancelled_bookings;
    document.getElementById('cancelled_amt').textContent = '₱'+data.cancelled_amt;
  }catch(e){
    // keep quiet; cards will keep previous values
  }
}

async function user_analytics(period=1)
{
  try{
    const data = await postForm('ajax/dashboard.php', { user_analytics: 1, period });
    document.getElementById('total_new_reg').textContent = data.total_new_reg;
    document.getElementById('total_queries').textContent = data.total_queries;
    document.getElementById('total_reviews').textContent = data.total_reviews;
  }catch(e){
  }
}


window.onload = function(){
  booking_analytics();
  user_analytics();
  load_today_occupancy();
}

async function load_today_occupancy(){
  const container = document.getElementById('occ-grid-dashboard');
  if(!container) return;
  container.innerHTML = '<div class="text-muted">Loading...</div>';
  const dateStr = new Date().toISOString().slice(0,10);
  try{
    const res = await postForm('ajax/reports.php', { get_occupancy_map: 1, date: dateStr });
    renderOccGrid(container, res.rooms || []);
  }catch(e){
    container.innerHTML = '<div class="text-danger">Failed to load</div>';
  }
}

function renderOccGrid(container, rooms){
  container.innerHTML = '';
  if(!rooms.length){ container.innerHTML = '<div class="text-muted">No data</div>'; return; }

  // Parent is already a card; we only fill the grid
  const grid = document.createElement('div');
  grid.className = 'seat-grid';

  rooms.forEach(r => {
    const row = document.createElement('div');
    row.className = 'seat-row';
    const left = document.createElement('div');
    left.className = 'seat-row-label';
    left.textContent = String(r.name || 'Room');
    left.title = String(r.name || 'Room');
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

  container.innerHTML = '';
  container.appendChild(grid);
}
