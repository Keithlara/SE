<?php
require 'C:/xampp/htdocs/SE/admin/inc/db_config.php';
$res = mysqli_query($con, "SHOW COLUMNS FROM `rating_review`");
while ($row = mysqli_fetch_assoc($res)) {
  echo $row['Field'] . '|' . $row['Type'] . PHP_EOL;
}
?>