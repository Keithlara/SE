async function postForm(url, params){
  const body = new URLSearchParams(params).toString();
  const res = await fetch(url, { method: 'POST', headers: { 'Content-Type':'application/x-www-form-urlencoded' }, body });
  return await res.text();
}

async function get_bookings(search = '', type = 'pending') {
  try {
    const response = await fetch('ajax/new_bookings.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `get_bookings=1&search=${encodeURIComponent(search)}&type=${type}`
    });
    
    const html = await response.text();
    const targetId = type === 'pending' ? 'pending-bookings-data' : 'assign-rooms-data';
    document.getElementById(targetId).innerHTML = html;
  } catch (e) {
    console.error('Error loading bookings:', e);
  }
}

// Initialize tabs and load data
document.addEventListener('DOMContentLoaded', function() {
  get_bookings('', 'pending');

  document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(btn => {
    btn.addEventListener('shown.bs.tab', function (e) {
      const target = e.target.getAttribute('data-bs-target');
      if (target === '#assign-rooms') {
        get_bookings('', 'confirmed');
      } else if (target === '#pending-bookings') {
        get_bookings('', 'pending');
      }
    });
  });
});

window.assign_room_form = window.assign_room_form || document.getElementById('assign_room_form');
window.assign_grid = window.assign_grid || document.getElementById('assign-grid');
window.assign_legend = window.assign_legend || document.getElementById('assign-legend');

function assign_room(bookingId, roomTypeId, preselectedRoomNo){
  assign_room_form.elements['booking_id'].value = bookingId;
  // legend is static in HTML
  if(assign_grid){ assign_grid.innerHTML = '<div class="text-muted">Loading...</div>'; }

  const dateStr = new Date().toISOString().slice(0,10);
  fetch('ajax/reports.php', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'get_occupancy_map=1&date='+encodeURIComponent(dateStr) })
    .then(r=>r.json())
    .then(res=>{
      const room = (res.rooms || []).find(r => parseInt(r.room_id) === parseInt(roomTypeId));
      renderAssignGrid(room, preselectedRoomNo);
    })
    .catch(()=>renderAssignGrid(null));
}

function renderAssignGrid(room, preselectedRoomNo){
  if(!assign_grid) return;
  assign_grid.innerHTML = '';
  if(!room || !Array.isArray(room.seats) || room.seats.length===0){
    assign_grid.innerHTML = '<div class="text-muted">No data</div>';
    return;
  }
  const perRow = 10;
  for(let i=0;i<room.seats.length;i+=perRow){
    const row = document.createElement('div');
    row.className = 'seat-row';
    const left = document.createElement('div');
    left.className = 'seat-row-label';
    left.textContent = '';
    row.appendChild(left);
    room.seats.slice(i,i+perRow).forEach((s, idx) => {
      const seat = document.createElement('div');
      const cls = (s.status==='occupied') ? 'occupied' : (s.status==='pending' ? 'pending' : 'available');
      seat.className = 'seat ' + cls;
      const label = String(i + idx + 1);
      seat.textContent = label;
      const currentBookingId = parseInt(assign_room_form.elements['booking_id'].value);
      const belongsToThisBooking = s && s.booking_id && (parseInt(s.booking_id) === currentBookingId);
      const isSelectable = (cls==='available') || (cls==='pending' && belongsToThisBooking);
      seat.title = isSelectable ? 'Click to assign' : (belongsToThisBooking ? 'Selected by guest' : cls);
      seat.style.cursor = isSelectable ? 'pointer' : 'not-allowed';
      if(preselectedRoomNo && label === String(preselectedRoomNo)){
        seat.classList.add('selected');
        assign_room_form.elements['room_no'].value = label;
      }
      if(isSelectable){
        seat.addEventListener('click', function(){
          Array.from(assign_grid.querySelectorAll('.seat.selected')).forEach(el=>el.classList.remove('selected'));
          seat.classList.add('selected');
          assign_room_form.elements['room_no'].value = label;
        });
      }
      row.appendChild(seat);
    });
    assign_grid.appendChild(row);
  }
}

assign_room_form.addEventListener('submit',function(e){
  e.preventDefault();
  let roomNo = assign_room_form.elements['room_no'].value;
  if(!roomNo){ alert('error','Please select an available room'); return; }
  let data = new FormData();
  data.append('room_no', roomNo);
  data.append('booking_id', assign_room_form.elements['booking_id'].value);
  data.append('assign_room','');

  fetch('ajax/new_bookings.php', { method:'POST', body:data })
    .then(r=>r.text())
    .then(txt=>{
      var myModal = document.getElementById('assign-room');
      var modal = bootstrap.Modal.getInstance(myModal);
      modal.hide();
      if(txt==1){
        alert('success','Room Number Alloted! Booking Finalized!');
        assign_room_form.reset();
        if(assign_grid) assign_grid.innerHTML='';
        get_bookings('', 'pending');
        get_bookings('', 'confirmed');
      } else {
        alert('error','Server Down!');
      }
    });
});

async function confirm_booking(booking_id, button) {
    const guestNote = (button && button.dataset && typeof button.dataset.guestNote === 'string') ? button.dataset.guestNote.trim() : '';
    const existingStaffNote = (button && button.dataset && typeof button.dataset.staffNote === 'string') ? button.dataset.staffNote.trim() : '';

    const escapeHtml = (s) => String(s)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');

    // Get the parent container
    const actionContainer = button.closest('.action-buttons');
    
    // Disable all buttons in the container
    if (actionContainer) {
        const buttons = actionContainer.querySelectorAll('button');
        buttons.forEach(btn => {
            btn.disabled = true;
            btn._originalState = {
                html: btn.innerHTML,
                disabled: false
            };
        });
    }
    
    // Update the clicked button
    const originalText = button.innerHTML;
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span>Confirming...';
    button.classList.add('disabled');

    Swal.fire({
        title: 'Confirm Booking',
        html: `
          <div class="text-start">
            <div class="mb-3 p-3 bg-light rounded-3">
              <h6 class="mb-2 fw-bold text-primary">
                <i class="bi bi-info-circle me-2"></i>Booking Confirmation
              </h6>
              <p class="mb-0">Are you sure you want to confirm this booking?</p>
              <p class="text-muted small mb-0">You can add a reply note that the guest will see.</p>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold text-secondary">
                <i class="bi bi-chat-quote me-1"></i>Guest's Request
              </label>
              <div class="p-3 border rounded-3 bg-white" style="min-height: 60px; max-height: 120px; overflow-y: auto;">
                ${guestNote ? `<div class="text-muted small" style="white-space:pre-wrap">${escapeHtml(guestNote)}</div>` : '<div class="text-muted small fst-italic">No special requests from guest</div>'}
              </div>
            </div>

            <div class="mb-3">
              <label for="staff_note" class="form-label fw-semibold">
                <i class="bi bi-reply me-1"></i>Your Reply to Guest
                <span class="text-muted fw-normal">(Optional)</span>
              </label>
              <textarea 
                id="staff_note" 
                class="form-control border-2 rounded-3 p-3" 
                placeholder="Type your reply to the guest... (e.g., 'Welcome! Your room is ready. Check-in time is 2 PM.')" 
                style="min-height: 100px; resize: vertical; font-size: 0.95rem;"
                maxlength="500">${escapeHtml(existingStaffNote)}</textarea>
              <div class="d-flex justify-content-between mt-2">
                <small class="text-muted">Guests will see this message in their notifications</small>
                <small class="text-muted" id="char-count">0 / 500</small>
              </div>
            </div>
          </div>
        `,
        icon: false,
        showCancelButton: true,
        confirmButtonText: '<i class="bi bi-check-circle me-2"></i>Confirm Booking',
        cancelButtonText: '<i class="bi bi-x-circle me-2"></i>Cancel',
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d',
        reverseButtons: true,
        showLoaderOnConfirm: true,
        width: '600px',
        didOpen: () => {
          // Character counter
          const staffNoteEl = document.getElementById('staff_note');
          const charCountEl = document.getElementById('char-count');
          
          if (staffNoteEl && charCountEl) {
            const updateCharCount = () => {
              const length = staffNoteEl.value.length;
              charCountEl.textContent = `${length} / 500`;
              charCountEl.className = length > 450 ? 'text-warning' : 'text-muted';
            };
            
            staffNoteEl.addEventListener('input', updateCharCount);
            updateCharCount(); // Initial count
          }
        },
        preConfirm: async () => {
            try {
                const staffNoteEl = document.getElementById('staff_note');
                const staffNote = staffNoteEl ? staffNoteEl.value.trim() : '';

                const response = await fetch('ajax/confirm_booking.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: `confirm_booking=1&booking_id=${booking_id}&staff_note=${encodeURIComponent(staffNote)}`
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                return await response.json();
            } catch (error) {
                console.error('Error:', error);
                return {
                    status: 'error',
                    message: 'Failed to confirm booking. Please try again.'
                };
            }
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            const response = result.value;
            
            if (response.status === 'success') {
                // Update the button to show confirmed state
                if (button) {
                    button.innerHTML = '<i class="bi bi-check-circle-fill"></i> Confirmed';
                    button.classList.remove('btn-primary');
                    button.classList.add('btn-success');
                    button.disabled = true;
                }

                // Update status badge if it exists
                const statusBadge = document.querySelector(`#booking-${booking_id}-status`);
                if (statusBadge) {
                    statusBadge.className = 'badge bg-success';
                    statusBadge.textContent = 'Confirmed';
                }

                // Show success message
                Swal.fire({
                    title: 'Success!',
                    text: response.message || 'Booking confirmed successfully!',
                    icon: 'success',
                    showConfirmButton: true,
                    confirmButtonText: 'Go to Assign Rooms',
                    showCancelButton: true,
                    cancelButtonText: 'Stay Here',
                    timer: 5000,
                    timerProgressBar: true
                }).then((result) => {
                    // Refresh both tabs
                    get_bookings('', 'pending');
                    get_bookings('', 'confirmed');

                    // If user clicks 'Go to Assign Rooms', switch to that tab
                    if (result.isConfirmed) {
                        const assignTab = document.getElementById('assign-tab');
                        if (assignTab) {
                            const tab = new bootstrap.Tab(assignTab);
                            tab.show();
                        }
                    }
                });
            } else {
                // Re-enable the button if there was an error
                if (button && button._originalState) {
                    button.disabled = button._originalState.disabled;
                    button.innerHTML = button._originalState.html;
                    delete button._originalState;
                }
                
                Swal.fire({
                    title: 'Error!',
                    text: response.message || 'Failed to confirm booking',
                    icon: 'error'
                });
            }
        } else if (button && button._originalState) {
            // Re-enable the button if the user cancels
            button.disabled = button._originalState.disabled;
            button.innerHTML = button._originalState.html;
            delete button._originalState;
        }
    }).catch(error => {
        console.error('Error:', error);
        // Re-enable the button on error
        if (button && button._originalState) {
            button.disabled = button._originalState.disabled;
            button.innerHTML = button._originalState.html;
            delete button._originalState;
        }
        
        Swal.fire(
            'Error!',
            'An unexpected error occurred. Please check the console for details.',
            'error'
        );
    });
}

function cancel_booking(id)
{
  if(confirm("Are you sure, you want to cancel this booking?")){
    let xhr = new XMLHttpRequest();
    xhr.open("POST","ajax/new_bookings.php",true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.setRequestHeader('Accept', 'application/json');

    xhr.onload = function(){
      let response = null;

      try {
        response = JSON.parse(this.responseText);
      } catch (error) {
        if(this.responseText == 1){
          response = { status: 'success', message: 'Booking Cancelled!' };
        } else if(this.responseText == 0){
          response = { status: 'error', message: 'Failed to cancel booking.' };
        }
      }

      if(response && response.status === 'success'){
        alert('success', response.message || 'Booking Cancelled!');
        get_bookings('', 'pending');
        get_bookings('', 'confirmed');
      }
      else{
        alert('error', (response && response.message) ? response.message : 'Failed to cancel booking.');
      }
    }

    xhr.onerror = function(){
      alert('error','Could not reach the booking server. Please try again.');
    }

    xhr.send('cancel_booking=1&booking_id='+id);
  }
}

function viewProof(url){
  if(!url){
    alert('error','No proof uploaded for this booking yet.');
    return;
  }
  const viewer = document.getElementById('payment-proof-viewer');
  const downloadLink = document.getElementById('payment-proof-download');
  if(!viewer || !downloadLink){
    window.open(url,'_blank');
    return;
  }
  const isPdf = /\.pdf($|\?)/i.test(url);
  if(isPdf){
    viewer.innerHTML = `<iframe src="${url}" class="w-100" style="height:70vh;" frameborder="0"></iframe>`;
  }else{
    viewer.innerHTML = `<img src="${url}" class="img-fluid rounded shadow-sm" alt="Payment proof">`;
  }
  downloadLink.href = url;
  const modalEl = document.getElementById('payment-proof-modal');
  if(modalEl){
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
  }else{
    window.open(url,'_blank');
  }
}
