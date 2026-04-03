<?php

function appSchemaQuery(mysqli $con, string $sql): bool
{
  return (bool)mysqli_query($con, $sql);
}

function appSchemaTableExists(mysqli $con, string $table): bool
{
  $table = mysqli_real_escape_string($con, $table);
  $res = mysqli_query($con, "SHOW TABLES LIKE '{$table}'");
  return $res && mysqli_num_rows($res) > 0;
}

function appSchemaColumnExists(mysqli $con, string $table, string $column): bool
{
  $table = mysqli_real_escape_string($con, $table);
  $column = mysqli_real_escape_string($con, $column);
  $res = mysqli_query($con, "SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
  return $res && mysqli_num_rows($res) > 0;
}

function appSchemaIndexExists(mysqli $con, string $table, string $index): bool
{
  $table = mysqli_real_escape_string($con, $table);
  $index = mysqli_real_escape_string($con, $index);
  $res = mysqli_query($con, "SHOW INDEX FROM `{$table}` WHERE Key_name = '{$index}'");
  return $res && mysqli_num_rows($res) > 0;
}

function appSchemaEnsureColumn(mysqli $con, string $table, string $column, string $definition): void
{
  if (!appSchemaColumnExists($con, $table, $column)) {
    @mysqli_query($con, "ALTER TABLE `{$table}` ADD `{$column}` {$definition}");
  }
}

function appSchemaEnsureIndex(mysqli $con, string $table, string $index, string $definition): void
{
  if (!appSchemaIndexExists($con, $table, $index)) {
    @mysqli_query($con, "ALTER TABLE `{$table}` ADD {$definition}");
  }
}

function appSchemaEnsureArchiveTables(mysqli $con): void
{
  appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `archived_booking_order` (
    `booking_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `room_id` int(11) NOT NULL,
    `check_in` date NOT NULL,
    `check_out` date NOT NULL,
    `arrival` int(11) NOT NULL DEFAULT 0,
    `refund` int(11) DEFAULT NULL,
    `booking_status` varchar(100) NOT NULL DEFAULT 'pending',
    `order_id` varchar(150) NOT NULL,
    `trans_id` varchar(200) DEFAULT NULL,
    `trans_amt` int(11) NOT NULL,
    `trans_status` varchar(100) NOT NULL DEFAULT 'pending',
    `trans_resp_msg` varchar(200) DEFAULT NULL,
    `rate_review` int(11) DEFAULT NULL,
    `datentime` datetime NOT NULL DEFAULT current_timestamp(),
    `payment_status` enum('pending','partial','paid') DEFAULT 'pending',
    `payment_proof` varchar(255) DEFAULT NULL,
    `refund_proof` varchar(255) DEFAULT NULL,
    `refund_amount` decimal(10,2) DEFAULT 0.00,
    `amount_paid` decimal(10,2) DEFAULT 0.00,
    `confirmed_at` datetime DEFAULT NULL,
    `total_amt` decimal(10,2) DEFAULT 0.00,
    `downpayment` decimal(10,2) DEFAULT 0.00,
    `balance_due` decimal(10,2) DEFAULT 0.00,
    `promo_code` varchar(50) DEFAULT NULL,
    `discount_amount` decimal(10,2) DEFAULT 0.00,
    `booking_source` varchar(20) NOT NULL DEFAULT 'online',
    `created_by_admin` int(11) DEFAULT NULL,
    `walkin_note` text DEFAULT NULL,
    `archive_source` varchar(30) NOT NULL DEFAULT 'general',
    `archived_at` datetime NOT NULL DEFAULT current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `archived_booking_details` (
    `sr_no` int(11) NOT NULL,
    `booking_id` int(11) NOT NULL,
    `room_name` varchar(100) NOT NULL,
    `price` int(11) NOT NULL,
    `total_pay` int(11) NOT NULL,
    `room_no` varchar(100) DEFAULT NULL,
    `user_name` varchar(100) NOT NULL,
    `phonenum` varchar(100) NOT NULL,
    `address` varchar(150) NOT NULL,
    `booking_note` text DEFAULT NULL,
    `staff_note` text DEFAULT NULL,
    `extras_total` decimal(10,2) DEFAULT 0.00,
    `downpayment` decimal(10,2) DEFAULT 0.00,
    `remaining_balance` decimal(10,2) DEFAULT 0.00
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `archived_rooms` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `room_id` int(11) NOT NULL,
    `name` varchar(150) NOT NULL,
    `area` int(11) NOT NULL,
    `price` int(11) NOT NULL,
    `quantity` int(11) NOT NULL DEFAULT 1,
    `adult` int(11) NOT NULL DEFAULT 1,
    `children` int(11) NOT NULL DEFAULT 0,
    `description` mediumtext NOT NULL,
    `status` tinyint(4) NOT NULL DEFAULT 1,
    `removed` tinyint(4) NOT NULL DEFAULT 1,
    `is_archived` tinyint(1) NOT NULL DEFAULT 1,
    `archived_at` datetime NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `room_id` (`room_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `archived_room_images` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `room_id` int(11) NOT NULL,
    `image` varchar(200) NOT NULL,
    `thumb` tinyint(4) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `room_id` (`room_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `archived_room_features` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `room_id` int(11) NOT NULL,
    `features_id` int(11) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `room_id` (`room_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `archived_room_facilities` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `room_id` int(11) NOT NULL,
    `facilities_id` int(11) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `room_id` (`room_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `archived_ratings_reviews` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `room_id` int(11) NOT NULL,
    `source_review_id` int(11) DEFAULT NULL,
    `booking_id` int(11) DEFAULT NULL,
    `user_id` int(11) NOT NULL,
    `rating` int(11) NOT NULL,
    `review` text NOT NULL,
    `seen` int(11) NOT NULL DEFAULT 0,
    `datentime` datetime NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `room_id` (`room_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `archived_booking_extras` (
    `id` int(11) NOT NULL,
    `booking_id` int(11) NOT NULL,
    `extra_id` int(11) NOT NULL DEFAULT 0,
    `name` varchar(150) NOT NULL,
    `quantity` int(11) NOT NULL DEFAULT 1,
    `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
    `total_price` decimal(10,2) NOT NULL DEFAULT 0.00,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `archived_at` datetime NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `booking_id` (`booking_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `archived_booking_history` (
    `id` int(11) NOT NULL,
    `booking_id` int(11) NOT NULL,
    `actor_type` varchar(30) NOT NULL DEFAULT 'system',
    `actor_id` int(11) DEFAULT NULL,
    `actor_name` varchar(150) DEFAULT NULL,
    `event_type` varchar(60) NOT NULL,
    `title` varchar(180) NOT NULL,
    `details` text DEFAULT NULL,
    `meta_json` longtext DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `archived_at` datetime NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `booking_id` (`booking_id`),
    KEY `event_type` (`event_type`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `archived_booking_transactions` (
    `id` int(11) NOT NULL,
    `booking_id` int(11) DEFAULT NULL,
    `guest_name` varchar(100) NOT NULL,
    `room_no` varchar(50) DEFAULT NULL,
    `amount` int(11) NOT NULL,
    `method` varchar(50) NOT NULL,
    `status` varchar(50) NOT NULL,
    `type` varchar(50) NOT NULL,
    `admin_id` int(11) NOT NULL DEFAULT 0,
    `datentime` datetime NOT NULL DEFAULT current_timestamp(),
    `archived_at` datetime NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `booking_id` (`booking_id`),
    KEY `status` (`status`),
    KEY `type` (`type`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `archived_booking_notifications` (
    `id` int(11) NOT NULL,
    `booking_id` int(11) DEFAULT NULL,
    `user_id` int(11) NOT NULL,
    `message` text NOT NULL,
    `type` varchar(30) NOT NULL DEFAULT 'system',
    `is_read` tinyint(1) NOT NULL DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `archived_at` datetime NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `booking_id` (`booking_id`),
    KEY `user_id` (`user_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `archived_booking_support_tickets` (
    `id` int(11) NOT NULL,
    `booking_id` int(11) DEFAULT NULL,
    `ticket_code` varchar(30) NOT NULL,
    `user_id` int(11) NOT NULL,
    `order_id` varchar(80) DEFAULT NULL,
    `subject` varchar(180) NOT NULL,
    `category` varchar(40) NOT NULL DEFAULT 'general',
    `priority` varchar(20) NOT NULL DEFAULT 'normal',
    `status` varchar(20) NOT NULL DEFAULT 'open',
    `assigned_to` int(11) DEFAULT NULL,
    `escalated` tinyint(1) NOT NULL DEFAULT 0,
    `last_reply_at` datetime DEFAULT NULL,
    `last_reply_by` varchar(20) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `archived_at` datetime NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `booking_id` (`booking_id`),
    KEY `user_id` (`user_id`),
    KEY `ticket_code` (`ticket_code`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `archived_booking_support_messages` (
    `id` int(11) NOT NULL,
    `booking_id` int(11) DEFAULT NULL,
    `ticket_id` int(11) NOT NULL,
    `sender_type` varchar(20) NOT NULL DEFAULT 'guest',
    `sender_id` int(11) DEFAULT NULL,
    `sender_name` varchar(150) DEFAULT NULL,
    `message` text NOT NULL,
    `attachment_path` varchar(255) DEFAULT NULL,
    `is_internal` tinyint(1) NOT NULL DEFAULT 0,
    `seen_by_user` tinyint(1) NOT NULL DEFAULT 0,
    `seen_by_staff` tinyint(1) NOT NULL DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `archived_at` datetime NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `booking_id` (`booking_id`),
    KEY `ticket_id` (`ticket_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `archived_booking_guest_notes` (
    `id` int(11) NOT NULL,
    `booking_id` int(11) DEFAULT NULL,
    `user_id` int(11) NOT NULL,
    `note_type` varchar(30) NOT NULL DEFAULT 'internal',
    `title` varchar(150) NOT NULL,
    `note` text NOT NULL,
    `created_by` int(11) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `archived_at` datetime NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `booking_id` (`booking_id`),
    KEY `user_id` (`user_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `archived_room_block_dates` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `source_block_id` int(11) DEFAULT NULL,
    `room_id` int(11) NOT NULL,
    `room_no` varchar(20) DEFAULT NULL,
    `start_date` date NOT NULL,
    `end_date` date NOT NULL,
    `block_type` varchar(30) NOT NULL DEFAULT 'maintenance',
    `reason` varchar(255) DEFAULT NULL,
    `status` varchar(20) NOT NULL DEFAULT 'active',
    `created_by` int(11) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `archived_at` datetime NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `room_id` (`room_id`),
    KEY `source_block_id` (`source_block_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `archived_user_notifications` (
    `id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `booking_id` int(11) DEFAULT NULL,
    `message` text NOT NULL,
    `type` varchar(30) NOT NULL DEFAULT 'system',
    `is_read` tinyint(1) NOT NULL DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `archived_at` datetime NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `booking_id` (`booking_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `archived_user_guest_notes` (
    `id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `booking_id` int(11) DEFAULT NULL,
    `note_type` varchar(30) NOT NULL DEFAULT 'internal',
    `title` varchar(150) NOT NULL,
    `note` text NOT NULL,
    `created_by` int(11) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `archived_at` datetime NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `booking_id` (`booking_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `archived_user_support_tickets` (
    `id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `booking_id` int(11) DEFAULT NULL,
    `ticket_code` varchar(30) NOT NULL,
    `order_id` varchar(80) DEFAULT NULL,
    `subject` varchar(180) NOT NULL,
    `category` varchar(40) NOT NULL DEFAULT 'general',
    `priority` varchar(20) NOT NULL DEFAULT 'normal',
    `status` varchar(20) NOT NULL DEFAULT 'open',
    `assigned_to` int(11) DEFAULT NULL,
    `escalated` tinyint(1) NOT NULL DEFAULT 0,
    `last_reply_at` datetime DEFAULT NULL,
    `last_reply_by` varchar(20) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `archived_at` datetime NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `booking_id` (`booking_id`),
    KEY `ticket_code` (`ticket_code`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `archived_user_support_messages` (
    `id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `ticket_id` int(11) NOT NULL,
    `sender_type` varchar(20) NOT NULL DEFAULT 'guest',
    `sender_id` int(11) DEFAULT NULL,
    `sender_name` varchar(150) DEFAULT NULL,
    `message` text NOT NULL,
    `attachment_path` varchar(255) DEFAULT NULL,
    `is_internal` tinyint(1) NOT NULL DEFAULT 0,
    `seen_by_user` tinyint(1) NOT NULL DEFAULT 0,
    `seen_by_staff` tinyint(1) NOT NULL DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `archived_at` datetime NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `ticket_id` (`ticket_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `archived_user_reviews` (
    `id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `booking_id` int(11) DEFAULT NULL,
    `room_id` int(11) NOT NULL,
    `rating` int(11) NOT NULL,
    `review` text NOT NULL,
    `seen` int(11) NOT NULL DEFAULT 0,
    `datentime` datetime NOT NULL DEFAULT current_timestamp(),
    `archived_at` datetime NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `room_id` (`room_id`),
    KEY `booking_id` (`booking_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `archived_user_cred` (
    `id` int(11) NOT NULL,
    `name` varchar(150) NOT NULL,
    `email` varchar(150) NOT NULL,
    `username` varchar(100) DEFAULT NULL,
    `address` varchar(255) NOT NULL,
    `phonenum` varchar(20) NOT NULL,
    `pincode` int(11) NOT NULL DEFAULT 0,
    `dob` date DEFAULT NULL,
    `password` varchar(255) NOT NULL,
    `is_verified` tinyint(1) NOT NULL DEFAULT 0,
    `verification_code` varchar(255) DEFAULT NULL,
    `token` varchar(255) DEFAULT NULL,
    `t_expire` date DEFAULT NULL,
    `datentime` datetime NOT NULL DEFAULT current_timestamp(),
    `status` tinyint(4) NOT NULL DEFAULT 1,
    `profile` varchar(255) DEFAULT NULL,
    `archived_at` datetime NOT NULL DEFAULT current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `archived_support_tickets` (
    `id` int(11) NOT NULL,
    `ticket_code` varchar(30) NOT NULL,
    `user_id` int(11) NOT NULL,
    `booking_id` int(11) DEFAULT NULL,
    `order_id` varchar(80) DEFAULT NULL,
    `subject` varchar(180) NOT NULL,
    `category` varchar(40) NOT NULL DEFAULT 'general',
    `priority` varchar(20) NOT NULL DEFAULT 'normal',
    `status` varchar(20) NOT NULL DEFAULT 'open',
    `assigned_to` int(11) DEFAULT NULL,
    `escalated` tinyint(1) NOT NULL DEFAULT 0,
    `last_reply_at` datetime DEFAULT NULL,
    `last_reply_by` varchar(20) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `archived_at` datetime NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `booking_id` (`booking_id`),
    KEY `ticket_code` (`ticket_code`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `archived_support_ticket_messages` (
    `id` int(11) NOT NULL,
    `ticket_id` int(11) NOT NULL,
    `user_id` int(11) DEFAULT NULL,
    `sender_type` varchar(20) NOT NULL DEFAULT 'guest',
    `sender_id` int(11) DEFAULT NULL,
    `sender_name` varchar(150) DEFAULT NULL,
    `message` text NOT NULL,
    `attachment_path` varchar(255) DEFAULT NULL,
    `is_internal` tinyint(1) NOT NULL DEFAULT 0,
    `seen_by_user` tinyint(1) NOT NULL DEFAULT 0,
    `seen_by_staff` tinyint(1) NOT NULL DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `archived_at` datetime NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `ticket_id` (`ticket_id`),
    KEY `user_id` (`user_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `archived_transactions` (
    `id` int(11) NOT NULL,
    `booking_id` int(11) DEFAULT NULL,
    `guest_name` varchar(100) NOT NULL,
    `room_no` varchar(50) DEFAULT NULL,
    `amount` int(11) NOT NULL,
    `method` varchar(50) NOT NULL,
    `status` varchar(50) NOT NULL,
    `type` varchar(50) NOT NULL,
    `admin_id` int(11) NOT NULL DEFAULT 0,
    `datentime` datetime NOT NULL DEFAULT current_timestamp(),
    `archived_at` datetime NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `booking_id` (`booking_id`),
    KEY `status` (`status`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `archived_notifications` (
    `id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `booking_id` int(11) DEFAULT NULL,
    `message` text NOT NULL,
    `type` varchar(30) NOT NULL DEFAULT 'system',
    `is_read` tinyint(1) NOT NULL DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `archived_at` datetime NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `booking_id` (`booking_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `archived_reviews` (
    `id` int(11) NOT NULL,
    `booking_id` int(11) DEFAULT NULL,
    `room_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `rating` int(11) NOT NULL,
    `review` text NOT NULL,
    `seen` int(11) NOT NULL DEFAULT 0,
    `datentime` datetime NOT NULL DEFAULT current_timestamp(),
    `archived_at` datetime NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `booking_id` (`booking_id`),
    KEY `room_id` (`room_id`),
    KEY `user_id` (`user_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  $archiveColumns = [
    'archived_booking_order' => [
      'payment_status' => "ENUM('pending','partial','paid') DEFAULT 'pending'",
      'payment_proof' => "VARCHAR(255) DEFAULT NULL",
      'refund_proof' => "VARCHAR(255) DEFAULT NULL",
      'refund_amount' => "DECIMAL(10,2) DEFAULT 0.00",
      'amount_paid' => "DECIMAL(10,2) DEFAULT 0.00",
      'confirmed_at' => "DATETIME DEFAULT NULL",
      'total_amt' => "DECIMAL(10,2) DEFAULT 0.00",
      'downpayment' => "DECIMAL(10,2) DEFAULT 0.00",
      'balance_due' => "DECIMAL(10,2) DEFAULT 0.00",
      'promo_code' => "VARCHAR(50) DEFAULT NULL",
      'discount_amount' => "DECIMAL(10,2) DEFAULT 0.00",
      'archive_source' => "VARCHAR(30) NOT NULL DEFAULT 'general'",
    ],
    'archived_booking_details' => [
      'booking_note' => "TEXT NULL",
      'staff_note' => "TEXT NULL",
      'extras_total' => "DECIMAL(10,2) DEFAULT 0.00",
      'downpayment' => "DECIMAL(10,2) DEFAULT 0.00",
      'remaining_balance' => "DECIMAL(10,2) DEFAULT 0.00",
    ],
    'archived_rooms' => [
      'is_archived' => "TINYINT(1) NOT NULL DEFAULT 1",
    ],
    'archived_ratings_reviews' => [
      'source_review_id' => "INT(11) DEFAULT NULL",
      'booking_id' => "INT(11) DEFAULT NULL",
      'seen' => "INT(11) NOT NULL DEFAULT 0",
    ],
    'archived_user_reviews' => [
      'seen' => "INT(11) NOT NULL DEFAULT 0",
    ],
    'archived_user_cred' => [
      'username' => "VARCHAR(100) DEFAULT NULL",
      'verification_code' => "VARCHAR(255) DEFAULT NULL",
    ],
    'archived_room_block_dates' => [
      'source_block_id' => "INT(11) DEFAULT NULL",
    ],
  ];

  foreach ($archiveColumns as $table => $columns) {
    if (!appSchemaTableExists($con, $table)) {
      continue;
    }
    foreach ($columns as $column => $definition) {
      appSchemaEnsureColumn($con, $table, $column, $definition);
    }
  }
}

function appSchemaBackfillArchivedBookings(mysqli $con): void
{
  if (
    !appSchemaTableExists($con, 'booking_order') ||
    !appSchemaTableExists($con, 'booking_details') ||
    !appSchemaTableExists($con, 'archived_booking_order') ||
    !appSchemaTableExists($con, 'archived_booking_details') ||
    !appSchemaColumnExists($con, 'booking_order', 'is_archived')
  ) {
    return;
  }

  @mysqli_query($con, "UPDATE `archived_booking_order` abo
    INNER JOIN `booking_order` bo ON bo.`booking_id` = abo.`booking_id`
    SET
      abo.`user_id` = bo.`user_id`,
      abo.`room_id` = bo.`room_id`,
      abo.`check_in` = bo.`check_in`,
      abo.`check_out` = bo.`check_out`,
      abo.`arrival` = bo.`arrival`,
      abo.`refund` = bo.`refund`,
      abo.`booking_status` = bo.`booking_status`,
      abo.`order_id` = bo.`order_id`,
      abo.`trans_id` = bo.`trans_id`,
      abo.`trans_amt` = bo.`trans_amt`,
      abo.`trans_status` = bo.`trans_status`,
      abo.`trans_resp_msg` = bo.`trans_resp_msg`,
      abo.`rate_review` = bo.`rate_review`,
      abo.`datentime` = bo.`datentime`,
      abo.`payment_status` = bo.`payment_status`,
      abo.`payment_proof` = bo.`payment_proof`,
      abo.`refund_proof` = bo.`refund_proof`,
      abo.`refund_amount` = bo.`refund_amount`,
      abo.`amount_paid` = bo.`amount_paid`,
      abo.`confirmed_at` = bo.`confirmed_at`,
      abo.`total_amt` = bo.`total_amt`,
      abo.`downpayment` = bo.`downpayment`,
      abo.`balance_due` = bo.`balance_due`,
      abo.`promo_code` = bo.`promo_code`,
      abo.`discount_amount` = bo.`discount_amount`,
      abo.`archive_source` = CASE
        WHEN bo.`is_archived` = 1 THEN 'workflow'
        ELSE abo.`archive_source`
      END
    WHERE bo.`is_archived` = 1");

  @mysqli_query($con, "UPDATE `archived_booking_details` abd
    INNER JOIN `booking_details` bd ON bd.`booking_id` = abd.`booking_id`
    INNER JOIN `booking_order` bo ON bo.`booking_id` = bd.`booking_id`
    SET
      abd.`room_name` = bd.`room_name`,
      abd.`price` = bd.`price`,
      abd.`total_pay` = bd.`total_pay`,
      abd.`room_no` = bd.`room_no`,
      abd.`user_name` = bd.`user_name`,
      abd.`phonenum` = bd.`phonenum`,
      abd.`address` = bd.`address`,
      abd.`booking_note` = bd.`booking_note`,
      abd.`staff_note` = bd.`staff_note`,
      abd.`extras_total` = bd.`extras_total`,
      abd.`downpayment` = bd.`downpayment`,
      abd.`remaining_balance` = bd.`remaining_balance`
    WHERE bo.`is_archived` = 1");

  @mysqli_query($con, "UPDATE `archived_booking_order` abo
    INNER JOIN `booking_order` bo ON bo.`booking_id` = abo.`booking_id`
    SET abo.`archive_source` = 'workflow'
    WHERE bo.`is_archived` = 1");

  @mysqli_query($con, "UPDATE `archived_booking_order` abo
    LEFT JOIN `booking_order` bo ON bo.`booking_id` = abo.`booking_id`
    SET abo.`archive_source` = 'records'
    WHERE bo.`booking_id` IS NULL
      AND (abo.`archive_source` IS NULL OR abo.`archive_source` = '' OR abo.`archive_source` = 'general')");

  $res = mysqli_query($con, "SELECT
      bo.`booking_id`,
      abo.`booking_id` AS `archived_order_id`,
      abd.`booking_id` AS `archived_detail_id`
    FROM `booking_order` bo
    INNER JOIN `booking_details` bd ON bd.`booking_id` = bo.`booking_id`
    LEFT JOIN `archived_booking_order` abo ON abo.`booking_id` = bo.`booking_id`
    LEFT JOIN `archived_booking_details` abd ON abd.`booking_id` = bo.`booking_id`
    WHERE bo.`is_archived` = 1
      AND (abo.`booking_id` IS NULL OR abd.`booking_id` IS NULL)");

  if (!$res) {
    return;
  }

  while ($row = mysqli_fetch_assoc($res)) {
    $bookingId = (int)$row['booking_id'];

    if (empty($row['archived_order_id'])) {
      @mysqli_query($con, "INSERT INTO `archived_booking_order`
        (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`,`archive_source`)
        SELECT
          `booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`,'workflow'
        FROM `booking_order`
        WHERE `booking_id` = {$bookingId}
        LIMIT 1");
    }

    if (empty($row['archived_detail_id'])) {
      @mysqli_query($con, "INSERT INTO `archived_booking_details`
        (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`)
        SELECT
          `sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`
        FROM `booking_details`
        WHERE `booking_id` = {$bookingId}
        LIMIT 1");
    }
  }
}

function appSchemaBackfillArchivedRooms(mysqli $con): void
{
  if (
    !appSchemaTableExists($con, 'rooms') ||
    !appSchemaTableExists($con, 'archived_rooms') ||
    !appSchemaColumnExists($con, 'rooms', 'is_archived')
  ) {
    return;
  }

  $res = mysqli_query($con, "SELECT *
    FROM `rooms` r
    WHERE (r.`is_archived` = 1 OR r.`removed` = 1)
      AND NOT EXISTS (
        SELECT 1 FROM `archived_rooms` ar WHERE ar.`room_id` = r.`id`
      )");

  if (!$res) {
    return;
  }

  while ($row = mysqli_fetch_assoc($res)) {
    $roomId = (int)$row['id'];
    $name = mysqli_real_escape_string($con, (string)$row['name']);
    $description = mysqli_real_escape_string($con, (string)$row['description']);
    $archivedAt = !empty($row['archived_at']) ? "'" . mysqli_real_escape_string($con, (string)$row['archived_at']) . "'" : 'NOW()';

    $inserted = @mysqli_query($con, "INSERT INTO `archived_rooms`
      (`room_id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`is_archived`,`archived_at`)
      VALUES (
        {$roomId},
        '{$name}',
        " . (int)$row['area'] . ",
        " . (int)$row['price'] . ",
        " . (int)$row['quantity'] . ",
        " . (int)$row['adult'] . ",
        " . (int)$row['children'] . ",
        '{$description}',
        " . (int)$row['status'] . ",
        " . (int)$row['removed'] . ",
        1,
        {$archivedAt}
      )");

    if (!$inserted) {
      continue;
    }

    $archivedId = (int)mysqli_insert_id($con);
    @mysqli_query($con, "INSERT INTO `archived_room_images` (`room_id`,`image`,`thumb`)
      SELECT {$archivedId}, `image`, `thumb`
      FROM `room_images`
      WHERE `room_id` = {$roomId}");
    @mysqli_query($con, "INSERT INTO `archived_room_features` (`room_id`,`features_id`)
      SELECT {$archivedId}, `features_id`
      FROM `room_features`
      WHERE `room_id` = {$roomId}");
    @mysqli_query($con, "INSERT INTO `archived_room_facilities` (`room_id`,`facilities_id`)
      SELECT {$archivedId}, `facilities_id`
      FROM `room_facilities`
      WHERE `room_id` = {$roomId}");
    @mysqli_query($con, "INSERT INTO `archived_ratings_reviews` (`room_id`,`source_review_id`,`booking_id`,`user_id`,`rating`,`review`,`seen`,`datentime`)
      SELECT {$archivedId}, `sr_no`, `booking_id`, `user_id`, `rating`, `review`, `seen`, `datentime`
      FROM `rating_review`
      WHERE `room_id` = {$roomId}");
    @mysqli_query($con, "INSERT INTO `archived_room_block_dates` (`source_block_id`,`room_id`,`room_no`,`start_date`,`end_date`,`block_type`,`reason`,`status`,`created_by`,`created_at`)
      SELECT `id`, {$archivedId}, `room_no`, `start_date`, `end_date`, `block_type`, `reason`, `status`, `created_by`, `created_at`
      FROM `room_block_dates`
      WHERE `room_id` = {$roomId}");
  }
}

function appSchemaBackfillArchivedQueries(mysqli $con): void
{
  if (!appSchemaTableExists($con, 'user_queries') || !appSchemaColumnExists($con, 'user_queries', 'archived_at')) {
    return;
  }

  @mysqli_query($con, "UPDATE `user_queries`
    SET `archived_at` = COALESCE(`archived_at`, `datentime`, NOW())
    WHERE `is_archived` = 1
      AND (`archived_at` IS NULL OR `archived_at` = '0000-00-00 00:00:00')");
}

function appSchemaBackfillArchivedBookingRelations(mysqli $con): void
{
  $requiredTables = [
    'archived_booking_order',
    'archived_booking_extras',
    'archived_booking_history',
    'archived_booking_transactions',
    'archived_booking_notifications',
    'archived_booking_support_tickets',
    'archived_booking_support_messages',
    'archived_booking_guest_notes',
    'booking_extras',
    'booking_history',
    'transactions',
    'notifications',
    'support_tickets',
    'support_ticket_messages',
    'guest_notes',
  ];

  foreach ($requiredTables as $table) {
    if (!appSchemaTableExists($con, $table)) {
      return;
    }
  }

  @mysqli_query($con, "DELETE abe FROM `archived_booking_extras` abe INNER JOIN `archived_booking_order` abo ON abo.`booking_id` = abe.`booking_id`");
  @mysqli_query($con, "DELETE abh FROM `archived_booking_history` abh INNER JOIN `archived_booking_order` abo ON abo.`booking_id` = abh.`booking_id`");
  @mysqli_query($con, "DELETE abt FROM `archived_booking_transactions` abt INNER JOIN `archived_booking_order` abo ON abo.`booking_id` = abt.`booking_id`");
  @mysqli_query($con, "DELETE abn FROM `archived_booking_notifications` abn INNER JOIN `archived_booking_order` abo ON abo.`booking_id` = abn.`booking_id`");
  @mysqli_query($con, "DELETE absm FROM `archived_booking_support_messages` absm INNER JOIN `archived_booking_order` abo ON abo.`booking_id` = absm.`booking_id`");
  @mysqli_query($con, "DELETE abst FROM `archived_booking_support_tickets` abst INNER JOIN `archived_booking_order` abo ON abo.`booking_id` = abst.`booking_id`");
  @mysqli_query($con, "DELETE abgn FROM `archived_booking_guest_notes` abgn INNER JOIN `archived_booking_order` abo ON abo.`booking_id` = abgn.`booking_id`");

  @mysqli_query($con, "INSERT INTO `archived_booking_extras`
    (`id`,`booking_id`,`extra_id`,`name`,`quantity`,`unit_price`,`total_price`,`created_at`)
    SELECT be.`id`, be.`booking_id`, be.`extra_id`, be.`name`, be.`quantity`, be.`unit_price`, be.`total_price`, be.`created_at`
    FROM `booking_extras` be
    INNER JOIN `archived_booking_order` abo ON abo.`booking_id` = be.`booking_id`");

  @mysqli_query($con, "INSERT INTO `archived_booking_history`
    (`id`,`booking_id`,`actor_type`,`actor_id`,`actor_name`,`event_type`,`title`,`details`,`meta_json`,`created_at`)
    SELECT bh.`id`, bh.`booking_id`, bh.`actor_type`, bh.`actor_id`, bh.`actor_name`, bh.`event_type`, bh.`title`, bh.`details`, bh.`meta_json`, bh.`created_at`
    FROM `booking_history` bh
    INNER JOIN `archived_booking_order` abo ON abo.`booking_id` = bh.`booking_id`");

  @mysqli_query($con, "INSERT INTO `archived_booking_transactions`
    (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`)
    SELECT tr.`id`, tr.`booking_id`, tr.`guest_name`, tr.`room_no`, tr.`amount`, tr.`method`, tr.`status`, tr.`type`, tr.`admin_id`, tr.`datentime`
    FROM `transactions` tr
    INNER JOIN `archived_booking_order` abo ON abo.`booking_id` = tr.`booking_id`");

  @mysqli_query($con, "INSERT INTO `archived_booking_notifications`
    (`id`,`booking_id`,`user_id`,`message`,`type`,`is_read`,`created_at`)
    SELECT n.`id`, n.`booking_id`, n.`user_id`, n.`message`, n.`type`, n.`is_read`, n.`created_at`
    FROM `notifications` n
    INNER JOIN `archived_booking_order` abo ON abo.`booking_id` = n.`booking_id`");

  @mysqli_query($con, "INSERT INTO `archived_booking_support_tickets`
    (`id`,`booking_id`,`ticket_code`,`user_id`,`order_id`,`subject`,`category`,`priority`,`status`,`assigned_to`,`escalated`,`last_reply_at`,`last_reply_by`,`created_at`,`updated_at`)
    SELECT st.`id`, st.`booking_id`, st.`ticket_code`, st.`user_id`, st.`order_id`, st.`subject`, st.`category`, st.`priority`, st.`status`, st.`assigned_to`, st.`escalated`, st.`last_reply_at`, st.`last_reply_by`, st.`created_at`, st.`updated_at`
    FROM `support_tickets` st
    INNER JOIN `archived_booking_order` abo ON abo.`booking_id` = st.`booking_id`");

  @mysqli_query($con, "INSERT INTO `archived_booking_support_messages`
    (`id`,`booking_id`,`ticket_id`,`sender_type`,`sender_id`,`sender_name`,`message`,`attachment_path`,`is_internal`,`seen_by_user`,`seen_by_staff`,`created_at`)
    SELECT stm.`id`, st.`booking_id`, stm.`ticket_id`, stm.`sender_type`, stm.`sender_id`, stm.`sender_name`, stm.`message`, stm.`attachment_path`, stm.`is_internal`, stm.`seen_by_user`, stm.`seen_by_staff`, stm.`created_at`
    FROM `support_ticket_messages` stm
    INNER JOIN `support_tickets` st ON st.`id` = stm.`ticket_id`
    INNER JOIN `archived_booking_order` abo ON abo.`booking_id` = st.`booking_id`");

  @mysqli_query($con, "INSERT INTO `archived_booking_guest_notes`
    (`id`,`booking_id`,`user_id`,`note_type`,`title`,`note`,`created_by`,`created_at`,`updated_at`)
    SELECT gn.`id`, gn.`booking_id`, gn.`user_id`, gn.`note_type`, gn.`title`, gn.`note`, gn.`created_by`, gn.`created_at`, gn.`updated_at`
    FROM `guest_notes` gn
    INNER JOIN `archived_booking_order` abo ON abo.`booking_id` = gn.`booking_id`");
}

function appSchemaBackfillArchivedRoomRelations(mysqli $con): void
{
  $requiredTables = [
    'archived_rooms',
    'archived_ratings_reviews',
    'archived_room_block_dates',
    'rating_review',
    'room_block_dates',
  ];

  foreach ($requiredTables as $table) {
    if (!appSchemaTableExists($con, $table)) {
      return;
    }
  }

  $res = mysqli_query($con, "SELECT `id`,`room_id` FROM `archived_rooms`");
  if (!$res) {
    return;
  }

  while ($row = mysqli_fetch_assoc($res)) {
    $archivedId = (int)$row['id'];
    $roomId = (int)$row['room_id'];

    @mysqli_query($con, "DELETE FROM `archived_ratings_reviews` WHERE `room_id` = {$archivedId}");
    @mysqli_query($con, "DELETE FROM `archived_room_block_dates` WHERE `room_id` = {$archivedId}");

    @mysqli_query($con, "INSERT INTO `archived_ratings_reviews`
      (`room_id`,`source_review_id`,`booking_id`,`user_id`,`rating`,`review`,`seen`,`datentime`)
      SELECT {$archivedId}, `sr_no`, `booking_id`, `user_id`, `rating`, `review`, `seen`, `datentime`
      FROM `rating_review`
      WHERE `room_id` = {$roomId}");

    @mysqli_query($con, "INSERT INTO `archived_room_block_dates`
      (`source_block_id`,`room_id`,`room_no`,`start_date`,`end_date`,`block_type`,`reason`,`status`,`created_by`,`created_at`)
      SELECT `id`, {$archivedId}, `room_no`, `start_date`, `end_date`, `block_type`, `reason`, `status`, `created_by`, `created_at`
      FROM `room_block_dates`
      WHERE `room_id` = {$roomId}");
  }
}

function appSchemaBackfillArchivedUserRelations(mysqli $con): void
{
  $requiredTables = [
    'archived_user_cred',
    'archived_user_notifications',
    'archived_user_guest_notes',
    'archived_user_support_tickets',
    'archived_user_support_messages',
    'archived_user_reviews',
    'notifications',
    'guest_notes',
    'support_tickets',
    'support_ticket_messages',
    'rating_review',
  ];

  foreach ($requiredTables as $table) {
    if (!appSchemaTableExists($con, $table)) {
      return;
    }
  }

  @mysqli_query($con, "DELETE aun FROM `archived_user_notifications` aun INNER JOIN `archived_user_cred` auc ON auc.`id` = aun.`user_id`");
  @mysqli_query($con, "DELETE augn FROM `archived_user_guest_notes` augn INNER JOIN `archived_user_cred` auc ON auc.`id` = augn.`user_id`");
  @mysqli_query($con, "DELETE ausm FROM `archived_user_support_messages` ausm INNER JOIN `archived_user_cred` auc ON auc.`id` = ausm.`user_id`");
  @mysqli_query($con, "DELETE aust FROM `archived_user_support_tickets` aust INNER JOIN `archived_user_cred` auc ON auc.`id` = aust.`user_id`");
  @mysqli_query($con, "DELETE aur FROM `archived_user_reviews` aur INNER JOIN `archived_user_cred` auc ON auc.`id` = aur.`user_id`");

  @mysqli_query($con, "INSERT INTO `archived_user_notifications`
    (`id`,`user_id`,`booking_id`,`message`,`type`,`is_read`,`created_at`)
    SELECT n.`id`, n.`user_id`, n.`booking_id`, n.`message`, n.`type`, n.`is_read`, n.`created_at`
    FROM `notifications` n
    INNER JOIN `archived_user_cred` auc ON auc.`id` = n.`user_id`");

  @mysqli_query($con, "INSERT INTO `archived_user_guest_notes`
    (`id`,`user_id`,`booking_id`,`note_type`,`title`,`note`,`created_by`,`created_at`,`updated_at`)
    SELECT gn.`id`, gn.`user_id`, gn.`booking_id`, gn.`note_type`, gn.`title`, gn.`note`, gn.`created_by`, gn.`created_at`, gn.`updated_at`
    FROM `guest_notes` gn
    INNER JOIN `archived_user_cred` auc ON auc.`id` = gn.`user_id`");

  @mysqli_query($con, "INSERT INTO `archived_user_support_tickets`
    (`id`,`user_id`,`booking_id`,`ticket_code`,`order_id`,`subject`,`category`,`priority`,`status`,`assigned_to`,`escalated`,`last_reply_at`,`last_reply_by`,`created_at`,`updated_at`)
    SELECT st.`id`, st.`user_id`, st.`booking_id`, st.`ticket_code`, st.`order_id`, st.`subject`, st.`category`, st.`priority`, st.`status`, st.`assigned_to`, st.`escalated`, st.`last_reply_at`, st.`last_reply_by`, st.`created_at`, st.`updated_at`
    FROM `support_tickets` st
    INNER JOIN `archived_user_cred` auc ON auc.`id` = st.`user_id`");

  @mysqli_query($con, "INSERT INTO `archived_user_support_messages`
    (`id`,`user_id`,`ticket_id`,`sender_type`,`sender_id`,`sender_name`,`message`,`attachment_path`,`is_internal`,`seen_by_user`,`seen_by_staff`,`created_at`)
    SELECT stm.`id`, st.`user_id`, stm.`ticket_id`, stm.`sender_type`, stm.`sender_id`, stm.`sender_name`, stm.`message`, stm.`attachment_path`, stm.`is_internal`, stm.`seen_by_user`, stm.`seen_by_staff`, stm.`created_at`
    FROM `support_ticket_messages` stm
    INNER JOIN `support_tickets` st ON st.`id` = stm.`ticket_id`
    INNER JOIN `archived_user_cred` auc ON auc.`id` = st.`user_id`");

  @mysqli_query($con, "INSERT INTO `archived_user_reviews`
    (`id`,`user_id`,`booking_id`,`room_id`,`rating`,`review`,`seen`,`datentime`)
    SELECT rr.`sr_no`, rr.`user_id`, rr.`booking_id`, rr.`room_id`, rr.`rating`, rr.`review`, rr.`seen`, rr.`datentime`
    FROM `rating_review` rr
    INNER JOIN `archived_user_cred` auc ON auc.`id` = rr.`user_id`");
}

function appSchemaNormalizeUsernameCandidate(string $value): string
{
  $value = strtolower(trim($value));
  $value = str_replace(' ', '.', $value);
  $value = preg_replace('/[^a-z0-9._-]+/', '', $value) ?? '';
  return trim($value, '._-');
}

function appSchemaUsernameExists(mysqli $con, string $username, int $excludeId = 0): bool
{
  if ($username === '' || !appSchemaTableExists($con, 'user_cred') || !appSchemaColumnExists($con, 'user_cred', 'username')) {
    return false;
  }

  $sql = "SELECT `id` FROM `user_cred` WHERE `username`=?";
  $types = 's';
  $params = [$username];

  if ($excludeId > 0) {
    $sql .= " AND `id`<>?";
    $types .= 'i';
    $params[] = $excludeId;
  }

  $sql .= " LIMIT 1";
  $res = select($sql, $params, $types);
  return $res && mysqli_num_rows($res) > 0;
}

function appSchemaBuildUniqueGuestUsername(mysqli $con, array $row): string
{
  $id = (int)($row['id'] ?? 0);
  $candidates = [];

  $email = trim((string)($row['email'] ?? ''));
  if ($email !== '' && strpos($email, '@') !== false) {
    $candidates[] = substr($email, 0, strpos($email, '@'));
  }

  $name = trim((string)($row['name'] ?? ''));
  if ($name !== '') {
    $candidates[] = $name;
  }

  $candidates[] = 'guest' . max(1, $id);
  $suffix = max(1, $id);

  foreach ($candidates as $candidate) {
    $normalized = appSchemaNormalizeUsernameCandidate($candidate);
    if ($normalized !== '' && !appSchemaUsernameExists($con, $normalized, $id)) {
      return $normalized;
    }
  }

  do {
    $fallback = 'guest' . $suffix;
    $suffix++;
  } while (appSchemaUsernameExists($con, $fallback, $id));

  return $fallback;
}

function appSchemaNormalizeWalkInBookings(mysqli $con): void
{
  if (!appSchemaTableExists($con, 'booking_order') || !appSchemaTableExists($con, 'booking_details')) {
    return;
  }

  @mysqli_query($con, "UPDATE `booking_order`
    SET `booking_status` = CASE
          WHEN COALESCE(`payment_status`, 'pending') = 'paid' THEN 'booked'
          ELSE 'pending'
        END
    WHERE COALESCE(`booking_source`, 'online') = 'walk_in'
      AND COALESCE(`booking_status`, 'pending') IN ('pending', 'booked')
      AND COALESCE(`is_archived`, 0) = 0");

  @mysqli_query($con, "UPDATE `booking_order` bo
    INNER JOIN `booking_details` bd ON bd.`booking_id` = bo.`booking_id`
    SET bo.`arrival` = CASE
          WHEN bo.`check_in` <= CURDATE()
            AND TRIM(COALESCE(bd.`room_no`, '')) <> ''
          THEN 1
          ELSE 0
        END
    WHERE COALESCE(bo.`booking_source`, 'online') = 'walk_in'
      AND COALESCE(bo.`booking_status`, 'pending') IN ('pending', 'booked')
      AND COALESCE(bo.`is_archived`, 0) = 0");
}

function appSchemaEnsureGuestUsernames(mysqli $con): void
{
  if (!appSchemaTableExists($con, 'user_cred') || !appSchemaColumnExists($con, 'user_cred', 'username')) {
    return;
  }

  $res = mysqli_query($con, "SELECT `id`,`name`,`email`,`username` FROM `user_cred` WHERE `username` IS NULL OR TRIM(`username`)=''");
  if (!$res) {
    return;
  }

  while ($row = mysqli_fetch_assoc($res)) {
    $username = appSchemaBuildUniqueGuestUsername($con, $row);
    if ($username === '') {
      continue;
    }

    $stmt = mysqli_prepare($con, "UPDATE `user_cred` SET `username`=? WHERE `id`=?");
    if ($stmt) {
      $id = (int)$row['id'];
      mysqli_stmt_bind_param($stmt, 'si', $username, $id);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_close($stmt);
    }
  }
}

function ensureAppSchema(): bool
{
  static $bootstrapped = false;
  if ($bootstrapped) {
    return true;
  }

  $con = $GLOBALS['con'] ?? null;
  if (!$con instanceof mysqli) {
    return false;
  }

  if (!appSchemaTableExists($con, 'notifications')) {
    appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `notifications` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  }

  if (!appSchemaTableExists($con, 'transactions')) {
    appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `transactions` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `booking_id` int(11) DEFAULT NULL,
      `guest_name` varchar(100) NOT NULL,
      `room_no` varchar(50) DEFAULT NULL,
      `amount` int(11) NOT NULL,
      `method` varchar(50) NOT NULL,
      `status` varchar(50) NOT NULL,
      `type` varchar(50) NOT NULL,
      `admin_id` int(11) NOT NULL DEFAULT 0,
      `datentime` datetime NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      KEY `booking_id` (`booking_id`),
      KEY `status` (`status`),
      KEY `type` (`type`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  }

  if (!appSchemaTableExists($con, 'booking_extras')) {
    appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `booking_extras` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `booking_id` int(11) NOT NULL,
      `extra_id` int(11) NOT NULL,
      `name` varchar(150) NOT NULL,
      `quantity` int(11) NOT NULL DEFAULT 1,
      `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
      `total_price` decimal(10,2) NOT NULL DEFAULT 0.00,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      KEY `booking_id` (`booking_id`),
      KEY `extra_id` (`extra_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  }

  if (!appSchemaTableExists($con, 'booking_history')) {
    appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `booking_history` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `booking_id` int(11) NOT NULL,
      `actor_type` varchar(30) NOT NULL DEFAULT 'system',
      `actor_id` int(11) DEFAULT NULL,
      `actor_name` varchar(150) DEFAULT NULL,
      `event_type` varchar(60) NOT NULL,
      `title` varchar(180) NOT NULL,
      `details` text DEFAULT NULL,
      `meta_json` longtext DEFAULT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      KEY `booking_id` (`booking_id`),
      KEY `event_type` (`event_type`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  }

  if (!appSchemaTableExists($con, 'email_logs')) {
    appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `email_logs` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `related_booking_id` int(11) DEFAULT NULL,
      `related_user_id` int(11) DEFAULT NULL,
      `recipient_email` varchar(255) NOT NULL,
      `recipient_name` varchar(150) DEFAULT NULL,
      `subject` varchar(255) NOT NULL,
      `template_key` varchar(80) NOT NULL DEFAULT 'general',
      `status` enum('queued','sent','failed') NOT NULL DEFAULT 'sent',
      `error_message` text DEFAULT NULL,
      `triggered_by` varchar(60) DEFAULT 'system',
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      KEY `related_booking_id` (`related_booking_id`),
      KEY `related_user_id` (`related_user_id`),
      KEY `status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  }

  if (!appSchemaTableExists($con, 'support_tickets')) {
    appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `support_tickets` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `ticket_code` varchar(30) NOT NULL,
      `user_id` int(11) NOT NULL,
      `booking_id` int(11) DEFAULT NULL,
      `order_id` varchar(80) DEFAULT NULL,
      `subject` varchar(180) NOT NULL,
      `category` varchar(40) NOT NULL DEFAULT 'general',
      `priority` varchar(20) NOT NULL DEFAULT 'normal',
      `status` varchar(20) NOT NULL DEFAULT 'open',
      `assigned_to` int(11) DEFAULT NULL,
      `escalated` tinyint(1) NOT NULL DEFAULT 0,
      `last_reply_at` datetime DEFAULT NULL,
      `last_reply_by` varchar(20) DEFAULT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
      PRIMARY KEY (`id`),
      UNIQUE KEY `ticket_code` (`ticket_code`),
      KEY `user_id` (`user_id`),
      KEY `booking_id` (`booking_id`),
      KEY `status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  }

  if (!appSchemaTableExists($con, 'support_ticket_messages')) {
    appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `support_ticket_messages` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `ticket_id` int(11) NOT NULL,
      `sender_type` varchar(20) NOT NULL DEFAULT 'guest',
      `sender_id` int(11) DEFAULT NULL,
      `sender_name` varchar(150) DEFAULT NULL,
      `message` text NOT NULL,
      `attachment_path` varchar(255) DEFAULT NULL,
      `is_internal` tinyint(1) NOT NULL DEFAULT 0,
      `seen_by_user` tinyint(1) NOT NULL DEFAULT 0,
      `seen_by_staff` tinyint(1) NOT NULL DEFAULT 0,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      KEY `ticket_id` (`ticket_id`),
      KEY `seen_by_user` (`seen_by_user`),
      KEY `seen_by_staff` (`seen_by_staff`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  }

  if (!appSchemaTableExists($con, 'support_canned_replies')) {
    appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `support_canned_replies` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `title` varchar(120) NOT NULL,
      `category` varchar(40) NOT NULL DEFAULT 'general',
      `reply_text` text NOT NULL,
      `is_active` tinyint(1) NOT NULL DEFAULT 1,
      `created_by` int(11) DEFAULT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  }

  if (!appSchemaTableExists($con, 'guest_notes')) {
    appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `guest_notes` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `user_id` int(11) NOT NULL,
      `booking_id` int(11) DEFAULT NULL,
      `note_type` varchar(30) NOT NULL DEFAULT 'internal',
      `title` varchar(150) NOT NULL,
      `note` text NOT NULL,
      `created_by` int(11) DEFAULT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
      PRIMARY KEY (`id`),
      KEY `user_id` (`user_id`),
      KEY `booking_id` (`booking_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  }

  if (!appSchemaTableExists($con, 'room_block_dates')) {
    appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `room_block_dates` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `room_id` int(11) NOT NULL,
      `room_no` varchar(20) DEFAULT NULL,
      `start_date` date NOT NULL,
      `end_date` date NOT NULL,
      `block_type` varchar(30) NOT NULL DEFAULT 'maintenance',
      `reason` varchar(255) DEFAULT NULL,
      `status` varchar(20) NOT NULL DEFAULT 'active',
      `created_by` int(11) DEFAULT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      KEY `room_id` (`room_id`),
      KEY `start_date` (`start_date`),
      KEY `end_date` (`end_date`),
      KEY `status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  }

  if (!appSchemaTableExists($con, 'promo_codes')) {
    appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `promo_codes` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `code` varchar(50) NOT NULL,
      `description` varchar(255) DEFAULT NULL,
      `discount_type` varchar(20) NOT NULL DEFAULT 'percent',
      `discount_value` decimal(10,2) NOT NULL DEFAULT 0.00,
      `min_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
      `max_discount` decimal(10,2) NOT NULL DEFAULT 0.00,
      `start_date` date DEFAULT NULL,
      `end_date` date DEFAULT NULL,
      `usage_limit` int(11) NOT NULL DEFAULT 0,
      `used_count` int(11) NOT NULL DEFAULT 0,
      `is_active` tinyint(1) NOT NULL DEFAULT 1,
      `created_by` int(11) DEFAULT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      UNIQUE KEY `code` (`code`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  }

  if (!appSchemaTableExists($con, 'promo_redemptions')) {
    appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `promo_redemptions` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `promo_id` int(11) NOT NULL,
      `booking_id` int(11) NOT NULL,
      `user_id` int(11) NOT NULL,
      `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      KEY `promo_id` (`promo_id`),
      KEY `booking_id` (`booking_id`),
      KEY `user_id` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  }

  if (!appSchemaTableExists($con, 'admin_user_permissions')) {
    appSchemaQuery($con, "CREATE TABLE IF NOT EXISTS `admin_user_permissions` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `admin_user_id` int(11) NOT NULL,
      `permission_code` varchar(80) NOT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      UNIQUE KEY `uniq_admin_permission` (`admin_user_id`,`permission_code`),
      KEY `permission_code` (`permission_code`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  }

  $tableColumns = [
    'user_cred' => [
      'username' => "VARCHAR(100) DEFAULT NULL",
      'is_verified' => "TINYINT(1) NOT NULL DEFAULT 0",
      'verification_code' => "VARCHAR(255) DEFAULT NULL",
      'token' => "VARCHAR(255) DEFAULT NULL",
      't_expire' => "DATE DEFAULT NULL",
    ],
    'booking_order' => [
      'refund_amount' => "DECIMAL(10,2) DEFAULT 0.00",
      'payment_status' => "ENUM('pending','partial','paid') DEFAULT 'pending'",
      'payment_proof' => "VARCHAR(255) DEFAULT NULL",
      'amount_paid' => "DECIMAL(10,2) DEFAULT 0.00",
      'total_amt' => "DECIMAL(10,2) DEFAULT 0.00",
      'downpayment' => "DECIMAL(10,2) DEFAULT 0.00",
      'balance_due' => "DECIMAL(10,2) DEFAULT 0.00",
      'confirmed_at' => "DATETIME DEFAULT NULL",
      'refund_proof' => "VARCHAR(255) DEFAULT NULL",
      'promo_code' => "VARCHAR(50) DEFAULT NULL",
      'discount_amount' => "DECIMAL(10,2) DEFAULT 0.00",
      'booking_source' => "VARCHAR(20) NOT NULL DEFAULT 'online'",
      'created_by_admin' => "INT(11) DEFAULT NULL",
      'walkin_note' => "TEXT NULL",
    ],
    'booking_details' => [
      'booking_note' => "TEXT NULL",
      'staff_note' => "TEXT NULL",
      'extras_total' => "DECIMAL(10,2) DEFAULT 0.00",
      'downpayment' => "DECIMAL(10,2) DEFAULT 0.00",
      'remaining_balance' => "DECIMAL(10,2) DEFAULT 0.00",
    ],
    'rooms' => [
      'archived_at' => "DATETIME DEFAULT NULL",
      'is_archived' => "TINYINT(1) NOT NULL DEFAULT 0",
    ],
    'user_queries' => [
      'is_archived' => "TINYINT(1) NOT NULL DEFAULT 0",
      'archived_at' => "DATETIME DEFAULT NULL",
    ],
    'support_tickets' => [
      'is_archived' => "TINYINT(1) NOT NULL DEFAULT 0",
      'archived_at' => "DATETIME DEFAULT NULL",
    ],
    'transactions' => [
      'is_archived' => "TINYINT(1) NOT NULL DEFAULT 0",
      'archived_at' => "DATETIME DEFAULT NULL",
    ],
    'notifications' => [
      'type' => "ENUM('booking','payment','refund','system') NOT NULL DEFAULT 'system'",
      'is_read' => "TINYINT(1) NOT NULL DEFAULT 0",
      'created_at' => "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP",
      'is_archived' => "TINYINT(1) NOT NULL DEFAULT 0",
      'archived_at' => "DATETIME DEFAULT NULL",
    ],
    'rating_review' => [
      'is_archived' => "TINYINT(1) NOT NULL DEFAULT 0",
      'archived_at' => "DATETIME DEFAULT NULL",
    ],
    'admin_users' => [
      'email' => "VARCHAR(255) DEFAULT NULL",
      'reset_token' => "VARCHAR(64) DEFAULT NULL",
      'reset_expires' => "DATETIME DEFAULT NULL",
    ],
  ];

  foreach ($tableColumns as $table => $columns) {
    if (!appSchemaTableExists($con, $table)) {
      continue;
    }
    foreach ($columns as $column => $definition) {
      appSchemaEnsureColumn($con, $table, $column, $definition);
    }
  }

  appSchemaEnsureArchiveTables($con);
  appSchemaNormalizeWalkInBookings($con);
  appSchemaEnsureGuestUsernames($con);
  appSchemaBackfillArchivedBookings($con);
  appSchemaBackfillArchivedBookingRelations($con);
  appSchemaBackfillArchivedRooms($con);
  appSchemaBackfillArchivedRoomRelations($con);
  appSchemaBackfillArchivedUserRelations($con);
  appSchemaBackfillArchivedQueries($con);

  if (appSchemaTableExists($con, 'notifications')) {
    appSchemaEnsureIndex($con, 'notifications', 'type', "INDEX `type` (`type`)");
    appSchemaEnsureIndex($con, 'notifications', 'is_read', "INDEX `is_read` (`is_read`)");
  }

  if (appSchemaTableExists($con, 'admin_users')) {
    appSchemaEnsureIndex($con, 'admin_users', 'uniq_admin_users_username', "UNIQUE KEY `uniq_admin_users_username` (`username`)");
  }

  if (appSchemaTableExists($con, 'user_cred')) {
    appSchemaEnsureIndex($con, 'user_cred', 'uniq_user_cred_username', "UNIQUE KEY `uniq_user_cred_username` (`username`)");
  }

  if (appSchemaTableExists($con, 'support_tickets')) {
    appSchemaEnsureIndex($con, 'support_tickets', 'ticket_code', "UNIQUE KEY `ticket_code` (`ticket_code`)");
  }

  if (appSchemaTableExists($con, 'promo_codes')) {
    appSchemaEnsureIndex($con, 'promo_codes', 'code', "UNIQUE KEY `code` (`code`)");
  }

  if (appSchemaTableExists($con, 'support_canned_replies')) {
    $seedCheck = mysqli_query($con, "SELECT COUNT(*) AS c FROM `support_canned_replies`");
    $seedRow = $seedCheck ? mysqli_fetch_assoc($seedCheck) : null;
    if ((int)($seedRow['c'] ?? 0) === 0) {
      @mysqli_query($con, "INSERT INTO `support_canned_replies` (`title`,`category`,`reply_text`,`is_active`) VALUES
        ('Booking received', 'booking', 'We have received your booking request and our team is currently reviewing the submitted details. We will update you as soon as verification is completed.', 1),
        ('Refund in progress', 'refund', 'Your refund request is already in process. Once the transfer is completed, we will upload the proof and send you a confirmation update.', 1),
        ('Need more payment details', 'payment', 'Thank you for your payment submission. We need a clearer screenshot or reference number to verify it properly. Please reply here and attach the updated proof.', 1)");
    }
  }

  $bootstrapped = true;
  return true;
}
