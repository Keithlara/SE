<?php
require 'admin/inc/db_config.php';
$tables = ['booking_order','user_cred','rooms','support_tickets','support_ticket_messages','transactions','notifications','rating_review','guest_notes','email_logs','user_queries'];
foreach ($tables as $table) {
  echo "TABLE:$table\n";
  $res = mysqli_query($con, "SHOW COLUMNS FROM `$table`");
  if (!$res) { echo "ERR:" . mysqli_error($con) . "\n\n"; continue; }
  while ($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . '|' . $row['Type'] . '|' . $row['Null'] . '|' . $row['Key'] . '|' . ($row['Default'] ?? 'NULL') . "\n";
  }
  echo "\n";
}
?>
