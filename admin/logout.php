<?php 
  session_start();
  require('inc/essentials.php');

  session_destroy();
  redirect('index.php');

?>