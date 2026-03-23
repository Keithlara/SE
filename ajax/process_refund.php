<?php
require_once('../admin/inc/db_config.php');
require_once('../admin/inc/essentials.php');
require_once('../inc/notifications_functions.php');

header('Content-Type: application/json');

// Check if user is admin
if(!(isset($_SESSION['adminLogin']) && $_SESSION['adminLogin'] == true)){
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);
$booking_id = isset($input['booking_id']) ? (int)$input['booking_id'] : 0;
$refund_amount = isset($input['refund_amount']) ? (float)$input['refund_amount'] : 0;

if($booking_id <= 0 || $refund_amount <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid booking ID or refund amount']);
    exit;
}

// Start transaction
mysqli_begin_transaction($con);

try {
    // Get booking details
    $booking_query = "SELECT user_id, trans_amt, refund FROM booking_order WHERE booking_id = ?";
    $booking_data = select($booking_query, [$booking_id], 'i');
    
    if(mysqli_num_rows($booking_data) === 0) {
        throw new Exception('Booking not found');
    }
    
    $booking = mysqli_fetch_assoc($booking_data);
    
    // Check if already refunded
    if($booking['refund'] == 1) {
        throw new Exception('This booking has already been refunded');
    }
    
    // Update booking with refund status
    $update_query = "UPDATE booking_order SET refund = 1, booking_status = 'cancelled' WHERE booking_id = ?";
    if(!update($update_query, [$booking_id], 'i')) {
        throw new Exception('Failed to update booking status');
    }
    
    // Add refund record to payment table if exists
    $payment_query = "INSERT INTO payments (booking_id, user_id, amount, payment_type, status, created_at) 
                     VALUES (?, ?, ?, 'refund', 'completed', NOW())";
    
    // Check if payments table exists
    $table_check = mysqli_query($con, "SHOW TABLES LIKE 'payments'");
    if(mysqli_num_rows($table_check) > 0) {
        if(!update($payment_query, [$booking_id, $booking['user_id'], $refund_amount], 'iid')) {
            throw new Exception('Failed to record payment');
        }
    }
    
    // Add notification for the user
    $message = sprintf(
        'Your refund of ₱%s for booking #%s has been processed successfully. The amount will be credited to your account within 3-5 business days.',
        number_format($refund_amount, 2),
        $booking_id
    );
    
    if(!add_notification($booking['user_id'], $message, 'refund', $booking_id)) {
        throw new Exception('Failed to send notification');
    }
    
    // Commit transaction
    mysqli_commit($con);
    
    // Send email notification
    $user_query = "SELECT email, name FROM user_cred WHERE id = ?";
    $user_data = select($user_query, [$booking['user_id']], 'i');
    
    if(mysqli_num_rows($user_data) > 0) {
        $user = mysqli_fetch_assoc($user_data);
        $to = $user['email'];
        $subject = "Refund Processed for Booking #$booking_id";
        $body = "
            <h2>Refund Processed</h2>
            <p>Dear {$user['name']},</p>
            <p>We have processed your refund for booking #$booking_id.</p>
            <p><strong>Refund Amount:</strong> ₱" . number_format($refund_amount, 2) . "</p>
            <p>The amount will be credited to your original payment method within 3-5 business days.</p>
            <p>If you have any questions, please contact our support team.</p>
            <p>Best regards,<br>Resort Management</p>
        ";
        
        // Use your existing email sending function
        send_mail($to, $subject, $body);
    }
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Refund processed successfully',
        'refund_amount' => $refund_amount
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($con);
    
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
