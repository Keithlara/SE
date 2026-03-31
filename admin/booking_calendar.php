<?php
  require('inc/essentials.php');
  require('inc/db_config.php');
  adminLogin();
  requireAdminPermission('calendar.manage');

  $message = '';
  $message_type = 'success';

  if (isset($_POST['add_block'])) {
    $room_id = (int)($_POST['room_id'] ?? 0);
    $room_no = trim((string)($_POST['room_no'] ?? ''));
    $start_date = trim((string)($_POST['start_date'] ?? ''));
    $end_date = trim((string)($_POST['end_date'] ?? ''));
    $block_type = trim((string)($_POST['block_type'] ?? 'maintenance'));
    $reason = trim((string)($_POST['reason'] ?? ''));

    if ($room_id > 0 && $start_date !== '' && $end_date !== '' && $start_date <= $end_date) {
      insert(
        "INSERT INTO `room_block_dates` (`room_id`,`room_no`,`start_date`,`end_date`,`block_type`,`reason`,`status`,`created_by`)
         VALUES (?,?,?,?,?,?,'active',?)",
        [$room_id, $room_no !== '' ? $room_no : null, $start_date, $end_date, $block_type, sanitizeMultilineText($reason, 255), (int)($_SESSION['adminId'] ?? 0)],
        'isssssi'
      );
      $message = 'Room block saved.';
    } else {
      $message = 'Please complete the block form with valid dates.';
      $message_type = 'error';
    }
  }

  if (isset($_POST['clear_block'])) {
    $block_id = (int)($_POST['block_id'] ?? 0);
    if ($block_id > 0) {
      update("UPDATE `room_block_dates` SET `status`='cancelled' WHERE `id`=?", [$block_id], 'i');
      $message = 'Room block removed from the calendar.';
    }
  }

  $month = max(1, min(12, (int)($_GET['month'] ?? date('n'))));
  $year = max((int)date('Y') - 2, min((int)date('Y') + 3, (int)($_GET['year'] ?? date('Y'))));
  $room_filter = (int)($_GET['room_id'] ?? 0);

  $month_start = new DateTime(sprintf('%04d-%02d-01', $year, $month));
  $month_end = (clone $month_start)->modify('last day of this month');
  $grid_start = (clone $month_start)->modify('monday this week');
  $grid_end = (clone $month_end)->modify('sunday this week');

  $room_options = [];
  $rooms_res = mysqli_query($con, "SELECT `id`,`name`,`quantity` FROM `rooms` WHERE `removed`=0 ORDER BY `name` ASC");
  while ($rooms_res && $row = mysqli_fetch_assoc($rooms_res)) {
    $room_options[] = $row;
  }

  $booking_where = "WHERE bo.check_in <= ? AND bo.check_out > ?";
  $booking_types = 'ss';
  $booking_values = [$grid_end->format('Y-m-d'), $grid_start->format('Y-m-d')];
  if ($room_filter > 0) {
    $booking_where .= " AND bo.room_id=?";
    $booking_types .= 'i';
    $booking_values[] = $room_filter;
  }

  $bookings_res = select(
    "SELECT bo.booking_id, bo.room_id, bo.booking_status, bo.check_in, bo.check_out, bd.room_name
     FROM `booking_order` bo
     INNER JOIN `booking_details` bd ON bd.booking_id = bo.booking_id
     {$booking_where}",
    $booking_values,
    $booking_types
  );

  $calendar = [];
  $cursor = clone $grid_start;
  while ($cursor <= $grid_end) {
    $calendar[$cursor->format('Y-m-d')] = [
      'booked' => 0,
      'pending' => 0,
      'cancelled' => 0,
      'blocked' => 0,
    ];
    $cursor->modify('+1 day');
  }

  while ($row = mysqli_fetch_assoc($bookings_res)) {
    $range_start = new DateTime($row['check_in']);
    $range_end = new DateTime($row['check_out']);
    $range_cursor = clone $range_start;
    while ($range_cursor < $range_end) {
      $key = $range_cursor->format('Y-m-d');
      if (isset($calendar[$key])) {
        if ($row['booking_status'] === 'booked') {
          $calendar[$key]['booked']++;
        } elseif ($row['booking_status'] === 'pending') {
          $calendar[$key]['pending']++;
        } elseif ($row['booking_status'] === 'cancelled') {
          $calendar[$key]['cancelled']++;
        }
      }
      $range_cursor->modify('+1 day');
    }
  }

  $block_where = "WHERE rbd.`status`='active' AND rbd.`start_date` <= ? AND rbd.`end_date` >= ?";
  $block_types = 'ss';
  $block_values = [$grid_end->format('Y-m-d'), $grid_start->format('Y-m-d')];
  if ($room_filter > 0) {
    $block_where .= " AND rbd.`room_id`=?";
    $block_types .= 'i';
    $block_values[] = $room_filter;
  }

  $blocks_res = select(
    "SELECT rbd.*, r.name AS room_name
     FROM `room_block_dates` rbd
     INNER JOIN `rooms` r ON r.id = rbd.room_id
     {$block_where}
     ORDER BY rbd.start_date ASC",
    $block_values,
    $block_types
  );

  $active_blocks = [];
  while ($row = mysqli_fetch_assoc($blocks_res)) {
    $active_blocks[] = $row;
    $range_cursor = new DateTime($row['start_date']);
    $range_end = new DateTime($row['end_date']);
    while ($range_cursor <= $range_end) {
      $key = $range_cursor->format('Y-m-d');
      if (isset($calendar[$key])) {
        $calendar[$key]['blocked']++;
      }
      $range_cursor->modify('+1 day');
    }
  }

  $prev = (clone $month_start)->modify('-1 month');
  $next = (clone $month_start)->modify('+1 month');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Booking Calendar</title>
  <?php require('inc/links.php'); ?>
  <style>
    .calendar-grid { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); gap: 12px; }
    .calendar-day-name { font-size: 0.8rem; font-weight: 700; color: var(--admin-text-muted); text-transform: uppercase; letter-spacing: 0.06em; }
    .calendar-cell {
      min-height: 150px; border-radius: 18px; border: 1px solid rgba(148,163,184,0.18);
      background: rgba(255,255,255,0.94); padding: 14px; display: flex; flex-direction: column; gap: 10px;
      box-shadow: 0 10px 28px rgba(15,23,42,0.04);
    }
    .calendar-cell.is-muted { opacity: 0.52; }
    .calendar-top { display: flex; justify-content: space-between; align-items: center; }
    .calendar-number { font-size: 1.2rem; font-weight: 700; color: var(--admin-text); }
    .calendar-badge {
      display: inline-flex; align-items: center; border-radius: 999px; padding: 4px 9px; font-size: 0.72rem; font-weight: 700;
    }
    .badge-booked { background: rgba(16,185,129,0.15); color: #047857; }
    .badge-pending { background: rgba(245,158,11,0.18); color: #b45309; }
    .badge-cancelled { background: rgba(148,163,184,0.18); color: #475569; }
    .badge-blocked { background: rgba(239,68,68,0.15); color: #b91c1c; }
    .calendar-stack { display: flex; flex-direction: column; gap: 6px; }
    .calendar-controls, .calendar-legend, .calendar-side-card {
      border-radius: 18px; border: 1px solid rgba(148,163,184,0.18); background: rgba(255,255,255,0.94); box-shadow: 0 14px 32px rgba(15,23,42,0.05);
    }
    .calendar-legend span { font-size: 0.85rem; color: var(--admin-text-muted); }
    .calendar-side-card { padding: 18px; }
  </style>
</head>
<body class="bg-light">
  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="p-4">
      <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
          <h3 class="mb-1">Booking Calendar</h3>
          <p class="text-muted mb-0">See booked, pending, cancelled, and blocked dates in one smoother occupancy calendar.</p>
        </div>
        <div class="calendar-legend px-3 py-2 d-flex flex-wrap gap-2 align-items-center">
          <span class="calendar-badge badge-booked">Booked</span>
          <span class="calendar-badge badge-pending">Pending</span>
          <span class="calendar-badge badge-cancelled">Cancelled</span>
          <span class="calendar-badge badge-blocked">Blocked</span>
        </div>
      </div>

      <?php if ($message !== '') { alert($message_type, $message); } ?>

      <div class="row g-4">
        <div class="col-xl-8">
          <div class="calendar-controls p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
              <div class="d-flex align-items-center gap-2">
                <a class="btn btn-outline-secondary shadow-none" href="?month=<?php echo $prev->format('n'); ?>&year=<?php echo $prev->format('Y'); ?>&room_id=<?php echo $room_filter; ?>">&larr;</a>
                <h4 class="mb-0"><?php echo $month_start->format('F Y'); ?></h4>
                <a class="btn btn-outline-secondary shadow-none" href="?month=<?php echo $next->format('n'); ?>&year=<?php echo $next->format('Y'); ?>&room_id=<?php echo $room_filter; ?>">&rarr;</a>
              </div>
              <form method="GET" class="d-flex align-items-center gap-2 flex-wrap">
                <input type="hidden" name="month" value="<?php echo $month; ?>">
                <input type="hidden" name="year" value="<?php echo $year; ?>">
                <select class="form-select shadow-none" name="room_id" onchange="this.form.submit()">
                  <option value="0">All rooms</option>
                  <?php foreach ($room_options as $room): ?>
                    <option value="<?php echo (int)$room['id']; ?>" <?php echo $room_filter === (int)$room['id'] ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($room['name']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </form>
            </div>
          </div>

          <div class="calendar-grid mb-2">
            <?php foreach (['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day_name): ?>
              <div class="calendar-day-name"><?php echo $day_name; ?></div>
            <?php endforeach; ?>
          </div>
          <div class="calendar-grid">
            <?php
              $cursor = clone $grid_start;
              while ($cursor <= $grid_end):
                $key = $cursor->format('Y-m-d');
                $day = $calendar[$key];
                $is_current_month = $cursor->format('n') == $month;
            ?>
              <div class="calendar-cell <?php echo !$is_current_month ? 'is-muted' : ''; ?>">
                <div class="calendar-top">
                  <div class="calendar-number"><?php echo $cursor->format('j'); ?></div>
                  <?php if ($cursor->format('Y-m-d') === date('Y-m-d')): ?>
                    <span class="calendar-badge badge-booked">Today</span>
                  <?php endif; ?>
                </div>
                <div class="calendar-stack">
                  <?php if ($day['booked'] > 0): ?><span class="calendar-badge badge-booked"><?php echo $day['booked']; ?> booked</span><?php endif; ?>
                  <?php if ($day['pending'] > 0): ?><span class="calendar-badge badge-pending"><?php echo $day['pending']; ?> pending</span><?php endif; ?>
                  <?php if ($day['cancelled'] > 0): ?><span class="calendar-badge badge-cancelled"><?php echo $day['cancelled']; ?> cancelled</span><?php endif; ?>
                  <?php if ($day['blocked'] > 0): ?><span class="calendar-badge badge-blocked"><?php echo $day['blocked']; ?> blocked</span><?php endif; ?>
                  <?php if ($day['booked'] == 0 && $day['pending'] == 0 && $day['cancelled'] == 0 && $day['blocked'] == 0): ?>
                    <span class="small text-muted">Available</span>
                  <?php endif; ?>
                </div>
              </div>
            <?php
                $cursor->modify('+1 day');
              endwhile;
            ?>
          </div>
        </div>

        <div class="col-xl-4">
          <div class="calendar-side-card mb-3">
            <h5 class="mb-3">Block Room Dates</h5>
            <form method="POST">
              <div class="mb-2">
                <label class="form-label">Room</label>
                <select class="form-select shadow-none" name="room_id" required>
                  <option value="">Select room...</option>
                  <?php foreach ($room_options as $room): ?>
                    <option value="<?php echo (int)$room['id']; ?>"><?php echo htmlspecialchars($room['name']); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label">Room Number</label>
                <input type="text" class="form-control shadow-none" name="room_no" placeholder="Optional room number">
              </div>
              <div class="row g-2">
                <div class="col-md-6">
                  <label class="form-label">Start</label>
                  <input type="date" class="form-control shadow-none" name="start_date" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">End</label>
                  <input type="date" class="form-control shadow-none" name="end_date" required>
                </div>
              </div>
              <div class="mb-2 mt-2">
                <label class="form-label">Block Type</label>
                <select class="form-select shadow-none" name="block_type">
                  <option value="maintenance">Maintenance</option>
                  <option value="unavailable">Unavailable</option>
                  <option value="internal">Internal use</option>
                  <option value="event">Special event</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Reason</label>
                <textarea class="form-control shadow-none" name="reason" rows="3" placeholder="What should staff know about this block?"></textarea>
              </div>
              <button type="submit" class="btn btn-primary shadow-none w-100" name="add_block">Save Block</button>
            </form>
          </div>

          <div class="calendar-side-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="mb-0">Active Blocks</h5>
              <span class="badge bg-danger rounded-pill"><?php echo count($active_blocks); ?></span>
            </div>
            <div class="d-flex flex-column gap-2">
              <?php foreach ($active_blocks as $block): ?>
                <div class="border rounded-3 p-3">
                  <div class="d-flex justify-content-between gap-2">
                    <div class="fw-semibold"><?php echo htmlspecialchars($block['room_name']); ?><?php echo $block['room_no'] ? ' • Room ' . htmlspecialchars($block['room_no']) : ''; ?></div>
                    <span class="calendar-badge badge-blocked"><?php echo htmlspecialchars(ucfirst($block['block_type'])); ?></span>
                  </div>
                  <div class="small text-muted mt-1"><?php echo date('M d', strtotime($block['start_date'])); ?> - <?php echo date('M d, Y', strtotime($block['end_date'])); ?></div>
                  <?php if (!empty($block['reason'])): ?>
                    <div class="small text-secondary mt-2"><?php echo htmlspecialchars($block['reason']); ?></div>
                  <?php endif; ?>
                  <form method="POST" class="mt-2">
                    <input type="hidden" name="block_id" value="<?php echo (int)$block['id']; ?>">
                    <button type="submit" class="btn btn-sm btn-outline-danger shadow-none" name="clear_block">Remove Block</button>
                  </form>
                </div>
              <?php endforeach; ?>
              <?php if (empty($active_blocks)): ?>
                <div class="small text-muted">No active room blocks for this view.</div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php require('inc/scripts.php'); ?>
</body>
</html>
