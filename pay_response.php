<?php 

  require('admin/inc/db_config.php');
  require('admin/inc/essentials.php');
  require_once('inc/booking_notifications.php');

  require('inc/paytm/config_paytm.php');
  require('inc/paytm/encdec_paytm.php');

  date_default_timezone_set("Asia/Kolkata");

  session_start();
  unset($_SESSION['room']);

  function regenrate_session($uid)
  {
    $user_q = select("SELECT * FROM `user_cred` WHERE `id`=? LIMIT 1",[$uid],'i');
    $user_fetch = mysqli_fetch_assoc($user_q);

    $_SESSION['login'] = true;
    $_SESSION['uId'] = $user_fetch['id'];
    $_SESSION['uName'] = $user_fetch['name'];
    $_SESSION['uPic'] = $user_fetch['profile'];
    $_SESSION['uPhone'] = $user_fetch['phonenum'];
  }


  header("Pragma: no-cache");
  header("Cache-Control: no-cache");
  header("Expires: 0");

  $paytmChecksum = "";
  $paramList = array();

  $isValidChecksum = "FALSE";

  $paramList = $_POST;
  $paytmChecksum = isset($_POST["CHECKSUMHASH"]) ? $_POST["CHECKSUMHASH"] : ""; //Sent by Paytm pg

  $isValidChecksum = verifychecksum_e($paramList, PAYTM_MERCHANT_KEY, $paytmChecksum); //will return TRUE or FALSE string.


  if($isValidChecksum == "TRUE") 
  {
    $slct_query = "SELECT `booking_id`, `user_id` FROM `booking_order` 
      WHERE `order_id`='$_POST[ORDERID]'";

    $slct_res = mysqli_query($con,$slct_query);

    if(mysqli_num_rows($slct_res)==0){
      redirect('index.php');
    }

    $slct_fetch = mysqli_fetch_assoc($slct_res);

    if(!(isset($_SESSION['login']) && $_SESSION['login']==true)){
      regenrate_session($slct_fetch['user_id']);
    }

    if ($_POST["STATUS"] == "TXN_SUCCESS") 
    {
      // Mark as booked (confirmed). If confirmed_at column exists, set it too.
      $col_res = mysqli_query($con, "SHOW COLUMNS FROM `booking_order` LIKE 'confirmed_at'");
      $has_confirmed_at = ($col_res && mysqli_num_rows($col_res) > 0);

      $upd_query = "UPDATE `booking_order` SET `booking_status`='booked'," .
        ($has_confirmed_at ? " `confirmed_at`=NOW()," : "") . "
        `trans_id`='$_POST[TXNID]',`trans_amt`='$_POST[TXNAMOUNT]',
        `trans_status`='$_POST[STATUS]',`trans_resp_msg`='$_POST[RESPMSG]' 
        WHERE `booking_id`='$slct_fetch[booking_id]'";

      mysqli_query($con,$upd_query);

      // Best-effort external notifications (email/SMS). Do not block redirect.
      try {
        $bk_res = select(
          "SELECT bo.*, u.email, u.name as user_name, u.phonenum,
                  bd.room_name, bd.room_no
           FROM booking_order bo
           JOIN user_cred u ON bo.user_id = u.id
           LEFT JOIN booking_details bd ON bd.booking_id = bo.booking_id
           WHERE bo.booking_id = ? LIMIT 1",
          [$slct_fetch['booking_id']],
          "i"
        );
        if($bk_res && mysqli_num_rows($bk_res) > 0){
          $booking = mysqli_fetch_assoc($bk_res);
          notify_booking_confirmed($booking);
        }
      } catch (Exception $e) {
        error_log("notify_booking_confirmed failed (pay_response): " . $e->getMessage());
      }
    }
    else 
    {
      $upd_query = "UPDATE `booking_order` SET `booking_status`='payment failed',
        `trans_id`='$_POST[TXNID]',`trans_amt`='$_POST[TXNAMOUNT]',
        `trans_status`='$_POST[STATUS]',`trans_resp_msg`='$_POST[RESPMSG]' 
        WHERE `booking_id`='$slct_fetch[booking_id]'";

      mysqli_query($con,$upd_query);

    }
    redirect('pay_status.php?order='.$_POST['ORDERID']);

  }
  else{
    redirect('index.php');
  }





?>