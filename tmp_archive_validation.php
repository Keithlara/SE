<?php
require 'C:/xampp/htdocs/SE/admin/inc/db_config.php';
require 'C:/xampp/htdocs/SE/admin/inc/essentials.php';

function fail($msg) {
  echo "FAIL: {$msg}\n";
  exit(1);
}

ensureAppSchema();

$token = 'QAARCH_' . time() . '_' . random_int(1000, 9999);
$userEmail = strtolower($token) . '@example.com';
$roomName = 'Room ' . $token;
$orderId = 'ORD-' . $token;
$ticketCode = 'TIC-' . substr(str_replace('_', '', $token), 0, 18);

mysqli_begin_transaction($con);
try {
  mysqli_query($con, "INSERT INTO `user_cred` (`name`,`email`,`username`,`address`,`phonenum`,`pincode`,`dob`,`password`,`is_verified`,`verification_code`,`status`,`profile`,`is_archived`) VALUES ('{$token}','{$userEmail}','{$token}','QA Address','09171234567',1234,'2000-01-01','test',1,NULL,1,NULL,0)");
  if (mysqli_errno($con)) fail('insert user: ' . mysqli_error($con));
  $userId = (int)mysqli_insert_id($con);

  mysqli_query($con, "INSERT INTO `rooms` (`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`is_archived`) VALUES ('{$roomName}',25,1500,1,2,1,'QA room',1,0,0)");
  if (mysqli_errno($con)) fail('insert room: ' . mysqli_error($con));
  $roomId = (int)mysqli_insert_id($con);

  mysqli_query($con, "INSERT INTO `booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`,`is_archived`) VALUES (990100{$userId},{$userId},{$roomId},'2026-05-01','2026-05-03',0,0,'cancelled','{$orderId}','TX-{$token}',800,'TXN_SUCCESS','ok',0,NOW(),'paid','uploads/billing_proofs/{$token}.pdf',NULL,0,800,NOW(),1600,800,800,'QA10',100,1)");
  if (mysqli_errno($con)) fail('insert booking_order: ' . mysqli_error($con));
  $bookingId = 990100 + $userId;
  mysqli_query($con, "UPDATE `booking_order` SET `booking_id`={$bookingId} WHERE `booking_id`=990100{$userId}");

  mysqli_query($con, "INSERT INTO `booking_details` (`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) VALUES ({$bookingId},'{$roomName}',1500,1600,'101','{$token}','09171234567','QA Address','Guest note','Staff note',100,800,800)");
  mysqli_query($con, "INSERT INTO `booking_extras` (`booking_id`,`extra_id`,`name`,`quantity`,`unit_price`,`total_price`) VALUES ({$bookingId},1,'Extra Pillow',2,50,100)");
  mysqli_query($con, "INSERT INTO `booking_history` (`booking_id`,`actor_type`,`actor_id`,`actor_name`,`event_type`,`title`,`details`,`meta_json`) VALUES ({$bookingId},'system',NULL,'System','created','Created','QA history',NULL)");
  mysqli_query($con, "INSERT INTO `transactions` (`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`) VALUES ({$bookingId},'{$token}','101',800,'online','paid','payment',1,NOW())");
  mysqli_query($con, "INSERT INTO `notifications` (`user_id`,`booking_id`,`message`,`type`,`is_read`,`created_at`) VALUES ({$userId},{$bookingId},'QA booking notice','booking',0,NOW())");
  mysqli_query($con, "INSERT INTO `support_tickets` (`ticket_code`,`user_id`,`booking_id`,`order_id`,`subject`,`category`,`priority`,`status`,`assigned_to`,`escalated`,`last_reply_at`,`last_reply_by`,`created_at`,`updated_at`) VALUES ('{$ticketCode}',{$userId},{$bookingId},'{$orderId}','QA support','booking','normal','open',NULL,0,NOW(),'guest',NOW(),NOW())");
  $ticketId = (int)mysqli_insert_id($con);
  mysqli_query($con, "INSERT INTO `support_ticket_messages` (`ticket_id`,`sender_type`,`sender_id`,`sender_name`,`message`,`attachment_path`,`is_internal`,`seen_by_user`,`seen_by_staff`,`created_at`) VALUES ({$ticketId},'guest',{$userId},'{$token}','QA support message',NULL,0,1,0,NOW())");
  mysqli_query($con, "INSERT INTO `guest_notes` (`user_id`,`booking_id`,`note_type`,`title`,`note`,`created_by`,`created_at`,`updated_at`) VALUES ({$userId},{$bookingId},'internal','QA Note','QA guest note',1,NOW(),NOW())");
  mysqli_query($con, "INSERT INTO `room_block_dates` (`room_id`,`room_no`,`start_date`,`end_date`,`block_type`,`reason`,`status`,`created_by`,`created_at`) VALUES ({$roomId},'101','2026-05-05','2026-05-06','maintenance','QA block','active',1,NOW())");
  mysqli_query($con, "INSERT INTO `rating_review` (`booking_id`,`room_id`,`user_id`,`rating`,`review`,`datentime`) VALUES ({$bookingId},{$roomId},{$userId},5,'QA review',NOW())");

  mysqli_query($con, "INSERT INTO `archived_booking_order` (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`) SELECT `booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount` FROM `booking_order` WHERE `booking_id`={$bookingId}");
  mysqli_query($con, "INSERT INTO `archived_booking_details` (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`) SELECT `sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance` FROM `booking_details` WHERE `booking_id`={$bookingId}");
  mysqli_query($con, "INSERT INTO `archived_user_cred` (`id`,`name`,`email`,`username`,`address`,`phonenum`,`pincode`,`dob`,`password`,`is_verified`,`verification_code`,`token`,`t_expire`,`datentime`,`status`,`profile`) SELECT `id`,`name`,`email`,`username`,`address`,`phonenum`,`pincode`,`dob`,`password`,`is_verified`,`verification_code`,`token`,`t_expire`,`datentime`,`status`,`profile` FROM `user_cred` WHERE `id`={$userId}");
  mysqli_query($con, "INSERT INTO `archived_rooms` (`room_id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`is_archived`,`archived_at`) SELECT `id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,1,NOW() FROM `rooms` WHERE `id`={$roomId}");
  $archivedRoomId = (int)mysqli_insert_id($con);
  mysqli_query($con, "INSERT INTO `archived_room_images` (`room_id`,`image`,`thumb`) VALUES ({$archivedRoomId},'qa-room.jpg',1)");
  mysqli_query($con, "INSERT INTO `archived_room_features` (`room_id`,`features_id`) VALUES ({$archivedRoomId},1)");
  mysqli_query($con, "INSERT INTO `archived_room_facilities` (`room_id`,`facilities_id`) VALUES ({$archivedRoomId},1)");

  archiveRefreshBookingChildren($bookingId);
  archiveRefreshRoomRelations($roomId, $archivedRoomId);
  archiveRefreshUserChildren($userId);

  $checks = [
    'archived_booking_extras' => "SELECT COUNT(*) c FROM `archived_booking_extras` WHERE `booking_id`={$bookingId}",
    'archived_booking_history' => "SELECT COUNT(*) c FROM `archived_booking_history` WHERE `booking_id`={$bookingId}",
    'archived_booking_transactions' => "SELECT COUNT(*) c FROM `archived_booking_transactions` WHERE `booking_id`={$bookingId}",
    'archived_booking_notifications' => "SELECT COUNT(*) c FROM `archived_booking_notifications` WHERE `booking_id`={$bookingId}",
    'archived_booking_support_tickets' => "SELECT COUNT(*) c FROM `archived_booking_support_tickets` WHERE `booking_id`={$bookingId}",
    'archived_booking_support_messages' => "SELECT COUNT(*) c FROM `archived_booking_support_messages` WHERE `booking_id`={$bookingId}",
    'archived_booking_guest_notes' => "SELECT COUNT(*) c FROM `archived_booking_guest_notes` WHERE `booking_id`={$bookingId}",
    'archived_room_block_dates' => "SELECT COUNT(*) c FROM `archived_room_block_dates` WHERE `room_id`={$archivedRoomId}",
    'archived_ratings_reviews' => "SELECT COUNT(*) c FROM `archived_ratings_reviews` WHERE `room_id`={$archivedRoomId}",
    'archived_user_notifications' => "SELECT COUNT(*) c FROM `archived_user_notifications` WHERE `user_id`={$userId}",
    'archived_user_guest_notes' => "SELECT COUNT(*) c FROM `archived_user_guest_notes` WHERE `user_id`={$userId}",
    'archived_user_support_tickets' => "SELECT COUNT(*) c FROM `archived_user_support_tickets` WHERE `user_id`={$userId}",
    'archived_user_support_messages' => "SELECT COUNT(*) c FROM `archived_user_support_messages` WHERE `user_id`={$userId}",
    'archived_user_reviews' => "SELECT COUNT(*) c FROM `archived_user_reviews` WHERE `user_id`={$userId}",
  ];
  foreach ($checks as $name => $sql) {
    $row = mysqli_fetch_assoc(mysqli_query($con, $sql));
    if ((int)($row['c'] ?? 0) < 1) fail('missing archived rows for ' . $name);
  }

  mysqli_query($con, "DELETE FROM `booking_extras` WHERE `booking_id`={$bookingId}");
  mysqli_query($con, "DELETE FROM `booking_history` WHERE `booking_id`={$bookingId}");
  mysqli_query($con, "DELETE FROM `transactions` WHERE `booking_id`={$bookingId}");
  mysqli_query($con, "DELETE FROM `notifications` WHERE `user_id`={$userId} AND `booking_id`={$bookingId}");
  mysqli_query($con, "DELETE FROM `support_ticket_messages` WHERE `ticket_id`={$ticketId}");
  mysqli_query($con, "DELETE FROM `support_tickets` WHERE `id`={$ticketId}");
  mysqli_query($con, "DELETE FROM `guest_notes` WHERE `user_id`={$userId} AND `booking_id`={$bookingId}");
  mysqli_query($con, "DELETE FROM `room_block_dates` WHERE `room_id`={$roomId}");
  mysqli_query($con, "DELETE FROM `rating_review` WHERE `user_id`={$userId} AND `room_id`={$roomId} AND `booking_id`={$bookingId}");
  mysqli_query($con, "DELETE FROM `notifications` WHERE `user_id`={$userId} AND `booking_id` IS NULL");
  mysqli_query($con, "DELETE FROM `guest_notes` WHERE `user_id`={$userId} AND `booking_id` IS NULL");

  archiveRestoreBookingChildren($bookingId);
  archiveRestoreRoomRelations($archivedRoomId, $roomId);
  archiveRestoreUserChildren($userId);

  $restoreChecks = [
    'booking_extras' => "SELECT COUNT(*) c FROM `booking_extras` WHERE `booking_id`={$bookingId}",
    'booking_history' => "SELECT COUNT(*) c FROM `booking_history` WHERE `booking_id`={$bookingId}",
    'transactions' => "SELECT COUNT(*) c FROM `transactions` WHERE `booking_id`={$bookingId}",
    'notifications' => "SELECT COUNT(*) c FROM `notifications` WHERE `user_id`={$userId} AND `booking_id`={$bookingId}",
    'support_tickets' => "SELECT COUNT(*) c FROM `support_tickets` WHERE `user_id`={$userId} AND `booking_id`={$bookingId}",
    'support_ticket_messages' => "SELECT COUNT(*) c FROM `support_ticket_messages` WHERE `ticket_id`={$ticketId}",
    'guest_notes' => "SELECT COUNT(*) c FROM `guest_notes` WHERE `user_id`={$userId} AND `booking_id`={$bookingId}",
    'room_block_dates' => "SELECT COUNT(*) c FROM `room_block_dates` WHERE `room_id`={$roomId}",
    'rating_review' => "SELECT COUNT(*) c FROM `rating_review` WHERE `user_id`={$userId} AND `room_id`={$roomId} AND `booking_id`={$bookingId}",
  ];
  foreach ($restoreChecks as $name => $sql) {
    $row = mysqli_fetch_assoc(mysqli_query($con, $sql));
    if ((int)($row['c'] ?? 0) < 1) fail('restore failed for ' . $name);
  }

  mysqli_rollback($con);
  echo "OK: archive child snapshots and restores validated\n";
} catch (Throwable $e) {
  mysqli_rollback($con);
  fail($e->getMessage());
}
?>