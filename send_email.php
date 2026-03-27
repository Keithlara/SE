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
        if (!defined('SMTP_USERNAME') || trim(SMTP_USERNAME) === '') {
            error_log('sendEmail: SMTP_USER not configured (set SMTP_USER env var).');
            return false;
        }
        if (!defined('SMTP_PASSWORD') || trim(SMTP_PASSWORD) === '') {
            error_log('sendEmail: SMTP_PASS not configured (set SMTP_PASS env var).');
            return false;
        }
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
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
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = defined('SMTP_PORT') ? (int) SMTP_PORT : 587;

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
            error_log('sendEmail PHPMailer error: ' . $mail->ErrorInfo);
            return false;
        } catch (\Throwable $e) {
            error_log('sendEmail unexpected error: ' . $e->getMessage());
            return false;
        }
    }
}
