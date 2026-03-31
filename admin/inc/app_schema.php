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
      'is_verified' => "TINYINT(1) NOT NULL DEFAULT 0",
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
    ],
    'booking_details' => [
      'booking_note' => "TEXT NULL",
      'staff_note' => "TEXT NULL",
      'extras_total' => "DECIMAL(10,2) DEFAULT 0.00",
      'downpayment' => "DECIMAL(10,2) DEFAULT 0.00",
      'remaining_balance' => "DECIMAL(10,2) DEFAULT 0.00",
    ],
    'notifications' => [
      'type' => "ENUM('booking','payment','refund','system') NOT NULL DEFAULT 'system'",
      'is_read' => "TINYINT(1) NOT NULL DEFAULT 0",
      'created_at' => "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP",
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

  if (appSchemaTableExists($con, 'notifications')) {
    appSchemaEnsureIndex($con, 'notifications', 'type', "INDEX `type` (`type`)");
    appSchemaEnsureIndex($con, 'notifications', 'is_read', "INDEX `is_read` (`is_read`)");
  }

  if (appSchemaTableExists($con, 'admin_users')) {
    appSchemaEnsureIndex($con, 'admin_users', 'uniq_admin_users_username', "UNIQUE KEY `uniq_admin_users_username` (`username`)");
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
