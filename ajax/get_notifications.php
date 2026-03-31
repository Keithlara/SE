<?php

ini_set('display_errors', 0);
error_reporting(E_ALL);
ob_start();

$responded = false;
register_shutdown_function(function() use (&$responded){
  $error = error_get_last();
  if(!$responded && $error){
    if(ob_get_length()){
      ob_clean();
    }
    header('Content-Type: application/json', true, 500);
    echo json_encode([
      'status' => 'error',
      'message' => $error['message'],
      'file' => $error['file'] ?? null,
      'line' => $error['line'] ?? null
    ]);
  }
});

require(__DIR__.'/../admin/inc/db_config.php');
require(__DIR__.'/../admin/inc/essentials.php');

if (session_status() === PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json');

function send_json($payload){
  global $responded;
  if(ob_get_length()){
    ob_clean();
  }
  $responded = true;
  echo json_encode($payload);
  exit;
}

if(!(isset($_SESSION['login']) && $_SESSION['login'] === true) || !isset($_SESSION['uId'])) {
  send_json([
    'status' => 'error',
    'message' => 'Unauthorized',
    'notifications' => [],
    'unread_count' => 0
  ]);
}

$user_id = (int)$_SESSION['uId'];

// Get limit from request (default 10, use 5 for dropdown)
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
if ($limit < 1 || $limit > 100) $limit = 10;

$notifications = [];

$stmt = $con->prepare("SELECT n.id, n.message, n.is_read, n.created_at, n.type, n.booking_id, bo.booking_status, bo.refund_proof
          FROM notifications n
          JOIN booking_order bo ON n.booking_id = bo.booking_id
          WHERE n.user_id = ?
          ORDER BY n.created_at DESC
          LIMIT ?");
if(!$stmt){
  error_log('[notifications] prepare failed: '.$con->error);
  send_json([
    'status' => 'error',
    'message' => 'Failed to load notifications'
  ]);
}
$stmt->bind_param('ii', $user_id, $limit);
if(!$stmt->execute()){
  error_log('[notifications] execute failed: '.$stmt->error);
  send_json([
    'status' => 'error',
    'message' => 'Failed to load notifications'
  ]);
}
$res = $stmt->get_result();
while($row = $res->fetch_assoc()) {
  $entry = [
    'id' => (int)$row['id'],
    'message' => $row['message'],
    'is_read' => (bool)$row['is_read'],
    'created_at' => $row['created_at'],
    'type' => $row['type'] ?? 'system',
    'booking_id' => (int)$row['booking_id'],
    'booking_status' => $row['booking_status']
  ];
  if(!empty($row['refund_proof'])){
    $entry['refund_proof_url'] = '/' . ltrim($row['refund_proof'], '/');
  }
  $notifications[] = $entry;
}
$stmt->close();

$stmt = $con->prepare("SELECT COUNT(*) AS total FROM notifications WHERE user_id=? AND is_read=0");
if(!$stmt){
  error_log('[notifications] unread prepare failed: '.$con->error);
  $unread_count = 0;
} else {
  $stmt->bind_param('i', $user_id);
  if($stmt->execute()){
    $res = $stmt->get_result();
    $unread_row = $res->fetch_assoc();
    $unread_count = (int)$unread_row['total'];
  } else {
    error_log('[notifications] unread execute failed: '.$stmt->error);
    $unread_count = 0;
  }
  $stmt->close();
}

send_json([
  'status' => 'success',
  'notifications' => $notifications,
  'unread_count' => $unread_count
]);

