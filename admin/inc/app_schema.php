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

  $bootstrapped = true;
  return true;
}
