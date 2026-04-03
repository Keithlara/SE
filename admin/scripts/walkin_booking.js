(function () {
  const form = document.getElementById('walkin-booking-form');
  if (!form) return;

  const currency = amount => `PHP ${Number(amount || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
  const data = window.walkInBookingData || { rooms: [], extrasEnabled: false };
  let lastAvailability = null;

  const guestModeInputs = Array.from(document.querySelectorAll('input[name="guest_mode"]'));
  const guestPanelExisting = document.getElementById('existing-guest-panel');
  const guestPanelManual = document.getElementById('manual-guest-panel');
  const roomSelect = document.getElementById('room_id');
  const roomNoInput = document.getElementById('room_no');
  const roomGrid = document.getElementById('walkin-assign-grid');
  const paymentStatus = document.getElementById('payment_status');
  const amountReceived = document.getElementById('amount_received');
  const statusBox = document.getElementById('walkin-status-box');
  const queueBody = document.getElementById('walkin-payment-queue-body');
  let availabilityTimer = null;

  function setStatus(message, tone = 'info') {
    const map = {
      info: ['#1e40af', '#f8fbff', '#bfdbfe'],
      success: ['#166534', '#f0fdf4', '#86efac'],
      warning: ['#92400e', '#fffbeb', '#fcd34d'],
      danger: ['#b91c1c', '#fef2f2', '#fca5a5']
    };
    const [color, bg, border] = map[tone] || map.info;
    statusBox.textContent = message;
    statusBox.style.color = color;
    statusBox.style.background = bg;
    statusBox.style.borderColor = border;
  }

  function getGuestMode() {
    const selected = guestModeInputs.find(input => input.checked);
    return selected ? selected.value : 'existing';
  }

  function syncGuestMode() {
    const manual = getGuestMode() === 'manual';
    guestPanelExisting.classList.toggle('hidden-panel', manual);
    guestPanelManual.classList.toggle('hidden-panel', !manual);
  }

  function getRoomMeta() {
    const option = roomSelect.selectedOptions[0];
    if (!option || !option.value) return null;
    return {
      id: Number(option.value),
      name: option.dataset.name || option.textContent.trim(),
      price: Number(option.dataset.price || 0),
      quantity: Number(option.dataset.quantity || 0),
      adult: Number(option.dataset.adult || 0),
      children: Number(option.dataset.children || 0)
    };
  }

  function getNightCount() {
    const checkIn = document.getElementById('check_in').value;
    const checkOut = document.getElementById('check_out').value;
    if (!checkIn || !checkOut) return 0;
    const start = new Date(`${checkIn}T00:00:00`);
    const end = new Date(`${checkOut}T00:00:00`);
    const diff = Math.round((end - start) / 86400000);
    return diff > 0 ? diff : 0;
  }

  function getSelectedExtras() {
    return Array.from(document.querySelectorAll('.extra-qty-input')).reduce((items, input) => {
      const quantity = Number(input.value || 0);
      if (quantity > 0) {
        items.push({
          id: Number(input.dataset.extraId),
          name: input.dataset.extraName || '',
          unit_price: Number(input.dataset.extraPrice || 0),
          qty: quantity
        });
      }
      return items;
    }, []);
  }

  function updateExtraLabels() {
    const nights = getNightCount();
    document.querySelectorAll('.extra-qty-input').forEach(input => {
      const quantity = Number(input.value || 0);
      const total = quantity * Number(input.dataset.extraPrice || 0) * nights;
      const totalLabel = document.querySelector(`[data-extra-total-for="${input.dataset.extraId}"]`);
      if (totalLabel) {
        totalLabel.textContent = currency(total);
      }
    });
  }

  function updateSummary() {
    const roomMeta = getRoomMeta();
    const nights = getNightCount();
    const roomTotal = roomMeta ? roomMeta.price * nights : 0;
    const extrasTotal = getSelectedExtras().reduce((sum, item) => sum + (item.unit_price * item.qty * nights), 0);
    const grandTotal = roomTotal + extrasTotal;
    const paid = Number(amountReceived.value || 0);
    const balance = Math.max(0, grandTotal - paid);

    document.getElementById('summary_nights').textContent = String(nights);
    document.getElementById('summary_room_total').textContent = currency(roomTotal);
    document.getElementById('summary_extras_total').textContent = currency(extrasTotal);
    document.getElementById('summary_balance_due').textContent = currency(balance);
    updateExtraLabels();
  }

  function syncPaymentStatus() {
    const total = parseFloat((document.getElementById('summary_room_total').textContent || '').replace(/[^\d.]/g, '')) +
      parseFloat((document.getElementById('summary_extras_total').textContent || '').replace(/[^\d.]/g, ''));
    if (paymentStatus.value === 'pending') {
      amountReceived.value = '0.00';
      amountReceived.readOnly = true;
    } else if (paymentStatus.value === 'paid') {
      amountReceived.readOnly = false;
      if (!Number(amountReceived.value || 0) && total > 0) {
        amountReceived.value = total.toFixed(2);
      }
    } else {
      amountReceived.readOnly = false;
    }
    updateSummary();
  }

  async function postForm(dataObj) {
    const body = new URLSearchParams();
    Object.entries(dataObj).forEach(([key, value]) => body.append(key, value));
    const response = await fetch('ajax/walkin_booking.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: body.toString()
    });
    const text = await response.text();
    try {
      return JSON.parse(text);
    } catch (error) {
      console.error('Invalid walk-in booking response:', text);
      throw new Error('Invalid server response.');
    }
  }

  async function loadPaymentQueue() {
    if (!queueBody) return;
    queueBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Loading walk-in payment queue...</td></tr>';

    try {
      const response = await postForm({ action: 'get_walkin_payment_queue' });
      if (!response.success) {
        queueBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">${response.message || 'Unable to load walk-in payment queue.'}</td></tr>`;
        return;
      }
      queueBody.innerHTML = response.table_html || '<tr><td colspan="6" class="text-center text-muted py-4">No pending walk-in payments right now.</td></tr>';
    } catch (error) {
      queueBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">${error.message || 'Unable to load walk-in payment queue.'}</td></tr>`;
    }
  }

  function buildPayload(action) {
    const formData = new FormData(form);
    const payload = {
      action,
      guest_mode: getGuestMode(),
      guest_id: formData.get('guest_id') || '',
      guest_name: formData.get('guest_name') || '',
      guest_email: formData.get('guest_email') || '',
      guest_phone: formData.get('guest_phone') || '',
      guest_address: formData.get('guest_address') || '',
      room_id: formData.get('room_id') || '',
      room_no: formData.get('room_no') || '',
      check_in: formData.get('check_in') || '',
      check_out: formData.get('check_out') || '',
      adults: formData.get('adults') || '',
      children: formData.get('children') || '',
      walkin_note: formData.get('walkin_note') || '',
      payment_status: formData.get('payment_status') || '',
      payment_method: formData.get('payment_method') || '',
      amount_received: formData.get('amount_received') || '0',
      payment_note: formData.get('payment_note') || '',
      extras_json: JSON.stringify(getSelectedExtras())
    };
    return payload;
  }

  function clearRoomGrid(message) {
    roomNoInput.value = '';
    roomGrid.innerHTML = `<div class="text-muted">${message}</div>`;
  }

  function maybeAutoCheckAvailability() {
    const roomMeta = getRoomMeta();
    const checkIn = document.getElementById('check_in').value;
    const checkOut = document.getElementById('check_out').value;

    window.clearTimeout(availabilityTimer);

    if (!roomMeta || !checkIn || !checkOut) {
      clearRoomGrid('Choose the room and stay dates to load available room numbers automatically.');
      setStatus('Choose the room and stay dates to load room numbers automatically.', 'info');
      lastAvailability = null;
      return;
    }

    availabilityTimer = window.setTimeout(() => {
      checkAvailability(false);
    }, 220);
  }

  function renderRoomGrid(seats, selectedValue = '') {
    roomGrid.innerHTML = '';
    roomNoInput.value = '';

    if (!Array.isArray(seats) || seats.length === 0) {
      clearRoomGrid('No room map is available for the selected stay dates.');
      return;
    }

    const perRow = 10;
    for (let i = 0; i < seats.length; i += perRow) {
      const row = document.createElement('div');
      row.className = 'seat-row';

      seats.slice(i, i + perRow).forEach(seatMeta => {
        const seat = document.createElement('div');
        const label = String(seatMeta.label || '');
        const status = seatMeta.status || 'available';
        seat.className = `seat ${status}`;
        seat.textContent = label;
        seat.title = status === 'available' ? 'Click to select this room number' : status;

        if (status === 'available') {
          seat.addEventListener('click', () => {
            Array.from(roomGrid.querySelectorAll('.seat.selected')).forEach(node => node.classList.remove('selected'));
            seat.classList.add('selected');
            roomNoInput.value = label;
            updateSummary();
          });

          if (selectedValue && label === String(selectedValue)) {
            seat.classList.add('selected');
            roomNoInput.value = label;
          }
        }

        row.appendChild(seat);
      });

      roomGrid.appendChild(row);
    }
  }

  async function checkAvailability(showPopup = true) {
    updateSummary();
    const payload = buildPayload('check_availability');
    const roomMeta = getRoomMeta();
    if (!roomMeta || !payload.check_in || !payload.check_out) {
      setStatus('Choose the room and valid stay dates before checking availability.', 'warning');
      if (showPopup) {
        Swal.fire('Missing details', 'Choose the room and valid stay dates first.', 'warning');
      }
      return false;
    }

    try {
      setStatus('Checking room availability against the live booking calendar...', 'info');
      const response = await postForm(payload);
      if (!response.success) {
        lastAvailability = null;
        clearRoomGrid('No room numbers are available for the selected dates.');
        setStatus(response.message || 'The selected room is not available for the chosen dates.', 'danger');
        if (showPopup) {
          Swal.fire('Unavailable', response.message || 'The selected room is not available.', 'error');
        }
        return false;
      }

      lastAvailability = response;
      renderRoomGrid(response.seats || [], payload.room_no || '');
      setStatus(response.message || 'Room numbers loaded automatically for this walk-in booking.', 'success');
      if (showPopup) {
        Swal.fire('Available', response.message || 'Room is available.', 'success');
      }
      return true;
    } catch (error) {
      lastAvailability = null;
      setStatus(error.message || 'Failed to check room availability.', 'danger');
      if (showPopup) {
        Swal.fire('Error', error.message || 'Failed to check room availability.', 'error');
      }
      return false;
    }
  }

  async function submitWalkInBooking(event) {
    event.preventDefault();
    updateSummary();

    if (!lastAvailability) {
      const okay = await checkAvailability(false);
      if (!okay) {
        Swal.fire('Room map not ready', 'Wait for the room map to finish loading, then choose an available room number.', 'warning');
        return;
      }
    }

    const payload = buildPayload('create_walkin_booking');
    const roomNo = roomNoInput.value || '';
    if (!roomNo) {
      setStatus('Select an available room number from the map before confirming the booking.', 'warning');
      Swal.fire('Select room number', 'Please click an available room number in the map first.', 'warning');
      return;
    }
    payload.room_no = roomNo;

    const result = await Swal.fire({
      title: 'Confirm walk-in booking?',
      text: 'This will create a live booking record, transaction, and booking history entry.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Confirm Booking',
      cancelButtonText: 'Cancel',
      reverseButtons: true
    });

    if (!result.isConfirmed) return;

    const submitButton = document.getElementById('confirm-walkin-btn');
    submitButton.disabled = true;

    try {
      setStatus('Saving the walk-in booking to booking records, transactions, and calendar...', 'info');
      const response = await postForm(payload);
      if (!response.success) {
        setStatus(response.message || 'Unable to create the walk-in booking.', 'danger');
        Swal.fire('Booking failed', response.message || 'Unable to create the walk-in booking.', 'error');
        return;
      }

      setStatus(response.message || 'Walk-in booking created successfully.', 'success');
      await Swal.fire({
        icon: 'success',
        title: 'Walk-in booking created',
        html: `
          <div class="text-start">
            <div><strong>Order ID:</strong> ${response.order_id || 'N/A'}</div>
            <div><strong>Booking ID:</strong> ${response.booking_id || 'N/A'}</div>
            <div><strong>Payment Status:</strong> ${response.payment_status || 'N/A'}</div>
          </div>`,
        confirmButtonText: response.payment_status === 'paid' ? 'Open Booking Records' : 'Stay on Walk-In Booking'
      });
      loadPaymentQueue();
      if (response.payment_status === 'paid') {
        window.location.href = 'booking_records.php';
      } else {
        form.reset();
        roomNoInput.value = '';
        syncGuestMode();
        clearRoomGrid('Choose the room and stay dates to load available room numbers automatically.');
        setStatus('Walk-in booking saved to the payment queue. Complete the remaining payment below when ready.', 'success');
        updateSummary();
        syncPaymentStatus();
      }
    } catch (error) {
      setStatus(error.message || 'Unable to create the walk-in booking.', 'danger');
      Swal.fire('Error', error.message || 'Unable to create the walk-in booking.', 'error');
    } finally {
      submitButton.disabled = false;
    }
  }

  guestModeInputs.forEach(input => input.addEventListener('change', syncGuestMode));
  document.addEventListener('click', event => {
    const qtyButton = event.target.closest('.js-extra-qty-btn');
    if (!qtyButton) return;

    const targetId = qtyButton.dataset.extraTarget;
    const delta = Number(qtyButton.dataset.extraDelta || 0);
    const input = targetId ? document.getElementById(targetId) : null;
    if (!input) return;

    const nextValue = Math.max(0, Number(input.value || 0) + delta);
    input.value = String(nextValue);
    lastAvailability = null;
    updateSummary();
  });
  document.addEventListener('click', async event => {
    const settleButton = event.target.closest('.js-walkin-settle');
    if (!settleButton) return;

    const bookingId = settleButton.dataset.bookingId;
    const orderId = settleButton.dataset.orderId || 'this booking';
    const balanceDue = settleButton.dataset.balanceDue || '0.00';

    const result = await Swal.fire({
      title: 'Mark walk-in as paid?',
      html: `
        <div class="text-start">
          <div class="mb-2"><strong>Order:</strong> ${orderId}</div>
          <div class="mb-3"><strong>Balance Due:</strong> PHP ${balanceDue}</div>
          <label for="swal-walkin-method" class="form-label fw-semibold">Payment Method</label>
          <select id="swal-walkin-method" class="form-select mb-3">
            <option value="cash">Cash</option>
            <option value="gcash">GCash</option>
            <option value="maya">Maya</option>
            <option value="bank">Bank Transfer</option>
          </select>
          <label for="swal-walkin-note" class="form-label fw-semibold">Payment Note</label>
          <input id="swal-walkin-note" class="form-control" placeholder="Optional receipt or front desk note">
        </div>`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Mark as Paid',
      cancelButtonText: 'Cancel',
      preConfirm: () => ({
        payment_method: document.getElementById('swal-walkin-method').value,
        payment_note: document.getElementById('swal-walkin-note').value.trim()
      })
    });

    if (!result.isConfirmed) return;

    try {
      const response = await postForm({
        action: 'settle_walkin_payment',
        booking_id: bookingId,
        payment_method: result.value.payment_method,
        payment_note: result.value.payment_note
      });

      if (!response.success) {
        Swal.fire('Error', response.message || 'Unable to complete the walk-in payment.', 'error');
        return;
      }

      queueBody.innerHTML = response.table_html || '<tr><td colspan="6" class="text-center text-muted py-4">No pending walk-in payments right now.</td></tr>';
      Swal.fire('Paid', response.message || 'Walk-in payment completed.', 'success');
    } catch (error) {
      Swal.fire('Error', error.message || 'Unable to complete the walk-in payment.', 'error');
    }
  });
  ['change', 'input'].forEach(evt => {
    form.addEventListener(evt, e => {
      if (e.target.matches('#room_id, #check_in, #check_out, #adults, #children, #amount_received, .extra-qty-input')) {
        lastAvailability = null;
        clearRoomGrid('Refreshing available room numbers for the updated stay details...');
        updateSummary();
        if (e.target.matches('#room_id, #check_in, #check_out, #adults, #children')) {
          maybeAutoCheckAvailability();
        }
      }
    });
  });
  paymentStatus.addEventListener('change', () => {
    lastAvailability = null;
    syncPaymentStatus();
  });
  const refreshQueueBtn = document.getElementById('refresh-walkin-queue-btn');
  if (refreshQueueBtn) {
    refreshQueueBtn.addEventListener('click', loadPaymentQueue);
  }
  form.addEventListener('submit', submitWalkInBooking);

  syncGuestMode();
  clearRoomGrid('Choose the room and stay dates to load available room numbers automatically.');
  setStatus('Choose the room and stay dates to load room numbers automatically.', 'info');
  updateSummary();
  syncPaymentStatus();
  loadPaymentQueue();
})();
