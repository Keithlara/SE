<?php 

  require('../admin/inc/db_config.php');
  require('../admin/inc/essentials.php');
  require("../inc/sendgrid/sendgrid-php.php");

  date_default_timezone_set("Asia/Kolkata");


  function send_mail($uemail,$token,$type)
  {

    if($type == "email_confirmation"){
      $page = 'email_confirm.php';
      $subject = "Account Verification Link";
      $content = "confirm your email";
    }
    else{
      $page = 'index.php';
      $subject = "Account Reset Link";
      $content = "reset your account";
    }

    $email = new \SendGrid\Mail\Mail(); 
    $email->setFrom(SENDGRID_EMAIL,SENDGRID_NAME);
    $email->setSubject($subject);

    $email->addTo($uemail);

    $email->addContent(
        "text/html", 
        "
          Click the link to $content: <br>
          <a href='".SITE_URL."$page?$type&email=$uemail&token=$token"."'>
            CLICK ME
          </a>
        "
    );

    $sendgrid = new \SendGrid(SENDGRID_API_KEY);

    try{
      $sendgrid->send($email);
      return 1;
    }
    catch (Exception $e){
      return 0;
    }
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


    // -->>> UN-COMMENT the below lines if you want to use email verification

    // -- START
    /* 
      $token = bin2hex(random_bytes(16));
      if(!send_mail($data['email'],$token,"email_confirmation")){
        echo 'mail_failed';
        exit;
      }  
      $pincode = isset($data['pincode']) && $data['pincode'] !== '' ? $data['pincode'] : '0';
      $query = "INSERT INTO `user_cred`(`name`, `email`, `address`, `phonenum`, `pincode`, `dob`,`profile`, `password`, `token`) VALUES (?,?,?,?,?,?,?,?,?)";
      $values = [$data['name'],$data['email'],$data['address'],$data['phonenum'],$pincode,$data['dob'],$img,$enc_pass,$token];
    */
    // -- END


    // --->>> COMMENT these lines if you are using email verification

    // -- START

    $pincode = isset($data['pincode']) && $data['pincode'] !== '' ? $data['pincode'] : '0';
    $query = "INSERT INTO `user_cred`(`name`, `email`, `address`, `phonenum`, `pincode`, `dob`,`profile`, `password`, `is_verified`) VALUES (?,?,?,?,?,?,?,?,?)";
    $values = [$data['name'],$data['email'],$data['address'],$data['phonenum'],$pincode,$data['dob'],$img,$enc_pass,'1'];

    // -- END

    if(insert($query,$values,'sssssssss')){
      echo 1;
    }
    else{
      echo 'ins_failed';
    }

  }

  if(isset($_POST['login']))
  {
    $data = filteration($_POST);
    
    // Start session early so we can log failed attempts
    session_start();
    
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
      
      if($u_fetch['is_verified']==0){
        // Log unverified account login attempt
        AuditLogger::logAuth('Login Failed - Unverified Account', 
          "Account not verified for user ID: $userId ($userEmail)");
        echo 'not_verified';
      }
      else if($u_fetch['status']==0){
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
      if($u_fetch['is_verified']==0){
        echo 'not_verified';
      }
      else if($u_fetch['status']==0){
        echo 'inactive';
      }
      else{
        // send reset link to email
        $token = bin2hex(random_bytes(16));

        if(!send_mail($data['email'],$token,'account_recovery')){
          echo 'mail_failed';
        }
        else
        {
          $date = date("Y-m-d");

          $query = mysqli_query($con,"UPDATE `user_cred` SET `token`='$token', `t_expire`='$date' 
            WHERE `id`='$u_fetch[id]'");

          if($query){
            echo 1;
          }
          else{
            echo 'upd_failed';
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
  
?>