<?php
require('admin/inc/db_config.php');

// Check if booking_order table exists
$table_check = mysqli_query($con, "SHOW TABLES LIKE 'booking_order'");
if(mysqli_num_rows($table_check) == 0) {
    die("Error: The 'booking_order' table does not exist in the database.\n");
}

// Get table structure
$result = mysqli_query($con, "DESCRIBE booking_order");
if(!$result) {
    die("Error describing table: " . mysqli_error($con) . "\n");
}

echo "=== booking_order Table Structure ===\n";
echo str_pad("Field", 30) . str_pad("Type", 20) . "Null\tKey\tDefault\tExtra\n";
echo str_repeat("-", 80) . "\n";

while($row = mysqli_fetch_assoc($result)) {
    echo str_pad($row['Field'], 30) . 
         str_pad($row['Type'], 20) . 
         $row['Null'] . "\t" . 
         ($row['Key'] ?: '') . "\t" . 
         ($row['Default'] ?: 'NULL') . "\t" . 
         ($row['Extra'] ?: '') . "\n";
}

// Check for required columns
$required_columns = ['booking_id', 'user_id', 'room_id', 'check_in', 'check_out', 'booking_status', 'payment_status'];
$missing_columns = [];

foreach($required_columns as $col) {
    $check = mysqli_query($con, "SHOW COLUMNS FROM booking_order LIKE '$col'");
    if(mysqli_num_rows($check) == 0) {
        $missing_columns[] = $col;
    }
}

if(!empty($missing_columns)) {
    echo "\n=== Missing Required Columns ===\n";
    echo "The following required columns are missing from the booking_order table:\n";
    foreach($missing_columns as $col) {
        echo "- $col\n";
    }
} else {
    echo "\nAll required columns are present in the booking_order table.\n";
}

// Check database connection
if($con->connect_error) {
    echo "\n=== Database Connection Error ===\n";
    die("Connection failed: " . $con->connect_error);
} else {
    echo "\n=== Database Connection ===\n";
    echo "Connected successfully to database: " . $con->host_info . "\n";
}

// Check if there are any records in the table
$result = mysqli_query($con, "SELECT COUNT(*) as count FROM booking_order");
$row = mysqli_fetch_assoc($result);
echo "\n=== Table Statistics ===\n";
echo "Number of records in booking_order table: " . $row['count'] . "\n";

// Check for any data in the table
if($row['count'] > 0) {
    $result = mysqli_query($con, "SELECT * FROM booking_order ORDER BY booking_id DESC LIMIT 1");
    $latest_booking = mysqli_fetch_assoc($result);
    
    echo "\n=== Latest Booking ===\n";
    foreach($latest_booking as $key => $value) {
        echo str_pad($key . ":", 20) . " $value\n";
    }
}

// Check if the user has permission to update the table
$result = mysqli_query($con, "SELECT CURRENT_USER() as user, DATABASE() as db");
$user_info = mysqli_fetch_assoc($result);

echo "\n=== User Permissions ===\n";
echo "Current User: " . $user_info['user'] . "\n";
echo "Current Database: " . $user_info['db'] . "\n";

// Test update query
$test_query = "UPDATE booking_order SET booking_status = 'booked' WHERE booking_id = ? LIMIT 1";
$stmt = $con->prepare($test_query);

if($stmt === false) {
    echo "\n=== Update Query Test ===\n";
    echo "Error preparing test query: " . $con->error . "\n";
} else {
    echo "\n=== Update Query Test ===\n";
    echo "Test query prepared successfully.\n";
    $stmt->close();
}

// Check for any errors in the error log
$error_log = ini_get('error_log');
echo "\n=== Error Log Location ===\n";
echo "PHP Error Log: " . (empty($error_log) ? 'Not set in php.ini' : $error_log) . "\n";

// Check if we can write to the error log
$test_log = dirname(__FILE__) . '/test_error.log';
if(@file_put_contents($test_log, 'test') !== false) {
    echo "Able to write to test log file: $test_log\n";
    @unlink($test_log);
} else {
    echo "Unable to write to test log file: $test_log\n";
    echo "Check directory permissions for: " . dirname($test_log) . "\n";
}

// Check PHP error reporting
echo "\n=== PHP Error Reporting ===\n";
echo "Error Reporting Level: " . ini_get('error_reporting') . "\n";
echo "Display Errors: " . ini_get('display_errors') . "\n";

// Check for any recent PHP errors in the error log
if (!empty($error_log) && file_exists($error_log)) {
    echo "\n=== Recent PHP Errors ===\n";
    $log_content = file($error_log);
    $recent_errors = array_slice($log_content, -10); // Get last 10 lines
    echo implode("", $recent_errors);
}

// Close the database connection
$con->close();
?>
