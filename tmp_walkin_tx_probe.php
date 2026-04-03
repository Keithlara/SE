<?php
require __DIR__ . '/admin/inc/db_config.php';
$res = mysqli_query($con, "SELECT booking_id,guest_name,amount,method,status,type FROM transactions WHERE booking_id=990008 ORDER BY id ASC");
while($row=mysqli_fetch_assoc($res)){ echo implode('|',$row), PHP_EOL; }
?>
