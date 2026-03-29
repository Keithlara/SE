<?php
  if (session_status() === PHP_SESSION_NONE) { session_start(); }
  require('inc/essentials.php');
  require('inc/db_config.php');
  require('inc/admin_users_table.php');
  require_once('inc/email_config.php');
  require_once('../inc/smtp_mailer.php');

  ensureAdminUsersTable();

  if(isset($_SESSION['adminLogin']) && $_SESSION['adminLogin']==true){
    redirect('dashboard.php');
  }

  $message = '';
  $msg_type = 'danger';

  if(isset($_POST['send_reset'])){
    $email = trim(filteration($_POST)['email'] ?? '');
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
      $message = 'Please enter a valid email address.';
    } else {
      $res = select("SELECT `id`,`username`,`email` FROM `admin_users` WHERE `email`=? LIMIT 1", [$email], 's');
      if(!$res || $res->num_rows === 0){
        // Generic message to prevent email enumeration
        $message = 'If that email exists in our system, a reset link has been sent.';
        $msg_type = 'success';
      } else {
        $row = mysqli_fetch_assoc($res);
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $upd = "UPDATE `admin_users` SET `reset_token`=?, `reset_expires`=? WHERE `id`=?";
        update($upd, [$token, $expires, $row['id']], 'ssi');

        $link = SITE_URL . 'admin/reset_admin_password.php?token=' . urlencode($token) . '&email=' . urlencode($email);
        $siteName = defined('SITE_NAME') ? SITE_NAME : 'Travelers Place';
        $name = htmlspecialchars($row['username']);

        $html = "
          <div style='font-family:Arial,sans-serif;max-width:560px;margin:0 auto;background:#fff;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden'>
            <div style='background:#1a1a2e;padding:24px;text-align:center'>
              <h1 style='color:#c8a951;margin:0;font-size:22px'>{$siteName}</h1>
              <p style='color:#d1d5db;margin:4px 0 0;font-size:13px'>Admin Panel</p>
            </div>
            <div style='padding:32px'>
              <h2 style='color:#1a1a2e;margin:0 0 8px'>Password Reset Request</h2>
              <p style='color:#374151'>Hello <strong>{$name}</strong>,</p>
              <p style='color:#374151'>We received a request to reset your admin panel password. Click the button below to set a new password. This link expires in <strong>1 hour</strong>.</p>
              <div style='text-align:center;margin:28px 0'>
                <a href='{$link}' style='background:#c8a951;color:#fff;padding:12px 32px;border-radius:6px;text-decoration:none;font-size:15px;font-weight:bold'>Reset Password</a>
              </div>
              <p style='font-size:13px;color:#6b7280'>If you did not request this, you can safely ignore this email. Your password will not change.</p>
              <p style='font-size:13px;color:#6b7280'>Or copy this link into your browser:<br><a href='{$link}' style='color:#c8a951;word-break:break-all'>{$link}</a></p>
            </div>
          </div>
        ";

        $sent = send_email_smtp_basic($email, $row['username'], "Admin Password Reset – {$siteName}", $html);
        $message = 'If that email exists in our system, a reset link has been sent.';
        $msg_type = 'success';
      }
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin – Forgot Password</title>
  <?php require('inc/links.php'); ?>
  <style>
    .reset-form {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 420px;
    }
  </style>
</head>
<body class="bg-light">

  <div class="reset-form text-center rounded bg-white shadow overflow-hidden">
    <div class="bg-dark text-white py-3">
      <h4 class="mb-0">FORGOT PASSWORD</h4>
    </div>
    <div class="p-4">
      <?php if($message): ?>
        <div class="alert alert-<?php echo $msg_type; ?> text-start"><?php echo htmlspecialchars($message); ?></div>
      <?php endif; ?>
      <?php if($msg_type !== 'success'): ?>
      <p class="text-muted small mb-3">Enter the email address linked to your admin account and we'll send you a reset link.</p>
      <form method="POST">
        <div class="mb-3">
          <input name="email" type="email" required class="form-control shadow-none text-center" placeholder="Admin email address">
        </div>
        <button name="send_reset" type="submit" class="btn btn-dark shadow-none w-100">Send Reset Link</button>
      </form>
      <?php endif; ?>
      <div class="mt-3">
        <a href="index.php" class="text-secondary small text-decoration-none">&larr; Back to Login</a>
      </div>
    </div>
  </div>

  <?php require('inc/scripts.php') ?>
</body>
</html>
