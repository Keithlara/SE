-- Add is_archived column to user_cred table
ALTER TABLE `user_cred` ADD COLUMN `is_archived` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`;

-- Add is_archived column to rooms table
ALTER TABLE `rooms` ADD COLUMN `is_archived` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`;

-- Create archived_rooms table
CREATE TABLE IF NOT EXISTS `archived_rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `area` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `adult` int(11) NOT NULL,
  `children` int(11) NOT NULL,
  `description` mediumtext NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `removed` int(11) NOT NULL DEFAULT 0,
  `archived_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create archived_user_cred table
CREATE TABLE IF NOT EXISTS `archived_user_cred` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `address` varchar(120) NOT NULL,
  `phonenum` varchar(100) NOT NULL,
  `pincode` int(11) NOT NULL,
  `dob` date NOT NULL,
  `password` varchar(200) NOT NULL,
  `is_verified` int(11) NOT NULL DEFAULT 0,
  `token` varchar(200) DEFAULT NULL,
  `t_expire` date DEFAULT NULL,
  `datentime` datetime NOT NULL DEFAULT current_timestamp(),
  `status` int(11) NOT NULL DEFAULT 1,
  `profile` varchar(100) DEFAULT NULL,
  `archived_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
