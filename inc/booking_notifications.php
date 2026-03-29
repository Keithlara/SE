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

    if ($hasPlus) {
        return '+' . $digits;
    }

    // Heuristic for PH numbers: 09xxxxxxxxx (11 digits) -> +639xxxxxxxxx
    if (strlen($digits) === 11 && str_starts_with($digits, '09')) {
        return '+63' . substr($digits, 1);
    }

    if (str_starts_with($digits, '63') && strlen($digits) >= 12) {
        return '+' . $digits;
    }

    return $digits;
}

function send_email_sendgrid($toEmail, $toName, $subject, $html)
{
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
 * Expects keys: booking_id, order_id, user_name, email, phonenum, room_name, room_no,
 *               check_in, check_out, confirmed_at,
 *               total_amt, downpayment, balance_due, trans_amt (downpayment paid)
 */
function notify_booking_confirmed($booking)
{
    $bookingId = $booking['booking_id'] ?? '';
    $orderId   = $booking['order_id']   ?? '';
    $name      = $booking['user_name']  ?? '';
    $email     = $booking['email']      ?? '';
    $phone     = $booking['phonenum']   ?? '';

    $roomName  = $booking['room_name'] ?? 'your room';
    $roomNo    = $booking['room_no']   ?? '';
    $roomText  = $roomNo !== '' ? "{$roomName} (Room {$roomNo})" : $roomName;

    $checkIn     = !empty($booking['check_in'])  ? date('F j, Y', strtotime($booking['check_in']))  : '';
    $checkOut    = !empty($booking['check_out']) ? date('F j, Y', strtotime($booking['check_out'])) : '';
    $confirmedAt = !empty($booking['confirmed_at'])
        ? date('F j, Y g:i A', strtotime($booking['confirmed_at']))
        : date('F j, Y g:i A');

    // Billing figures — gracefully handle missing columns
    $totalAmt    = isset($booking['total_amt'])   ? (float)$booking['total_amt']   : (float)($booking['trans_amt'] ?? 0);
    $downpayment = isset($booking['downpayment']) ? (float)$booking['downpayment'] : (float)($booking['trans_amt'] ?? 0);
    $balanceDue  = isset($booking['balance_due']) ? (float)$booking['balance_due'] : max(0, $totalAmt - $downpayment);

    $refId       = $orderId !== '' ? $orderId : "#{$bookingId}";

    $siteName = defined('SITE_NAME') ? SITE_NAME : 'Travelers Place';
    $subject  = "Booking Confirmed {$refId} – {$siteName}";

    $billingRows = "
      <tr style='background:#f9fafb'>
        <td style='padding:10px 14px;color:#6b7280;font-size:13px;border-bottom:1px solid #e5e7eb'>Total Stay Amount</td>
        <td style='padding:10px 14px;color:#111827;font-weight:bold;border-bottom:1px solid #e5e7eb'>&#8369;" . number_format($totalAmt, 2) . "</td>
      </tr>
      <tr>
        <td style='padding:10px 14px;color:#b8860b;font-weight:bold;border-bottom:1px solid #f0c040'>Downpayment Paid (50%)</td>
        <td style='padding:10px 14px;color:#b8860b;font-weight:bold;border-bottom:1px solid #f0c040'>&#8369;" . number_format($downpayment, 2) . "</td>
      </tr>
      <tr style='background:#fff8e1'>
        <td style='padding:10px 14px;color:#374151;font-size:13px'>Remaining Balance (due at check-in)</td>
        <td style='padding:10px 14px;color:#374151;font-size:13px'>&#8369;" . number_format($balanceDue, 2) . "</td>
      </tr>
    ";

    $html = "
      <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;background:#fff;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden'>
        <div style='background:#1a1a2e;padding:28px 32px;text-align:center'>
          <h1 style='color:#c8a951;margin:0 0 4px;font-size:24px'>{$siteName}</h1>
          <p style='color:#d1d5db;margin:0;font-size:13px'>Comfort. Convenience. Relaxation.</p>
        </div>
        <div style='padding:32px'>
          <h2 style='color:#1a1a2e;margin:0 0 8px'>&#10003; Booking Confirmed!</h2>
          <p style='color:#374151;margin:0 0 24px'>Dear <strong>{$name}</strong>, your reservation has been approved. We look forward to welcoming you!</p>

          <h3 style='color:#1a1a2e;font-size:15px;margin:0 0 8px'>Reservation Details</h3>
          <table style='width:100%;border-collapse:collapse;margin-bottom:24px'>
            <tr style='background:#f9fafb'>
              <td style='padding:10px 14px;color:#6b7280;font-size:13px;border-bottom:1px solid #e5e7eb'>Booking Reference</td>
              <td style='padding:10px 14px;color:#111827;font-weight:bold;border-bottom:1px solid #e5e7eb'>{$refId}</td>
            </tr>
            <tr>
              <td style='padding:10px 14px;color:#6b7280;font-size:13px;border-bottom:1px solid #e5e7eb'>Room</td>
              <td style='padding:10px 14px;color:#111827;border-bottom:1px solid #e5e7eb'>{$roomText}</td>
            </tr>
            <tr style='background:#f9fafb'>
              <td style='padding:10px 14px;color:#6b7280;font-size:13px;border-bottom:1px solid #e5e7eb'>Check-in</td>
              <td style='padding:10px 14px;color:#111827;border-bottom:1px solid #e5e7eb'>{$checkIn}</td>
            </tr>
            <tr>
              <td style='padding:10px 14px;color:#6b7280;font-size:13px;border-bottom:1px solid #e5e7eb'>Check-out</td>
              <td style='padding:10px 14px;color:#111827;border-bottom:1px solid #e5e7eb'>{$checkOut}</td>
            </tr>
            <tr style='background:#f9fafb'>
              <td style='padding:10px 14px;color:#6b7280;font-size:13px;border-bottom:1px solid #e5e7eb'>Confirmed at</td>
              <td style='padding:10px 14px;color:#111827;border-bottom:1px solid #e5e7eb'>{$confirmedAt}</td>
            </tr>
          </table>

          <h3 style='color:#1a1a2e;font-size:15px;margin:0 0 8px'>Billing Summary</h3>
          <table style='width:100%;border-collapse:collapse;margin-bottom:24px'>
            {$billingRows}
          </table>

          <div style='background:#f0f9f4;border:1px solid #a7f3d0;border-radius:8px;padding:14px;margin-bottom:16px'>
            <p style='margin:0;color:#065f46;font-size:14px;'>Please bring the remaining balance of <strong>&#8369;" . number_format($balanceDue, 2) . "</strong> upon check-in.</p>
          </div>

          <p style='color:#374151;font-size:14px'>If you have any questions, please contact us. We're happy to help!</p>
          <p style='color:#374151;font-size:14px;margin-top:24px'>See you soon,<br><strong>{$siteName} Team</strong></p>
        </div>
        <div style='background:#f3f4f6;padding:16px 32px;text-align:center'>
          <p style='color:#9ca3af;font-size:12px;margin:0'>This is an automated message from {$siteName}. Please do not reply to this email.</p>
        </div>
      </div>
    ";

    $smsMessage = "Booking {$refId} CONFIRMED. Room: {$roomText}. Stay: {$checkIn} to {$checkOut}. Balance due at check-in: PHP " . number_format($balanceDue, 2) . ".";

    $emailSent = false;
    $smsSent   = false;

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailSent = send_email_smtp($email, $name, $subject, $html);
        if (!$emailSent) {
            $emailSent = send_email_sendgrid($email, $name, $subject, $html);
        }
    }

    if (defined('SMS_PROVIDER') && SMS_PROVIDER === 'twilio') {
        $smsSent = send_sms_twilio($phone, $smsMessage);
    }

    return [
        'email_sent' => $emailSent,
        'sms_sent'   => $smsSent,
    ];
}
