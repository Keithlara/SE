<?php 
// This endpoint must return clean JSON (no HTML warnings/notices).
// Log errors to file, but do not display them in the response.
ini_set('display_errors', 0);
ini_set('log_errors', 1);
$error_log_path = dirname(__DIR__, 2) . '/error_log.txt';
ini_set('error_log', $error_log_path);

error_log('\n=== ' . date('Y-m-d H:i:s') . ' Starting confirm_booking.php ===');

// Prevent any accidental output (warnings, BOM, etc.) from breaking JSON.
ob_start();

function safe_debug_log($payload){
    // Optional dev logging; never allow failures to bubble into output.
    try {
        $dir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . '.cursor';
        if (!is_dir($dir)) { return; }
        @file_put_contents($dir . DIRECTORY_SEPARATOR . 'debug.log', json_encode($payload) . PHP_EOL, FILE_APPEND);
    } catch (Throwable $e) {
        // ignore
    }
}

function ensure_confirmed_at_column($con){
    $col = $con->query("SHOW COLUMNS FROM `booking_order` LIKE 'confirmed_at'");
    if(!$col || $col->num_rows === 0){
        $con->query("ALTER TABLE `booking_order` ADD `confirmed_at` DATETIME NULL DEFAULT NULL");
    }
}

function ensure_staff_note_column($con){
    $col = $con->query("SHOW COLUMNS FROM `booking_details` LIKE 'staff_note'");
    if(!$col || $col->num_rows === 0){
        $con->query("ALTER TABLE `booking_details` ADD `staff_note` TEXT NULL");
    }
}

try {
    // Set content type to JSON
    header('Content-Type: application/json');
    
    // Function to send JSON response
    function sendJsonResponse($status, $message, $data = []) {
        if (ob_get_length()) { @ob_clean(); }
        $response = ['status' => $status, 'message' => $message];
        if (!empty($data)) {
            $response['data'] = $data;
        }
        echo json_encode($response);
        error_log("Response: " . json_encode($response));
        exit;
    }
    
    // Log POST data
    error_log('POST data: ' . print_r($_POST, true));
    
    // Include required files
    $db_config_path = dirname(__DIR__) . '/inc/db_config.php';
    $essentials_path = dirname(__DIR__) . '/inc/essentials.php';
    
    if (!file_exists($db_config_path) || !file_exists($essentials_path)) {
        error_log("Error: Required files not found. db_config: " . (file_exists($db_config_path) ? 'exists' : 'missing') . ", essentials: " . (file_exists($essentials_path) ? 'exists' : 'missing'));
        throw new Exception("Required files not found");
    }
    
    require($db_config_path);
    require($essentials_path);
    require_once(dirname(__DIR__, 2) . '/inc/booking_notifications.php');
    
    error_log('Required files included successfully');
    ensure_confirmed_at_column($con);
    ensure_staff_note_column($con);
    
    // Check if database connection is established
    if (!isset($con) || !$con) {
        error_log("Database connection not established");
        throw new Exception("Database connection failed");
    }
    
    // Check if required POST parameters are set
    if (!isset($_POST['confirm_booking']) || !isset($_POST['booking_id'])) {
        error_log("Missing required parameters: " . print_r($_POST, true));
        sendJsonResponse('error', 'Missing required parameters');
    }
    
    $booking_id = (int)$_POST['booking_id'];
    error_log("Processing booking ID: $booking_id");
    $staff_note = '';
    if(isset($_POST['staff_note'])){
        $staff_note = trim((string)$_POST['staff_note']);
        if(function_exists('mb_substr')){
            $staff_note = mb_substr($staff_note, 0, 500);
        } else {
            $staff_note = substr($staff_note, 0, 500);
        }
    }

    // #region agent log
    safe_debug_log([
        'sessionId' => 'debug-session',
        'runId' => 'confirm-booking',
        'location' => __FILE__ . ':entry',
        'message' => 'Admin confirm_booking entry',
        'data' => [
            'booking_id' => $booking_id,
            'post_keys' => array_keys($_POST),
        ],
        'timestamp' => round(microtime(true) * 1000),
    ]);
    // #endregion

    adminLogin();

    if (!isset($_POST['confirm_booking']) || !isset($_POST['booking_id'])) {
        sendJsonResponse('error', 'Invalid request');
    }

    // First, check if the booking exists
    $check_query = "SELECT * FROM booking_order WHERE booking_id = ?";
    $stmt = $con->prepare($check_query);
    if (!$stmt) {
        error_log("Prepare failed: " . $con->error);
        throw new Exception("Database error");
    }
    
    $stmt->bind_param('i', $booking_id);
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        throw new Exception("Database error");
    }
    
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        error_log("Booking not found with ID: $booking_id");
        sendJsonResponse('error', 'Booking not found');
    }
    
    $booking_data = $result->fetch_assoc();
    error_log("Found booking: " . print_r($booking_data, true));

    // #region agent log
    safe_debug_log([
        'sessionId' => 'debug-session',
        'runId' => 'confirm-booking',
        'location' => __FILE__ . ':booking-loaded',
        'message' => 'Booking loaded for confirmation',
        'data' => [
            'booking_id' => $booking_id,
            'user_id' => $booking_data['user_id'] ?? null,
            'booking_status' => $booking_data['booking_status'] ?? null,
            'trans_status' => $booking_data['trans_status'] ?? null,
        ],
        'timestamp' => round(microtime(true) * 1000),
    ]);
    // #endregion
    
    // First, check if payment_status column exists
    $check_column = $con->query("SHOW COLUMNS FROM `booking_order` LIKE 'payment_status'");
    $payment_status_exists = ($check_column && $check_column->num_rows > 0);
    
    // Update booking status to confirmed
    if ($payment_status_exists) {
        $query = "UPDATE booking_order SET booking_status = 'booked', payment_status = 'paid', confirmed_at = NOW() WHERE booking_id = ?";
    } else {
        $query = "UPDATE booking_order SET booking_status = 'booked', confirmed_at = NOW() WHERE booking_id = ?";
    }
    
    $stmt = $con->prepare($query);
    if (!$stmt) {
        error_log("Prepare failed: " . $con->error);
        throw new Exception("Database error: " . $con->error);
    }
    
    // Log the query being executed
    error_log("Executing query: " . $query . " with booking_id: " . $booking_id);
    
    $stmt->bind_param('i', $booking_id);
    $result = $stmt->execute();

    if (!$result) {
        error_log("Update failed: " . $stmt->error);
        sendJsonResponse('error', 'Failed to update booking status: ' . $stmt->error);
    }

    error_log("Booking $booking_id updated successfully");

    // Persist staff/admin note (optional)
    if($staff_note !== ''){
        $stmtNote = $con->prepare("UPDATE booking_details SET staff_note = ? WHERE booking_id = ? LIMIT 1");
        if($stmtNote){
            $stmtNote->bind_param('si', $staff_note, $booking_id);
            if(!$stmtNote->execute()){
                error_log("Failed to save staff_note for booking {$booking_id}: " . $stmtNote->error);
            }
            $stmtNote->close();
        } else {
            error_log("Failed to prepare staff_note update for booking {$booking_id}: " . $con->error);
        }
    }

    // #region agent log
    safe_debug_log([
        'sessionId' => 'debug-session',
        'runId' => 'confirm-booking',
        'location' => __FILE__ . ':booking-updated',
        'message' => 'Booking status updated',
        'data' => [
            'booking_id' => $booking_id,
            'payment_status_column_exists' => $payment_status_exists,
        ],
        'timestamp' => round(microtime(true) * 1000),
    ]);
    // #endregion

    // Always create notification for the user
    $details_res = select(
        "SELECT bd.room_name, bd.room_no FROM booking_details bd WHERE bd.booking_id = ? LIMIT 1",
        [$booking_id],
        'i'
    );
    $detail = mysqli_fetch_assoc($details_res);
    $room_name = $detail['room_name'] ?? 'your room';
    $room_no = $detail['room_no'] ?? '';
    $confirmed_at = select("SELECT confirmed_at, check_in, check_out FROM booking_order WHERE booking_id=? LIMIT 1", [$booking_id], 'i');
    $confirmed_row = mysqli_fetch_assoc($confirmed_at);
    $confirmed_at_ts = $confirmed_row && $confirmed_row['confirmed_at'] ? strtotime($confirmed_row['confirmed_at']) : time();
    $confirmed_at_str = date("M d, Y g:i A", $confirmed_at_ts);
    $checkin_str = $confirmed_row && $confirmed_row['check_in'] ? date("M d, Y", strtotime($confirmed_row['check_in'])) : '';
    $checkout_str = $confirmed_row && $confirmed_row['check_out'] ? date("M d, Y", strtotime($confirmed_row['check_out'])) : '';

    $room_detail_text = $room_no ? "$room_name (Room $room_no)" : $room_name;
    $notificationMessage = "Booking #$booking_id confirmed on $confirmed_at_str for $room_detail_text. Stay: $checkin_str to $checkout_str.";

    $notifCreated = createNotification($con, $booking_data['user_id'], $booking_id, $notificationMessage);
    if (!$notifCreated) {
        error_log("Failed to create notification for booking $booking_id");
    }

    // #region agent log
    safe_debug_log([
        'sessionId' => 'debug-session',
        'runId' => 'confirm-booking',
        'location' => __FILE__ . ':notification',
        'message' => 'Notification creation result',
        'data' => [
            'booking_id' => $booking_id,
            'user_id' => $booking_data['user_id'] ?? null,
            'notification_created' => $notifCreated,
        ],
        'timestamp' => round(microtime(true) * 1000),
    ]);
    // #endregion

    // Get booking + user details for email/SMS
    $res = select(
        "SELECT bo.*, u.email, u.name as user_name, u.phonenum,
                bd.room_name, bd.room_no
         FROM booking_order bo 
         JOIN user_cred u ON bo.user_id = u.id 
         LEFT JOIN booking_details bd ON bd.booking_id = bo.booking_id
         WHERE bo.booking_id = ?",
        [$booking_id],
        'i'
    );
    
    if (mysqli_num_rows($res) === 0) {
        sendJsonResponse('success', 'Booking confirmed but user details not found');
    }

    $booking = mysqli_fetch_assoc($res);

    // Best-effort external notifications (do not fail confirmation if sending fails)
    $notifyResult = [];
    try {
        $notifyResult = notify_booking_confirmed($booking);
    } catch (Exception $e) {
        error_log("notify_booking_confirmed failed for booking {$booking_id}: " . $e->getMessage());
        $notifyResult = ['email_sent' => false, 'sms_sent' => false];
    }
    
    $response = [
        'status' => 'success',
        'message' => 'Booking confirmed successfully!',
        'booking_id' => $booking_id,
        'new_status' => 'confirmed',
        'notify' => $notifyResult
    ];
    
    error_log("Booking #$booking_id confirmed successfully");

    // #region agent log
    safe_debug_log([
        'sessionId' => 'debug-session',
        'runId' => 'confirm-booking',
        'location' => __FILE__ . ':success',
        'message' => 'Confirm booking success response',
        'data' => [
            'booking_id' => $booking_id,
            'new_status' => 'confirmed',
        ],
        'timestamp' => round(microtime(true) * 1000),
    ]);
    // #endregion

    sendJsonResponse('success', 'Booking confirmed successfully!', $response);
} catch (Exception $e) {
    $errorMessage = 'PHP Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine();
    error_log($errorMessage);
    error_log('Stack trace: ' . $e->getTraceAsString());
    
    // Check for database connection errors
    if (isset($con) && $con->connect_error) {
        error_log('Database connection error: ' . $con->connect_error);
    }
    
    // Send detailed error in development, generic in production
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        $errorDetails = (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || $_SERVER['SERVER_ADDR'] === '127.0.0.1')
            ? $errorMessage
            : 'An error occurred while processing your request';
    } else {
        $errorDetails = 'An error occurred while processing your request';
    }
    
    sendJsonResponse('error', $errorDetails);
}
?>
