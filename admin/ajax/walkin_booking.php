<?php
require('../inc/db_config.php');
require('../inc/essentials.php');
require_once('../inc/email_config.php');
require_once('../../inc/smtp_mailer.php');
date_default_timezone_set("Asia/Manila");
adminLogin();
requireAdminPermission('bookings.walkin');

header('Content-Type: application/json');

function walkin_json(bool $success, string $message, array $extra = []): void
{
  echo json_encode(array_merge([
    'success' => $success,
    'message' => $message,
  ], $extra));
  exit;
}

function walkin_post(): array
{
  return filteration($_POST);
}

function walkin_default_user_profile(): string
{
  $dir = rtrim(UPLOAD_IMAGE_PATH . USERS_FOLDER, '/\\') . DIRECTORY_SEPARATOR;
  if (is_dir($dir)) {
    $files = glob($dir . '*.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}', GLOB_BRACE);
    if (!empty($files)) {
      return basename($files[0]);
    }
  }
  return 'default.png';
}

function walkin_generate_order_id(mysqli $con): string
{
  do {
    $orderId = 'WIN_' . date('Ymd') . '_' . random_int(10000, 99999);
    $res = select("SELECT `booking_id` FROM `booking_order` WHERE `order_id`=? LIMIT 1", [$orderId], 's');
  } while ($res && mysqli_num_rows($res) > 0);

  return $orderId;
}

function walkin_generate_username(mysqli $con, string $guestName, string $email): string
{
  $base = $email !== '' && strpos($email, '@') !== false
    ? substr($email, 0, strpos($email, '@'))
    : preg_replace('/[^a-z0-9]+/i', '.', strtolower($guestName));
  $base = trim((string)$base, '.');
  if ($base === '') {
    $base = 'walkin.guest';
  }

  $candidate = $base;
  $suffix = 1;
  while (appSchemaUsernameExists($con, $candidate)) {
    $candidate = $base . $suffix;
    $suffix++;
  }

  return $candidate;
}

function walkin_resolve_guest(mysqli $con, array $frm): array
{
  $mode = $frm['guest_mode'] ?? 'existing';

  if ($mode === 'existing') {
    $guestId = (int)($frm['guest_id'] ?? 0);
    if ($guestId <= 0) {
      walkin_json(false, 'Please choose an existing guest account.');
    }

    $res = select(
      "SELECT `id`,`name`,`email`,`phonenum`,`address` FROM `user_cred` WHERE `id`=? AND `is_archived`=0 LIMIT 1",
      [$guestId],
      'i'
    );
    if (!$res || mysqli_num_rows($res) === 0) {
      walkin_json(false, 'The selected guest account could not be found.');
    }

    $guest = mysqli_fetch_assoc($res);
    $guest['can_email'] = !empty($guest['email']) && filter_var($guest['email'], FILTER_VALIDATE_EMAIL);
    return $guest;
  }

  $name = trim((string)($frm['guest_name'] ?? ''));
  $email = strtolower(trim((string)($frm['guest_email'] ?? '')));
  $phone = trim((string)($frm['guest_phone'] ?? ''));
  $address = trim((string)($frm['guest_address'] ?? ''));

  if ($name === '' || $phone === '') {
    walkin_json(false, 'Walk-in guest name and phone number are required.');
  }

  if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    walkin_json(false, 'Please provide a valid guest email address or leave it blank.');
  }

  if ($email !== '') {
    $existing = select(
      "SELECT `id`,`name`,`email`,`phonenum`,`address` FROM `user_cred` WHERE `email`=? AND `is_archived`=0 LIMIT 1",
      [$email],
      's'
    );
    if ($existing && mysqli_num_rows($existing) > 0) {
      $guest = mysqli_fetch_assoc($existing);
      $guest['can_email'] = true;
      return $guest;
    }
  }

  $canEmail = true;
  if ($email === '') {
    $email = 'walkin.' . time() . '.' . random_int(100, 999) . '@travelersplace.local';
    $canEmail = false;
  }
  if ($address === '') {
    $address = 'Walk-in guest';
  }

  $username = walkin_generate_username($con, $name, $email);
  $passwordHash = password_hash(bin2hex(random_bytes(8)), PASSWORD_BCRYPT);
  $profile = walkin_default_user_profile();
  $pincode = 0;
  $dob = '2000-01-01';
  $verified = 1;
  $status = 1;

  $stmt = mysqli_prepare($con, "INSERT INTO `user_cred`
    (`name`,`email`,`address`,`phonenum`,`pincode`,`dob`,`profile`,`password`,`is_verified`,`status`,`username`)
    VALUES (?,?,?,?,?,?,?,?,?,?,?)");
  if (!$stmt) {
    walkin_json(false, 'Unable to prepare the walk-in guest account.');
  }

  mysqli_stmt_bind_param($stmt, 'ssssisssiis', $name, $email, $address, $phone, $pincode, $dob, $profile, $passwordHash, $verified, $status, $username);
  if (!mysqli_stmt_execute($stmt)) {
    $error = mysqli_stmt_error($stmt);
    mysqli_stmt_close($stmt);
    walkin_json(false, 'Unable to create the walk-in guest account. ' . $error);
  }
  mysqli_stmt_close($stmt);

  $guestId = (int)mysqli_insert_id($con);
  return [
    'id' => $guestId,
    'name' => $name,
    'email' => $email,
    'phonenum' => $phone,
    'address' => $address,
    'can_email' => $canEmail,
  ];
}

function walkin_send_confirmation_email(array $guest, array $room, string $orderId, string $checkIn, string $checkOut, int $nights, string $roomNo, float $roomTotal, float $extrasTotal, float $grandTotal, array $paymentMeta, string $paymentMethod, string $paymentNote): bool
{
  $guestEmail = trim((string)($guest['email'] ?? ''));
  $guestName = trim((string)($guest['name'] ?? 'Guest'));
  $canEmail = (bool)($guest['can_email'] ?? false);

  if (!$canEmail || !filter_var($guestEmail, FILTER_VALIDATE_EMAIL)) {
    return false;
  }

  $siteName = defined('SITE_NAME') ? SITE_NAME : 'Travelers Place';
  $subject = "Walk-In Booking Confirmed - {$orderId}";
  $checkInLabel = date('F j, Y', strtotime($checkIn));
  $checkOutLabel = date('F j, Y', strtotime($checkOut));
  $paymentLabel = ucfirst($paymentMeta['payment_status']);
  $amountPaid = (float)$paymentMeta['amount_paid'];
  $balanceDue = (float)$paymentMeta['balance_due'];
  $safeGuestName = htmlspecialchars($guestName, ENT_QUOTES, 'UTF-8');
  $safeOrderId = htmlspecialchars($orderId, ENT_QUOTES, 'UTF-8');
  $safeRoomName = htmlspecialchars((string)$room['name'], ENT_QUOTES, 'UTF-8');
  $safeRoomNo = htmlspecialchars($roomNo, ENT_QUOTES, 'UTF-8');
  $safePaymentMethod = htmlspecialchars(ucfirst($paymentMethod), ENT_QUOTES, 'UTF-8');
  $safePaymentNote = htmlspecialchars($paymentNote !== '' ? $paymentNote : 'Front desk payment recorded', ENT_QUOTES, 'UTF-8');

  $html = "
    <div style='font-family:Arial,sans-serif;max-width:640px;margin:0 auto;background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden'>
      <div style='background:#1f2937;padding:26px 30px'>
        <h1 style='margin:0;color:#fff;font-size:24px'>{$siteName}</h1>
        <p style='margin:6px 0 0;color:#d1d5db;font-size:13px'>Walk-in booking confirmation and receipt summary</p>
      </div>
      <div style='padding:28px 30px'>
        <h2 style='margin:0 0 10px;color:#0f172a;font-size:22px'>Booking confirmed</h2>
        <p style='margin:0 0 18px;color:#475569;line-height:1.7'>Hello <strong>{$safeGuestName}</strong>, your walk-in booking at {$siteName} has been successfully recorded. Your receipt summary is included below for easy reference.</p>

        <table style='width:100%;border-collapse:collapse;margin-bottom:18px'>
          <tr><td style='padding:10px 12px;border-bottom:1px solid #e5e7eb;color:#64748b;font-size:13px'>Booking Reference</td><td style='padding:10px 12px;border-bottom:1px solid #e5e7eb;color:#111827;font-weight:700'>{$safeOrderId}</td></tr>
          <tr><td style='padding:10px 12px;border-bottom:1px solid #e5e7eb;color:#64748b;font-size:13px'>Room</td><td style='padding:10px 12px;border-bottom:1px solid #e5e7eb;color:#111827'>{$safeRoomName} - Room {$safeRoomNo}</td></tr>
          <tr><td style='padding:10px 12px;border-bottom:1px solid #e5e7eb;color:#64748b;font-size:13px'>Stay Dates</td><td style='padding:10px 12px;border-bottom:1px solid #e5e7eb;color:#111827'>{$checkInLabel} to {$checkOutLabel}</td></tr>
          <tr><td style='padding:10px 12px;border-bottom:1px solid #e5e7eb;color:#64748b;font-size:13px'>Duration</td><td style='padding:10px 12px;border-bottom:1px solid #e5e7eb;color:#111827'>{$nights} night(s)</td></tr>
        </table>

        <div style='border:1px solid #dbeafe;border-radius:12px;background:#f8fbff;padding:16px 18px;margin-bottom:18px'>
          <div style='font-size:14px;font-weight:700;color:#1d4ed8;margin-bottom:10px'>Receipt Summary</div>
          <div style='display:flex;justify-content:space-between;gap:10px;margin-bottom:8px;color:#334155'><span>Room Charge</span><strong>PHP " . number_format($roomTotal, 2) . "</strong></div>
          <div style='display:flex;justify-content:space-between;gap:10px;margin-bottom:8px;color:#334155'><span>Extras</span><strong>PHP " . number_format($extrasTotal, 2) . "</strong></div>
          <div style='display:flex;justify-content:space-between;gap:10px;margin-bottom:8px;color:#334155'><span>Total Amount</span><strong>PHP " . number_format($grandTotal, 2) . "</strong></div>
          <div style='display:flex;justify-content:space-between;gap:10px;margin-bottom:8px;color:#334155'><span>Amount Paid</span><strong>PHP " . number_format($amountPaid, 2) . "</strong></div>
          <div style='display:flex;justify-content:space-between;gap:10px;color:#334155'><span>Balance Due</span><strong>PHP " . number_format($balanceDue, 2) . "</strong></div>
        </div>

        <table style='width:100%;border-collapse:collapse;margin-bottom:18px'>
          <tr><td style='padding:10px 12px;border-bottom:1px solid #e5e7eb;color:#64748b;font-size:13px'>Payment Status</td><td style='padding:10px 12px;border-bottom:1px solid #e5e7eb;color:#111827'>{$paymentLabel}</td></tr>
          <tr><td style='padding:10px 12px;border-bottom:1px solid #e5e7eb;color:#64748b;font-size:13px'>Payment Method</td><td style='padding:10px 12px;border-bottom:1px solid #e5e7eb;color:#111827'>{$safePaymentMethod}</td></tr>
          <tr><td style='padding:10px 12px;color:#64748b;font-size:13px'>Front Desk Note</td><td style='padding:10px 12px;color:#111827'>{$safePaymentNote}</td></tr>
        </table>

        <p style='margin:0;color:#64748b;line-height:1.7'>Please keep this email as your walk-in booking receipt reference. If you need help, you may contact {$siteName} support.</p>
      </div>
    </div>";

  return send_email_smtp_basic($guestEmail, $guestName, $subject, $html);
}

function walkin_payment_badge(string $status): string
{
  $status = strtolower(trim($status));
  if ($status === 'paid') {
    return "<span class='badge rounded-pill bg-success-subtle text-success border border-success-subtle'>Paid</span>";
  }
  if ($status === 'partial') {
    return "<span class='badge rounded-pill bg-warning-subtle text-warning border border-warning-subtle'>Partial</span>";
  }
  return "<span class='badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle'>Pending</span>";
}

function walkin_render_payment_queue(mysqli $con): string
{
  $res = select(
    "SELECT bo.`booking_id`, bo.`order_id`, bo.`payment_status`, bo.`amount_paid`, bo.`total_amt`, bo.`balance_due`, bo.`check_in`, bo.`check_out`, bo.`datentime`,
            bd.`user_name`, bd.`room_name`, bd.`room_no`
     FROM `booking_order` bo
     INNER JOIN `booking_details` bd ON bo.`booking_id` = bd.`booking_id`
     WHERE bo.`booking_source`='walk_in' AND bo.`is_archived`=0 AND bo.`payment_status` IN ('pending','partial')
     ORDER BY bo.`booking_id` DESC",
    [],
    ''
  );

  if (!$res || mysqli_num_rows($res) === 0) {
    return "<tr><td colspan='6' class='text-center text-muted py-4'>No pending walk-in payments right now.</td></tr>";
  }

  $html = '';
  $i = 1;
  while ($row = mysqli_fetch_assoc($res)) {
    $bookingId = (int)$row['booking_id'];
    $orderId = htmlspecialchars((string)$row['order_id']);
    $guestName = htmlspecialchars((string)$row['user_name']);
    $roomName = htmlspecialchars((string)$row['room_name']);
    $roomNo = trim((string)($row['room_no'] ?? '')) !== '' ? htmlspecialchars((string)$row['room_no']) : 'Not selected';
    $amountPaid = number_format((float)$row['amount_paid'], 2);
    $totalAmt = number_format((float)$row['total_amt'], 2);
    $balanceDue = number_format((float)$row['balance_due'], 2);
    $statusBadge = walkin_payment_badge((string)$row['payment_status']);
    $checkIn = date('M d, Y', strtotime((string)$row['check_in']));
    $checkOut = date('M d, Y', strtotime((string)$row['check_out']));

    $html .= "
      <tr>
        <td>{$i}</td>
        <td>
          <div class='fw-bold text-dark'>{$guestName}</div>
          <div class='small text-muted'>Order: {$orderId}</div>
        </td>
        <td>
          <div class='fw-semibold text-dark'>{$roomName}</div>
          <div class='small text-muted'>Room {$roomNo}</div>
          <div class='small text-muted'>{$checkIn} to {$checkOut}</div>
        </td>
        <td>{$statusBadge}</td>
        <td>
          <div class='small text-muted'>Paid: PHP {$amountPaid}</div>
          <div class='small text-muted'>Total: PHP {$totalAmt}</div>
          <div class='fw-semibold text-danger'>Balance: PHP {$balanceDue}</div>
        </td>
        <td>
          <button type='button'
            class='btn btn-sm btn-success shadow-none js-walkin-settle'
            data-booking-id='{$bookingId}'
            data-order-id='{$orderId}'
            data-balance-due='{$balanceDue}'>
            <i class='bi bi-cash-coin me-1'></i>Mark as Paid
          </button>
        </td>
      </tr>";
    $i++;
  }

  return $html;
}

function walkin_resolve_room(mysqli $con, int $roomId): array
{
  if ($roomId <= 0) {
    walkin_json(false, 'Please choose a room for the walk-in booking.');
  }

  $res = select(
    "SELECT `id`,`name`,`price`,`quantity`,`adult`,`children` FROM `rooms`
     WHERE `id`=? AND `removed`=0 AND `status`=1 AND `is_archived`=0 LIMIT 1",
    [$roomId],
    'i'
  );

  if (!$res || mysqli_num_rows($res) === 0) {
    walkin_json(false, 'The selected room is not available.');
  }

  return mysqli_fetch_assoc($res);
}

function walkin_validate_dates(string $checkIn, string $checkOut): array
{
  $start = DateTime::createFromFormat('Y-m-d', $checkIn);
  $end = DateTime::createFromFormat('Y-m-d', $checkOut);
  $today = new DateTime(date('Y-m-d'));

  if (!$start || !$end) {
    walkin_json(false, 'Please provide valid check-in and check-out dates.');
  }
  if ($start >= $end) {
    walkin_json(false, 'Check-out date must be after check-in date.');
  }
  if ($start < $today) {
    walkin_json(false, 'Walk-in bookings cannot start in the past.');
  }

  $nights = (int)$start->diff($end)->days;
  if ($nights <= 0) {
    walkin_json(false, 'Stay duration must be at least one night.');
  }

  return [$start, $end, $nights];
}

function walkin_resolve_extras(mysqli $con, string $extrasJson, int $nights): array
{
  $decoded = json_decode($extrasJson, true);
  if (!is_array($decoded)) {
    $decoded = [];
  }

  $items = [];
  $extrasTotal = 0.0;

  foreach ($decoded as $item) {
    $extraId = (int)($item['id'] ?? 0);
    $qty = (int)($item['qty'] ?? 0);
    if ($extraId <= 0 || $qty <= 0) {
      continue;
    }

    $res = select("SELECT `id`,`name`,`price` FROM `extras` WHERE `id`=? AND `status`=1 LIMIT 1", [$extraId], 'i');
    if (!$res || mysqli_num_rows($res) === 0) {
      continue;
    }

    $dbExtra = mysqli_fetch_assoc($res);
    $unitPrice = (float)$dbExtra['price'];
    $lineTotal = $unitPrice * $qty * $nights;
    $extrasTotal += $lineTotal;

    $items[] = [
      'id' => (int)$dbExtra['id'],
      'name' => $dbExtra['name'],
      'qty' => $qty,
      'unit_price' => $unitPrice,
      'line_total' => $lineTotal,
    ];
  }

  return [$items, $extrasTotal];
}

function walkin_get_blocked_room_numbers(mysqli $con, int $roomId, string $checkIn, string $checkOut): array
{
  $blocked = [];

  if (appSchemaTableExists($con, 'room_block_dates')) {
    $stmt = mysqli_prepare($con, "SELECT `room_no`,`block_type`
      FROM `room_block_dates`
      WHERE `room_id`=? AND `status`='active' AND `end_date` > ? AND `start_date` < ?");
    if ($stmt) {
      mysqli_stmt_bind_param($stmt, 'iss', $roomId, $checkIn, $checkOut);
      mysqli_stmt_execute($stmt);
      $res = mysqli_stmt_get_result($stmt);
      while ($row = mysqli_fetch_assoc($res)) {
        $roomNo = trim((string)($row['room_no'] ?? ''));
        if ($roomNo === '') {
          $blocked['__full__'] = true;
        } else {
          $blocked[$roomNo] = true;
        }
      }
      mysqli_stmt_close($stmt);
    }
  }

  return $blocked;
}

function walkin_check_availability(mysqli $con, array $room, string $checkIn, string $checkOut, string $requestedRoomNo = ''): array
{
  $roomId = (int)$room['id'];
  $quantity = max(0, (int)$room['quantity']);
  if ($quantity <= 0) {
    return ['success' => false, 'message' => 'This room has no available inventory configured.'];
  }

  $seats = [];
  for ($i = 1; $i <= $quantity; $i++) {
    $seats[$i] = [
      'label' => (string)$i,
      'status' => 'available',
      'room_no' => (string)$i,
    ];
  }

  $blocked = walkin_get_blocked_room_numbers($con, $roomId, $checkIn, $checkOut);
  if (!empty($blocked['__full__'])) {
    return ['success' => false, 'message' => 'The selected room is blocked for the chosen dates.'];
  }
  foreach ($blocked as $roomNo => $_blocked) {
    if ($roomNo === '__full__') {
      continue;
    }
    if (ctype_digit((string)$roomNo)) {
      $label = (int)$roomNo;
      if (isset($seats[$label])) {
        $seats[$label]['status'] = 'occupied';
      }
    }
  }

  $overlapStmt = mysqli_prepare($con, "SELECT COUNT(*) AS total_bookings
    FROM `booking_order`
    WHERE `room_id`=? AND `is_archived`=0
      AND `booking_status` IN ('pending','booked')
      AND `check_out` > ? AND `check_in` < ?");
  mysqli_stmt_bind_param($overlapStmt, 'iss', $roomId, $checkIn, $checkOut);
  mysqli_stmt_execute($overlapStmt);
  $overlapRes = mysqli_stmt_get_result($overlapStmt);
  $overlapRow = $overlapRes ? mysqli_fetch_assoc($overlapRes) : ['total_bookings' => 0];
  mysqli_stmt_close($overlapStmt);
  $totalOverlap = (int)($overlapRow['total_bookings'] ?? 0);

  if ($totalOverlap >= $quantity) {
    return ['success' => false, 'message' => 'All room slots are already booked for the selected stay dates.'];
  }

  $occupiedNumbers = [];
  $assignedCount = 0;
  $assignedStmt = mysqli_prepare($con, "SELECT bd.`room_no`
    FROM `booking_details` bd
    INNER JOIN `booking_order` bo ON bo.`booking_id` = bd.`booking_id`
    WHERE bo.`room_id`=? AND bo.`is_archived`=0
      AND bo.`booking_status` IN ('pending','booked')
      AND bo.`check_out` > ? AND bo.`check_in` < ?
      AND bd.`room_no` IS NOT NULL AND TRIM(bd.`room_no`) <> ''");
  mysqli_stmt_bind_param($assignedStmt, 'iss', $roomId, $checkIn, $checkOut);
  mysqli_stmt_execute($assignedStmt);
  $assignedRes = mysqli_stmt_get_result($assignedStmt);
  while ($assignedRes && $row = mysqli_fetch_assoc($assignedRes)) {
    $roomNo = trim((string)$row['room_no']);
    if ($roomNo !== '') {
      $assignedCount++;
      $occupiedNumbers[$roomNo] = true;
      if (ctype_digit($roomNo)) {
        $label = (int)$roomNo;
        if (isset($seats[$label])) {
          $seats[$label]['status'] = 'occupied';
        }
      }
    }
  }
  mysqli_stmt_close($assignedStmt);

  $unassignedReservations = max(0, $totalOverlap - $assignedCount);

  $availableNumbers = [];
  for ($i = 1; $i <= $quantity; $i++) {
    $roomNo = (string)$i;
    if (isset($occupiedNumbers[$roomNo]) || isset($blocked[$roomNo])) {
      continue;
    }
    $availableNumbers[] = $roomNo;
  }

  if ($unassignedReservations > 0 && count($availableNumbers) > 0) {
    $pendingSlice = array_slice($availableNumbers, 0, $unassignedReservations);
    foreach ($pendingSlice as $pendingRoomNo) {
      $label = (int)$pendingRoomNo;
      if (isset($seats[$label])) {
        $seats[$label]['status'] = 'pending';
      }
    }
    $availableNumbers = array_slice($availableNumbers, $unassignedReservations);
  }

  if (empty($availableNumbers)) {
    return ['success' => false, 'message' => 'No room numbers are currently available for the selected dates.'];
  }

  if ($requestedRoomNo !== '' && !in_array($requestedRoomNo, $availableNumbers, true)) {
    return ['success' => false, 'message' => 'The selected room number is not available for the selected dates.'];
  }

  return [
    'success' => true,
    'message' => 'Room is available for the selected dates.',
    'available_room_numbers' => array_values($availableNumbers),
    'suggested_room_no' => $availableNumbers[0],
    'remaining_capacity' => count($availableNumbers),
    'overlap_count' => $totalOverlap,
    'seats' => array_values($seats),
  ];
}

function walkin_payment_meta(string $paymentStatus, float $amountReceived, float $grandTotal, string $paymentNote): array
{
  $paymentStatus = strtolower(trim($paymentStatus));
  if (!in_array($paymentStatus, ['paid', 'partial', 'pending'], true)) {
    walkin_json(false, 'Please choose a valid payment status.');
  }

  if ($paymentStatus === 'pending') {
    if ($amountReceived > 0) {
      walkin_json(false, 'Pending payment cannot include an amount received.');
    }
    return [
      'payment_status' => 'pending',
      'amount_paid' => 0.00,
      'balance_due' => $grandTotal,
      'trans_status' => 'pending',
      'trans_resp_msg' => $paymentNote !== '' ? $paymentNote : 'Awaiting walk-in payment',
      'transaction_status' => 'pending',
    ];
  }

  if ($paymentStatus === 'partial') {
    if ($amountReceived <= 0 || $amountReceived >= $grandTotal) {
      walkin_json(false, 'Partial payment must be greater than zero and less than the total amount.');
    }
    return [
      'payment_status' => 'partial',
      'amount_paid' => $amountReceived,
      'balance_due' => max(0, $grandTotal - $amountReceived),
      'trans_status' => 'TXN_SUCCESS',
      'trans_resp_msg' => $paymentNote !== '' ? $paymentNote : 'Partial walk-in payment received',
      'transaction_status' => 'partial',
    ];
  }

  if ($amountReceived < $grandTotal) {
    walkin_json(false, 'Paid bookings must receive the full amount.');
  }

  return [
    'payment_status' => 'paid',
    'amount_paid' => $grandTotal,
    'balance_due' => 0.00,
    'trans_status' => 'TXN_SUCCESS',
    'trans_resp_msg' => $paymentNote !== '' ? $paymentNote : 'Walk-in payment settled in full',
    'transaction_status' => 'paid',
  ];
}

$frm = walkin_post();
$action = $frm['action'] ?? '';

if ($action === '') {
  walkin_json(false, 'Invalid walk-in booking request.');
}

if ($action === 'get_walkin_payment_queue') {
  walkin_json(true, 'Walk-in payment queue loaded.', [
    'table_html' => walkin_render_payment_queue($con),
  ]);
}

if ($action === 'settle_walkin_payment') {
  $bookingId = (int)($frm['booking_id'] ?? 0);
  $paymentMethod = trim((string)($frm['payment_method'] ?? 'cash'));
  $paymentNote = trim((string)($frm['payment_note'] ?? ''));

  if ($bookingId <= 0) {
    walkin_json(false, 'Invalid walk-in booking selected.');
  }
  if (!in_array($paymentMethod, ['cash', 'gcash', 'maya', 'bank'], true)) {
    walkin_json(false, 'Please choose a valid payment method.');
  }

  $res = select(
    "SELECT bo.`booking_id`, bo.`user_id`, bo.`order_id`, bo.`payment_status`, bo.`amount_paid`, bo.`total_amt`, bo.`balance_due`, bo.`check_in`, bo.`check_out`,
            bd.`user_name`, bd.`room_name`, bd.`room_no`, bd.`price`, bd.`extras_total`,
            uc.`email`, uc.`name` AS guest_name, uc.`phonenum`, uc.`address`
     FROM `booking_order` bo
     INNER JOIN `booking_details` bd ON bd.`booking_id` = bo.`booking_id`
     INNER JOIN `user_cred` uc ON uc.`id` = bo.`user_id`
     WHERE bo.`booking_id`=? AND bo.`booking_source`='walk_in' AND bo.`is_archived`=0 LIMIT 1",
    [$bookingId],
    'i'
  );
  if (!$res || mysqli_num_rows($res) === 0) {
    walkin_json(false, 'Walk-in booking not found.');
  }

  $booking = mysqli_fetch_assoc($res);
  if (($booking['payment_status'] ?? '') === 'paid') {
    walkin_json(false, 'This walk-in booking is already fully paid.');
  }

  $remaining = round((float)$booking['balance_due'], 2);
  $totalAmt = round((float)$booking['total_amt'], 2);
  $amountPaid = round((float)$booking['amount_paid'], 2);
  if ($remaining <= 0) {
    $remaining = max(0, $totalAmt - $amountPaid);
  }
  if ($remaining <= 0) {
    walkin_json(false, 'No remaining balance was found for this booking.');
  }

  $adminId = (int)($_SESSION['adminId'] ?? 0);
  $adminName = (string)($_SESSION['adminName'] ?? 'Admin');
  $transId = 'WALKIN-SETTLE-' . date('YmdHis') . '-' . random_int(100, 999);
  $confirmedAt = date('Y-m-d H:i:s');
  $paymentMessage = $paymentNote !== '' ? $paymentNote : 'Walk-in balance fully settled';
  $guest = [
    'id' => (int)$booking['user_id'],
    'name' => (string)($booking['guest_name'] ?: $booking['user_name']),
    'email' => (string)$booking['email'],
    'phonenum' => (string)$booking['phonenum'],
    'address' => (string)$booking['address'],
    'can_email' => !empty($booking['email']) && filter_var($booking['email'], FILTER_VALIDATE_EMAIL),
  ];
  $room = [
    'name' => (string)$booking['room_name'],
  ];
  $nights = max(1, (int)((new DateTime((string)$booking['check_in']))->diff(new DateTime((string)$booking['check_out']))->days));
  $roomNo = trim((string)$booking['room_no']) !== '' ? (string)$booking['room_no'] : '1';

  mysqli_begin_transaction($con);
  try {
    $transactionStmt = mysqli_prepare($con, "INSERT INTO `transactions`
      (`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`)
      VALUES (?,?,?,?,?,?,?,?)");
    if (!$transactionStmt) {
      throw new Exception('Failed to prepare walk-in settlement transaction.');
    }
    $transactionAmount = (int)round($remaining);
    $transactionStatus = 'paid';
    $transactionType = 'walk_in_balance';
    $guestName = (string)$booking['user_name'];
    mysqli_stmt_bind_param($transactionStmt, 'ississsi', $bookingId, $guestName, $roomNo, $transactionAmount, $paymentMethod, $transactionStatus, $transactionType, $adminId);
    if (!mysqli_stmt_execute($transactionStmt)) {
      throw new Exception(mysqli_stmt_error($transactionStmt));
    }
    mysqli_stmt_close($transactionStmt);

    $newTotalPaid = $totalAmt;
    $zeroBalance = 0.00;
    $bookingStatus = 'booked';
    $paymentStatus = 'paid';
    $transStatus = 'TXN_SUCCESS';
    $arrival = (((string)$booking['check_in']) <= date('Y-m-d') && $roomNo !== '') ? 1 : 0;
    $updateStmt = mysqli_prepare($con, "UPDATE `booking_order`
      SET `booking_status`=?, `payment_status`=?, `amount_paid`=?, `balance_due`=?, `trans_amt`=?, `trans_id`=?, `trans_status`=?, `trans_resp_msg`=?, `confirmed_at`=?, `arrival`=?
      WHERE `booking_id`=?");
    if (!$updateStmt) {
      throw new Exception('Failed to prepare walk-in settlement update.');
    }
    $transAmt = (int)round($totalAmt);
    mysqli_stmt_bind_param($updateStmt, 'ssddissssii', $bookingStatus, $paymentStatus, $newTotalPaid, $zeroBalance, $transAmt, $transId, $transStatus, $paymentMessage, $confirmedAt, $arrival, $bookingId);
    if (!mysqli_stmt_execute($updateStmt)) {
      throw new Exception(mysqli_stmt_error($updateStmt));
    }
    mysqli_stmt_close($updateStmt);

    createBookingHistoryEntry(
      $bookingId,
      'walkin_payment_completed',
      'Walk-in payment marked as paid',
      'The remaining walk-in balance was received and the booking moved to booking records.',
      [
        'order_id' => $booking['order_id'],
        'booking_source' => 'walk_in',
        'remaining_paid' => $remaining,
        'total_amount' => $totalAmt,
      ],
      'admin',
      $adminId,
      $adminName
    );

    mysqli_commit($con);

    $extrasTotal = round((float)($booking['extras_total'] ?? 0), 2);
    $roomTotal = max(0, $totalAmt - $extrasTotal);
    walkin_send_confirmation_email(
      $guest,
      $room,
      (string)$booking['order_id'],
      (string)$booking['check_in'],
      (string)$booking['check_out'],
      $nights,
      $roomNo,
      $roomTotal,
      $extrasTotal,
      $totalAmt,
      [
        'payment_status' => 'paid',
        'amount_paid' => $totalAmt,
        'balance_due' => 0.0,
      ],
      $paymentMethod,
      $paymentMessage
    );

    walkin_json(true, 'Walk-in payment completed. The booking is now in Booking Records.', [
      'table_html' => walkin_render_payment_queue($con),
      'booking_id' => $bookingId,
      'order_id' => $booking['order_id'],
    ]);
  } catch (Throwable $e) {
    mysqli_rollback($con);
    error_log('walkin settlement failed: ' . $e->getMessage());
    walkin_json(false, 'Failed to complete the walk-in payment. ' . $e->getMessage());
  }
}

$roomId = (int)($frm['room_id'] ?? 0);
$checkIn = trim((string)($frm['check_in'] ?? ''));
$checkOut = trim((string)($frm['check_out'] ?? ''));
$requestedRoomNo = trim((string)($frm['room_no'] ?? ''));
$adults = max(0, (int)($frm['adults'] ?? 0));
$children = max(0, (int)($frm['children'] ?? 0));

$room = walkin_resolve_room($con, $roomId);
[$startDate, $endDate, $nights] = walkin_validate_dates($checkIn, $checkOut);

if ($requestedRoomNo !== '' && !ctype_digit($requestedRoomNo)) {
  walkin_json(false, 'Please choose a valid room number.');
}

if ($adults <= 0) {
  walkin_json(false, 'At least one adult is required for the booking.');
}
if ($adults > (int)$room['adult']) {
  walkin_json(false, 'The selected room cannot handle that many adults.');
}
if ($children > (int)$room['children']) {
  walkin_json(false, 'The selected room cannot handle that many children.');
}

$availability = walkin_check_availability($con, $room, $checkIn, $checkOut, $requestedRoomNo);
if ($action === 'check_availability') {
  if (!$availability['success']) {
    walkin_json(false, $availability['message']);
  }
  walkin_json(true, $availability['message'], $availability);
}

if ($action !== 'create_walkin_booking') {
  walkin_json(false, 'Unsupported walk-in booking action.');
}

if (!$availability['success']) {
  walkin_json(false, $availability['message']);
}

$guest = walkin_resolve_guest($con, $frm);
[$extras, $extrasTotal] = walkin_resolve_extras($con, (string)($frm['extras_json'] ?? '[]'), $nights);

if ($requestedRoomNo === '') {
  walkin_json(false, 'Please select an available room number from the room map.');
}

$roomPrice = (float)$room['price'];
$roomTotal = $roomPrice * $nights;
$grandTotal = $roomTotal + $extrasTotal;
$paymentMethod = trim((string)($frm['payment_method'] ?? 'cash'));
$paymentNote = trim((string)($frm['payment_note'] ?? ''));
$walkinNote = trim((string)($frm['walkin_note'] ?? ''));
$amountReceived = round((float)($frm['amount_received'] ?? 0), 2);

if (!in_array($paymentMethod, ['cash', 'gcash', 'maya', 'bank'], true)) {
  walkin_json(false, 'Please choose a valid payment method.');
}

$paymentMeta = walkin_payment_meta((string)($frm['payment_status'] ?? 'pending'), $amountReceived, $grandTotal, $paymentNote);

$roomNo = $requestedRoomNo;

$adminId = (int)($_SESSION['adminId'] ?? 0);
$adminName = (string)($_SESSION['adminName'] ?? 'Admin');
$orderId = walkin_generate_order_id($con);
$transId = 'WALKIN-' . date('YmdHis') . '-' . random_int(100, 999);
$isFullyPaid = ($paymentMeta['payment_status'] === 'paid');
$arrival = ($checkIn <= date('Y-m-d') && $roomNo !== '') ? 1 : 0;
$bookingStatus = $isFullyPaid ? 'booked' : 'pending';
$roundedGrandTotal = round($grandTotal, 2);
$roundedRoomTotal = round($roomTotal, 2);
$roundedExtrasTotal = round($extrasTotal, 2);
$downpayment = $paymentMeta['amount_paid'];
$balanceDue = $paymentMeta['balance_due'];
$confirmedAt = date('Y-m-d H:i:s');

mysqli_begin_transaction($con);

try {
  $bookingStmt = mysqli_prepare($con, "INSERT INTO `booking_order`
    (`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`payment_status`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`booking_source`,`created_by_admin`,`walkin_note`)
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
  if (!$bookingStmt) {
    throw new Exception('Failed to prepare booking order insert.');
  }

  $refund = 0;
  $rateReview = 0;
  $transAmt = (int)round($paymentMeta['amount_paid']);
  $bookingSource = 'walk_in';
  $userId = (int)$guest['id'];
  $bookingWalkinNote = $walkinNote !== '' ? $walkinNote : null;
  mysqli_stmt_bind_param(
    $bookingStmt,
    'iissiisssississsdddsis',
    $userId,
    $roomId,
    $checkIn,
    $checkOut,
    $arrival,
    $refund,
    $bookingStatus,
    $orderId,
    $transId,
    $transAmt,
    $paymentMeta['trans_status'],
    $paymentMeta['trans_resp_msg'],
    $rateReview,
    $paymentMeta['payment_status'],
    $paymentMeta['amount_paid'],
    $confirmedAt,
    $roundedGrandTotal,
    $downpayment,
    $balanceDue,
    $bookingSource,
    $adminId,
    $bookingWalkinNote
  );
  if (!mysqli_stmt_execute($bookingStmt)) {
    throw new Exception(mysqli_stmt_error($bookingStmt));
  }
  mysqli_stmt_close($bookingStmt);
  $bookingId = (int)mysqli_insert_id($con);

  $detailStmt = mysqli_prepare($con, "INSERT INTO `booking_details`
    (`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`)
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
  if (!$detailStmt) {
    throw new Exception('Failed to prepare booking details insert.');
  }
  $guestName = (string)$guest['name'];
  $guestPhone = (string)($guest['phonenum'] ?? '');
  $guestAddress = (string)($guest['address'] ?? 'Walk-in guest');
  $bookingNote = $paymentNote !== '' ? $paymentNote : null;
  $staffNote = $walkinNote !== '' ? $walkinNote : null;
  $priceInt = (int)round($roomPrice);
  $totalPayInt = (int)round($roundedGrandTotal);
  mysqli_stmt_bind_param(
    $detailStmt,
    'isiisssssdddd',
    $bookingId,
    $room['name'],
    $priceInt,
    $totalPayInt,
    $roomNo,
    $guestName,
    $guestPhone,
    $guestAddress,
    $bookingNote,
    $staffNote,
    $roundedExtrasTotal,
    $downpayment,
    $balanceDue
  );
  if (!mysqli_stmt_execute($detailStmt)) {
    throw new Exception(mysqli_stmt_error($detailStmt));
  }
  mysqli_stmt_close($detailStmt);

  if (!empty($extras)) {
    $extraStmt = mysqli_prepare($con, "INSERT INTO `booking_extras`
      (`booking_id`,`extra_id`,`name`,`quantity`,`unit_price`,`total_price`)
      VALUES (?,?,?,?,?,?)");
    if (!$extraStmt) {
      throw new Exception('Failed to prepare booking extras insert.');
    }

    foreach ($extras as $extra) {
      $extraId = (int)$extra['id'];
      $extraName = (string)$extra['name'];
      $extraQty = (int)$extra['qty'];
      $extraUnitPrice = (float)$extra['unit_price'];
      $extraLineTotal = (float)$extra['line_total'];
      mysqli_stmt_bind_param($extraStmt, 'iisidd', $bookingId, $extraId, $extraName, $extraQty, $extraUnitPrice, $extraLineTotal);
      if (!mysqli_stmt_execute($extraStmt)) {
        throw new Exception(mysqli_stmt_error($extraStmt));
      }
    }

    mysqli_stmt_close($extraStmt);
  }

  $transactionStmt = mysqli_prepare($con, "INSERT INTO `transactions`
    (`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`)
    VALUES (?,?,?,?,?,?,?,?)");
  if (!$transactionStmt) {
    throw new Exception('Failed to prepare transaction insert.');
  }
  $transactionAmount = (int)round($paymentMeta['amount_paid']);
  $transactionType = 'walk_in';
  mysqli_stmt_bind_param(
    $transactionStmt,
    'ississsi',
    $bookingId,
    $guestName,
    $roomNo,
    $transactionAmount,
    $paymentMethod,
    $paymentMeta['transaction_status'],
    $transactionType,
    $adminId
  );
  if (!mysqli_stmt_execute($transactionStmt)) {
    throw new Exception(mysqli_stmt_error($transactionStmt));
  }
  mysqli_stmt_close($transactionStmt);

  createBookingHistoryEntry(
    $bookingId,
    'walkin_booking_created',
    'Walk-in booking created by admin',
    'A walk-in booking was created from the admin panel and marked as a front-desk reservation.',
    [
      'order_id' => $orderId,
      'booking_source' => 'walk_in',
      'room_no' => $roomNo,
      'payment_status' => $paymentMeta['payment_status'],
      'amount_paid' => $paymentMeta['amount_paid'],
      'grand_total' => $roundedGrandTotal,
      'nights' => $nights,
      'created_by_admin' => $adminId,
    ],
    'admin',
    $adminId,
    $adminName
  );

  mysqli_commit($con);

  $emailSent = false;
  if ($isFullyPaid) {
    $emailSent = walkin_send_confirmation_email(
      $guest,
      $room,
      $orderId,
      $checkIn,
      $checkOut,
      $nights,
      $roomNo,
      $roundedRoomTotal,
      $roundedExtrasTotal,
      $roundedGrandTotal,
      $paymentMeta,
      $paymentMethod,
      $paymentNote
    );
  }

  $successMessage = $isFullyPaid
    ? 'Walk-in booking created successfully.'
    : 'Walk-in booking saved to the payment queue. Complete the remaining payment to move it into Booking Records.';

  walkin_json(true, $successMessage, [
    'booking_id' => $bookingId,
    'order_id' => $orderId,
    'booking_source' => 'walk_in',
    'room_no' => $roomNo,
    'email_sent' => $emailSent,
    'booking_status' => $bookingStatus,
    'payment_status' => $paymentMeta['payment_status'],
  ]);
} catch (Throwable $e) {
  mysqli_rollback($con);
  error_log('walkin booking failed: ' . $e->getMessage());
  walkin_json(false, 'Failed to create the walk-in booking. ' . $e->getMessage());
}
