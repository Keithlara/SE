<?php
/**
 * SMTP mail sender - uses PHPMailer (Gmail SMTP) as primary.
 * Falls back to raw STARTTLS socket if PHPMailer is unavailable.
 *
 * Constants consumed (from admin/inc/email_config.php):
 *   SMTP_HOST, SMTP_PORT, SMTP_USERNAME, SMTP_PASSWORD,
 *   SMTP_FROM_EMAIL, SMTP_FROM_NAME
 */

function smtp_read_response($fp)
{
    $data = '';
    while (!feof($fp)) {
        $line = fgets($fp, 515);
        if ($line === false) {
            break;
        }
        $data .= $line;
        if (preg_match('/^\d{3} /', $line)) {
            break;
        }
    }
    return $data;
}

function smtp_expect($fp, $expectedCodes)
{
    $resp = smtp_read_response($fp);
    $code = (int)substr($resp, 0, 3);
    $expected = (array)$expectedCodes;
    if (!in_array($code, $expected, true)) {
        throw new Exception("SMTP unexpected response: {$resp}");
    }
    return $resp;
}

function smtp_send_cmd($fp, $cmd, $expectedCodes)
{
    fwrite($fp, $cmd . "\r\n");
    return smtp_expect($fp, $expectedCodes);
}

/**
 * Primary entry point used by booking_notifications.php and login_register.php.
 * Delegates to PHPMailer (send_email.php) when available; falls back to raw socket.
 */
function send_email_smtp_basic($toEmail, $toName, $subject, $htmlBody)
{
    // Try PHPMailer first (more reliable, handles OAuth, proper TLS, etc.)
    $sendEmailPhp = __DIR__ . '/../send_email.php';
    if (file_exists($sendEmailPhp)) {
        if (!function_exists('sendEmail')) {
            require_once $sendEmailPhp;
        }
        if (function_exists('sendEmail')) {
            $result = sendEmail((string)$toEmail, (string)$subject, (string)$htmlBody);
            if ($result) {
                return true;
            }
            // Fall through to raw SMTP on failure
        }
    }

    // --- Raw STARTTLS fallback ---
    if (!defined('SMTP_HOST') || !defined('SMTP_PORT') || !defined('SMTP_USERNAME') || !defined('SMTP_PASSWORD')) {
        error_log('SMTP constants not defined; skipping SMTP email send');
        return false;
    }

    $host     = (string)SMTP_HOST;
    $port     = (int)SMTP_PORT;
    $username = trim((string)SMTP_USERNAME);
    $password = trim((string)SMTP_PASSWORD);
    $fromEmail = defined('SMTP_FROM_EMAIL') ? (string)SMTP_FROM_EMAIL : $username;
    $fromName  = defined('SMTP_FROM_NAME')  ? (string)SMTP_FROM_NAME  : 'Hotel';

    if ($host === '' || $port <= 0 || $username === '' || $password === '') {
        return false;
    }

    if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $toName      = trim((string)$toName);
    $safeSubject = trim((string)$subject);

    $context = stream_context_create([
        'ssl' => [
            'verify_peer'      => true,
            'verify_peer_name' => true,
            'allow_self_signed' => false,
        ],
    ]);

    $fp = @stream_socket_client(
        "tcp://{$host}:{$port}",
        $errno,
        $errstr,
        20,
        STREAM_CLIENT_CONNECT,
        $context
    );

    if (!$fp) {
        error_log("SMTP connect failed: {$errstr} ({$errno})");
        return false;
    }

    stream_set_timeout($fp, 20);

    try {
        smtp_expect($fp, [220]);

        $helo = gethostname() ?: 'localhost';
        smtp_send_cmd($fp, "EHLO {$helo}", [250]);

        smtp_send_cmd($fp, "STARTTLS", [220]);
        $cryptoOk = @stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        if ($cryptoOk !== true) {
            throw new Exception('SMTP TLS negotiation failed');
        }

        smtp_send_cmd($fp, "EHLO {$helo}", [250]);
        smtp_send_cmd($fp, "AUTH LOGIN", [334]);
        smtp_send_cmd($fp, base64_encode($username), [334]);
        smtp_send_cmd($fp, base64_encode($password), [235]);

        smtp_send_cmd($fp, "MAIL FROM:<{$fromEmail}>", [250]);
        smtp_send_cmd($fp, "RCPT TO:<{$toEmail}>", [250, 251]);
        smtp_send_cmd($fp, "DATA", [354]);

        $boundary = 'b' . bin2hex(random_bytes(8));
        $headers  = [];
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = "From: " . ($fromName !== '' ? "\"{$fromName}\" <{$fromEmail}>" : $fromEmail);
        $headers[] = "To: "   . ($toName   !== '' ? "\"{$toName}\" <{$toEmail}>"     : $toEmail);
        $headers[] = "Subject: " . $safeSubject;
        $headers[] = "Content-Type: multipart/alternative; boundary=\"{$boundary}\"";

        $body   = [];
        $body[] = "--{$boundary}";
        $body[] = "Content-Type: text/plain; charset=UTF-8";
        $body[] = "Content-Transfer-Encoding: 7bit";
        $body[] = "";
        $body[] = strip_tags($htmlBody);
        $body[] = "";
        $body[] = "--{$boundary}";
        $body[] = "Content-Type: text/html; charset=UTF-8";
        $body[] = "Content-Transfer-Encoding: 7bit";
        $body[] = "";
        $body[] = $htmlBody;
        $body[] = "";
        $body[] = "--{$boundary}--";
        $body[] = "";

        $data = implode("\r\n", array_merge($headers, [''], $body));
        $data = preg_replace('/\r\n\./', "\r\n..", $data);

        fwrite($fp, $data . "\r\n.\r\n");
        smtp_expect($fp, [250]);

        smtp_send_cmd($fp, "QUIT", [221]);
        fclose($fp);
        return true;
    } catch (Exception $e) {
        error_log('SMTP send failed: ' . $e->getMessage());
        @fwrite($fp, "QUIT\r\n");
        @fclose($fp);
        return false;
    }
}
