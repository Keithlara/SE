<?php
$sessionDir = __DIR__ . '/tmp_sessions';
if(!is_dir($sessionDir)){ mkdir($sessionDir, 0777, true); }
session_save_path($sessionDir);
session_id('walkin-settle-flow');
session_start();
$_SESSION['adminLogin']=true;
$_SESSION['adminRole']='admin';
$_SESSION['adminId']=1;
$_SESSION['adminName']='Admin';
$_SERVER['PHP_SELF']='/SE/admin/ajax/walkin_booking.php';
$_POST=[ 'action'=>'settle_walkin_payment', 'booking_id'=>'990008', 'payment_method'=>'cash', 'payment_note'=>'remaining paid' ];
chdir(__DIR__ . '/admin/ajax');
include 'walkin_booking.php';
?>
