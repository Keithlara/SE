<?php
require_once('admin/inc/db_config.php');

header('Content-Type: text/plain; charset=UTF-8');

if (function_exists('ensureAppSchema') && ensureAppSchema()) {
    echo "Booking schema is up to date.\n";
} else {
    http_response_code(500);
    echo "Failed to initialize application schema.\n";
}
return;
// Database connection details - using common XAMPP defaults
$host = 'localhost';
$username = 'root';
$password = '';

// Try to detect the database name
$dbname = '';
$conn = new mysqli($host, $username, $password);

if ($conn->connect_error) {
    die("<h2>❌ Connection failed: " . $conn->connect_error . "</h2>");
}

// Get list of databases
$result = $conn->query("SHOW DATABASES");
$possible_dbs = [];
while($row = $result->fetch_array()) {
    $db = $row[0];
    if (!in_array($db, ['information_schema', 'mysql', 'performance_schema', 'phpmyadmin', 'sys'])) {
        $possible_dbs[] = $db;
    }
}

// Try to find the correct database
if (count($possible_dbs) === 1) {
    $dbname = $possible_dbs[0];
    log_message("✅ Auto-detected database: " . $dbname, 'success');
} else if (count($possible_dbs) > 1) {
    echo "<h2>Multiple databases found. Please select one:</h2>";
    foreach ($possible_dbs as $i => $db) {
        echo "<a href='?db=" . urlencode($db) . "'>" . htmlspecialchars($db) . "</a><br>";
    }
    exit;
} else {
    echo "<h2>No databases found. Please create a database first.</h2>";
    exit;
}

$conn->close();

// If database is specified in URL, use it
if (isset($_GET['db']) && !empty($_GET['db'])) {
    $dbname = $_GET['db'];
}

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("<h2>❌ Connection failed: " . $conn->connect_error . "</h2>");
}

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");

// Start output buffering
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fixing Booking System</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 20px; }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .info { color: #17a2b8; }
        table { border-collapse: collapse; margin: 15px 0; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
<h1>Booking System Fix</h1>
<?php

function log_message($message, $type = 'info') {
    $class = '';
    switch ($type) {
        case 'success': $class = 'success'; break;
        case 'error': $class = 'error'; break;
        default: $class = 'info';
    }
    echo "<div class='$class'>• " . htmlspecialchars($message) . "</div>\n";
}

// 1. Check if table exists
log_message("Checking if booking_order table exists...");
$table_check = $conn->query("SHOW TABLES LIKE 'booking_order'");

if ($table_check->num_rows === 0) {
    log_message("❌ Error: The 'booking_order' table does not exist in the database.", 'error');
    exit;
}

// 2. Check current table structure
log_message("Checking current table structure...");
$result = $conn->query("SHOW CREATE TABLE booking_order");
$row = $result->fetch_assoc();
$create_table = $row['Create Table'];

echo "<details><summary>📋 Current Table Structure</summary><pre>" . htmlspecialchars($create_table) . "</pre></details>";

// 3. Add missing columns if they don't exist
$columns_to_add = [
    [
        'name' => 'payment_status',
        'type' => "ENUM('pending', 'partial', 'paid') DEFAULT 'pending' AFTER `booking_status`"
    ],
    [
        'name' => 'amount_paid',
        'type' => 'DECIMAL(10,2) DEFAULT 0.00 AFTER `payment_status`'
    ]
];

foreach ($columns_to_add as $column) {
    $column_name = $column['name'];
    $column_check = $conn->query("SHOW COLUMNS FROM `booking_order` LIKE '$column_name'");
    
    if ($column_check->num_rows === 0) {
        log_message("Adding column: $column_name...");
        $alter_sql = "ALTER TABLE `booking_order` ADD COLUMN `$column_name` {$column['type']}";
        
        if ($conn->query($alter_sql) === TRUE) {
            log_message("✅ Successfully added column: $column_name", 'success');
        } else {
            log_message("❌ Error adding column $column_name: " . $conn->error, 'error');
        }
    } else {
        log_message("ℹ️ Column '$column_name' already exists", 'info');
    }
}

// 4. Update existing records to mark as paid if they're booked
log_message("Updating existing bookings...");
$update_sql = "UPDATE `booking_order` 
              SET `payment_status` = 'paid', 
                  `amount_paid` = `trans_amt` 
              WHERE `booking_status` = 'booked' 
              AND (`payment_status` IS NULL OR `payment_status` = '')";

if ($conn->query($update_sql) === TRUE) {
    $affected_rows = $conn->affected_rows;
    log_message("✅ Updated $affected_rows booking(s) to mark as paid", 'success');
} else {
    log_message("❌ Error updating bookings: " . $conn->error, 'error');
}

// 5. Show sample data
log_message("Showing sample booking data...");
$sample = $conn->query("SELECT 
    booking_id, 
    booking_status, 
    payment_status, 
    amount_paid, 
    trans_amt,
    DATE_FORMAT(datentime, '%Y-%m-%d %H:%i:%s') as booking_date
    FROM booking_order 
    ORDER BY booking_id DESC 
    LIMIT 5");

if ($sample->num_rows > 0) {
    echo "<h3>📊 Sample Booking Data</h3>";
    echo "<table>";
    echo "<tr><th>ID</th><th>Status</th><th>Payment Status</th><th>Amount Paid</th><th>Trans Amount</th><th>Booking Date</th></tr>";
    
    while($row = $sample->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row["booking_id"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["booking_status"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["payment_status"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["amount_paid"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["trans_amt"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["booking_date"]) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// 6. Show final status
log_message("✅ Database update process completed", 'success');

// 7. Clean up
$conn->close();

// 8. Self-destruct the file if everything is successful
if (!isset($_GET['keep'])) {
    $file = __FILE__;
    if (unlink($file)) {
        log_message("🔒 This file has been automatically deleted for security.", 'info');
    } else {
        log_message("⚠️ Please manually delete this file: " . htmlspecialchars($file), 'error');
    }
} else {
    log_message("ℹ️ File kept as requested (keep parameter present).", 'info');
}

// Flush output buffer
ob_end_flush();
?>
</body>
</html>
