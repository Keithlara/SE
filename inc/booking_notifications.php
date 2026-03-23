<?php
require_once(__DIR__ . '/../admin/inc/essentials.php');

// SMTP (free/dev friendly)
@require_once(__DIR__ . '/../admin/inc/email_config.php');
@require_once(__DIR__ . '/smtp_mailer.php');

// SendGrid (optional)
@require_once(__DIR__ . '/sendgrid/sendgrid-php.php');

// Optional SMS config (Twilio)
@require_once(__DIR__ . '/sms_config.php');

function normalize_phone_e164_guess($phone)
{
    $raw = trim((string)$phone);
    if ($raw === '') {
        return '';
    }

    $hasPlus = str_starts_with($raw, '+');
    $digits = preg_replace('/[^0-9]/', '', $raw);
    if ($digits === '') {
        return '';
    }

    // If user already provided '+', keep it.
    if ($hasPlus) {
        return '+' . $digits;
    }

    // Heuristic for PH numbers: 09xxxxxxxxx (11 digits) -> +639xxxxxxxxx
    if (strlen($digits) === 11 && str_starts_with($digits, '09')) {
        return '+63' . substr($digits, 1);
    }

    // If it looks like a country-code number already.
    if (str_starts_with($digits, '63') && strlen($digits) >= 12) {
        return '+' . $digits;
    }

    // Fallback: return digits as-is.
    return $digits;
}

function send_email_sendgrid($toEmail, $toName, $subject, $html)
{
    // Skip if config looks like placeholders.
    if (!defined('SENDGRID_API_KEY') || !defined('SENDGRID_EMAIL') || !defined('SENDGRID_NAME')) {
        error_log('SendGrid constants not defined; skipping email send');
        return false;
    }
    if (trim(SENDGRID_API_KEY) === '' || stripos(SENDGRID_API_KEY, 'PASTE YOUR API KEY') !== false) {
        error_log('SendGrid API key not configured; skipping email send');
        return false;
    }
    if (trim(SENDGRID_EMAIL) === '' || stripos(SENDGRID_EMAIL, 'PUT YOU EMAIL') !== false) {
        error_log('SendGrid sender email not configured; skipping email send');
        return false;
    }

    if (!class_exists('\\SendGrid\\Mail\\Mail') || !class_exists('\\SendGrid')) {
        error_log('SendGrid library not available; skipping email send');
        return false;
    }

    try {
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom(SENDGRID_EMAIL, SENDGRID_NAME);
        $email->setSubject($subject);
        $email->addTo($toEmail, $toName ?: null);
        $email->addContent('text/html', $html);

        $sendgrid = new \SendGrid(SENDGRID_API_KEY);
        $sendgrid->send($email);
        return true;
    } catch (Exception $e) {
        error_log('SendGrid send failed: ' . $e->getMessage());
        return false;
    }
}

function send_email_smtp($toEmail, $toName, $subject, $html)
{
    if (!function_exists('send_email_smtp_basic')) {
        return false;
    }
    return send_email_smtp_basic($toEmail, $toName, $subject, $html);
}

function send_sms_twilio($toPhone, $message)
{
    if (!defined('TWILIO_ACCOUNT_SID') || !defined('TWILIO_AUTH_TOKEN') || !defined('TWILIO_FROM_NUMBER')) {
        error_log('Twilio constants not defined; skipping SMS send');
        return false;
    }

    $sid = trim((string)TWILIO_ACCOUNT_SID);
    $token = trim((string)TWILIO_AUTH_TOKEN);
    $from = trim((string)TWILIO_FROM_NUMBER);

    if ($sid === '' || $token === '' || $from === '') {
        // Not configured; treat as "disabled", not an error.
        return false;
    }

    $to = normalize_phone_e164_guess($toPhone);
    if ($to === '' || trim($message) === '') {
        return false;
    }

    if (!function_exists('curl_init')) {
        error_log('cURL extension not available; skipping SMS send');
        return false;
    }

    $url = "https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json";
    $post = http_build_query([
        'From' => $from,
        'To' => $to,
        'Body' => $message,
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, $sid . ':' . $token);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

    $resp = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);

    if ($resp === false) {
        error_log('Twilio SMS failed: ' . $err);
        return false;
    }

    if ($httpCode < 200 || $httpCode >= 300) {
        error_log("Twilio SMS HTTP {$httpCode}: {$resp}");
        return false;
    }

    return true;
}

/**
 * Notify customer that booking is confirmed.
 * Expects keys: booking_id, user_name, email, phonenum, room_name, room_no, check_in, check_out, confirmed_at
 */
function notify_booking_confirmed($booking)
{
    $bookingId = $booking['booking_id'] ?? '';
    $name = $booking['user_name'] ?? '';
    $email = $booking['email'] ?? '';
    $phone = $booking['phonenum'] ?? '';

    $roomName = $booking['room_name'] ?? 'your room';
    $roomNo = $booking['room_no'] ?? '';
    $roomText = $roomNo !== '' ? "{$roomName} (Room {$roomNo})" : $roomName;

    $checkIn = !empty($booking['check_in']) ? date('F j, Y', strtotime($booking['check_in'])) : '';
    $checkOut = !empty($booking['check_out']) ? date('F j, Y', strtotime($booking['check_out'])) : '';
    $confirmedAt = !empty($booking['confirmed_at']) ? date('F j, Y g:i A', strtotime($booking['confirmed_at'])) : date('F j, Y g:i A');

    $subject = "Booking Confirmed #{$bookingId}";
    $html = "
        <h2>Booking Confirmed</h2>
        <p>Dear {$name},</p>
        <p>Your booking has been confirmed.</p>
        <h3>Booking Details</h3>
        <p><strong>Booking ID:</strong> #{$bookingId}</p>
        <p><strong>Room:</strong> {$roomText}</p>
        <p><strong>Check-in:</strong> {$checkIn}</p>
        <p><strong>Check-out:</strong> {$checkOut}</p>
        <p><strong>Confirmed at:</strong> {$confirmedAt}</p>
        <p>Thank you for choosing our service!</p>
    ";

    $smsMessage = "Booking #{$bookingId} CONFIRMED. Room: {$roomText}. Stay: {$checkIn} to {$checkOut}.";

    $emailSent = false;
    $smsSent = false;

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Prefer free SMTP for dev/defense; fallback to SendGrid if SMTP not configured.
        $emailSent = send_email_smtp($email, $name, $subject, $html);
        if (!$emailSent) {
            $emailSent = send_email_sendgrid($email, $name, $subject, $html);
        }
    }

    // SMS: currently Twilio (if configured)
    if (defined('SMS_PROVIDER') && SMS_PROVIDER === 'twilio') {
        $smsSent = send_sms_twilio($phone, $smsMessage);
    }

    return [
        'email_sent' => $emailSent,
        'sms_sent' => $smsSent,
    ];
}

