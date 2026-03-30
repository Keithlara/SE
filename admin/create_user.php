<?php
  require('inc/essentials.php');
  require('inc/db_config.php');
  requireAdminRole();
  header('Location: manage_users.php?tab=create');
  exit;
