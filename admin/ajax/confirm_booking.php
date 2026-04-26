<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
date_default_timezone_set('Asia/Manila');

ob_start();
header('Content-Type: application/json');

require(dirname(__DIR__) . '/inc/db_config.php');
require(dirname(__DIR__) . '/inc/essentials.php');

function finish_json_payload(string $payload): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        @session_write_close();
    }

    ignore_user_abort(true);
    @set_time_limit(0);
    @ini_set('zlib.output_compression', '0');
    @ini_set('output_buffering', 'off');
    @ini_set('implicit_flush', '1');

    if (function_exists('apache_setenv')) {
        @apache_setenv('no-gzip', '1');
    }

    while (ob_get_level() > 0) {
        @ob_end_clean();
    }

    if (function_exists('fastcgi_finish_request')) {
        echo $payload;
        fastcgi_finish_request();
        return;
    }

    header('X-Accel-Buffering: no');
    header('Content-Encoding: none');
    header('Connection: close');
    header('Content-Length: ' . strlen($payload));
    echo $payload;
    @ob_flush();
    @flush();
}

function send_json_response(string $status, string $message, array $extra = [], bool $finishEarly = false): void
{
    $response = array_merge([
        'status' => $status,
        'message' => $message,
    ], $extra);

    $payload = json_encode($response);

    if ($finishEarly) {
        finish_json_payload($payload);
        return;
    }

    if (ob_get_length()) {
        @ob_clean();
    }

    echo $payload;
    exit;
}

adminLogin();

if (!isset($_POST['confirm_booking'], $_POST['booking_id'])) {
    send_json_response('error', 'Invalid request.');
}

$booking_id = (int)($_POST['booking_id'] ?? 0);
if ($booking_id <= 0) {
    send_json_response('error', 'Invalid booking selected.');
}

$staff_note = trim((string)($_POST['staff_note'] ?? ''));
if ($staff_note !== '') {
    if (function_exists('mb_substr')) {
        $staff_note = mb_substr($staff_note, 0, 500);
    } else {
        $staff_note = substr($staff_note, 0, 500);
    }
}

$booking_res = select(
    "SELECT `booking_id`
     FROM `booking_order`
     WHERE `booking_id` = ?
     LIMIT 1",
    [$booking_id],
    'i'
);

if (!$booking_res || mysqli_num_rows($booking_res) === 0) {
    send_json_response('error', 'Booking not found.');
}
mysqli_begin_transaction($con);

try {
    update(
        "UPDATE `booking_order`
         SET `booking_status`='booked',
             `payment_status`='paid',
             `confirmed_at`=COALESCE(`confirmed_at`, NOW())
         WHERE `booking_id`=?",
        [$booking_id],
        'i'
    );

    if ($staff_note !== '') {
        update(
            "UPDATE `booking_details`
             SET `staff_note`=?
             WHERE `booking_id`=?
             LIMIT 1",
            [$staff_note, $booking_id],
            'si'
        );
    }

    mysqli_commit($con);
} catch (Throwable $e) {
    mysqli_rollback($con);
    error_log("confirm_booking update failed for booking {$booking_id}: " . $e->getMessage());
    send_json_response('error', 'Failed to confirm booking.');
}

send_json_response('success', 'Booking confirmed successfully!', [
    'booking_id' => $booking_id,
    'new_status' => 'booked',
], true);

try {
    $booking_res = select(
        "SELECT bo.`booking_id`, bo.`user_id`, bo.`order_id`, bo.`booking_status`, bo.`payment_status`,
                bo.`check_in`, bo.`check_out`, bo.`confirmed_at`, bo.`total_amt`, bo.`downpayment`,
                bo.`balance_due`, bo.`trans_amt`,
                u.`email`, u.`name` AS `user_name`, u.`phonenum`,
                bd.`room_name`, bd.`room_no`
         FROM `booking_order` bo
         INNER JOIN `user_cred` u ON bo.`user_id` = u.`id`
         LEFT JOIN `booking_details` bd ON bd.`booking_id` = bo.`booking_id`
         WHERE bo.`booking_id` = ?
         LIMIT 1",
        [$booking_id],
        'i'
    );

    if ($booking_res && mysqli_num_rows($booking_res) > 0) {
        $booking = mysqli_fetch_assoc($booking_res);
        $confirmedAtRaw = !empty($booking['confirmed_at']) ? (string)$booking['confirmed_at'] : date('Y-m-d H:i:s');
        $confirmedAtTs = strtotime($confirmedAtRaw) ?: time();
        $confirmedAtStr = date('M d, Y g:i A', $confirmedAtTs);
        $checkInStr = !empty($booking['check_in']) ? date('M d, Y', strtotime((string)$booking['check_in'])) : '';
        $checkOutStr = !empty($booking['check_out']) ? date('M d, Y', strtotime((string)$booking['check_out'])) : '';
        $roomName = $booking['room_name'] ?? 'your room';
        $roomNo = trim((string)($booking['room_no'] ?? ''));
        $roomDetailText = $roomNo !== '' ? "{$roomName} (Room {$roomNo})" : $roomName;

        $notificationMessage = "Booking #{$booking_id} confirmed on {$confirmedAtStr} for {$roomDetailText}. Stay: {$checkInStr} to {$checkOutStr}.";
        if ($staff_note !== '') {
            $notificationMessage .= " | Admin reply: {$staff_note}";
        }

        createNotification($con, (int)$booking['user_id'], $booking_id, $notificationMessage);
        createBookingHistoryEntry(
            $booking_id,
            'booking_confirmed',
            'Booking confirmed',
            $staff_note !== ''
                ? 'Admin confirmed the booking and left a note: ' . $staff_note
                : 'Admin confirmed the booking.',
            [],
            $_SESSION['adminRole'] ?? 'admin',
            (int)($_SESSION['adminId'] ?? 0),
            (string)($_SESSION['adminName'] ?? 'Admin')
        );

        require_once(dirname(__DIR__, 2) . '/inc/booking_notifications.php');
        notify_booking_confirmed($booking);
    }
} catch (Throwable $e) {
    error_log("confirm_booking follow-up failed for booking {$booking_id}: " . $e->getMessage());
}

exit;
