<?php
  if (session_status() === PHP_SESSION_NONE) { session_start(); }

  require('../admin/inc/db_config.php');
  require('../admin/inc/essentials.php');

  header('Content-Type: application/json');

  $booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
  if ($booking_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid booking selected.']);
    exit;
  }

  $is_admin = isset($_SESSION['adminLogin']) && $_SESSION['adminLogin'] == true;
  $is_guest = isset($_SESSION['login']) && $_SESSION['login'] == true;

  if (!$is_admin && !$is_guest) {
    echo json_encode(['status' => 'error', 'message' => 'Please sign in first.']);
    exit;
  }

  if ($is_guest && !$is_admin) {
    $ownCheck = select(
      "SELECT `booking_id` FROM `booking_order` WHERE `booking_id`=? AND `user_id`=? LIMIT 1",
      [$booking_id, (int)$_SESSION['uId']],
      'ii'
    );
    if (!$ownCheck || mysqli_num_rows($ownCheck) !== 1) {
      echo json_encode(['status' => 'error', 'message' => 'You do not have access to this booking timeline.']);
      exit;
    }
  }

  $entries = getBookingHistoryEntries($booking_id);
  if (empty($entries)) {
    echo json_encode([
      'status' => 'success',
      'html' => "
        <div class='text-center py-4 text-muted'>
          <i class='bi bi-clock-history fs-3 d-block mb-2'></i>
          No timeline records are available for this booking yet.
        </div>"
    ]);
    exit;
  }

  $html = "<div class='booking-timeline-list'>";
  foreach ($entries as $entry) {
    $actor = htmlspecialchars($entry['actor_name'] ?: ucfirst($entry['actor_type']));
    $title = htmlspecialchars($entry['title']);
    $details = trim((string)($entry['details'] ?? ''));
    $detailsHtml = $details !== ''
      ? "<div class='small text-muted mt-1' style='white-space:pre-wrap;'>" . htmlspecialchars($details) . "</div>"
      : "";

    $created = date('M d, Y h:i A', strtotime($entry['created_at']));
    $html .= "
      <div class='d-flex gap-3 position-relative pb-3'>
        <div class='flex-shrink-0'>
          <div class='rounded-circle d-flex align-items-center justify-content-center' style='width:42px;height:42px;background:#eaf2ff;color:#2563eb;'>
            <i class='bi bi-check2-circle'></i>
          </div>
        </div>
        <div class='flex-grow-1 border-start ps-3' style='margin-left:-22px;padding-left:32px;'>
          <div class='d-flex justify-content-between flex-wrap gap-2'>
            <div class='fw-semibold text-dark'>{$title}</div>
            <div class='text-muted small'>{$created}</div>
          </div>
          <div class='small text-secondary mt-1'>By {$actor}</div>
          {$detailsHtml}
        </div>
      </div>";
  }
  $html .= "</div>";

  echo json_encode(['status' => 'success', 'html' => $html]);
