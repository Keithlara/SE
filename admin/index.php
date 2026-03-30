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
      background: #ffffff;
      color: #000;
      font-family: 'Poppins', 'Segoe UI', sans-serif;
      transition: background 0.3s, color 0.3s;
    }

    body.dark-mode {
      background: #0f172a;
      color: #fff;
    }

    .login-card {
      width: 100%;
      max-width: 400px;
      padding: 40px 36px;
      border-radius: 16px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.1);
      background: #fff;
      animation: fadeIn 0.5s ease;
      position: relative;
    }

    .dark-mode .login-card {
      background: rgba(255,255,255,0.06);
      backdrop-filter: blur(14px);
      -webkit-backdrop-filter: blur(14px);
      border: 1px solid rgba(255,255,255,0.1);
      box-shadow: 0 10px 40px rgba(0,0,0,0.4);
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(12px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    .brand-title {
      font-weight: 700;
      font-size: 26px;
      text-align: center;
      margin-bottom: 4px;
      color: #0d6efd;
      letter-spacing: 0.5px;
    }
    .dark-mode .brand-title { color: #60a5fa; }

    .subtitle {
      text-align: center;
      font-size: 13.5px;
      opacity: 0.6;
      margin-bottom: 28px;
    }

    .form-label-custom {
      font-size: 13px;
      font-weight: 600;
      margin-bottom: 6px;
      display: block;
      opacity: 0.75;
    }

    .input-wrap {
      position: relative;
      margin-bottom: 18px;
    }
    .input-wrap .input-icon {
      position: absolute;
      left: 13px;
      top: 50%;
      transform: translateY(-50%);
      color: #aaa;
      font-size: 15px;
      pointer-events: none;
    }
    .input-wrap input {
      width: 100%;
      padding: 12px 14px 12px 40px;
      border: 1.5px solid #e2e8f0;
      border-radius: 10px;
      font-size: 14px;
      background: #f8fafc;
      color: #222;
      outline: none;
      transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
      font-family: inherit;
    }
    .input-wrap input:focus {
      border-color: #0d6efd;
      background: #fff;
      box-shadow: 0 0 0 3px rgba(13,110,253,0.1);
    }
    .input-wrap input::placeholder { color: #b0b8c4; }

    .dark-mode .input-wrap input {
      background: rgba(255,255,255,0.08);
      border-color: rgba(255,255,255,0.12);
      color: #fff;
    }
    .dark-mode .input-wrap input:focus {
      background: rgba(255,255,255,0.12);
      border-color: #60a5fa;
      box-shadow: 0 0 0 3px rgba(96,165,250,0.15);
    }
    .dark-mode .input-wrap input::placeholder { color: rgba(255,255,255,0.3); }

    .toggle-pass {
      position: absolute;
      right: 13px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #aaa;
      font-size: 15px;
      user-select: none;
      transition: color 0.2s;
    }
    .toggle-pass:hover { color: #0d6efd; }
    .dark-mode .toggle-pass:hover { color: #60a5fa; }

    .forgot-link {
      display: block;
      text-align: right;
      font-size: 12.5px;
      color: #0d6efd;
      text-decoration: none;
      margin-top: -8px;
      margin-bottom: 24px;
      transition: color 0.2s;
    }
    .forgot-link:hover { color: #0b5ed7; text-decoration: underline; }
    .dark-mode .forgot-link { color: #60a5fa; }

    .btn-login {
      width: 100%;
      padding: 13px;
      border: none;
      border-radius: 50px;
      background: #0d6efd;
      color: #fff;
      font-size: 14.5px;
      font-weight: 600;
      letter-spacing: 0.5px;
      cursor: pointer;
      transition: background 0.3s, transform 0.15s, box-shadow 0.2s;
      box-shadow: 0 4px 18px rgba(13,110,253,0.3);
      font-family: inherit;
    }
    .btn-login:hover {
      background: #0b5ed7;
      transform: translateY(-1px);
      box-shadow: 0 6px 22px rgba(13,110,253,0.4);
    }
    .btn-login:active { transform: translateY(0); }

    /* Dark mode toggle */
    .mode-toggle {
      position: absolute;
      top: 16px;
      right: 16px;
      background: none;
      border: 1.5px solid #e2e8f0;
      border-radius: 50%;
      width: 34px;
      height: 34px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      color: #666;
      font-size: 15px;
      transition: all 0.25s;
    }
    .mode-toggle:hover { border-color: #0d6efd; color: #0d6efd; }
    .dark-mode .mode-toggle { border-color: rgba(255,255,255,0.2); color: #cbd5e1; }
    .dark-mode .mode-toggle:hover { border-color: #60a5fa; color: #60a5fa; }

    @media (max-width: 480px) {
      .login-card { padding: 32px 22px; margin: 16px; border-radius: 14px; }
    }
  </style>
</head>
<body>

  <div class="login-card">

    <!-- Dark mode toggle -->
    <button class="mode-toggle" id="modeToggle" title="Toggle dark mode" type="button">
      <i class="bi bi-moon-stars-fill" id="modeIcon"></i>
    </button>

    <h2 class="brand-title">Travelers Place</h2>
    <p class="subtitle">Sign in to your admin account</p>

    <form method="POST" autocomplete="off">
      <div class="input-wrap">
        <i class="bi bi-person input-icon"></i>
        <input name="admin_name" required type="text" placeholder="Username or Email" autocomplete="username">
      </div>

      <div class="input-wrap">
        <i class="bi bi-lock input-icon"></i>
        <input name="admin_pass" required type="password" id="adminPass" placeholder="Password" autocomplete="current-password">
        <span class="toggle-pass" onclick="togglePass()" title="Show/hide password">
          <i class="bi bi-eye" id="eyeIcon"></i>
        </span>
      </div>

      <a href="forgot_password.php" class="forgot-link">Forgot password?</a>

      <button name="login" type="submit" class="btn-login">SIGN IN</button>
    </form>

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

    // Dark mode toggle with localStorage persistence
    const body = document.body;
    const modeToggle = document.getElementById('modeToggle');
    const modeIcon = document.getElementById('modeIcon');

    function applyDark(on) {
      if (on) {
        body.classList.add('dark-mode');
        modeIcon.classList.replace('bi-moon-stars-fill', 'bi-sun-fill');
      } else {
        body.classList.remove('dark-mode');
        modeIcon.classList.replace('bi-sun-fill', 'bi-moon-stars-fill');
      }
    }

    // Load saved preference
    applyDark(localStorage.getItem('adminDarkMode') === '1');

    modeToggle.addEventListener('click', function() {
      const isDark = body.classList.contains('dark-mode');
      applyDark(!isDark);
      localStorage.setItem('adminDarkMode', isDark ? '0' : '1');
    });
  </script>

  <?php require('inc/scripts.php') ?>
</body>
</html>
