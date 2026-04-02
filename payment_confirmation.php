<?php
require('admin/inc/db_config.php');
require('admin/inc/essentials.php');
require_once('admin/inc/email_config.php');
require_once('inc/smtp_mailer.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Manila');

if(!(isset($_SESSION['login']) && $_SESSION['login']==true)){
    redirect('index.php');
}

if(isset($_POST['confirm_payment'])) {
    $frm_data = filteration($_POST);
    $booking_id = (int)($frm_data['booking_id'] ?? 0);
    $total_amount = (float)($frm_data['total_amount'] ?? 0);
    $down_payment = ceil($total_amount / 2);
    $balance_due = $total_amount - $down_payment;
    
    // Handle file upload
    $payment_proof = '';
    if(isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] == 0) {
        $img = $_FILES['payment_proof'];
        $maxSize = 10 * 1024 * 1024; // 10MB
        
        // Validate file
        $detected_type = function_exists('mime_content_type') ? mime_content_type($img['tmp_name']) : ($img['type'] ?? '');
        $allowed_types = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'application/pdf' => 'pdf'
        ];
        if(!isset($allowed_types[$detected_type])) {
            alert('error', 'Only JPG, JPEG, PNG & PDF files are allowed.');
            redirect('bookings.php');
        }
        
        if($img['size'] > $maxSize) {
            alert('error', 'File size too large. Max 10MB allowed.');
            redirect('bookings.php');
        }
        
        // Generate unique filename
        $ext = $allowed_types[$detected_type];
        $payment_proof = 'BILLING_'.$_SESSION['uId'].'_'.time().'_'.random_int(1000,9999).'.'.$ext;
        $upload_path = UPLOADS_PATH.'/billing_proofs/';
        
        // Create directory if it doesn't exist
        if(!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }
        
        if(!move_uploaded_file($img['tmp_name'], $upload_path.$payment_proof)) {
            alert('error', 'Failed to upload payment proof. Please try again.');
            redirect('bookings.php');
        }
    }
    
    // Update booking with payment details
    $query = "UPDATE booking_order SET 
        payment_status = 'partial',
        amount_paid = ?,
        total_amt = ?,
        downpayment = ?,
        balance_due = ?,
        payment_proof = ?,
        booking_status = 'pending',
        trans_amt = ?,
        trans_status = 'AWAITING_PROOF',
        trans_resp_msg = 'Awaiting payment verification'
        WHERE booking_id = ? AND user_id = ?";
    
    $values = [$down_payment, $total_amount, $down_payment, $balance_due, $payment_proof, $down_payment, $booking_id, $_SESSION['uId']];
    
    if(update($query, $values, 'ddddsdii')) {
    // Send confirmation email
    sendBookingConfirmation($booking_id);
        alert('success', 'Payment proof uploaded successfully! Your booking is pending verification.');
    } else {
        // Delete uploaded file if database update fails
        if(!empty($payment_proof) && file_exists($upload_path.$payment_proof)) {
            unlink($upload_path.$payment_proof);
        }
        alert('error', 'Failed to process payment. Please try again.');
    }
    
    redirect('bookings.php');
}

function sendBookingConfirmation($booking_id) {
    global $con;
    
    // Get booking details
    $res = select(
        "SELECT bo.*, u.email, u.name as user_name FROM booking_order bo 
        JOIN user_cred u ON bo.user_id = u.id 
        WHERE bo.booking_id = ? AND bo.user_id = ?",
        [$booking_id, $_SESSION['uId']],
        'ii'
    );
    
    if(mysqli_num_rows($res) == 0) return false;
    
    $booking = mysqli_fetch_assoc($res);
    
    // Email content
    $siteName = defined('SITE_NAME') ? SITE_NAME : 'Travelers Place';
    $subject = "Booking Confirmation #" . $booking['booking_id'] . " - " . $siteName;
    $total_email_amount = (float)($booking['total_amt'] ?? 0);
    if($total_email_amount <= 0){
        $total_email_amount = (float)($booking['trans_amt'] ?? 0) * 2;
    }
    $paid_email_amount = (float)($booking['amount_paid'] ?? 0);
    if($paid_email_amount <= 0){
        $paid_email_amount = (float)($booking['trans_amt'] ?? 0);
    }
    
    $email_content = "
    <h2>Booking Confirmation</h2>
    <p>Dear {$booking['user_name']},</p>
    <p>Thank you for your booking! We have received your payment proof and your booking is now pending verification.</p>
    
    <h3>Booking Details:</h3>
    <p><strong>Booking ID:</strong> #{$booking['booking_id']}</p>
    <p><strong>Check-in:</strong> " . date("F j, Y", strtotime($booking['check_in'])) . "</p>
    <p><strong>Check-out:</strong> " . date("F j, Y", strtotime($booking['check_out'])) . "</p>
    <p><strong>Total Amount:</strong> ₱" . number_format($booking['trans_amt'], 2) . "</p>
    <p><strong>Amount Paid (50%):</strong> ₱" . number_format($booking['trans_amt'] * 0.5, 2) . "</p>
    <p><strong>Status:</strong> Pending Verification</p>
    
    <p>We will verify your payment and update your booking status shortly. You will receive another email once your booking is confirmed.</p>
    
    <p>Thank you for choosing our service!</p>
    
    <p>Best regards,<br>{$siteName} Team</p>
    ";

    if (!function_exists('send_email_smtp_basic')) {
        error_log('sendBookingConfirmation: shared SMTP mailer is unavailable.');
        return false;
    }

    $sent = send_email_smtp_basic($booking['email'], $booking['user_name'], $subject, $email_content);
    if (!$sent) {
        $detail = function_exists('smtp_get_last_error') ? smtp_get_last_error() : 'unknown mailer error';
        error_log("sendBookingConfirmation failed: {$detail}");
    }

    return $sent;
}

// If accessed directly, redirect to bookings page
redirect('bookings.php');
?>
