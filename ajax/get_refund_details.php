<?php
require('../inc/db_config.php');
require('../inc/essentials.php');

// Check if user is logged in
if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
    echo json_encode(['status' => 'error', 'message' => 'Please login to view refund details.']);
    exit;
}

if (!isset($_GET['booking_id']) || !is_numeric($_GET['booking_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid booking ID.']);
    exit;
}

$booking_id = (int)$_GET['booking_id'];
$user_id = $_SESSION['uId'];

try {
    // Get booking details
    $query = "SELECT bo.*, bd.*, r.name as room_name, r.price as room_price 
              FROM `booking_order` bo 
              INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
              LEFT JOIN `rooms` r ON bd.room_id = r.id
              WHERE bo.booking_id = ? AND bo.user_id = ?";
    
    $result = select($query, [$booking_id, $user_id], 'ii');
    
    if (mysqli_num_rows($result) === 0) {
        throw new Exception('Booking not found or access denied.');
    }
    
    $booking = mysqli_fetch_assoc($result);
    
    // Format dates
    $booking['check_in'] = date("M d, Y", strtotime($booking['check_in']));
    $booking['check_out'] = date("M d, Y", strtotime($booking['check_out']));
    $booking['datentime'] = date("M d, Y h:i A", strtotime($booking['datentime']));
    
    // Get refund details
    $refund = [
        'status' => 'completed',
        'amount' => $booking['refund_amount'] ?? ($booking['trans_amt'] * 0.5), // 50% refund policy
        'method' => 'Original Payment Method',
        'processed_at' => $booking['datentime'],
        'reference_id' => 'RFND-' . strtoupper(uniqid()),
        'notes' => 'The refund has been processed and the amount will be credited to your original payment method within 3-5 business days.',
        'additional_notes' => 'If you have not received the refund within 5 business days, please contact our customer support.'
    ];
    
    // If there's a payment record, use that for refund details
    $payment_query = "SELECT * FROM `payment` WHERE booking_id = ? ORDER BY id DESC LIMIT 1";
    $payment_result = select($payment_query, [$booking_id], 'i');
    
    if (mysqli_num_rows($payment_result) > 0) {
        $payment = mysqli_fetch_assoc($payment_result);
        $refund['method'] = ucfirst($payment['payment_method']);
        $refund['reference_id'] = $payment['transaction_id'] ?? $refund['reference_id'];
    }
    
    // Mark refund notification as read
    $mark_read_query = "UPDATE `notifications` SET is_read = 1 
                       WHERE booking_id = ? AND user_id = ? AND type = 'refund' AND is_read = 0";
    update($mark_read_query, [$booking_id, $user_id], 'ii');
    
    // Return the response
    echo json_encode([
        'status' => 'success',
        'booking' => $booking,
        'refund' => $refund
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
