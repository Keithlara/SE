<?php
require('admin/inc/db_config.php');

header('Content-Type: text/plain; charset=UTF-8');

if (function_exists('ensureAppSchema') && ensureAppSchema()) {
    echo "Booking payment schema is up to date.\n";
} else {
    http_response_code(500);
    echo "Failed to initialize application schema.\n";
}
?>
