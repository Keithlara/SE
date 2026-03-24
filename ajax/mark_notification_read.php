<?php
/**
 * Mark Notification(s) as Read
 * Handles both single notification and mark all as read
 */

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

// Verify user is logged in
if(!(isset($_SESSION['login']) && $_SESSION['login'] === true) || !isset($_SESSION['uId'])) {
  send_json([
    'status' => 'error',
    'message' => 'Unauthorized',
    'marked' => false
  ]);
}

$user_id = (int)$_SESSION['uId'];

// Get request data
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? $_POST['action'] ?? '';
$notification_id = isset($input['notification_id']) ? (int)$input['notification_id'] : (isset($_POST['notification_id']) ? (int)$_POST['notification_id'] : 0);

if ($action === 'mark_all') {
    // Mark all notifications as read for this user
    $stmt = $con->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0");
    if(!$stmt){
        send_json([
            'status' => 'error',
            'message' => 'Database error: ' . $con->error,
            'marked' => false
        ]);
    }
    
    $stmt->bind_param('i', $user_id);
    if($stmt->execute()){
        $affected = $stmt->affected_rows;
        $stmt->close();
        send_json([
            'status' => 'success',
            'message' => $affected . ' notification(s) marked as read',
            'marked' => true,
            'marked_count' => $affected
        ]);
    } else {
        $stmt->close();
        send_json([
            'status' => 'error',
            'message' => 'Failed to mark notifications as read',
            'marked' => false
        ]);
    }
    
} elseif ($action === 'mark_single' && $notification_id > 0) {
    // Mark single notification as read
    $stmt = $con->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ? AND is_read = 0");
    if(!$stmt){
        send_json([
            'status' => 'error',
            'message' => 'Database error: ' . $con->error,
            'marked' => false
        ]);
    }
    
    $stmt->bind_param('ii', $notification_id, $user_id);
    if($stmt->execute()){
        $affected = $stmt->affected_rows;
        $stmt->close();
        send_json([
            'status' => 'success',
            'message' => 'Notification marked as read',
            'marked' => true,
            'notification_id' => $notification_id,
            'was_unread' => $affected > 0
        ]);
    } else {
        $stmt->close();
        send_json([
            'status' => 'error',
            'message' => 'Failed to mark notification as read',
            'marked' => false
        ]);
    }
    
} else {
    send_json([
        'status' => 'error',
        'message' => 'Invalid action or missing notification_id',
        'marked' => false
    ]);
}
