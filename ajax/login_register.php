<?php 
  if (session_status() === PHP_SESSION_NONE) { session_start(); }

  require('../admin/inc/db_config.php');
  require('../admin/inc/essentials.php');
  require_once('../admin/inc/email_config.php');
  require_once('../inc/smtp_mailer.php');

  date_default_timezone_set("Asia/Kolkata");

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
    // normalize phone to digits-only to avoid false positives due to formatting
    if(isset($data['phonenum'])){
      $data['phonenum'] = preg_replace('/[^0-9]/','',$data['phonenum']);
    }

    // match password and confirm password field

    if($data['pass'] != $data['cpass']) {
      echo 'pass_mismatch';
      exit;
    }

    // check user exists or not (compare email OR digits-only phone stored with any formatting)
    $digits_only = $data['phonenum'];
    $u_exist = select(
      "SELECT `email`,`phonenum` FROM `user_cred` 
       WHERE `email` = ? 
          OR REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(`phonenum`,'+',''),'-',''),' ',''),'(',''),')','') = ? 
       LIMIT 1",
      [$data['email'],$digits_only],
      "ss"
    );

    if(mysqli_num_rows($u_exist)!=0){
      $u_exist_fetch = mysqli_fetch_assoc($u_exist);
      if($u_exist_fetch['email'] == $data['email']){
        echo 'email_already';
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
    $token   = bin2hex(random_bytes(16));

    // Check whether SMTP is configured
    $smtp_configured = defined('SMTP_USERNAME') && SMTP_USERNAME !== '' && defined('SMTP_PASSWORD') && SMTP_PASSWORD !== '';

    // If SMTP is not configured, auto-verify the account so registration works
    $initial_verified = $smtp_configured ? '0' : '1';

    $query  = "INSERT INTO `user_cred`(`name`, `email`, `address`, `phonenum`, `pincode`, `dob`, `profile`, `password`, `is_verified`, `token`) VALUES (?,?,?,?,?,?,?,?,?,?)";
    $values = [$data['name'],$data['email'],$data['address'],$data['phonenum'],$pincode,$data['dob'],$img,$enc_pass,$initial_verified,$token];
    if (!insert($query, $values, 'ssssssssss')) {
      echo 'ins_failed';
      exit;
    }

    if ($smtp_configured) {
      if (!send_mail($data['email'], $token, "email_confirmation")) {
        echo 'mail_failed';
        exit;
      }
      echo 'verify_email';
    } else {
      echo 'registered';
    }

  }

  if(isset($_POST['login']))
  {
    $data = filteration($_POST);

    // normalize potential phone number for login as well
    $login_input = $data['email_mob'];
    $digits_login = preg_replace('/[^0-9]/','',$login_input);
    $u_exist = select(
      "SELECT * FROM `user_cred` WHERE `email`=? OR REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(`phonenum`,'+',''),'-',''),' ',''),'(',''),')','')=? LIMIT 1",
      [$login_input,$digits_login],
      "ss"
    );

    if(mysqli_num_rows($u_exist)==0){
      // Log failed login attempt with security event
      $ip = $_SERVER['REMOTE_ADDR'];
      $userAgent = $_SERVER['HTTP_USER_AGENT'];
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
          $_SESSION['is_verified'] = (int)$u_fetch['is_verified'];
          
          // For admin panel compatibility
          $_SESSION['adminLogin'] = true;
          $_SESSION['adminId'] = $userId;
          $_SESSION['adminName'] = $u_fetch['name'];
          
          // Log successful login with session ID and other details
          $sessionId = session_id();
          $ip = $_SERVER['REMOTE_ADDR'];
          $userAgent = $_SERVER['HTTP_USER_AGENT'];
          
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
        $date  = date("Y-m-d");

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
          $smtp_configured = defined('SMTP_USERNAME') && SMTP_USERNAME !== '' && defined('SMTP_PASSWORD') && SMTP_PASSWORD !== '';
          if(!$smtp_configured){
            // No SMTP — return the reset link directly so the user can use it
            $reset_link = SITE_URL . 'reset_password.php?account_recovery&email=' . urlencode($data['email']) . '&token=' . urlencode($token);
            echo 'no_smtp|' . $reset_link;
          }
          else if(!send_mail($data['email'], $token, 'account_recovery')){
            echo 'mail_failed';
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
    
    $enc_pass = password_hash($data['pass'],PASSWORD_BCRYPT);

    $query = "UPDATE `user_cred` SET `password`=?, `token`=?, `t_expire`=? 
      WHERE `email`=? AND `token`=?";

    $values = [$enc_pass,null,null,$data['email'],$data['token']];

    if(update($query,$values,'sssss'))
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

    $token = bin2hex(random_bytes(16));

    $updated = update("UPDATE `user_cred` SET `token`=? WHERE `id`=?", [$token, $uid], 'si');
    if(!$updated){
      echo 'upd_failed';
      exit;
    }

    if(!send_mail($u_row['email'], $token, 'email_confirmation')){
      echo 'mail_failed';
      exit;
    }

    echo 'sent';
  }
  
?>