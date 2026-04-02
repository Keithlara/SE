<?php
ob_start();
require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');
ob_clean();
header('Content-Type: application/json');

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

if(!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
    echo json_encode(['status' => 'error', 'message' => 'Please log in first.']);
    exit;
}

if(isset($_FILES['payment_proof']) && isset($_POST['booking_id'])) {
    $booking_id = (int)$_POST['booking_id'];
    $img = $_FILES['payment_proof'];
    $maxSize = 10 * 1024 * 1024; // 10MB

    $booking_check = select(
        "SELECT `booking_id`,`booking_status` FROM `booking_order` WHERE `booking_id`=? AND `user_id`=? LIMIT 1",
        [$booking_id, (int)$_SESSION['uId']],
        'ii'
    );

    if(!$booking_check || mysqli_num_rows($booking_check) !== 1) {
        echo json_encode(['status' => 'error', 'message' => 'Booking not found for this account.']);
        exit;
    }

    $booking_row = mysqli_fetch_assoc($booking_check);
    if(!in_array((string)($booking_row['booking_status'] ?? ''), ['payment failed', 'pending'], true)) {
        echo json_encode(['status' => 'error', 'message' => 'This booking is not eligible for payment-proof upload.']);
        exit;
    }
    
    // Validate file
    if($img['error'] == UPLOAD_ERR_OK) {
        // Check file size
        if($img['size'] > $maxSize) {
            echo json_encode(['status' => 'error', 'message' => 'File size too large. Max 10MB allowed.']);
            exit;
        }
        
        // Check file type using the actual uploaded file, not the browser-reported MIME header
        $detected_type = function_exists('mime_content_type') ? mime_content_type($img['tmp_name']) : ($img['type'] ?? '');
        $allowed_types = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'application/pdf' => 'pdf'
        ];
        if(!isset($allowed_types[$detected_type])) {
            echo json_encode(['status' => 'error', 'message' => 'Only JPG, PNG, and PDF files are allowed.']);
            exit;
        }
        
        // Generate unique filename
        $ext = $allowed_types[$detected_type];
        $filename = 'BILLING_'.(int)$_SESSION['uId'].'_'.time().'_'.random_int(1000,9999).'.'.$ext;
        $upload_path = UPLOADS_PATH.'/billing_proofs/';
        
        // Create directory if it doesn't exist
        if(!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }
        
        if(move_uploaded_file($img['tmp_name'], $upload_path.$filename)) {
            // Update database with payment proof
            $query = "UPDATE booking_order
                      SET payment_proof = ?, booking_status = 'pending', payment_status = 'pending', trans_status = 'AWAITING_PROOF', trans_resp_msg = 'Payment proof resubmitted for verification'
                      WHERE booking_id = ? AND user_id = ?";
            $values = [$filename, $booking_id, (int)$_SESSION['uId']];
            
            if(update($query, $values, 'sii')) {
                createBookingHistoryEntry(
                    $booking_id,
                    'payment_proof_uploaded',
                    'Payment proof re-uploaded',
                    'Guest uploaded a new payment proof for review.'
                );
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'Payment proof uploaded successfully. Your booking is back in review.',
                    'filename' => $filename
                ]);
            } else {
                unlink($upload_path.$filename); // Delete the uploaded file if DB update fails
                echo json_encode(['status' => 'error', 'message' => 'Failed to update database.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload file.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'File upload error.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>
