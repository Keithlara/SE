<?php
require('inc/db_config.php');

// Check if the notifications table exists
$table_check = mysqli_query($con, "SHOW TABLES LIKE 'notifications'");

if (mysqli_num_rows($table_check) == 0) {
    // Create the notifications table if it doesn't exist
    $create_table = "CREATE TABLE `notifications` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) NOT NULL,
        `booking_id` int(11) DEFAULT NULL,
        `message` text NOT NULL,
        `type` enum('booking','payment','refund','system') NOT NULL DEFAULT 'system',
        `is_read` tinyint(1) NOT NULL DEFAULT 0,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `user_id` (`user_id`),
        KEY `booking_id` (`booking_id`),
        KEY `is_read` (`is_read`),
        KEY `type` (`type`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    if (mysqli_query($con, $create_table)) {
        echo "Notifications table created successfully.\n";
    } else {
        die("Error creating notifications table: " . mysqli_error($con));
    }
} else {
    // Check if the type column exists
    $column_check = mysqli_query($con, "SHOW COLUMNS FROM `notifications` LIKE 'type'");
    
    if (mysqli_num_rows($column_check) == 0) {
        // Add the type column if it doesn't exist
        $alter_table = "ALTER TABLE `notifications` 
                       ADD COLUMN `type` ENUM('booking','payment','refund','system') NOT NULL DEFAULT 'system' AFTER `message`,
                       ADD COLUMN `is_read` TINYINT(1) NOT NULL DEFAULT 0 AFTER `type`,
                       ADD COLUMN `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `is_read`,
                       ADD INDEX `type` (`type`),
                       ADD INDEX `is_read` (`is_read`)";
        
        if (mysqli_query($con, $alter_table)) {
            echo "Notifications table updated successfully.\n";
            
            // Set default type for existing notifications
            mysqli_query($con, "UPDATE `notifications` SET `type` = 'system' WHERE `type` = '' OR `type` IS NULL");
        } else {
            die("Error updating notifications table: " . mysqli_error($con));
        }
    } else {
        echo "Notifications table is already up to date.\n";
    }
    
    // Check if the index on type column exists
    $index_check = mysqli_query($con, "SHOW INDEX FROM `notifications` WHERE Key_name = 'type'");
    if (mysqli_num_rows($index_check) == 0) {
        mysqli_query($con, "ALTER TABLE `notifications` ADD INDEX `type` (`type`)");
        echo "Added index on type column.\n";
    }
    
    // Check if the index on is_read column exists
    $index_check = mysqli_query($con, "SHOW INDEX FROM `notifications` WHERE Key_name = 'is_read'");
    if (mysqli_num_rows($index_check) == 0) {
        mysqli_query($con, "ALTER TABLE `notifications` ADD INDEX `is_read` (`is_read`)");
        echo "Added index on is_read column.\n";
    }
}

echo "Database update completed successfully.\n";
?>
