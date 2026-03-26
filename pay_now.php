<?php 

  require('admin/inc/db_config.php');
  require('admin/inc/essentials.php');

  date_default_timezone_set("Asia/Kolkata");

  session_start();

  if(!(isset($_SESSION['login']) && $_SESSION['login']==true)){
    redirect('index.php');
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

  if($file['size'] > 2 * 1024 * 1024){
    abort_booking('Proof file is too large. Maximum size is 2MB.');
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

  $payment = (int)$room_meta['price'] * $count_days;
  $_SESSION['room']['payment'] = $payment;
  $_SESSION['room']['available'] = true;

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
  $CUST_ID = $_SESSION['uId'];
  $TXN_AMOUNT = $_SESSION['room']['payment'];

// Insert booking record
$query1 = "INSERT INTO `booking_order`(`user_id`, `room_id`, `check_in`, `check_out`,`order_id`) VALUES (?,?,?,?,?)";
insert($query1,[$CUST_ID,$room_id,$checkin,$checkout,$ORDER_ID],'issss');
  
$booking_id = mysqli_insert_id($con);

$query2 = "INSERT INTO `booking_details`(`booking_id`, `room_name`, `price`, `total_pay`,
  `user_name`, `phonenum`, `address`, `room_no`, `booking_note`) VALUES (?,?,?,?,?,?,?,?,?)";

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

// Ensure booking_note column exists (idempotent)
$col = mysqli_query($con, "SHOW COLUMNS FROM `booking_details` LIKE 'booking_note'");
if(!$col || mysqli_num_rows($col)==0){
  mysqli_query($con, "ALTER TABLE `booking_details` ADD `booking_note` TEXT NULL");
}

insert($query2,[$booking_id,$_SESSION['room']['name'],$_SESSION['room']['price'],
  $TXN_AMOUNT,$frm_data['name'],$frm_data['phonenum'],$frm_data['address'],$chosen_room_no,$booking_note],'isiisssss');

$col_defs = [
  'payment_status' => "ENUM('pending','partial','paid') DEFAULT 'pending'",
  'payment_proof' => "VARCHAR(255) DEFAULT NULL",
  'amount_paid' => "DECIMAL(10,2) DEFAULT 0.00"
];
foreach($col_defs as $column => $definition){
  $col = mysqli_query($con, "SHOW COLUMNS FROM `booking_order` LIKE '$column'");
  if(!$col || mysqli_num_rows($col)==0){
    mysqli_query($con, "ALTER TABLE `booking_order` ADD `$column` $definition");
  }
}

$update_order = "UPDATE `booking_order` 
  SET `booking_status`='pending',
      `payment_status`='pending',
      `payment_proof`=?,
      `amount_paid`=0,
      `trans_id`='OFFLINE',
      `trans_amt`=?,
      `trans_status`='AWAITING_PROOF',
      `trans_resp_msg`='Awaiting manual verification'
  WHERE `booking_id`=?";

$update_result = update($update_order, [$billing_proof, $TXN_AMOUNT, $booking_id], 'sii');
if($update_result === false){
  $dir = UPLOADS_PATH.'/billing_proofs/';
  if(file_exists($dir.$billing_proof)){
    unlink($dir.$billing_proof);
  }
  abort_booking('Failed to record booking, please try again.');
}

// Save selected extras to booking_extras
$extras_json = isset($_POST['extras_json']) ? trim($_POST['extras_json']) : '[]';
$extras_data = json_decode($extras_json, true);
if(is_array($extras_data) && count($extras_data) > 0){
  foreach($extras_data as $ex){
    $ex_id    = intval($ex['id'] ?? 0);
    $ex_qty   = intval($ex['qty'] ?? 1);
    $ex_price = floatval($ex['unit_price'] ?? 0);
    $ex_name  = isset($ex['name']) ? trim($ex['name']) : '';
    $ex_total = $ex_price * $ex_qty;
    if($ex_id > 0 && $ex_qty > 0){
      insert(
        "INSERT INTO `booking_extras` (`booking_id`,`extra_id`,`name`,`quantity`,`unit_price`,`total_price`) VALUES (?,?,?,?,?,?)",
        [$booking_id, $ex_id, $ex_name, $ex_qty, $ex_price, $ex_total], 'iisidd'
      );
    }
  }
}

redirect('confirm_booking.php?id='.$room_id.'&booking=success');