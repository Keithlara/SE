<?php
$sessionDir = __DIR__ . '/tmp_sessions';
if(!is_dir($sessionDir)){ mkdir($sessionDir, 0777, true); }
session_save_path($sessionDir);
session_id('walkin-new-flow');
session_start();
$_SESSION['adminLogin']=true;
$_SESSION['adminRole']='admin';
$_SESSION['adminId']=1;
$_SESSION['adminName']='Admin';
$_SERVER['PHP_SELF']='/SE/admin/ajax/walkin_booking.php';
$_POST=[
'action'=>'create_walkin_booking',
'guest_mode'=>'existing','guest_id'=>'9','room_id'=>'4','check_in'=>'2026-04-20','check_out'=>'2026-04-21','adults'=>'2','children'=>'0','room_no'=>'2','walkin_note'=>'new flow test','extras_json'=>'[]','payment_status'=>'partial','payment_method'=>'cash','amount_received'=>'1000','payment_note'=>'new partial'
];
chdir(__DIR__ . '/admin/ajax');
include 'walkin_booking.php';
?>
