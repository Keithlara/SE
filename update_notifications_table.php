<?php
require('admin/inc/db_config.php');

if (function_exists('ensureAppSchema') && ensureAppSchema()) {
    echo "Notifications schema is up to date.\n";
} else {
    http_response_code(500);
    echo "Failed to initialize application schema.\n";
}
?>
