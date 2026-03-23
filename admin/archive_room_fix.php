<?php
require('inc/essentials.php');
require('inc/db_config.php');
adminLogin();

if(isset($_POST['room_id'])) {
    header('Content-Type: application/json');
    
    $room_id = (int)$_POST['room_id'];
    
    try {
        // Start transaction
        $con->begin_transaction();
        
        // 1. Check if room exists and is not already archived
        $room = $con->query("SELECT * FROM `rooms` WHERE `id` = $room_id AND `is_archived` = 0");
        
        if($room->num_rows === 0) {
            throw new Exception("Room not found or already archived");
        }
        
        // 2. Mark the room as archived and removed
        $update = $con->query("UPDATE `rooms` SET `is_archived` = 1, `removed` = 1, `status` = 0 WHERE `id` = $room_id");
        
        if(!$update) {
            throw new Exception("Failed to update room status");
        }
        
        // 3. Create archived_rooms table if it doesn't exist
        $create_table = "CREATE TABLE IF NOT EXISTS `archived_rooms` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `room_id` int(11) NOT NULL,
            `name` varchar(150) NOT NULL,
            `area` int(11) NOT NULL,
            `price` int(11) NOT NULL,
            `quantity` int(11) NOT NULL,
            `adult` int(11) NOT NULL,
            `children` int(11) NOT NULL,
            `description` mediumtext NOT NULL,
            `status` tinyint(4) NOT NULL DEFAULT 1,
            `removed` tinyint(4) NOT NULL DEFAULT 0,
            `is_archived` tinyint(1) NOT NULL DEFAULT 1,
            `archived_at` datetime NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`),
            KEY `room_id` (`room_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        if(!$con->query($create_table)) {
            throw new Exception("Failed to create archived_rooms table: " . $con->error);
        }
        
        // 4. Copy room data to archived_rooms
        $copy_query = "INSERT INTO `archived_rooms` 
                      (`room_id`, `name`, `area`, `price`, `quantity`, `adult`, `children`, `description`, `status`, `removed`)
                      SELECT `id`, `name`, `area`, `price`, `quantity`, `adult`, `children`, `description`, `status`, `removed` 
                      FROM `rooms` 
                      WHERE `id` = $room_id";
        
        if(!$con->query($copy_query)) {
            throw new Exception("Failed to archive room data: " . $con->error);
        }
        
        // 5. Create archived_room_images table if it doesn't exist
        $create_images_table = "CREATE TABLE IF NOT EXISTS `archived_room_images` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `room_id` int(11) NOT NULL,
            `image` varchar(150) NOT NULL,
            `thumb` tinyint(4) NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`),
            KEY `room_id` (`room_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        if(!$con->query($create_images_table)) {
            throw new Exception("Failed to create archived_room_images table: " . $con->error);
        }
        
        // 6. Copy room images to archived_room_images
        $copy_images = "INSERT INTO `archived_room_images` (`room_id`, `image`, `thumb`)
                       SELECT $room_id, `image`, `thumb` FROM `room_images` WHERE `room_id` = $room_id";
        
        if(!$con->query($copy_images)) {
            // Don't fail the whole operation if images fail, just log it
            error_log("Warning: Failed to archive room images: " . $con->error);
        }
        
        // 7. Commit the transaction
        if($con->commit()) {
            echo json_encode(['success' => true, 'message' => 'Room archived successfully']);
        } else {
            throw new Exception("Failed to commit transaction");
        }
        
    } catch (Exception $e) {
        $con->rollback();
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    
    exit;
}

// If not a POST request, redirect
echo json_encode(['success' => false, 'error' => 'Invalid request']);
?>
