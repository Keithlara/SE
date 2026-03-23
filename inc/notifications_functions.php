<?php
// Use a path relative to this file so it works from any caller
require_once(__DIR__ . '/../admin/inc/db_config.php');

/**
 * Add a new notification for a user
 * 
 * @param int $user_id The ID of the user to notify
 * @param string $message The notification message
 * @param string $type The type of notification (booking, payment, refund, system)
 * @param int|null $booking_id Optional booking ID if related to a booking
 * @return bool True on success, false on failure
 */
function add_notification($user_id, $message, $type = 'system', $booking_id = null) {
    $con = $GLOBALS['con'];
    
    $query = "INSERT INTO notifications (user_id, booking_id, message, type, is_read) 
              VALUES (?, ?, ?, ?, 0)";
    
    $values = [$user_id, $booking_id, $message, $type];
    
    return insert($query, $values, 'iiss');
}

/**
 * Mark a notification as read
 * 
 * @param int $notification_id The ID of the notification to mark as read
 * @param int $user_id The ID of the user who owns the notification
 * @return bool True on success, false on failure
 */
function mark_notification_read($notification_id, $user_id) {
    $con = $GLOBALS['con'];
    
    $query = "UPDATE notifications SET is_read = 1 
              WHERE id = ? AND user_id = ?";
    
    return update($query, [$notification_id, $user_id], 'ii');
}

/**
 * Mark all notifications as read for a user
 * 
 * @param int $user_id The ID of the user
 * @return bool True on success, false on failure
 */
function mark_all_notifications_read($user_id) {
    $con = $GLOBALS['con'];
    
    $query = "UPDATE notifications SET is_read = 1 
              WHERE user_id = ? AND is_read = 0";
    
    return update($query, [$user_id], 'i');
}

/**
 * Get unread notifications count for a user
 * 
 * @param int $user_id The ID of the user
 * @return int Number of unread notifications
 */
function get_unread_notifications_count($user_id) {
    $con = $GLOBALS['con'];
    
    $query = "SELECT COUNT(*) as count FROM notifications 
              WHERE user_id = ? AND is_read = 0";
    
    $res = select($query, [$user_id], 'i');
    $data = mysqli_fetch_assoc($res);
    
    return $data ? (int)$data['count'] : 0;
}

/**
 * Get notifications for a user
 * 
 * @param int $user_id The ID of the user
 * @param int $limit Maximum number of notifications to return (0 for no limit)
 * @return array Array of notification data
 */
function get_user_notifications($user_id, $limit = 10) {
    $con = $GLOBALS['con'];
    
    $query = "SELECT * FROM notifications 
              WHERE user_id = ? 
              ORDER BY created_at DESC";
    
    if ($limit > 0) {
        $query .= " LIMIT ?";
        $res = select($query, [$user_id, $limit], 'ii');
    } else {
        $res = select($query, [$user_id], 'i');
    }
    
    $notifications = [];
    while($row = mysqli_fetch_assoc($res)) {
        $notifications[] = $row;
    }
    
    return $notifications;
}

/**
 * Add a refund notification
 * 
 * @param int $user_id The ID of the user receiving the refund
 * @param int $booking_id The ID of the booking being refunded
 * @param float $amount The amount being refunded
 * @return bool True on success, false on failure
 */
function add_refund_notification($user_id, $booking_id, $amount) {
    $message = sprintf(
        'Your refund of ₱%s for booking #%s has been processed successfully.',
        number_format($amount, 2),
        $booking_id
    );
    
    return add_notification(
        $user_id,
        $message,
        'refund',
        $booking_id
    );
}
?>
