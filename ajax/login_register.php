<?php 
  if (session_status() === PHP_SESSION_NONE) { session_start(); }

  require('../admin/inc/db_config.php');
  require('../admin/inc/essentials.php');
  require_once('../admin/inc/email_config.php');
  require_once('../inc/smtp_mailer.php');

  date_default_timezone_set("Asia/Manila");

  function smtp_ready_for_auth_mail()
  {
    return function_exists('smtp_is_configured') && smtp_is_configured();
  }

  function auth_mail_error_message()
  {
    $detail = function_exists('smtp_get_last_error') ? trim((string)smtp_get_last_error()) : '';
    return $detail !== '' ? $detail : 'Unable to send email right now. Please check SMTP settings.';
  }

  function normalize_guest_username(string $value): string
  {
    $value = strtolower(trim($value));
    $value = str_replace(' ', '.', $value);
    $value = preg_replace('/[^a-z0-9._-]+/', '', $value) ?? '';
    return trim($value, '._-');
  }

  function guest_username_exists(string $username, int $excludeId = 0): bool
  {
    if ($username === '') {
      return false;
    }

    $sql = "SELECT `id` FROM `user_cred` WHERE `username`=?";
    $types = 's';
    $params = [$username];

    if ($excludeId > 0) {
      $sql .= " AND `id`<>?";
      $types .= 'i';
      $params[] = $excludeId;
    }

    $sql .= " LIMIT 1";
    $res = select($sql, $params, $types);
    return $res && mysqli_num_rows($res) > 0;
  }

  function build_guest_username(array $data): string
  {
    $candidates = [];

    if (!empty($data['username'])) {
      $candidates[] = $data['username'];
    }

    if (!empty($data['email']) && strpos((string)$data['email'], '@') !== false) {
      $candidates[] = substr((string)$data['email'], 0, strpos((string)$data['email'], '@'));
    }

    if (!empty($data['name'])) {
      $candidates[] = $data['name'];
    }

    foreach ($candidates as $candidate) {
      $normalized = normalize_guest_username((string)$candidate);
      if ($normalized !== '' && !guest_username_exists($normalized)) {
        return $normalized;
      }
    }

    $suffix = 1000;
    do {
      $fallback = 'guest' . $suffix;
      $suffix++;
    } while (guest_username_exists($fallback));

    return $fallback;
  }

  function send_mail($uemail, $token, $type)
  {
    if ($type == "email_confirmation") {
      $page    = 'verify.php';
      $subject = "Verify Your Email – " . (defined('SITE_NAME') ? SITE_NAME : 'Travelers Place');
      $content = "confirm your email";
      $action  = "Verify Email";
    } else {
      $page    = 'reset_password.php';
      $subject = "Password Reset – " . (defined('SITE_NAME') ? SITE_NAME : 'Travelers Place');
      $content = "reset your password";
      $action  = "Reset Password";
    }

    $link     = SITE_URL . "$page?$type&email=" . urlencode($uemail) . "&token=" . urlencode($token);
    $siteName = defined('SITE_NAME') ? SITE_NAME : 'Travelers Place';

    $html = "
      <div style='font-family:Arial,sans-serif;max-width:560px;margin:0 auto;background:#fff;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden'>
        <div style='background:#1a1a2e;padding:24px;text-align:center'>
          <h1 style='color:#fff;margin:0;font-size:22px'>{$siteName}</h1>
        </div>
        <div style='padding:32px'>
          <p style='font-size:16px;color:#374151'>Hello,</p>
          <p style='font-size:15px;color:#374151'>Please click the button below to {$content}.</p>
          <div style='text-align:center;margin:28px 0'>
            <a href='{$link}' style='background:#c8a951;color:#fff;padding:12px 32px;border-radius:6px;text-decoration:none;font-size:15px;font-weight:bold'>{$action}</a>
          </div>
          <p style='font-size:13px;color:#6b7280'>If you did not request this, you can safely ignore this email.</p>
          <p style='font-size:13px;color:#6b7280'>Or copy this link into your browser:<br><a href='{$link}' style='color:#c8a951;word-break:break-all'>{$link}</a></p>
        </div>
      </div>
    ";

    return send_email_smtp_basic($uemail, '', $subject, $html) ? 1 : 0;
  }

  if(isset($_POST['register']))
  {
    $data = filteration($_POST);
    $data['username'] = normalize_guest_username((string)($data['username'] ?? ''));
    // normalize phone to digits-only to avoid false positives due to formatting
    if(isset($data['phonenum'])){
      $data['phonenum'] = preg_replace('/[^0-9]/','',$data['phonenum']);
    }

    // match password and confirm password field

    if($data['pass'] != $data['cpass']) {
      echo 'pass_mismatch';
      exit;
    }

    if($data['username'] === ''){
      $data['username'] = build_guest_username($data);
    }

    if($data['username'] === ''){
      echo 'invalid_username';
      exit;
    }

    // check user exists or not (compare email, username, OR digits-only phone stored with any formatting)
    $digits_only = $data['phonenum'];
    $u_exist = select(
      "SELECT `email`,`phonenum`,`username` FROM `user_cred` 
       WHERE `email` = ? 
          OR `username` = ?
          OR REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(`phonenum`,'+',''),'-',''),' ',''),'(',''),')','') = ? 
       LIMIT 1",
      [$data['email'],$data['username'],$digits_only],
      "sss"
    );

    if(mysqli_num_rows($u_exist)!=0){
      $u_exist_fetch = mysqli_fetch_assoc($u_exist);
      if($u_exist_fetch['email'] == $data['email']){
        echo 'email_already';
      }
      else if(($u_exist_fetch['username'] ?? '') == $data['username']){
        echo 'username_already';
      }
      else if($u_exist_fetch['phonenum'] == $data['phonenum']){
        echo 'phone_already';
      }
      else{
        echo 'exists_already';
      }
      exit;
    }

    // upload user image to server

    $img = uploadUserImage($_FILES['profile']);

    if($img == 'inv_img'){
      echo 'inv_img';
      exit;
    }
    else if($img == 'upd_failed'){
      echo 'upd_failed';
      exit;
    }

    $enc_pass = password_hash($data['pass'],PASSWORD_BCRYPT);

    $pincode = isset($data['pincode']) && $data['pincode'] !== '' ? $data['pincode'] : '0';
    $verification_code = bin2hex(random_bytes(16));

    $smtp_configured = smtp_ready_for_auth_mail();
    $initial_verified = '0';

    $query  = "INSERT INTO `user_cred`(`name`, `email`, `username`, `address`, `phonenum`, `pincode`, `dob`, `profile`, `password`, `is_verified`, `verification_code`, `token`) VALUES (?,?,?,?,?,?,?,?,?,?,?,NULL)";
    $values = [$data['name'],$data['email'],$data['username'],$data['address'],$data['phonenum'],$pincode,$data['dob'],$img,$enc_pass,$initial_verified,$verification_code];
    if (!insert($query, $values, 'sssssssssss')) {
      echo 'ins_failed';
      exit;
    }

    $new_user_id = mysqli_insert_id($con);

    if (!$smtp_configured) {
      if($new_user_id > 0){
        delete("DELETE FROM `user_cred` WHERE `id`=?", [$new_user_id], 'i');
      }
      if($img && $img !== 'inv_img' && $img !== 'upd_failed'){
        deleteImage($img, USERS_FOLDER);
      }
      echo 'mail_unavailable';
      exit;
    }

    if (!send_mail($data['email'], $verification_code, "email_confirmation")) {
      if($new_user_id > 0){
        delete("DELETE FROM `user_cred` WHERE `id`=?", [$new_user_id], 'i');
      }
      if($img && $img !== 'inv_img' && $img !== 'upd_failed'){
        deleteImage($img, USERS_FOLDER);
      }
      echo 'mail_failed|' . auth_mail_error_message();
      exit;
    }

    echo 'verify_email';

  }

  if(isset($_POST['login']))
  {
    $data = filteration($_POST);

    // normalize potential phone number for login as well
    $login_input = $data['email_mob'];
    $digits_login = preg_replace('/[^0-9]/','',$login_input);
    $username_login = normalize_guest_username($login_input);
    $u_exist = select(
      "SELECT * FROM `user_cred` WHERE `email`=? OR `username`=? OR REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(`phonenum`,'+',''),'-',''),' ',''),'(',''),')','')=? LIMIT 1",
      [$login_input,$username_login,$digits_login],
      "sss"
    );

    if(mysqli_num_rows($u_exist)==0){
      // Log failed login attempt with security event
      $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
      $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
      AuditLogger::logSecurity('Failed Login - Invalid Credentials', 
        "Email/Phone: $login_input | IP: $ip | User-Agent: $userAgent");
      
      // Generic error message to prevent user enumeration
      echo 'invalid_credentials';
    }
    else{
      $u_fetch = mysqli_fetch_assoc($u_exist);
      $userId = $u_fetch['id'];
      $userEmail = $u_fetch['email'];
      
      if($u_fetch['status']==0){
        // Log inactive account login attempt
        AuditLogger::logAuth('Login Failed - Account Inactive', 
          "Inactive account login attempt for user ID: $userId ($userEmail)");
        echo 'inactive';
      }
      else{
        if(!password_verify($data['pass'], $u_fetch['password'])){
          // Log failed password attempt
          AuditLogger::logSecurity('Login Failed - Invalid Password', 
            "Failed password attempt for user ID: $userId ($userEmail)");
            
          // Check for multiple failed attempts (you can implement account lockout here)
          $failedAttempts = ($_SESSION['login_attempts'] ?? 0) + 1;
          $_SESSION['login_attempts'] = $failedAttempts;
          
          if($failedAttempts >= 3) {
            AuditLogger::logSecurity('Login Failed - Multiple Failed Attempts', 
              "User ID: $userId ($userEmail) has $failedAttempts failed login attempts");
          }
          
          echo 'invalid_credentials'; // Generic message for security
        }
        else{
          // Reset failed attempts on successful login
          unset($_SESSION['login_attempts']);
          
          // Set session variables
          $_SESSION['login'] = true;
          $_SESSION['uId'] = $userId;
          $_SESSION['uName'] = $u_fetch['name'];
          $_SESSION['uPic'] = $u_fetch['profile'];
          $_SESSION['uUsername'] = $u_fetch['username'] ?? '';
          $_SESSION['is_verified'] = (int)$u_fetch['is_verified'];
          
          // Log successful login with session ID and other details
          $sessionId = session_id();
          $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
          $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
          
          AuditLogger::logAuth('Login Success', 
            "User ID: $userId ($userEmail) logged in successfully | " .
            "Session: $sessionId | IP: $ip | User-Agent: $userAgent");
            
          // Log session creation
          logAction('Session Started', 
            "New session created for user ID: $userId ($userEmail)");
          
          $_SESSION['uPhone'] = $u_fetch['phonenum'];
          echo 1;
        }
      }
    }
  }

  if(isset($_POST['forgot_pass']))
  {
    $data = filteration($_POST);
    
    $u_exist = select("SELECT * FROM `user_cred` WHERE `email`=? LIMIT 1", [$data['email']], "s");

    if(mysqli_num_rows($u_exist)==0){
      echo 'inv_email';
    }
    else
    {
      $u_fetch = mysqli_fetch_assoc($u_exist);
      if($u_fetch['status']==0){
        echo 'inactive';
      }
      else{
        // generate reset token and expiry
        $token = bin2hex(random_bytes(16));
        $date  = date("Y-m-d", strtotime('+1 day'));

        // save token first so the link is valid even if mail is slow
        $updated = update(
          "UPDATE `user_cred` SET `token`=?, `t_expire`=? WHERE `id`=?",
          [$token, $date, $u_fetch['id']],
          'ssi'
        );

        if(!$updated){
          echo 'upd_failed';
        }
        else{
          $smtp_configured = smtp_ready_for_auth_mail();
          if(!$smtp_configured){
            // No SMTP — return the reset link directly so the user can use it
            echo 'mail_unavailable';
          }
          else if(!send_mail($data['email'], $token, 'account_recovery')){
            echo 'mail_failed|' . auth_mail_error_message();
          }
          else{
            echo 1;
          }
        }
      }
    }

  }

  if(isset($_POST['recover_user']))
  {
    $data = filteration($_POST);

    if(($data['pass'] ?? '') !== ($data['cpass'] ?? '')){
      echo 'pass_mismatch';
      exit;
    }

    $token_check = select(
      "SELECT `id` FROM `user_cred` WHERE `email`=? AND `token`=? AND `t_expire`>=? AND `status`=1 LIMIT 1",
      [$data['email'], $data['token'], date('Y-m-d')],
      'sss'
    );

    if(!$token_check || mysqli_num_rows($token_check) !== 1){
      echo 'invalid_token';
      exit;
    }

    $enc_pass = password_hash($data['pass'],PASSWORD_BCRYPT);

    $query = "UPDATE `user_cred` SET `password`=?, `token`=NULL, `t_expire`=NULL 
      WHERE `email`=? AND `token`=?";

    $values = [$enc_pass,$data['email'],$data['token']];

    if(update($query,$values,'sss'))
    {
      echo 1;
    }
    else{
      echo 'failed';
    }
  }

  if(isset($_POST['resend_verification']))
  {
    if (session_status() === PHP_SESSION_NONE) session_start();

    if(!(isset($_SESSION['login']) && $_SESSION['login'] == true)){
      echo 'not_logged_in';
      exit;
    }

    $uid = (int)$_SESSION['uId'];
    $u_res = select("SELECT `email`, `is_verified` FROM `user_cred` WHERE `id`=? LIMIT 1", [$uid], 'i');

    if(mysqli_num_rows($u_res) === 0){
      echo 'not_found';
      exit;
    }

    $u_row = mysqli_fetch_assoc($u_res);

    if($u_row['is_verified'] == 1){
      echo 'already_verified';
      exit;
    }

    $verification_code = bin2hex(random_bytes(16));

    $updated = update("UPDATE `user_cred` SET `verification_code`=? WHERE `id`=?", [$verification_code, $uid], 'si');
    if(!$updated){
      echo 'upd_failed';
      exit;
    }

    if(!smtp_ready_for_auth_mail()){
      echo 'mail_unavailable';
      exit;
    }

    if(!send_mail($u_row['email'], $verification_code, 'email_confirmation')){
      echo 'mail_failed|' . auth_mail_error_message();
      exit;
    }

    echo 'sent';
  }
  
?>
