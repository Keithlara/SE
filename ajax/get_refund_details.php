<?php
ob_start();
require(__DIR__ . '/../admin/inc/db_config.php');
require(__DIR__ . '/../admin/inc/essentials.php');
if (session_status() === PHP_SESSION_NONE) { session_start(); }
ob_clean();
header('Content-Type: application/json');

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
    $query = "SELECT bo.*, bd.*
              FROM `booking_order` bo 
              INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
              WHERE bo.booking_id = ? AND bo.user_id = ?
              LIMIT 1";
    
    $result = select($query, [$booking_id, $user_id], 'ii');
    
    if (mysqli_num_rows($result) === 0) {
        throw new Exception('Booking not found or access denied.');
    }
    
    $booking = mysqli_fetch_assoc($result);
    
    if ((int)($booking['refund'] ?? 0) !== 1) {
        throw new Exception('Refund details are not available for this booking yet.');
    }

    $processedAtRaw = !empty($booking['confirmed_at']) ? $booking['confirmed_at'] : $booking['datentime'];

    // Format dates
    $booking['check_in'] = date("M d, Y", strtotime($booking['check_in']));
    $booking['check_out'] = date("M d, Y", strtotime($booking['check_out']));
    $booking['datentime'] = date("M d, Y h:i A", strtotime($booking['datentime']));
    
    // Build refund proof URL if available
    $proof_url = null;
    if (!empty($booking['refund_proof'])) {
        $proof_url = rtrim(SITE_URL, '/') . '/' . ltrim($booking['refund_proof'], '/');
    }

    // Get refund details
    $refund = [
        'status' => 'completed',
        'amount' => isset($booking['refund_amount']) && $booking['refund_amount'] !== null
            ? (float)$booking['refund_amount']
            : (float)$booking['trans_amt'],
        'method' => 'Original Payment Method',
        'processed_at' => $processedAtRaw,
        'processed_at_label' => date("M d, Y h:i A", strtotime($processedAtRaw)),
        'reference_id' => 'RFND-' . str_pad((string)$booking_id, 6, '0', STR_PAD_LEFT),
        'proof_url' => $proof_url,
        'notes' => 'The refund has been processed and the amount will be credited to your original payment method within 3-5 business days.',
        'additional_notes' => 'If you have not received the refund within 5 business days, please contact our customer support.'
    ];
    
    // If there's a payment/payments record, use that for refund details
    if (function_exists('appSchemaTableExists') && appSchemaTableExists($con, 'payments')) {
        $payment_query = "SELECT * FROM `payments` WHERE booking_id = ? ORDER BY id DESC LIMIT 1";
        $payment_result = select($payment_query, [$booking_id], 'i');
        if ($payment_result && mysqli_num_rows($payment_result) > 0) {
            $payment = mysqli_fetch_assoc($payment_result);
            if (!empty($payment['payment_method'])) {
                $refund['method'] = ucfirst($payment['payment_method']);
            }
            if (!empty($payment['transaction_id'])) {
                $refund['reference_id'] = $payment['transaction_id'];
            }
        }
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
