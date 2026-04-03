<?php
require __DIR__ . '/admin/inc/db_config.php';
$res = select("SELECT booking_id,order_id,booking_status,payment_status,amount_paid,total_amt,balance_due,trans_amt,trans_status,arrival,confirmed_at FROM booking_order WHERE booking_id=?", [990008], 'i');
$row = mysqli_fetch_assoc($res);
var_export($row);
?>
