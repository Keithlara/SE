<?php
require_once('db_config.php');

$sql = "CREATE TABLE IF NOT EXISTS `notifications` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `booking_id` int(11) DEFAULT NULL,
    `message` text NOT NULL,
    `type` enum('booking','payment','refund','system') NOT NULL,
    `is_read` tinyint(1) NOT NULL DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `booking_id` (`booking_id`),
    KEY `is_read` (`is_read`),
    KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

if(mysqli_query($con, $sql)) {
    echo "Notifications table created successfully";
} else {
    echo "Error creating table: " . mysqli_error($con);
}

