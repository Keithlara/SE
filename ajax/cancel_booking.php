<?php 

  require('../admin/inc/db_config.php');
  require('../admin/inc/essentials.php');
  require_once('../admin/inc/email_config.php');
  require_once('../inc/smtp_mailer.php');

  date_default_timezone_set("Asia/Manila");
  if (session_status() === PHP_SESSION_NONE) { session_start(); }

  function send_booking_cancelled_email(mysqli $con, int $bookingId, int $userId): bool
  {
    $res = select(
      "SELECT bo.`order_id`, bo.`check_in`, bo.`check_out`, bo.`booking_status`, bo.`total_amt`, bo.`amount_paid`, bo.`balance_due`,
              bd.`room_name`, bd.`room_no`, bd.`user_name`,
              uc.`email`, uc.`name`
       FROM `booking_order` bo
       INNER JOIN `booking_details` bd ON bd.`booking_id` = bo.`booking_id`
       INNER JOIN `user_cred` uc ON uc.`id` = bo.`user_id`
       WHERE bo.`booking_id`=? AND bo.`user_id`=? LIMIT 1",
      [$bookingId, $userId],
      'ii'
    );

    if (!$res || mysqli_num_rows($res) === 0) {
      return false;
    }

    $booking = mysqli_fetch_assoc($res);
    $guestEmail = trim((string)($booking['email'] ?? ''));
    $guestName = trim((string)($booking['name'] ?? $booking['user_name'] ?? 'Guest'));

    if (!filter_var($guestEmail, FILTER_VALIDATE_EMAIL)) {
      return false;
    }

    $siteName = defined('SITE_NAME') ? SITE_NAME : 'Travelers Place';
    $roomLabel = trim((string)($booking['room_name'] ?? 'Room'));
    $roomNo = trim((string)($booking['room_no'] ?? ''));
    if ($roomNo !== '') {
      $roomLabel .= ' - Room ' . $roomNo;
    }

    $subject = 'Booking Cancelled - ' . (string)($booking['order_id'] ?? ('#' . $bookingId));
    $checkIn = !empty($booking['check_in']) ? date('F j, Y', strtotime((string)$booking['check_in'])) : 'N/A';
    $checkOut = !empty($booking['check_out']) ? date('F j, Y', strtotime((string)$booking['check_out'])) : 'N/A';
    $safeGuestName = htmlspecialchars($guestName, ENT_QUOTES, 'UTF-8');
    $safeOrderId = htmlspecialchars((string)($booking['order_id'] ?? $bookingId), ENT_QUOTES, 'UTF-8');
    $safeRoom = htmlspecialchars($roomLabel, ENT_QUOTES, 'UTF-8');

    $html = "
      <div style='font-family:Arial,sans-serif;max-width:640px;margin:0 auto;background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden'>
        <div style='background:#7f1d1d;padding:26px 30px'>
          <h1 style='margin:0;color:#fff;font-size:24px'>{$siteName}</h1>
          <p style='margin:6px 0 0;color:#fecaca;font-size:13px'>Booking status update</p>
        </div>
        <div style='padding:28px 30px'>
          <h2 style='margin:0 0 12px;color:#0f172a;font-size:22px'>Booking cancelled</h2>
          <p style='margin:0 0 18px;color:#475569;line-height:1.7'>Hello <strong>{$safeGuestName}</strong>, your booking has been marked as <strong>Cancelled</strong>.</p>
          <table style='width:100%;border-collapse:collapse;margin-bottom:18px'>
            <tr><td style='padding:10px 12px;border-bottom:1px solid #e5e7eb;color:#64748b;font-size:13px'>Booking Reference</td><td style='padding:10px 12px;border-bottom:1px solid #e5e7eb;color:#111827;font-weight:700'>{$safeOrderId}</td></tr>
            <tr><td style='padding:10px 12px;border-bottom:1px solid #e5e7eb;color:#64748b;font-size:13px'>Room</td><td style='padding:10px 12px;border-bottom:1px solid #e5e7eb;color:#111827'>{$safeRoom}</td></tr>
            <tr><td style='padding:10px 12px;border-bottom:1px solid #e5e7eb;color:#64748b;font-size:13px'>Stay Dates</td><td style='padding:10px 12px;border-bottom:1px solid #e5e7eb;color:#111827'>{$checkIn} to {$checkOut}</td></tr>
            <tr><td style='padding:10px 12px;color:#64748b;font-size:13px'>Status</td><td style='padding:10px 12px;color:#b91c1c;font-weight:700'>Cancelled</td></tr>
          </table>
          <p style='margin:0;color:#64748b;line-height:1.7'>If you need help with a new schedule or another booking, please contact {$siteName} support.</p>
        </div>
      </div>";

    return send_email_smtp_basic($guestEmail, $guestName, $subject, $html);
  }


  if(!(isset($_SESSION['login']) && $_SESSION['login']==true)){
    redirect('index.php');
  }

  if(isset($_POST['cancel_booking']))
  {
    $frm_data = filteration($_POST);

    $query = "UPDATE `booking_order` SET `booking_status`=?, `refund`=? 
      WHERE `booking_id`=? AND `user_id`=?";

    $values = ['cancelled',0,$frm_data['id'],$_SESSION['uId']];

    $result = update($query,$values,'siii');

    if($result){
      createBookingHistoryEntry(
        (int)$frm_data['id'],
        'booking_cancelled',
        'Booking cancelled',
        'Guest cancelled the booking before arrival.'
      );
      send_booking_cancelled_email($con, (int)$frm_data['id'], (int)$_SESSION['uId']);
    }

    echo $result;
  }

?>
