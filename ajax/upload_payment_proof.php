<?php 
require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');

if(isset($_FILES['payment_proof']) && isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];
    $img = $_FILES['payment_proof'];
    $maxSize = 2 * 1024 * 1024; // 2MB
    
    // Validate file
    if($img['error'] == UPLOAD_ERR_OK) {
        // Check file size
        if($img['size'] > $maxSize) {
            echo json_encode(['status' => 'error', 'message' => 'File size too large. Max 2MB allowed.']);
            exit;
        }
        
        // Check file type
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        if(!in_array($img['type'], $allowed_types)) {
            echo json_encode(['status' => 'error', 'message' => 'Only JPG, JPEG & PNG files are allowed.']);
            exit;
        }
        
        // Generate unique filename
        $ext = pathinfo($img['name'], PATHINFO_EXTENSION);
        $filename = 'PAYMENT_'.time().'_'.$booking_id.'.'.$ext;
        $upload_path = UPLOADS_PATH.'/payment_proofs/';
        
        // Create directory if it doesn't exist
        if(!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }
        
        if(move_uploaded_file($img['tmp_name'], $upload_path.$filename)) {
            // Update database with payment proof
            $query = "UPDATE booking_order SET payment_proof = ? WHERE booking_id = ?";
            $values = [$filename, $booking_id];
            
            if(update($query, $values, 'ss')) {
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'Payment proof uploaded successfully!',
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
