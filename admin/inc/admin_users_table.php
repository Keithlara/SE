<?php

function ensureAdminUsersTable()
{
  $con = $GLOBALS['con'] ?? null;
  if(!$con){ return false; }

  $sql = "CREATE TABLE IF NOT EXISTS `admin_users` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin','staff') NOT NULL DEFAULT 'staff',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_admin_users_username` (`username`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

  return mysqli_query($con, $sql) ? true : false;
}

