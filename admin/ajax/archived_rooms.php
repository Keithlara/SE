<?php 
require('../inc/db_config.php');
require('../inc/essentials.php');
adminLogin();

// Set content type to JSON
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

// Handle get archived rooms request
if(isset($_POST['get_archived_rooms'])) {
    try {
        // Check if archived_rooms table exists
        $table_check = mysqli_query($con, "SHOW TABLES LIKE 'archived_rooms'");
        if(mysqli_num_rows($table_check) == 0) {
            sendResponse('error', 'Archived rooms table does not exist', []);
        }
        
        // Get all archived rooms
        $query = "SELECT 
                    ar.id,
                    ar.room_id,
                    ar.name,
                    ar.area,
                    ar.price,
                    ar.quantity,
                    ar.adult,
                    ar.children,
                    ar.description,
                    ar.status,
                    ar.removed,
                    ar.archived_at
                  FROM `archived_rooms` ar 
                  ORDER BY ar.archived_at DESC, ar.id DESC";
        
        $res = mysqli_query($con, $query);
        
        if(!$res) {
            throw new Exception("Database error: " . mysqli_error($con));
        }
        
        $rooms = [];
        while($row = mysqli_fetch_assoc($res)) {
            // Get features
            $features_query = "SELECT f.name 
                             FROM `archived_room_features` arf 
                             JOIN `features` f ON arf.features_id = f.id 
                             WHERE arf.room_id = " . (int)$row['id'];
            $features_res = mysqli_query($con, $features_query);
            $features = [];
            if($features_res) {
                while($f = mysqli_fetch_assoc($features_res)) {
                    $features[] = $f['name'];
                }
            }
            
            // Get facilities
            $facilities_query = "SELECT f.name 
                               FROM `archived_room_facilities` arf 
                               JOIN `facilities` f ON arf.facilities_id = f.id 
                               WHERE arf.room_id = " . (int)$row['id'];
            $facilities_res = mysqli_query($con, $facilities_query);
            $facilities = [];
            if($facilities_res) {
                while($f = mysqli_fetch_assoc($facilities_res)) {
                    $facilities[] = $f['name'];
                }
            }
            
            // Get images
            $images_query = "SELECT * FROM `archived_room_images` WHERE `room_id` = " . (int)$row['id'];
            $images_res = mysqli_query($con, $images_query);
            $images = [];
            if($images_res) {
                while($img = mysqli_fetch_assoc($images_res)) {
                    if(isset($img['image'])) {
                        $images[] = $img['image'];
                    }
                }
            }
            
            $row['features'] = $features;
            $row['facilities'] = $facilities;
            $row['images'] = $images;
            $rooms[] = $row;
        }
        
        sendResponse('success', '', $rooms);
        
    } catch (Exception $e) {
        error_log("Error in archived_rooms.php: " . $e->getMessage());
        http_response_code(500);
        sendResponse('error', 'An error occurred while fetching archived rooms');
    }
}

// For any other request
sendResponse('error', 'Invalid request');
