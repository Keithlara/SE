<?php 
  if (session_status() === PHP_SESSION_NONE) { session_start(); }
  require('inc/essentials.php');

  session_destroy();
  redirect('index.php');

?>