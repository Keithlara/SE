<?php
require('admin/inc/db_config.php');
require('admin/inc/essentials.php');
require('admin/inc/smtp/PHPMailerAutoload.php');

if(!(isset($_SESSION['login']) && $_SESSION['login']==true)){
    redirect('index.php');
}

if(isset($_POST['confirm_payment'])) {
    $frm_data = filteration($_POST);
    $booking_id = $frm_data['booking_id'];
    $total_amount = $frm_data['total_amount'];
    $down_payment = $total_amount * 0.5; // 50% down payment
    
    // Handle file upload
    $payment_proof = '';
    if(isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] == 0) {
        $img = $_FILES['payment_proof'];
        $maxSize = 2 * 1024 * 1024; // 2MB
        
        // Validate file
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
        if(!in_array($img['type'], $allowed_types)) {
            alert('error', 'Only JPG, JPEG, PNG & PDF files are allowed.');
            redirect('bookings.php');
        }
        
        if($img['size'] > $maxSize) {
            alert('error', 'File size too large. Max 2MB allowed.');
            redirect('bookings.php');
        }
        
        // Generate unique filename
        $ext = pathinfo($img['name'], PATHINFO_EXTENSION);
        $payment_proof = 'PAYMENT_'.time().'_'.$booking_id.'.'.$ext;
        $upload_path = UPLOADS_PATH.'/payment_proofs/';
        
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
        payment_proof = ?,
        booking_status = 'pending',
        trans_resp_msg = 'Awaiting payment verification'
        WHERE booking_id = ? AND user_id = ?";
    
    $values = [$down_payment, $payment_proof, $booking_id, $_SESSION['uId']];
    
    if(update($query, $values, 'dssi')) {
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
    $subject = "Booking Confirmation #" . $booking['booking_id'];
    
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
    
    <p>Best regards,<br>Resort Management</p>
    ";
    
    // Send email using PHPMailer
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_EMAIL;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;
        
        $mail->setFrom(SMTP_EMAIL, 'Resort Management');
        $mail->addAddress($booking['email'], $booking['user_name']);
        
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $email_content;
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log error if needed
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

// If accessed directly, redirect to bookings page
redirect('bookings.php');
?>
