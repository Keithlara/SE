<?php

function ensureAdminUsersTable()
{
  $con = $GLOBALS['con'] ?? null;
  if(!$con){ return false; }

  if(function_exists('ensureAppSchema') && appSchemaTableExists($con, 'admin_users')){
    ensureAppSchema();
    return true;
  }

  $sql = "CREATE TABLE IF NOT EXISTS `admin_users` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin','staff') NOT NULL DEFAULT 'staff',
    `email` VARCHAR(255) DEFAULT NULL,
    `reset_token` VARCHAR(64) DEFAULT NULL,
    `reset_expires` DATETIME DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_admin_users_username` (`username`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

  if(!mysqli_query($con, $sql)){ return false; }

  // Idempotently add columns if upgrading from an older schema
  $extra_columns = [
    'email'         => "ALTER TABLE `admin_users` ADD `email` VARCHAR(255) DEFAULT NULL",
    'reset_token'   => "ALTER TABLE `admin_users` ADD `reset_token` VARCHAR(64) DEFAULT NULL",
    'reset_expires' => "ALTER TABLE `admin_users` ADD `reset_expires` DATETIME DEFAULT NULL",
  ];
  foreach($extra_columns as $col => $alter){
    $check = mysqli_query($con, "SHOW COLUMNS FROM `admin_users` LIKE '$col'");
    if($check && mysqli_num_rows($check) === 0){
      mysqli_query($con, $alter);
    }
  }

  return true;
}
