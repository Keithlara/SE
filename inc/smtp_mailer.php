<?php
/**
 * SMTP mail sender - uses PHPMailer as primary.
 * Falls back to raw SMTP when PHPMailer is unavailable.
 *
 * Constants consumed (from admin/inc/email_config.php):
 *   SMTP_HOST, SMTP_PORT, SMTP_USERNAME, SMTP_PASSWORD,
 *   SMTP_FROM_EMAIL, SMTP_FROM_NAME
 */

$GLOBALS['smtp_last_error'] = null;

function smtp_set_last_error($message)
{
    $GLOBALS['smtp_last_error'] = (string)$message;
    error_log('SMTP mailer: ' . $GLOBALS['smtp_last_error']);
}

function smtp_get_last_error()
{
    return $GLOBALS['smtp_last_error'] ?? '';
}

function smtp_is_configured()
{
    return defined('SMTP_HOST')
        && defined('SMTP_PORT')
        && defined('SMTP_USERNAME')
        && defined('SMTP_PASSWORD')
        && trim((string)SMTP_HOST) !== ''
        && (int)SMTP_PORT > 0
        && trim((string)SMTP_USERNAME) !== ''
        && trim((string)SMTP_PASSWORD) !== '';
}

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
    $GLOBALS['smtp_last_error'] = null;

    if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
        smtp_set_last_error("Invalid recipient email: {$toEmail}");
        if (function_exists('logEmailDispatch')) {
            @logEmailDispatch([
                'recipient_email' => $toEmail,
                'recipient_name' => $toName,
                'subject' => $subject,
                'status' => 'failed',
                'error_message' => smtp_get_last_error(),
                'triggered_by' => 'system',
            ]);
        }
        return false;
    }

    if (!smtp_is_configured()) {
        smtp_set_last_error('SMTP is not configured. Set SMTP_HOST, SMTP_PORT, SMTP_USER, and SMTP_PASS.');
        if (function_exists('logEmailDispatch')) {
            @logEmailDispatch([
                'recipient_email' => $toEmail,
                'recipient_name' => $toName,
                'subject' => $subject,
                'status' => 'failed',
                'error_message' => smtp_get_last_error(),
                'triggered_by' => 'system',
            ]);
        }
        return false;
    }

    // Try PHPMailer first (more reliable, handles OAuth, proper TLS, etc.)
    $sendEmailPhp = __DIR__ . '/../send_email.php';
    if (file_exists($sendEmailPhp)) {
        if (!function_exists('sendEmail')) {
            require_once $sendEmailPhp;
        }
        if (function_exists('sendEmail')) {
            $result = sendEmail((string)$toEmail, (string)$subject, (string)$htmlBody);
            if ($result) {
                if (function_exists('logEmailDispatch')) {
                    @logEmailDispatch([
                        'recipient_email' => $toEmail,
                        'recipient_name' => $toName,
                        'subject' => $subject,
                        'status' => 'sent',
                        'triggered_by' => (isset($_SESSION['adminLogin']) && $_SESSION['adminLogin'] == true) ? 'admin_panel' : 'guest_flow',
                    ]);
                }
                return true;
            }
            $phpMailerError = function_exists('sendEmailLastError') ? sendEmailLastError() : '';
            if ($phpMailerError !== '') {
                smtp_set_last_error($phpMailerError);
            }
            // Fall through to raw SMTP on failure
        }
    }

    // --- Raw STARTTLS fallback ---
    $host     = (string)SMTP_HOST;
    $port     = (int)SMTP_PORT;
    $username = trim((string)SMTP_USERNAME);
    $password = trim((string)SMTP_PASSWORD);
    $fromEmail = defined('SMTP_FROM_EMAIL') ? (string)SMTP_FROM_EMAIL : $username;
    $fromName  = defined('SMTP_FROM_NAME')  ? (string)SMTP_FROM_NAME  : 'Hotel';

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
        smtp_set_last_error("SMTP connect failed: {$errstr} ({$errno})");
        return false;
    }

    stream_set_timeout($fp, 20);

    try {
        smtp_expect($fp, [220]);

        $helo = gethostname() ?: 'localhost';
        $secureMode = ($port === 465) ? 'ssl' : 'tls';

        if ($secureMode === 'ssl') {
            $sslContext = stream_context_create([
                'ssl' => [
                    'verify_peer' => true,
                    'verify_peer_name' => true,
                    'allow_self_signed' => false,
                ],
            ]);
            @fclose($fp);
            $fp = @stream_socket_client(
                "ssl://{$host}:{$port}",
                $errno,
                $errstr,
                20,
                STREAM_CLIENT_CONNECT,
                $sslContext
            );
            if (!$fp) {
                throw new Exception("SMTP SSL connect failed: {$errstr} ({$errno})");
            }
            stream_set_timeout($fp, 20);
            smtp_expect($fp, [220]);
        }

        smtp_send_cmd($fp, "EHLO {$helo}", [250]);
        if ($secureMode === 'tls') {
            smtp_send_cmd($fp, "STARTTLS", [220]);
            $cryptoOk = @stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            if ($cryptoOk !== true) {
                throw new Exception('SMTP TLS negotiation failed');
            }
            smtp_send_cmd($fp, "EHLO {$helo}", [250]);
        }

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
        if (function_exists('logEmailDispatch')) {
            @logEmailDispatch([
                'recipient_email' => $toEmail,
                'recipient_name' => $toName,
                'subject' => $subject,
                'status' => 'sent',
                'triggered_by' => (isset($_SESSION['adminLogin']) && $_SESSION['adminLogin'] == true) ? 'admin_panel' : 'guest_flow',
            ]);
        }
        return true;
    } catch (Exception $e) {
        smtp_set_last_error('SMTP send failed: ' . $e->getMessage());
        if (function_exists('logEmailDispatch')) {
            @logEmailDispatch([
                'recipient_email' => $toEmail,
                'recipient_name' => $toName,
                'subject' => $subject,
                'status' => 'failed',
                'error_message' => smtp_get_last_error(),
                'triggered_by' => (isset($_SESSION['adminLogin']) && $_SESSION['adminLogin'] == true) ? 'admin_panel' : 'guest_flow',
            ]);
        }
        @fwrite($fp, "QUIT\r\n");
        @fclose($fp);
        return false;
    }
}
