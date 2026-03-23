<?php
require_once('../inc/db_config.php');
require_once('../inc/essentials.php');
adminLogin();

header('Content-Type: application/json');

// Function to send JSON response
function sendResponse($status, $message = '', $data = []) {
    $response = [
        'status' => $status,
        'message' => $message,
        'data' => $data
    ];
    echo json_encode($response);
    exit;
}

try {
    // Check if room_id is provided
    if (!isset($_POST['room_id']) || !is_numeric($_POST['room_id'])) {
        sendResponse('error', 'Invalid room ID');
    }
    
    $room_id = (int)$_POST['room_id'];
    
    // Start transaction
    mysqli_begin_transaction($con);
    
    // 1. Get archived room data
    $room_query = "SELECT * FROM archived_rooms WHERE id = $room_id";
    $room_result = mysqli_query($con, $room_query);
    
    if (mysqli_num_rows($room_result) === 0) {
        throw new Exception("Room not found in archive");
    }
    
    $room_data = mysqli_fetch_assoc($room_result);
    $original_room_id = $room_data['room_id'];
    
    // 2. Check if the room already exists in the main table
    $check_query = "SELECT id FROM rooms WHERE id = $original_room_id";
    $check_result = mysqli_query($con, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        // Room exists, update it
        $update_fields = [];
        foreach ($room_data as $key => $value) {
            if (!in_array($key, ['id', 'room_id', 'archived_at']) && $value !== null) {
                $update_fields[] = "`$key` = " . (is_numeric($value) ? $value : "'" . mysqli_real_escape_string($con, $value) . "'");
            }
        }
        
        $update_query = "UPDATE rooms SET " . implode(', ', $update_fields) . " WHERE id = $original_room_id";
        if (!mysqli_query($con, $update_query)) {
            throw new Exception("Failed to update room: " . mysqli_error($con));
        }
    } else {
        // Room doesn't exist, insert it
        unset($room_data['id']);
        $room_data['id'] = $original_room_id;
        
        $columns = array_keys($room_data);
        $values = array_map(function($value) use ($con) {
            return $value === null ? 'NULL' : "'" . mysqli_real_escape_string($con, $value) . "'";
        }, $room_data);
        
        $insert_query = "INSERT INTO rooms (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $values) . ")";
        if (!mysqli_query($con, $insert_query)) {
            throw new Exception("Failed to insert room: " . mysqli_error($con));
        }
    }
    
    // 3. Restore features
    $features_query = "SELECT features_id FROM archived_room_features WHERE room_id = $room_id";
    $features_result = mysqli_query($con, $features_query);
    
    if ($features_result) {
        // Delete existing features for this room
        mysqli_query($con, "DELETE FROM room_features WHERE room_id = $original_room_id");
        
        // Insert restored features
        while ($feature = mysqli_fetch_assoc($features_result)) {
            $feature_id = (int)$feature['features_id'];
            mysqli_query($con, "INSERT INTO room_features (room_id, features_id) VALUES ($original_room_id, $feature_id)");
        }
    }
    
    // 4. Restore facilities
    $facilities_query = "SELECT facilities_id FROM archived_room_facilities WHERE room_id = $room_id";
    $facilities_result = mysqli_query($con, $facilities_query);
    
    if ($facilities_result) {
        // Delete existing facilities for this room
        mysqli_query($con, "DELETE FROM room_facilities WHERE room_id = $original_room_id");
        
        // Insert restored facilities
        while ($facility = mysqli_fetch_assoc($facilities_result)) {
            $facility_id = (int)$facility['facilities_id'];
            mysqli_query($con, "INSERT INTO room_facilities (room_id, facilities_id) VALUES ($original_room_id, $facility_id)");
        }
    }
    
    // 5. Restore images
    $images_query = "SELECT * FROM archived_room_images WHERE room_id = $room_id";
    $images_result = mysqli_query($con, $images_query);
    
    if ($images_result) {
        // Delete existing images for this room
        mysqli_query($con, "DELETE FROM room_images WHERE room_id = $original_room_id");
        
        // Insert restored images
        while ($image = mysqli_fetch_assoc($images_result)) {
            unset($image['id']);
            $image['room_id'] = $original_room_id;
            
            $columns = array_keys($image);
            $values = array_map(function($value) use ($con) {
                return $value === null ? 'NULL' : "'" . mysqli_real_escape_string($con, $value) . "'";
            }, $image);
            
            $insert_image = "INSERT INTO room_images (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $values) . ")";
            mysqli_query($con, $insert_image);
        }
    }
    
    // 6. Delete from archive tables
    mysqli_query($con, "DELETE FROM archived_room_features WHERE room_id = $room_id");
    mysqli_query($con, "DELETE FROM archived_room_facilities WHERE room_id = $room_id");
    mysqli_query($con, "DELETE FROM archived_room_images WHERE room_id = $room_id");
    mysqli_query($con, "DELETE FROM archived_rooms WHERE id = $room_id");
    
    // Commit transaction
    mysqli_commit($con);
    
    sendResponse('success', 'Room restored successfully');
    
} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($con);
    
    // Log the error
    error_log("Error in restore_room.php: " . $e->getMessage());
    
    // Send error response
    sendResponse('error', 'Failed to restore room: ' . $e->getMessage());
}
