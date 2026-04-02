<?php
require 'C:/xampp/htdocs/SE/admin/inc/db_config.php';
ensureAppSchema();
$queries = [
  "SELECT bo.*, bd.*, (SELECT COUNT(*) FROM `archived_booking_extras` abe WHERE abe.`booking_id` = bo.`booking_id`) AS extras_count FROM `archived_booking_order` bo INNER JOIN `archived_booking_details` bd ON bo.booking_id = bd.booking_id LIMIT 1",
  "SELECT ar.*, (SELECT COUNT(*) FROM `archived_room_block_dates` arbd WHERE arbd.`room_id` = ar.`id`) AS block_count FROM `archived_rooms` ar LIMIT 1",
  "SELECT auc.*, (SELECT COUNT(*) FROM `archived_user_reviews` aur WHERE aur.`user_id` = auc.`id`) AS review_count FROM `archived_user_cred` auc LIMIT 1"
];
foreach ($queries as $sql) {
  $res = mysqli_query($con, $sql);
  if (!$res) {
    echo 'FAIL: ' . mysqli_error($con) . PHP_EOL;
    exit(1);
  }
}
echo "OK: archive listing queries validated\n";
?>