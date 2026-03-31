<?php 

  require('admin/inc/db_config.php');
  require('admin/inc/essentials.php');
  require_once('admin/inc/email_config.php');
  require_once('inc/smtp_mailer.php');

  date_default_timezone_set("Asia/Manila");

  session_start();

  if(!(isset($_SESSION['login']) && $_SESSION['login']==true)){
    redirect('index.php');
  }

  if(isset($_SESSION['is_verified']) && $_SESSION['is_verified'] == 0){
    http_response_code(403);
    exit('<script>alert("Please verify your email before making a booking.");window.location.href="profile.php";</script>');
  }

  function abort_booking($message = 'Unable to process booking request')
  {
    http_response_code(400);
    exit($message);
  }

function save_billing_proof(array $file)
{
  if(!isset($file) || $file['error'] !== UPLOAD_ERR_OK){
    abort_booking('Please attach a proof of billing.');
  }

  $allowed_ext = ['jpg','jpeg','png','pdf'];
  $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
  if(!in_array($ext, $allowed_ext)){
    abort_booking('Unsupported proof file. Allowed: JPG, PNG, PDF.');
  }

  if($file['size'] > 10 * 1024 * 1024){
    abort_booking('Proof file is too large. Maximum size is 10MB.');
  }

  $upload_dir = UPLOADS_PATH.'/billing_proofs/';
  if(!is_dir($upload_dir)){
    mkdir($upload_dir, 0777, true);
  }

  $filename = 'BILLING_'.$_SESSION['uId'].'_'.time().'_'.random_int(1000,9999).'.'.$ext;
  $destination = $upload_dir.$filename;

  if(!move_uploaded_file($file['tmp_name'], $destination)){
    abort_booking('Failed to store billing proof. Please try again.');
  }

  return $filename;
}

  if($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['pay_now'])){
    redirect('rooms.php');
  }

  if(!isset($_SESSION['room']['id'])){
    redirect('rooms.php');
  }

  $frm_data = filteration($_POST);

  if(
    empty($_SESSION['booking_csrf']) ||
    empty($frm_data['csrf_token']) ||
    !hash_equals($_SESSION['booking_csrf'], $frm_data['csrf_token'])
  )
  {
    abort_booking('Invalid session, please try again.');
  }

  unset($_SESSION['booking_csrf']);

  $room_id = (int)$_SESSION['room']['id'];
  $room_res = select("SELECT `id`,`name`,`price`,`quantity` FROM `rooms` WHERE `id`=? AND `status`=1 AND `removed`=0 LIMIT 1", [$room_id], 'i');
  if(mysqli_num_rows($room_res)===0){
    abort_booking('Room is no longer available.');
  }
  $room_meta = mysqli_fetch_assoc($room_res);
  $_SESSION['room']['name'] = $room_meta['name'];
  $_SESSION['room']['price'] = $room_meta['price'];

  $checkin = $frm_data['checkin'] ?? '';
  $checkout = $frm_data['checkout'] ?? '';

  $checkin_date = DateTime::createFromFormat('Y-m-d', $checkin);
  $checkout_date = DateTime::createFromFormat('Y-m-d', $checkout);
  $today = new DateTime(date("Y-m-d"));

  if(!$checkin_date || !$checkout_date){
    abort_booking('Invalid dates submitted.');
  }

  if($checkin_date >= $checkout_date){
    abort_booking('Check-out must be after check-in.');
  }

  if($checkin_date < $today){
    abort_booking('Check-in date cannot be in the past.');
  }

  $tb_query = "SELECT COUNT(*) AS `total_bookings` FROM `booking_order`
    WHERE booking_status=? AND room_id=?
    AND check_out > ? AND check_in < ?";
  $tb_fetch = mysqli_fetch_assoc(select($tb_query, ['booked',$room_id,$checkin,$checkout], 'siss'));

  if(($room_meta['quantity'] - $tb_fetch['total_bookings']) <= 0){
    abort_booking('Room not available for the selected dates.');
  }

  $count_days = $checkin_date->diff($checkout_date)->days;
  if($count_days <= 0){
    abort_booking('Stay duration must be at least 1 night.');
  }

  // --- Billing calculation ---
  $room_total   = (float)$room_meta['price'] * $count_days;

  // Extras calculation
  $extras_json  = isset($_POST['extras_json']) ? trim($_POST['extras_json']) : '[]';
  $extras_data  = json_decode($extras_json, true);
  $extras_total = 0.0;
  if(is_array($extras_data)){
    foreach($extras_data as $ex){
      $extras_total += (float)($ex['unit_price'] ?? 0) * (int)($ex['qty'] ?? 1) * $count_days;
    }
  }

  $subtotal_total = $room_total + $extras_total;
  $promo_code = strtoupper(trim((string)($frm_data['promo_code'] ?? '')));
  $discount_amount = 0.0;
  $promo_id = 0;
  if($promo_code !== ''){
    $promo_result = validatePromoForAmount($promo_code, $subtotal_total, (int)$_SESSION['uId']);
    if(!$promo_result['ok']){
      abort_booking($promo_result['message']);
    }
    $discount_amount = (float)$promo_result['discount'];
    $promo_id = (int)($promo_result['promo']['id'] ?? 0);
    $promo_code = strtoupper((string)($promo_result['promo']['code'] ?? $promo_code));
  }

  $grand_total  = max(0, $subtotal_total - $discount_amount); // Full stay cost after promo
  $downpayment  = ceil($grand_total / 2);                 // 50% upfront (rounded up)
  $balance_due  = $grand_total - $downpayment;            // Remaining at check-in

  $_SESSION['room']['payment']    = $grand_total;
  $_SESSION['room']['downpayment'] = $downpayment;
  $_SESSION['room']['available']  = true;

$billing_proof = save_billing_proof($_FILES['billing_proof'] ?? []);

  $chosen_room_no = isset($frm_data['room_no']) ? trim($frm_data['room_no']) : '';
  if($chosen_room_no === ''){
    $chosen_room_no = null;
  }else{
    if(!ctype_digit($chosen_room_no)){
      abort_booking('Invalid room number selection.');
    }
    $chosen_room_no_int = (int)$chosen_room_no;
    if($chosen_room_no_int < 1 || $chosen_room_no_int > (int)$room_meta['quantity']){
      abort_booking('Selected room number is out of range.');
    }

    $seat_check = select(
      "SELECT COUNT(*) AS `taken`
        FROM booking_details bd
        INNER JOIN booking_order bo ON bd.booking_id = bo.booking_id
        WHERE bo.room_id=? AND bo.booking_status='booked'
          AND bo.check_out > ? AND bo.check_in < ?
          AND bd.room_no=?",
      [$room_id, $checkin, $checkout, $chosen_room_no],
      'isss'
    );
    $seat_taken = mysqli_fetch_assoc($seat_check);
    if($seat_taken && (int)$seat_taken['taken'] > 0){
      abort_booking('Selected room number is no longer available.');
    }
  }

  $ORDER_ID = 'ORD_'.$_SESSION['uId'].random_int(11111,9999999);
  $CUST_ID  = $_SESSION['uId'];

// Insert booking record
$query1 = "INSERT INTO `booking_order`(`user_id`, `room_id`, `check_in`, `check_out`,`order_id`) VALUES (?,?,?,?,?)";
insert($query1,[$CUST_ID,$room_id,$checkin,$checkout,$ORDER_ID],'issss');
  
$booking_id = mysqli_insert_id($con);

$booking_note = isset($frm_data['booking_note']) ? trim($frm_data['booking_note']) : '';
if($booking_note === ''){
  $booking_note = null;
} else {
  if(function_exists('mb_substr')){
    $booking_note = mb_substr($booking_note, 0, 500);
  } else {
    $booking_note = substr($booking_note, 0, 500);
  }
}

$query2 = "INSERT INTO `booking_details`(`booking_id`, `room_name`, `price`, `total_pay`,
  `user_name`, `phonenum`, `address`, `room_no`, `booking_note`,
  `extras_total`, `downpayment`, `remaining_balance`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";

insert($query2,[$booking_id,$_SESSION['room']['name'],$_SESSION['room']['price'],
  $grand_total,$frm_data['name'],$frm_data['phonenum'],$frm_data['address'],$chosen_room_no,$booking_note,
  $extras_total,$downpayment,$balance_due],'isiisssssddd');

$update_order = "UPDATE `booking_order` 
  SET `booking_status`='pending',
      `payment_status`='pending',
      `payment_proof`=?,
      `amount_paid`=?,
      `total_amt`=?,
      `downpayment`=?,
      `balance_due`=?,
      `promo_code`=?,
      `discount_amount`=?,
      `trans_id`='OFFLINE',
      `trans_amt`=?,
      `trans_status`='AWAITING_PROOF',
      `trans_resp_msg`='Awaiting manual verification'
  WHERE `booking_id`=?";

$update_result = update($update_order, [$billing_proof, $downpayment, $grand_total, $downpayment, $balance_due, $promo_code !== '' ? $promo_code : null, $discount_amount, $downpayment, $booking_id], 'sddddsddi');
if($update_result === false){
  $dir = UPLOADS_PATH.'/billing_proofs/';
  if(file_exists($dir.$billing_proof)){
    unlink($dir.$billing_proof);
  }
  abort_booking('Failed to record booking, please try again.');
}

// Save selected extras to booking_extras
if(is_array($extras_data) && count($extras_data) > 0){
  foreach($extras_data as $ex){
    $ex_id    = intval($ex['id'] ?? 0);
    $ex_qty   = intval($ex['qty'] ?? 1);
    $ex_price = floatval($ex['unit_price'] ?? 0);
    $ex_name  = isset($ex['name']) ? trim($ex['name']) : '';
    $ex_total = $ex_price * $ex_qty * $count_days;
    if($ex_id > 0 && $ex_qty > 0){
      insert(
        "INSERT INTO `booking_extras` (`booking_id`,`extra_id`,`name`,`quantity`,`unit_price`,`total_price`) VALUES (?,?,?,?,?,?)",
        [$booking_id, $ex_id, $ex_name, $ex_qty, $ex_price, $ex_total], 'iisidd'
      );
    }
  }
}

if($promo_id > 0){
  recordPromoRedemption($promo_id, $booking_id, (int)$CUST_ID, $discount_amount);
}

createBookingHistoryEntry(
  $booking_id,
  'booking_created',
  'Booking submitted',
  'Booking request was submitted and is now awaiting admin confirmation.',
  [
    'order_id' => $ORDER_ID,
    'room_name' => $_SESSION['room']['name'],
    'nights' => $count_days,
    'promo_code' => $promo_code,
    'discount_amount' => $discount_amount,
  ]
);

// Send "booking received" confirmation email to the guest
$user_res = select("SELECT `email`,`name` FROM `user_cred` WHERE `id`=? LIMIT 1", [$CUST_ID], 'i');
if($user_res && mysqli_num_rows($user_res) > 0){
  $user_row = mysqli_fetch_assoc($user_res);
  $guest_email = $user_row['email'] ?? '';
  $guest_name  = $user_row['name']  ?? $frm_data['name'];

  if(filter_var($guest_email, FILTER_VALIDATE_EMAIL)){
    $siteName  = defined('SITE_NAME') ? SITE_NAME : 'Travelers Place';
    $checkin_f  = date('F j, Y', strtotime($checkin));
    $checkout_f = date('F j, Y', strtotime($checkout));
    $nights_label = $count_days . ' ' . ($count_days === 1 ? 'night' : 'nights');
    $room_name_e  = htmlspecialchars($_SESSION['room']['name']);

    $extras_rows_html = '';
    if(is_array($extras_data) && count($extras_data) > 0){
      foreach($extras_data as $ex){
        $ex_name_e  = htmlspecialchars($ex['name'] ?? '');
        $ex_qty_v   = intval($ex['qty'] ?? 1);
        $ex_price_v = floatval($ex['unit_price'] ?? 0);
        $ex_line    = $ex_price_v * $ex_qty_v * $count_days;
        $extras_rows_html .= "
          <tr>
            <td style='padding:8px 14px;color:#6b7280;font-size:13px;border-bottom:1px solid #e5e7eb'>{$ex_name_e} &times;{$ex_qty_v}</td>
            <td style='padding:8px 14px;color:#111827;border-bottom:1px solid #e5e7eb'>&#8369;" . number_format($ex_line,2) . "</td>
          </tr>";
      }
    }

    $discount_row_html = '';
    if($discount_amount > 0){
      $discount_row_html = "
        <tr style='background:#f0fdf4'>
          <td style='padding:8px 14px;color:#047857;font-size:13px;border-bottom:1px solid #dcfce7'>Promo Discount" . ($promo_code !== '' ? " ({$promo_code})" : '') . "</td>
          <td style='padding:8px 14px;color:#047857;border-bottom:1px solid #dcfce7'>-&#8369;" . number_format($discount_amount,2) . "</td>
        </tr>";
    }

    $html_email = "
      <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;background:#fff;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden'>
        <div style='background:#1a1a2e;padding:28px 32px;text-align:center'>
          <h1 style='color:#c8a951;margin:0 0 4px;font-size:24px'>{$siteName}</h1>
          <p style='color:#d1d5db;margin:0;font-size:13px'>Comfort. Convenience. Relaxation.</p>
        </div>
        <div style='padding:32px'>
          <h2 style='color:#1a1a2e;margin:0 0 8px'>Booking Received!</h2>
          <p style='color:#374151;margin:0 0 4px'>Dear <strong>" . htmlspecialchars($guest_name) . "</strong>,</p>
          <p style='color:#374151;margin:0 0 24px'>Thank you for your reservation! Your booking is now <strong>pending admin confirmation</strong>. You will receive another email once it is approved.</p>

          <table style='width:100%;border-collapse:collapse;margin-bottom:16px'>
            <tr style='background:#f9fafb'>
              <td style='padding:10px 14px;color:#6b7280;font-size:13px;border-bottom:1px solid #e5e7eb'>Booking Reference</td>
              <td style='padding:10px 14px;color:#111827;font-weight:bold;border-bottom:1px solid #e5e7eb'>{$ORDER_ID}</td>
            </tr>
            <tr>
              <td style='padding:10px 14px;color:#6b7280;font-size:13px;border-bottom:1px solid #e5e7eb'>Room</td>
              <td style='padding:10px 14px;color:#111827;border-bottom:1px solid #e5e7eb'>{$room_name_e}</td>
            </tr>
            <tr style='background:#f9fafb'>
              <td style='padding:10px 14px;color:#6b7280;font-size:13px;border-bottom:1px solid #e5e7eb'>Check-in</td>
              <td style='padding:10px 14px;color:#111827;border-bottom:1px solid #e5e7eb'>{$checkin_f}</td>
            </tr>
            <tr>
              <td style='padding:10px 14px;color:#6b7280;font-size:13px;border-bottom:1px solid #e5e7eb'>Check-out</td>
              <td style='padding:10px 14px;color:#111827;border-bottom:1px solid #e5e7eb'>{$checkout_f}</td>
            </tr>
            <tr style='background:#f9fafb'>
              <td style='padding:10px 14px;color:#6b7280;font-size:13px;border-bottom:1px solid #e5e7eb'>Duration</td>
              <td style='padding:10px 14px;color:#111827;border-bottom:1px solid #e5e7eb'>{$nights_label}</td>
            </tr>
          </table>

          <h3 style='color:#1a1a2e;font-size:16px;margin:0 0 8px'>Billing Summary</h3>
          <table style='width:100%;border-collapse:collapse;margin-bottom:24px'>
            <tr style='background:#f9fafb'>
              <td style='padding:8px 14px;color:#6b7280;font-size:13px;border-bottom:1px solid #e5e7eb'>Room Charge ({$nights_label})</td>
              <td style='padding:8px 14px;color:#111827;border-bottom:1px solid #e5e7eb'>&#8369;" . number_format($room_total,2) . "</td>
            </tr>
            {$extras_rows_html}
            {$discount_row_html}
            <tr>
              <td style='padding:8px 14px;color:#374151;font-weight:bold;border-bottom:1px solid #e5e7eb'>Total Amount</td>
              <td style='padding:8px 14px;color:#374151;font-weight:bold;border-bottom:1px solid #e5e7eb'>&#8369;" . number_format($grand_total,2) . "</td>
            </tr>
            <tr style='background:#fffbf0'>
              <td style='padding:10px 14px;color:#b8860b;font-weight:bold;border-bottom:1px solid #f0c040'>Downpayment Due (50%)</td>
              <td style='padding:10px 14px;color:#b8860b;font-weight:bold;border-bottom:1px solid #f0c040'>&#8369;" . number_format($downpayment,2) . "</td>
            </tr>
            <tr>
              <td style='padding:10px 14px;color:#374151;font-size:13px'>Remaining Balance (at check-in)</td>
              <td style='padding:10px 14px;color:#374151;font-size:13px'>&#8369;" . number_format($balance_due,2) . "</td>
            </tr>
          </table>

          <div style='background:#f0f9f4;border:1px solid #a7f3d0;border-radius:8px;padding:14px;margin-bottom:16px'>
            <p style='margin:0;color:#065f46;font-size:14px;font-weight:bold'>&#10003; Downpayment proof submitted successfully.</p>
            <p style='margin:4px 0 0;color:#065f46;font-size:13px'>Your payment is under review. You will receive a confirmation email once verified.</p>
          </div>

          <p style='color:#374151;font-size:14px'>If you have any questions, feel free to contact us. We look forward to welcoming you!</p>
          <p style='color:#374151;font-size:14px;margin-top:24px'>Warm regards,<br><strong>{$siteName} Team</strong></p>
        </div>
        <div style='background:#f3f4f6;padding:16px 32px;text-align:center'>
          <p style='color:#9ca3af;font-size:12px;margin:0'>This is an automated message. Please do not reply to this email.</p>
        </div>
      </div>
    ";

    @send_email_smtp_basic($guest_email, $guest_name, "Booking Received – {$siteName}", $html_email);
  }
}

redirect('confirm_booking.php?id='.$room_id.'&booking=success');
