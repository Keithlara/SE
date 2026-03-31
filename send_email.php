<?php
/**
 * Reusable email sender using PHPMailer + Gmail SMTP.
 * Usage: sendEmail($to, $subject, $body)
 *
 * Configure SMTP credentials via environment variables:
 *   SMTP_USER  – your Gmail address
 *   SMTP_PASS  – your Gmail App Password (16 characters, no spaces)
 *
 * Returns true on success, false on failure (never throws; logs errors instead).
 */

require_once __DIR__ . '/inc/PHPMailer/PHPMailer-master/src/Exception.php';
require_once __DIR__ . '/inc/PHPMailer/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/inc/PHPMailer/PHPMailer-master/src/SMTP.php';
require_once __DIR__ . '/admin/inc/email_config.php';

if (!isset($GLOBALS['send_email_last_error'])) {
    $GLOBALS['send_email_last_error'] = null;
}

if (!function_exists('sendEmailLastError')) {
    function sendEmailLastError(): string
    {
        return (string)($GLOBALS['send_email_last_error'] ?? '');
    }
}

if (!function_exists('sendEmail')) {
    /**
     * Send an HTML email via Gmail SMTP using PHPMailer.
     *
     * @param  string $to       Recipient email address
     * @param  string $subject  Email subject
     * @param  string $body     HTML body content
     * @return bool             true on success, false on failure
     */
    function sendEmail(string $to, string $subject, string $body): bool
    {
        $GLOBALS['send_email_last_error'] = null;

        if (!defined('SMTP_USERNAME') || trim(SMTP_USERNAME) === '') {
            $GLOBALS['send_email_last_error'] = 'SMTP username is not configured.';
            error_log('sendEmail: SMTP_USER not configured (set SMTP_USER env var).');
            return false;
        }
        if (!defined('SMTP_PASSWORD') || trim(SMTP_PASSWORD) === '') {
            $GLOBALS['send_email_last_error'] = 'SMTP password is not configured.';
            error_log('sendEmail: SMTP_PASS not configured (set SMTP_PASS env var).');
            return false;
        }
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $GLOBALS['send_email_last_error'] = 'Recipient email address is invalid.';
            error_log("sendEmail: Invalid recipient address: $to");
            return false;
        }

        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = defined('SMTP_HOST') ? SMTP_HOST : 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USERNAME;
            $mail->Password   = SMTP_PASSWORD;
            $mail->Port       = defined('SMTP_PORT') ? (int) SMTP_PORT : 587;
            $mail->SMTPSecure = ($mail->Port === 465)
                ? PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS
                : PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->CharSet    = 'UTF-8';

            $fromEmail = defined('SMTP_FROM_EMAIL') && SMTP_FROM_EMAIL !== '' ? SMTP_FROM_EMAIL : SMTP_USERNAME;
            $fromName  = defined('SMTP_FROM_NAME')  && SMTP_FROM_NAME  !== '' ? SMTP_FROM_NAME  : (defined('SITE_NAME') ? SITE_NAME : 'Travelers Place');

            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = strip_tags($body);

            $mail->send();
            return true;
        } catch (PHPMailer\PHPMailer\Exception $e) {
            $GLOBALS['send_email_last_error'] = $mail->ErrorInfo ?: $e->getMessage();
            error_log('sendEmail PHPMailer error: ' . $mail->ErrorInfo);
            return false;
        } catch (\Throwable $e) {
            $GLOBALS['send_email_last_error'] = $e->getMessage();
            error_log('sendEmail unexpected error: ' . $e->getMessage());
            return false;
        }
    }
}
