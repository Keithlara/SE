<?php
require __DIR__ . '/admin/inc/db_config.php';
$res = mysqli_query($con, "SELECT booking_id,order_id,booking_status,payment_status,amount_paid,total_amt,balance_due,booking_source FROM booking_order WHERE booking_source='walk_in' ORDER BY booking_id DESC LIMIT 5");
while($row=mysqli_fetch_assoc($res)){ echo implode('|',$row), PHP_EOL; }
?>
