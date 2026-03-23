-- Add is_archived column to booking_order table
ALTER TABLE `booking_order` ADD COLUMN `is_archived` TINYINT(1) NOT NULL DEFAULT 0;

-- Add is_archived column to booking_details table
ALTER TABLE `booking_details` ADD COLUMN `is_archived` TINYINT(1) NOT NULL DEFAULT 0;

-- Add is_archived column to user_queries table
ALTER TABLE `user_queries` ADD COLUMN `is_archived` TINYINT(1) NOT NULL DEFAULT 0;
