<?php 
ob_start();
require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');
ob_clean();
header('Content-Type: application/json');

  date_default_timezone_set("Asia/Manila");

  if(isset($_POST['check_availability']))
  {
    $frm_data = filteration($_POST);
    $status = "";
    $result = "";

    $today_date = new DateTime(date("Y-m-d"));
    $checkin_date = new DateTime($frm_data['check_in']);
    $checkout_date = new DateTime($frm_data['check_out']);

    if($checkin_date == $checkout_date){
      $status = 'check_in_out_equal';
      $result = json_encode(["status"=>$status]);
    }
    else if($checkout_date < $checkin_date){
      $status = 'check_out_earlier';
      $result = json_encode(["status"=>$status]);
    }
    else if($checkin_date < $today_date){
      $status = 'check_in_earlier';
      $result = json_encode(["status"=>$status]);
    }

    if($status!=''){
      echo $result;
    }
    else{
      if (session_status() === PHP_SESSION_NONE) { session_start(); }

      $tb_query = "SELECT COUNT(*) AS `total_bookings` FROM `booking_order`
        WHERE `room_id`=?
          AND `is_archived`=0
          AND `booking_status` IN ('pending','booked')
          AND `check_out` > ? AND `check_in` < ?";

      $values = [$_SESSION['room']['id'],$frm_data['check_in'],$frm_data['check_out']];
      $tb_fetch = mysqli_fetch_assoc(select($tb_query,$values,'iss'));
      
      $rq_result = select("SELECT `quantity`,`price` FROM `rooms` WHERE `id`=?",[$_SESSION['room']['id']],'i');
      $rq_fetch = mysqli_fetch_assoc($rq_result);

      if(($rq_fetch['quantity']-$tb_fetch['total_bookings'])==0){
        $status = 'unavailable';
        $result = json_encode(['status'=>$status]);
        echo $result;
        exit;
      }

      $count_days  = date_diff($checkin_date,$checkout_date)->days;
      $price_night = (float)($rq_fetch['price'] ?? $_SESSION['room']['price']);
      $room_total  = $price_night * $count_days;

      $_SESSION['room']['payment']    = $room_total;
      $_SESSION['room']['available']  = true;
      
      $result = json_encode([
        "status"       => 'available',
        "days"         => $count_days,
        "payment"      => $room_total,       // kept for backward compat
        "price_night"  => $price_night,
        "room_total"   => $room_total,
      ]);
      echo $result;
    }

  }

?>
