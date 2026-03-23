<?php
// Database connection
try {
    $host = 'localhost';
    $dbname = 'travelers_db';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create notifications table
    $sql = "CREATE TABLE IF NOT EXISTS `notifications` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) NOT NULL,
        `booking_id` int(11) NOT NULL,
        `message` text NOT NULL,
        `is_read` tinyint(1) DEFAULT 0,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `user_id` (`user_id`),
        KEY `booking_id` (`booking_id`),
        CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_cred` (`id`) ON DELETE CASCADE,
        CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`booking_id`) REFERENCES `booking_order` (`booking_id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $pdo->exec($sql);
    echo "✅ Notifications table created successfully\n";
    
    // Create notification function in functions.php
    $functions_file = __DIR__ . '/inc/functions.php';
    $notification_function = "
// Function to create a new notification
function createNotification(\$con, \$user_id, \$booking_id, \$message) {
    \$query = \"INSERT INTO notifications (user_id, booking_id, message) VALUES (?, ?, ?)\";
    \$stmt = \$con->prepare(\$query);
    return \$stmt->execute([\$user_id, \$booking_id, \$message]);
}";

    if (file_put_contents($functions_file, $notification_function, FILE_APPEND) !== false) {
        echo "✅ Notification function added to functions.php\n";
    } else {
        echo "❌ Could not write to functions.php. Please add the function manually.\n";
    }
    
    echo "🎉 Notification system setup complete!\n";
    echo "<a href='javascript:history.back()'>Go back</a> or <a href='../'>Go to homepage</a>";
    
} catch(PDOException $e) {
    die("❌ Error: " . $e->getMessage());
}
?>
