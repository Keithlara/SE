<?php
require __DIR__ . '/admin/inc/db_config.php';
$res = select("SELECT bo.booking_id, bo.order_id, bo.booking_status, bo.payment_status, bo.arrival, bo.check_in, bo.check_out, bd.room_no FROM booking_order bo LEFT JOIN booking_details bd ON bd.booking_id=bo.booking_id WHERE bo.booking_source='walk_in' ORDER BY bo.booking_id DESC LIMIT 6", [], '');
while($row=mysqli_fetch_assoc($res)){ echo implode('|',$row), PHP_EOL; }
?>
