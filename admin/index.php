<?php
  if (session_status() === PHP_SESSION_NONE) { session_start(); }
  require('inc/essentials.php');
  require('inc/db_config.php');
  require('inc/admin_users_table.php');

  if((isset($_SESSION['adminLogin']) && $_SESSION['adminLogin']==true)){
    redirect('dashboard.php');
  }

  ensureAdminUsersTable();

  $login_error = null;

  if(isset($_POST['login']))
  {
    $frm_data = filteration($_POST);
    $login_input = trim($frm_data['admin_name'] ?? '');
    $login_pass = (string)($frm_data['admin_pass'] ?? '');

    $query = "SELECT `id`,`username`,`password`,`role`,`email` FROM `admin_users`
              WHERE `username`=? OR (`email` IS NOT NULL AND `email`!='' AND `email`=?) LIMIT 1";
    $values = [$login_input, $login_input];
    $res = select($query,$values,"ss");

    if($res && $res->num_rows==1){
      $row = mysqli_fetch_assoc($res);
      if(password_verify($login_pass, $row['password'])){
        session_regenerate_id(true);
        $_SESSION['adminLogin'] = true;
        $_SESSION['adminId'] = (int)$row['id'];
        $_SESSION['adminName'] = $row['username'];
        $_SESSION['adminRole'] = $row['role'];
        $_SESSION['adminAuthSource'] = 'admin_users';
        session_write_close();
        redirect('dashboard.php');
      }
      $login_error = 'Login failed - Invalid Credentials!';
    }
    else{
      $query2 = "SELECT * FROM `admin_cred` WHERE `admin_name`=? AND `admin_pass`=?";
      $values2 = [$login_input,$login_pass];
      $res2 = select($query2,$values2,"ss");

      if($res2 && $res2->num_rows==1){
        $row2 = mysqli_fetch_assoc($res2);
        session_regenerate_id(true);
        $_SESSION['adminLogin'] = true;
        $_SESSION['adminId'] = $row2['sr_no'];
        $_SESSION['adminName'] = $row2['admin_name'];
        $_SESSION['adminRole'] = 'admin';
        $_SESSION['adminAuthSource'] = 'admin_cred';
        session_write_close();
        redirect('dashboard.php');
      }

      $login_error = 'Login failed - Invalid Credentials!';
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login - Travelers Place</title>
  <?php require('inc/links.php'); ?>
  <style>
    * {
      box-sizing: border-box;
    }

    body.admin-auth-page {
      min-height: 100vh;
      margin: 0;
      padding: 0 !important;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Poppins', 'Segoe UI', sans-serif;
      position: relative;
      overflow: hidden;
    }

    body.admin-auth-page::before,
    body.admin-auth-page::after {
      content: '';
      position: absolute;
      border-radius: 999px;
      pointer-events: none;
    }

    body.admin-auth-page::before {
      width: 360px;
      height: 360px;
      top: -120px;
      left: -90px;
      background: rgba(var(--admin-accent-rgb), 0.16);
      filter: blur(8px);
    }

    body.admin-auth-page::after {
      width: 280px;
      height: 280px;
      right: -60px;
      bottom: -90px;
      background: rgba(var(--admin-accent-rgb), 0.1);
      filter: blur(8px);
    }

    .login-shell {
      position: relative;
      z-index: 1;
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
    }

    .login-card {
      width: 100%;
      max-width: 430px;
      padding: 42px 36px 34px;
      border-radius: 24px;
      background: var(--admin-login-card);
      border: 1px solid var(--admin-login-card-border);
      box-shadow: var(--admin-login-shadow);
      backdrop-filter: blur(18px);
      -webkit-backdrop-filter: blur(18px);
      position: relative;
      animation: fadeIn 0.45s ease;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(12px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .brand-title {
      margin: 0 0 6px;
      text-align: center;
      font-size: 2rem;
      font-weight: 700;
      letter-spacing: 0.01em;
      color: var(--admin-accent);
    }

    .subtitle {
      margin: 0 0 30px;
      text-align: center;
      font-size: 0.95rem;
      color: var(--admin-login-muted);
    }

    .input-wrap {
      position: relative;
      margin-bottom: 18px;
    }

    .input-wrap .input-icon {
      position: absolute;
      top: 50%;
      left: 15px;
      transform: translateY(-50%);
      color: var(--admin-login-muted);
      font-size: 15px;
      pointer-events: none;
    }

    .input-wrap input {
      width: 100%;
      min-height: 52px;
      padding: 12px 16px 12px 44px;
      border: 1.5px solid var(--admin-login-input-border);
      border-radius: 14px;
      background: var(--admin-login-input);
      color: var(--admin-login-input-text);
      font-size: 0.95rem;
      font-family: inherit;
      outline: none;
      transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
    }

    .input-wrap input::placeholder {
      color: var(--admin-login-muted);
      opacity: 0.78;
    }

    .input-wrap input:focus {
      border-color: rgba(var(--admin-accent-rgb), 0.48);
      box-shadow: 0 0 0 4px rgba(var(--admin-accent-rgb), 0.12);
      transform: translateY(-1px);
    }

    .toggle-pass {
      position: absolute;
      top: 50%;
      right: 15px;
      transform: translateY(-50%);
      color: var(--admin-login-muted);
      font-size: 15px;
      cursor: pointer;
      user-select: none;
      transition: color 0.2s ease;
    }

    .toggle-pass:hover {
      color: var(--admin-accent);
    }

    .forgot-link {
      display: block;
      margin-top: -8px;
      margin-bottom: 26px;
      text-align: right;
      font-size: 0.82rem;
      color: var(--admin-accent);
      text-decoration: none;
      transition: filter 0.2s ease;
    }

    .forgot-link:hover {
      filter: brightness(0.92);
      text-decoration: underline;
    }

    .btn-login {
      width: 100%;
      min-height: 54px;
      border: none;
      border-radius: 999px;
      background: linear-gradient(135deg, rgba(var(--admin-accent-rgb), 0.94) 0%, var(--admin-accent) 100%);
      color: #fff;
      font-size: 0.96rem;
      font-weight: 700;
      letter-spacing: 0.08em;
      font-family: inherit;
      cursor: pointer;
      box-shadow: 0 16px 30px rgba(var(--admin-accent-rgb), 0.26);
      transition: transform 0.15s ease, box-shadow 0.2s ease, filter 0.2s ease;
    }

    .btn-login:hover {
      transform: translateY(-1px);
      box-shadow: 0 18px 34px rgba(var(--admin-accent-rgb), 0.32);
      filter: brightness(0.98);
    }

    .btn-login:active {
      transform: translateY(0);
    }

    .mode-toggle {
      position: absolute;
      top: 18px;
      right: 18px;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      border: 1px solid var(--admin-login-input-border);
      background: var(--admin-login-toggle-bg);
      color: var(--admin-login-muted);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 15px;
      cursor: pointer;
      box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
      transition: all 0.2s ease;
    }

    .mode-toggle:hover {
      color: var(--admin-accent);
      border-color: rgba(var(--admin-accent-rgb), 0.4);
      transform: translateY(-1px);
    }

    .login-helper {
      margin: 18px 0 0;
      text-align: center;
      font-size: 0.78rem;
      color: var(--admin-login-muted);
    }

    @media (max-width: 480px) {
      .login-shell {
        padding: 16px;
      }

      .login-card {
        padding: 34px 22px 28px;
        border-radius: 20px;
      }

      .brand-title {
        font-size: 1.75rem;
      }
    }
  </style>
</head>
<body class="admin-auth-page">

  <div class="login-shell">
    <div class="login-card">
      <button class="mode-toggle" id="modeToggle" title="Switch to dark mode" type="button" aria-pressed="false" onclick="return toggleAdminThemeMode()">
        <i class="bi bi-moon-stars-fill" id="modeIcon"></i>
      </button>

      <h2 class="brand-title">Travelers Place</h2>
      <p class="subtitle">Sign in to your admin or staff account</p>

      <?php
        if($login_error !== null){
          alert('error', $login_error);
        }
      ?>

      <form method="POST" autocomplete="off">
        <div class="input-wrap">
          <i class="bi bi-person input-icon"></i>
          <input name="admin_name" required type="text" placeholder="Username or Email" autocomplete="username">
        </div>

        <div class="input-wrap">
          <i class="bi bi-lock input-icon"></i>
          <input name="admin_pass" required type="password" id="adminPass" placeholder="Password" autocomplete="current-password">
          <span class="toggle-pass" onclick="togglePass()" title="Show or hide password">
            <i class="bi bi-eye" id="eyeIcon"></i>
          </span>
        </div>

        <a href="forgot_password.php" class="forgot-link">Forgot password?</a>

        <button name="login" type="submit" class="btn-login">SIGN IN</button>
      </form>

      <p class="login-helper">Your selected mode and color theme will also appear across the admin and staff workspace after login.</p>
    </div>
  </div>

  <?php require('inc/scripts.php') ?>

  <script>
    function togglePass() {
      const input = document.getElementById('adminPass');
      const icon = document.getElementById('eyeIcon');
      if (!input || !icon) return;

      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
      }
    }

    document.addEventListener('DOMContentLoaded', function () {
      if (typeof syncAdminThemeControls === 'function') {
        syncAdminThemeControls();
      }
    });
  </script>
</body>
</html>
