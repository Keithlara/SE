<?php
  if (session_status() === PHP_SESSION_NONE) { session_start(); }
  require('inc/essentials.php');
  require('inc/db_config.php');
  require('inc/admin_users_table.php');
  if((isset($_SESSION['adminLogin']) && $_SESSION['adminLogin']==true)){
    redirect('dashboard.php');
  }

  // Ensure role-based users table exists (safe to run repeatedly)
  ensureAdminUsersTable();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login — Travelers Place</title>
  <?php require('inc/links.php'); ?>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      overflow: hidden;
    }

    /* Subtle animated background blobs */
    body::before, body::after {
      content: '';
      position: fixed;
      border-radius: 50%;
      filter: blur(80px);
      opacity: 0.18;
      z-index: 0;
    }
    body::before {
      width: 500px; height: 500px;
      background: #00b4d8;
      top: -120px; left: -120px;
      animation: blob1 8s ease-in-out infinite alternate;
    }
    body::after {
      width: 400px; height: 400px;
      background: #48cae4;
      bottom: -100px; right: -100px;
      animation: blob2 10s ease-in-out infinite alternate;
    }
    @keyframes blob1 { to { transform: translate(60px, 80px) scale(1.1); } }
    @keyframes blob2 { to { transform: translate(-60px, -80px) scale(1.15); } }

    .login-wrapper {
      position: relative;
      z-index: 1;
      display: flex;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 30px 80px rgba(0,0,0,0.5);
      width: 820px;
      max-width: 96vw;
      min-height: 480px;
    }

    /* Left decorative panel */
    .login-side {
      flex: 1;
      background: linear-gradient(160deg, #00b4d8 0%, #0077b6 60%, #03045e 100%);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 40px 30px;
      color: #fff;
      text-align: center;
    }
    .login-side .brand-icon {
      width: 70px; height: 70px;
      background: rgba(255,255,255,0.15);
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      margin-bottom: 20px;
      font-size: 32px;
      backdrop-filter: blur(6px);
    }
    .login-side h1 {
      font-size: 1.9rem;
      font-weight: 700;
      letter-spacing: 1px;
      margin-bottom: 10px;
    }
    .login-side p {
      font-size: 0.88rem;
      opacity: 0.78;
      line-height: 1.6;
      max-width: 200px;
    }
    .login-side .divider {
      width: 40px; height: 3px;
      background: rgba(255,255,255,0.5);
      border-radius: 2px;
      margin: 18px auto;
    }

    /* Right form panel */
    .login-form {
      flex: 1.1;
      background: #fff;
      padding: 50px 44px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    .login-form .form-title {
      font-size: 1.5rem;
      font-weight: 700;
      color: #03045e;
      margin-bottom: 4px;
    }
    .login-form .form-subtitle {
      font-size: 0.85rem;
      color: #888;
      margin-bottom: 32px;
    }

    .input-group-custom {
      position: relative;
      margin-bottom: 20px;
    }
    .input-group-custom .input-icon {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: #aaa;
      font-size: 15px;
    }
    .input-group-custom input {
      width: 100%;
      padding: 13px 16px 13px 42px;
      border: 1.5px solid #e2e8f0;
      border-radius: 10px;
      font-size: 0.92rem;
      color: #333;
      background: #f8fafc;
      transition: border-color 0.25s, box-shadow 0.25s;
      outline: none;
    }
    .input-group-custom input:focus {
      border-color: #00b4d8;
      background: #fff;
      box-shadow: 0 0 0 3px rgba(0,180,216,0.12);
    }
    .input-group-custom input::placeholder { color: #b0b8c4; }

    .toggle-pass {
      position: absolute;
      right: 14px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #aaa;
      font-size: 15px;
      user-select: none;
      transition: color 0.2s;
    }
    .toggle-pass:hover { color: #00b4d8; }

    .forgot-link {
      display: block;
      text-align: right;
      font-size: 0.82rem;
      color: #00b4d8;
      text-decoration: none;
      margin-top: -10px;
      margin-bottom: 26px;
      transition: color 0.2s;
    }
    .forgot-link:hover { color: #0077b6; text-decoration: underline; }

    .btn-login {
      width: 100%;
      padding: 13px;
      border: none;
      border-radius: 10px;
      background: linear-gradient(90deg, #00b4d8, #0077b6);
      color: #fff;
      font-size: 0.95rem;
      font-weight: 600;
      letter-spacing: 1px;
      cursor: pointer;
      transition: opacity 0.25s, box-shadow 0.25s, transform 0.15s;
      box-shadow: 0 4px 18px rgba(0,119,182,0.35);
    }
    .btn-login:hover {
      opacity: 0.92;
      transform: translateY(-1px);
      box-shadow: 0 8px 24px rgba(0,119,182,0.45);
    }
    .btn-login:active { transform: translateY(0); }

    .back-link {
      display: block;
      text-align: center;
      margin-top: 22px;
      font-size: 0.82rem;
      color: #aaa;
      text-decoration: none;
      transition: color 0.2s;
    }
    .back-link:hover { color: #0077b6; }
    .back-link i { margin-right: 5px; }

    @media (max-width: 640px) {
      .login-side { display: none; }
      .login-form { padding: 40px 26px; }
      .login-wrapper { width: 100%; border-radius: 0; min-height: 100vh; }
    }
  </style>
</head>
<body>

  <div class="login-wrapper">

    <!-- Left decorative side -->
    <div class="login-side">
      <div class="brand-icon">
        <i class="bi bi-building"></i>
      </div>
      <h1>Travelers Place</h1>
      <div class="divider"></div>
      <p>Secure admin portal for managing bookings, rooms, and operations.</p>
    </div>

    <!-- Right form side -->
    <div class="login-form">
      <div class="form-title">Welcome back</div>
      <div class="form-subtitle">Sign in to your admin account</div>

      <form method="POST" autocomplete="off">
        <div class="input-group-custom">
          <i class="bi bi-person input-icon"></i>
          <input name="admin_name" required type="text" placeholder="Username or Email" autocomplete="username">
        </div>

        <div class="input-group-custom">
          <i class="bi bi-lock input-icon"></i>
          <input name="admin_pass" required type="password" id="adminPass" placeholder="Password" autocomplete="current-password">
          <span class="toggle-pass" onclick="togglePass()">
            <i class="bi bi-eye" id="eyeIcon"></i>
          </span>
        </div>

        <a href="forgot_password.php" class="forgot-link">Forgot password?</a>

        <button name="login" type="submit" class="btn-login">SIGN IN</button>
      </form>

      <a href="../index.php" class="back-link">
        <i class="bi bi-arrow-left"></i> Back to main site
      </a>
    </div>

  </div>

  <?php 
    
    if(isset($_POST['login']))
    {
      $frm_data = filteration($_POST);
      $login_input = trim($frm_data['admin_name'] ?? '');

      // New role-based login (admin_users) — match by username OR email
      $res = null;
      $query = "SELECT `id`,`username`,`password`,`role`,`email` FROM `admin_users`
                WHERE `username`=? OR (`email` IS NOT NULL AND `email`!='' AND `email`=?) LIMIT 1";
      $values = [$login_input, $login_input];
      $res = select($query,$values,"ss");

        if($res && $res->num_rows==1){
          $row = mysqli_fetch_assoc($res);
          if(password_verify($frm_data['admin_pass'], $row['password'])){
            $_SESSION['adminLogin'] = true;
            $_SESSION['adminId'] = (int)$row['id'];
            $_SESSION['adminName'] = $row['username'];
            $_SESSION['adminRole'] = $row['role'];
            $_SESSION['adminAuthSource'] = 'admin_users';

            $roleLabel = ucfirst($row['role'] ?? 'Admin');
            $unameEsc = htmlspecialchars($row['username'], ENT_QUOTES);
            echo "<script>
              Swal.fire({
                icon: 'success',
                title: 'Welcome back, {$roleLabel}!',
                text: 'Hello {$unameEsc}, you are logged in as {$roleLabel}.',
                timer: 1500,
                showConfirmButton: false
              }).then(()=>{ window.location.href='dashboard.php'; });
            </script>";
          }
          else{
            alert('error','Login failed - Invalid Credentials!');
          }
      }
      else{
        // Legacy fallback (admin_cred) - plaintext password
        $query2 = "SELECT * FROM `admin_cred` WHERE `admin_name`=? AND `admin_pass`=?";
        $values2 = [$login_input,$frm_data['admin_pass']];
        $res2 = select($query2,$values2,"ss");

        if($res2 && $res2->num_rows==1){
          $row2 = mysqli_fetch_assoc($res2);
          $_SESSION['adminLogin'] = true;
          $_SESSION['adminId'] = $row2['sr_no'];
          $_SESSION['adminName'] = $row2['admin_name'];
          $_SESSION['adminRole'] = 'admin';
          $_SESSION['adminAuthSource'] = 'admin_cred';

          $unameEsc2 = htmlspecialchars($row2['admin_name'], ENT_QUOTES);
          echo "<script>
            Swal.fire({
              icon: 'success',
              title: 'Welcome back, Admin!',
              text: 'Hello {$unameEsc2}, you are logged in as Admin.',
              timer: 1500,
              showConfirmButton: false
            }).then(()=>{ window.location.href='dashboard.php'; });
          </script>";
        }
        else{
          alert('error','Login failed - Invalid Credentials!');
        }
      }
    }
  
  ?>

  <script>
    function togglePass() {
      const input = document.getElementById('adminPass');
      const icon = document.getElementById('eyeIcon');
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
      }
    }
  </script>

  <?php require('inc/scripts.php') ?>
</body>
</html>
