<?php
require('inc/essentials.php');
require('inc/db_config.php');
adminLogin();
requireAdminPermission('bookings.manage');

$guestRows = [];
$guestRes = mysqli_query($con, "SELECT `id`,`name`,`email`,`phonenum` FROM `user_cred` WHERE `is_archived`=0 ORDER BY `name` ASC");
if ($guestRes) {
  while ($row = mysqli_fetch_assoc($guestRes)) {
    $guestRows[] = $row;
  }
}

$roomRows = [];
$roomRes = mysqli_query($con, "SELECT `id`,`name`,`price`,`quantity`,`adult`,`children` FROM `rooms` WHERE `removed`=0 AND `status`=1 AND `is_archived`=0 ORDER BY `name` ASC");
if ($roomRes) {
  while ($row = mysqli_fetch_assoc($roomRes)) {
    $roomRows[] = $row;
  }
}

$extrasRows = [];
if (appSchemaTableExists($con, 'extras')) {
  $extrasRes = mysqli_query($con, "SELECT `id`,`name`,`price`,`description` FROM `extras` WHERE `status`=1 ORDER BY `name` ASC");
  if ($extrasRes) {
    while ($row = mysqli_fetch_assoc($extrasRes)) {
      $extrasRows[] = $row;
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Walk-In Booking</title>
  <?php require('inc/links.php'); ?>
  <style>
    .walkin-shell { display:grid; gap:20px; }
    .walkin-card { border:0; border-radius:20px; box-shadow:0 20px 45px rgba(15,23,42,.08); overflow:hidden; }
    .walkin-head { display:flex; justify-content:space-between; gap:18px; align-items:flex-start; margin-bottom:20px; }
    .walkin-title { margin:0; font-size:1.7rem; font-weight:800; color:#0f172a; }
    .walkin-subtitle { margin:6px 0 0; color:#64748b; max-width:720px; }
    .walkin-pill { display:inline-flex; align-items:center; gap:8px; padding:10px 14px; border-radius:999px; background:#e0f2fe; color:#075985; font-weight:700; font-size:.82rem; }
    .walkin-grid { display:grid; grid-template-columns:repeat(12,minmax(0,1fr)); gap:16px; }
    .walkin-section { grid-column:span 12; border:1px solid #e2e8f0; border-radius:18px; background:linear-gradient(180deg,#fff 0%,#f8fafc 100%); padding:18px; }
    .walkin-section h5 { margin:0 0 14px; font-weight:800; color:#0f172a; }
    .walkin-section small { color:#64748b; }
    .span-3 { grid-column:span 3; }
    .span-4 { grid-column:span 4; }
    .span-6 { grid-column:span 6; }
    .span-8 { grid-column:span 8; }
    .span-12 { grid-column:span 12; }
    .walkin-form label { display:block; margin-bottom:7px; font-size:.82rem; font-weight:800; color:#475569; text-transform:uppercase; letter-spacing:.04em; }
    .walkin-form .form-control, .walkin-form .form-select { min-height:48px; border-radius:14px; border-color:#cbd5e1; }
    .walkin-form .form-control:focus, .walkin-form .form-select:focus { border-color:#2563eb; box-shadow:0 0 0 4px rgba(37,99,235,.12); }
    .walkin-choice-row { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:14px; }
    .walkin-choice { flex:1 1 220px; }
    .walkin-choice input { display:none; }
    .walkin-choice span { display:flex; align-items:center; justify-content:center; min-height:48px; border:1px solid #cbd5e1; border-radius:14px; background:#fff; font-weight:700; color:#334155; cursor:pointer; }
    .walkin-choice input:checked + span { border-color:#2563eb; background:#eff6ff; color:#1d4ed8; box-shadow:0 0 0 3px rgba(37,99,235,.12); }
    .walkin-summary { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px; }
    .walkin-kpi { border:1px solid #dbeafe; border-radius:16px; background:#fff; padding:14px; }
    .walkin-kpi .label { display:block; font-size:.76rem; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:.05em; margin-bottom:6px; }
    .walkin-kpi .value { font-size:1.15rem; font-weight:800; color:#0f172a; }
    .walkin-actions { display:flex; gap:12px; flex-wrap:wrap; justify-content:flex-end; }
    .walkin-status { min-height:54px; border-radius:16px; border:1px dashed #bfdbfe; background:#f8fbff; padding:14px 16px; color:#1e40af; font-weight:600; }
    .walkin-queue-table th, .walkin-queue-table td { vertical-align: middle; }
    .extra-row { display:grid; grid-template-columns:minmax(0,1fr) 120px 120px; gap:12px; align-items:center; padding:12px 0; border-bottom:1px solid #e2e8f0; }
    .extra-row:last-child { border-bottom:0; padding-bottom:0; }
    .extra-meta { color:#64748b; font-size:.9rem; margin-top:4px; }
    .extra-qty-wrap { display:flex; align-items:center; gap:6px; }
    .extra-qty-btn {
      width:34px; height:34px; border-radius:10px; border:1px solid #cbd5e1; background:#fff;
      color:#334155; font-weight:800; display:inline-flex; align-items:center; justify-content:center;
    }
    .extra-qty-btn:hover { background:#f8fafc; border-color:#94a3b8; color:#0f172a; }
    .extra-qty-input { text-align:center; min-width:52px; }
    .legend { display:flex; flex-wrap:wrap; gap:10px; }
    .legend-item { display:inline-flex; align-items:center; gap:6px; font-size:.82rem; color:#64748b; font-weight:700; }
    .legend-swatch { width:14px; height:14px; border-radius:999px; display:inline-block; }
    .legend-swatch.available { background:#dcfce7; border:1px solid #22c55e; }
    .legend-swatch.pending { background:#fef3c7; border:1px solid #f59e0b; }
    .legend-swatch.occupied { background:#fee2e2; border:1px solid #ef4444; }
    .seat-grid { display:grid; gap:10px; }
    .seat-row { display:grid; grid-template-columns:repeat(10, minmax(0, 1fr)); gap:8px; }
    .seat {
      min-height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center;
      font-weight:800; border:1px solid #dbe5ef; background:#fff; color:#0f172a; user-select:none;
    }
    .seat.available { background:#f0fdf4; border-color:#86efac; color:#166534; cursor:pointer; }
    .seat.pending { background:#fffbeb; border-color:#fcd34d; color:#92400e; cursor:not-allowed; }
    .seat.occupied { background:#fef2f2; border-color:#fca5a5; color:#b91c1c; cursor:not-allowed; }
    .seat.selected { box-shadow:0 0 0 3px rgba(37,99,235,.18); border-color:#2563eb; background:#eff6ff; color:#1d4ed8; }
    .hidden-panel { display:none; }
    @media (max-width: 992px) {
      .span-3,.span-4,.span-6,.span-8 { grid-column:span 12; }
      .walkin-summary { grid-template-columns:repeat(2,minmax(0,1fr)); }
      .walkin-head { flex-direction:column; }
    }
    @media (max-width: 576px) {
      .walkin-summary { grid-template-columns:1fr; }
      .extra-row { grid-template-columns:1fr; }
    }
  </style>
</head>
<body class="bg-light">
  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4 overflow-hidden">
        <div class="walkin-head">
          <div>
            <h1 class="walkin-title">Walk-In Booking</h1>
            <p class="walkin-subtitle">Create front-desk reservations without touching the online guest booking flow. Confirmed walk-ins are saved to the same booking, calendar, transaction, and report modules and are tagged as walk-in records.</p>
          </div>
          <span class="walkin-pill"><i class="bi bi-person-walking"></i>Front desk booking flow</span>
        </div>

        <div class="card walkin-card">
          <div class="card-body p-4 p-xl-5">
            <form id="walkin-booking-form" class="walkin-form walkin-shell" autocomplete="off">
              <div class="walkin-section">
                <h5>Guest Source</h5>
                <div class="walkin-choice-row">
                  <label class="walkin-choice">
                    <input type="radio" name="guest_mode" value="existing" checked>
                    <span>Use Existing Guest</span>
                  </label>
                  <label class="walkin-choice">
                    <input type="radio" name="guest_mode" value="manual">
                    <span>Create Manual Walk-In Guest</span>
                  </label>
                </div>

                <div class="walkin-grid">
                  <div class="span-6" id="existing-guest-panel">
                    <label for="guest_id">Select Guest</label>
                    <select class="form-select" id="guest_id" name="guest_id">
                      <option value="">Choose a guest account</option>
                      <?php foreach ($guestRows as $guest): ?>
                        <option value="<?php echo (int)$guest['id']; ?>"
                          data-name="<?php echo htmlspecialchars($guest['name'], ENT_QUOTES); ?>"
                          data-email="<?php echo htmlspecialchars($guest['email'], ENT_QUOTES); ?>"
                          data-phone="<?php echo htmlspecialchars($guest['phonenum'], ENT_QUOTES); ?>">
                          <?php echo htmlspecialchars($guest['name']); ?><?php echo $guest['email'] ? ' - ' . htmlspecialchars($guest['email']) : ''; ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <div class="span-12 hidden-panel" id="manual-guest-panel">
                    <div class="walkin-grid">
                      <div class="span-6">
                        <label for="guest_name">Guest Name</label>
                        <input type="text" class="form-control" id="guest_name" name="guest_name" placeholder="Enter walk-in guest name">
                      </div>
                      <div class="span-3">
                        <label for="guest_email">Email</label>
                        <input type="email" class="form-control" id="guest_email" name="guest_email" placeholder="Optional email">
                      </div>
                      <div class="span-3">
                        <label for="guest_phone">Phone Number</label>
                        <input type="text" class="form-control" id="guest_phone" name="guest_phone" placeholder="Required phone number">
                      </div>
                      <div class="span-12">
                        <label for="guest_address">Address</label>
                        <input type="text" class="form-control" id="guest_address" name="guest_address" placeholder="Guest address">
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="walkin-section">
                <h5>Stay Details</h5>
                <div class="walkin-grid">
                  <div class="span-4">
                    <label for="room_id">Room Type</label>
                    <select class="form-select" id="room_id" name="room_id" required>
                      <option value="">Select room</option>
                      <?php foreach ($roomRows as $room): ?>
                        <option value="<?php echo (int)$room['id']; ?>"
                          data-name="<?php echo htmlspecialchars($room['name'], ENT_QUOTES); ?>"
                          data-price="<?php echo (float)$room['price']; ?>"
                          data-quantity="<?php echo (int)$room['quantity']; ?>"
                          data-adult="<?php echo (int)$room['adult']; ?>"
                          data-children="<?php echo (int)$room['children']; ?>">
                          <?php echo htmlspecialchars($room['name']); ?> - PHP <?php echo number_format((float)$room['price'], 2); ?>/night
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="span-4">
                    <label for="check_in">Check-In</label>
                    <input type="date" class="form-control" id="check_in" name="check_in" min="<?php echo date('Y-m-d'); ?>" required>
                  </div>
                  <div class="span-4">
                    <label for="check_out">Check-Out</label>
                    <input type="date" class="form-control" id="check_out" name="check_out" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                  </div>
                  <div class="span-3">
                    <label for="adults">Adults</label>
                    <input type="number" class="form-control" id="adults" name="adults" min="1" value="2" required>
                  </div>
                  <div class="span-3">
                    <label for="children">Children</label>
                    <input type="number" class="form-control" id="children" name="children" min="0" value="0" required>
                  </div>
                  <div class="span-12">
                    <label>Room Number Selection</label>
                    <p class="text-muted mb-2 small">Available room numbers will load automatically once the room type and stay dates are complete.</p>
                    <div id="walkin-assign-legend" class="legend mb-2">
                      <span class="legend-item"><span class="legend-swatch available"></span><span>Available</span></span>
                      <span class="legend-item"><span class="legend-swatch pending"></span><span>Pending</span></span>
                      <span class="legend-item"><span class="legend-swatch occupied"></span><span>Occupied</span></span>
                    </div>
                    <div id="walkin-assign-grid" class="seat-grid">
                      <div class="text-muted">Choose dates and check availability to load room numbers.</div>
                    </div>
                    <input type="hidden" id="room_no" name="room_no">
                  </div>
                  <div class="span-12">
                    <label for="walkin_note">Booking Note</label>
                    <textarea class="form-control" id="walkin_note" name="walkin_note" rows="3" placeholder="Optional note for this walk-in booking"></textarea>
                  </div>
                </div>
              </div>

              <div class="walkin-section">
                <h5>Extras</h5>
                <?php if (empty($extrasRows)): ?>
                  <div class="walkin-status">No active extras are available right now. You can still proceed with the room-only walk-in booking.</div>
                <?php else: ?>
                  <div id="extras-list">
                    <?php foreach ($extrasRows as $extra): ?>
                      <div class="extra-row">
                        <div>
                          <div class="fw-bold text-dark"><?php echo htmlspecialchars($extra['name']); ?></div>
                          <div class="extra-meta">PHP <?php echo number_format((float)$extra['price'], 2); ?> per night<?php echo $extra['description'] ? ' - ' . htmlspecialchars($extra['description']) : ''; ?></div>
                        </div>
                        <div>
                          <label class="mb-1" for="extra_qty_<?php echo (int)$extra['id']; ?>">Quantity</label>
                          <div class="extra-qty-wrap">
                            <button type="button" class="extra-qty-btn shadow-none js-extra-qty-btn" data-extra-delta="-1" data-extra-target="extra_qty_<?php echo (int)$extra['id']; ?>">-</button>
                            <input type="number" min="0" value="0" class="form-control extra-qty-input"
                              id="extra_qty_<?php echo (int)$extra['id']; ?>"
                              data-extra-id="<?php echo (int)$extra['id']; ?>"
                              data-extra-name="<?php echo htmlspecialchars($extra['name'], ENT_QUOTES); ?>"
                              data-extra-price="<?php echo (float)$extra['price']; ?>">
                            <button type="button" class="extra-qty-btn shadow-none js-extra-qty-btn" data-extra-delta="1" data-extra-target="extra_qty_<?php echo (int)$extra['id']; ?>">+</button>
                          </div>
                        </div>
                        <div>
                          <label class="mb-1">Estimated Total</label>
                          <div class="form-control d-flex align-items-center bg-light extra-total-label" data-extra-total-for="<?php echo (int)$extra['id']; ?>">PHP 0.00</div>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
              </div>

              <div class="walkin-section">
                <h5>Payment</h5>
                <div class="walkin-grid">
                  <div class="span-4">
                    <label for="payment_status">Payment Status</label>
                    <select class="form-select" id="payment_status" name="payment_status" required>
                      <option value="paid">Paid</option>
                      <option value="partial">Partial</option>
                      <option value="pending">Pending</option>
                    </select>
                  </div>
                  <div class="span-4">
                    <label for="payment_method">Payment Method</label>
                    <select class="form-select" id="payment_method" name="payment_method" required>
                      <option value="cash">Cash</option>
                      <option value="gcash">GCash</option>
                      <option value="maya">Maya</option>
                      <option value="bank">Bank Transfer</option>
                    </select>
                  </div>
                  <div class="span-4">
                    <label for="amount_received">Amount Received</label>
                    <input type="number" step="0.01" min="0" class="form-control" id="amount_received" name="amount_received" value="0.00" required>
                  </div>
                  <div class="span-12">
                    <label for="payment_note">Payment Note / Reference</label>
                    <input type="text" class="form-control" id="payment_note" name="payment_note" placeholder="Optional receipt note, OR number, or front-desk remark">
                  </div>
                </div>
              </div>

              <div class="walkin-section">
                <h5>Summary</h5>
                <div class="walkin-summary mb-3">
                  <div class="walkin-kpi"><span class="label">Nights</span><span class="value" id="summary_nights">0</span></div>
                  <div class="walkin-kpi"><span class="label">Room Total</span><span class="value" id="summary_room_total">PHP 0.00</span></div>
                  <div class="walkin-kpi"><span class="label">Extras Total</span><span class="value" id="summary_extras_total">PHP 0.00</span></div>
                  <div class="walkin-kpi"><span class="label">Balance Due</span><span class="value" id="summary_balance_due">PHP 0.00</span></div>
                </div>
                <div class="walkin-status mb-3" id="walkin-status-box">Choose the room and dates, then run availability check before confirming the booking.</div>
                <div class="walkin-actions">
                  <button type="submit" class="btn btn-primary shadow-none" id="confirm-walkin-btn">
                    <i class="bi bi-check2-circle me-1"></i>Confirm Walk-In Booking
                  </button>
                </div>
              </div>
            </form>

            <div class="walkin-section mt-4" id="walkin-payment-queue">
              <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div>
                  <h5 class="mb-1">Walk-In Payment Queue</h5>
                  <small>Pending and partial walk-in bookings stay here until full payment is completed, then they move to Booking Records.</small>
                </div>
                <button type="button" class="btn btn-outline-secondary shadow-none btn-sm" id="refresh-walkin-queue-btn">
                  <i class="bi bi-arrow-repeat me-1"></i>Refresh
                </button>
              </div>
              <div class="table-responsive">
                <table class="table table-hover align-middle walkin-queue-table mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>#</th>
                      <th>Guest</th>
                      <th>Stay</th>
                      <th>Status</th>
                      <th>Payment</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody id="walkin-payment-queue-body">
                    <tr><td colspan="6" class="text-center text-muted py-4">Loading walk-in payment queue...</td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php require('inc/scripts.php'); ?>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    window.walkInBookingData = {
      rooms: <?php echo json_encode($roomRows, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>,
      extrasEnabled: <?php echo empty($extrasRows) ? 'false' : 'true'; ?>
    };
  </script>
  <script src="scripts/walkin_booking.js?v=<?php echo filemtime('scripts/walkin_booking.js'); ?>"></script>
</body>
</html>
