<?php

require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');
  date_default_timezone_set("Asia/Manila");

header('Content-Type: application/json');

$roomId = isset($_GET['room_id']) ? (int)$_GET['room_id'] : 0;
$checkIn = $_GET['check_in'] ?? '';
$checkOut = $_GET['check_out'] ?? '';

if($roomId <= 0 || !$checkIn || !$checkOut){
  echo json_encode(['status'=>0,'msg'=>'Invalid params','rooms'=>[]]);
  exit;
}

// Validate dates
$ci = date_create($checkIn);
$co = date_create($checkOut);
if(!$ci || !$co || $ci >= $co){
  echo json_encode(['status'=>0,'msg'=>'Invalid dates','rooms'=>[]]);
  exit;
}

// Load room
$roomRes = select("SELECT id,name,quantity FROM rooms WHERE id=? AND status=1 AND removed=0",[$roomId],'i');
if(mysqli_num_rows($roomRes)==0){
  echo json_encode(['status'=>0,'msg'=>'Room not found','rooms'=>[]]);
  exit;
}
$room = mysqli_fetch_assoc($roomRes);
$qty = (int)$room['quantity'];

// Initialize seats
$seats = [];
for($i=1;$i<=$qty;$i++){
  $seats[$i] = [
    'label' => (string)$i,
    'status' => 'available',
    'booking_id' => null,
    'room_no' => null
  ];
}

// Find bookings overlapping date range: check_out > check_in AND check_in < check_out
$bRes = select(
  "SELECT bo.booking_id, bo.booking_status
   FROM booking_order bo
   WHERE bo.room_id=?
     AND bo.booking_status='booked'
     AND bo.check_out > ? AND bo.check_in < ?",
  [$roomId, $checkIn, $checkOut],
  'iss'
);

$bookingIds = [];
while($b = mysqli_fetch_assoc($bRes)){
  $bookingIds[] = (int)$b['booking_id'];
}

if(count($bookingIds)){
  $in = implode(',', array_fill(0, count($bookingIds), '?'));
  $types = str_repeat('i', count($bookingIds));
  $params = $bookingIds;

  // Map explicit room numbers
  $detailsRes = select(
    "SELECT booking_id, room_no FROM booking_details WHERE booking_id IN ($in) AND room_no IS NOT NULL AND room_no<>''",
    $params,
    $types
  );
  $occupiedByNo = [];
  while($d = mysqli_fetch_assoc($detailsRes)){
    $roomNoRaw = trim((string)$d['room_no']);
    $label = null;
    if(ctype_digit($roomNoRaw)){
      $num = (int)$roomNoRaw;
      if($num>=1 && $num<=$qty){ $label = $num; }
    }
    if($label === null){
      for($i=1;$i<=$qty;$i++){
        if((string)$i === $roomNoRaw){ $label = $i; break; }
      }
    }
    if($label !== null && isset($seats[$label]) && $seats[$label]['status']==='available'){
      $seats[$label]['status'] = 'occupied';
      $seats[$label]['booking_id'] = (int)$d['booking_id'];
      $seats[$label]['room_no'] = $roomNoRaw;
      $occupiedByNo[$label] = true;
    }
  }

  // Mark remaining seats as occupied if booking count exceeds explicit assignments
  $needOccupied = max(0, count($bookingIds) - count($occupiedByNo));
  for($i=1;$i<=$qty && $needOccupied>0;$i++){
    if($seats[$i]['status']==='available'){
      $seats[$i]['status'] = 'occupied';
      $needOccupied--;
    }
  }
}

$flat = [];
for($i=1;$i<=$qty;$i++){ $flat[] = $seats[$i]; }

echo json_encode([
  'status'=>1,
  'room'=>[
    'room_id' => (int)$room['id'],
    'name' => $room['name'],
    'quantity' => $qty,
    'seats' => $flat
  ]
]);

