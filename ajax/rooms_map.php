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

// Apply active room blocks overlapping the requested stay range.
if(function_exists('appSchemaTableExists') && appSchemaTableExists($con, 'room_block_dates')){
  $blockRes = select(
    "SELECT `room_no`
     FROM `room_block_dates`
     WHERE `room_id`=? AND `status`='active'
       AND `end_date` >= ? AND `start_date` < ?",
    [$roomId, $checkIn, $checkOut],
    'iss'
  );

  $hasFullBlock = false;
  while($blockRes && $blk = mysqli_fetch_assoc($blockRes)){
    $blkNo = trim((string)($blk['room_no'] ?? ''));
    if($blkNo === ''){
      $hasFullBlock = true;
      break;
    }
    if(ctype_digit($blkNo)){
      $idx = (int)$blkNo;
      if($idx >= 1 && $idx <= $qty && isset($seats[$idx])){
        $seats[$idx]['status'] = 'occupied';
        $seats[$idx]['booking_id'] = null;
        $seats[$idx]['room_no'] = $blkNo;
      }
    }
  }

  if($hasFullBlock){
    for($i=1;$i<=$qty;$i++){
      $seats[$i]['status'] = 'occupied';
      $seats[$i]['booking_id'] = null;
      $seats[$i]['room_no'] = (string)$i;
    }
  }
}

// Find overlapping reservations and mark online pending bookings immediately.
$todayStr = date('Y-m-d');
$bRes = select(
  "SELECT bo.`booking_id`, bo.`booking_status`, bo.`arrival`,
          COALESCE(bo.`booking_source`, 'online') AS `booking_source`,
          bo.`check_in`, bd.`room_no`
   FROM `booking_order` bo
   LEFT JOIN `booking_details` bd ON bd.`booking_id` = bo.`booking_id`
   WHERE bo.`room_id`=?
     AND bo.`is_archived`=0
     AND bo.`booking_status` IN ('pending','booked')
     AND bo.`check_out` > ? AND bo.`check_in` < ?",
  [$roomId, $checkIn, $checkOut],
  'iss'
);

$bookingIds = [];
$bookingStatusMap = [];
while($b = mysqli_fetch_assoc($bRes)){
  $bid = (int)$b['booking_id'];
  $bookingIds[] = $bid;
  $isWalkIn = (($b['booking_source'] ?? 'online') === 'walk_in');
  $hasAssignedRoom = trim((string)($b['room_no'] ?? '')) !== '';
  $walkInOccupied = $isWalkIn && $hasAssignedRoom && ((string)($b['check_in'] ?? '') <= $todayStr);
  $status = ($walkInOccupied || ((int)($b['arrival'] ?? 0) === 1 && ($b['booking_status'] ?? '') === 'booked'))
    ? 'occupied'
    : 'pending';
  $bookingStatusMap[$bid] = $status;
}

if(count($bookingIds)){
  $in = implode(',', array_fill(0, count($bookingIds), '?'));
  $types = str_repeat('i', count($bookingIds));
  $params = $bookingIds;

  // Map explicit room numbers first so the chosen room shows as pending immediately.
  $detailsRes = select(
    "SELECT `booking_id`, `room_no`
     FROM `booking_details`
     WHERE `booking_id` IN ($in) AND `room_no` IS NOT NULL AND `room_no`<>''",
    $params,
    $types
  );
  $assignedByNo = [];
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
      $seats[$label]['status'] = $bookingStatusMap[(int)$d['booking_id']] ?? 'pending';
      $seats[$label]['booking_id'] = (int)$d['booking_id'];
      $seats[$label]['room_no'] = $roomNoRaw;
      $assignedByNo[$label] = true;
    }
  }

  $needOccupied = 0;
  $needPending = 0;
  foreach($bookingIds as $bid){
    $status = $bookingStatusMap[$bid] ?? 'pending';
    if($status === 'occupied'){
      $needOccupied++;
    } else {
      $needPending++;
    }
  }

  foreach($assignedByNo as $idx => $_assigned){
    $assignedStatus = $seats[$idx]['status'];
    if($assignedStatus === 'occupied'){
      $needOccupied--;
    } elseif($assignedStatus === 'pending'){
      $needPending--;
    }
  }

  for($i=1;$i<=$qty && ($needOccupied>0 || $needPending>0);$i++){
    if($seats[$i]['status'] !== 'available'){
      continue;
    }
    if($needOccupied>0){
      $seats[$i]['status'] = 'occupied';
      $needOccupied--;
    } elseif($needPending>0){
      $seats[$i]['status'] = 'pending';
      $needPending--;
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
