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
  <title>Admin Login Panel</title>
  <?php require('inc/links.php'); ?>
  <style>
    div.login-form{
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%,-50%);
      width: 420px;
    }
  </style>
</head>
<body class="bg-light">
  
  <div class="login-form text-center rounded bg-white shadow overflow-hidden">
    <form method="POST">
      <h4 class="bg-dark text-white py-3">ADMIN LOGIN PANEL</h4>
      <div class="p-4">
        <div class="mb-3">
          <input name="admin_name" required type="text" class="form-control shadow-none text-center" placeholder="Username or Email">
        </div>
        <div class="mb-3">
          <input name="admin_pass" required type="password" class="form-control shadow-none text-center" placeholder="Password">
        </div>
        <div class="mb-4 text-end">
          <a href="forgot_password.php" class="text-secondary small text-decoration-none">Forgot password?</a>
        </div>
        <button name="login" type="submit" class="btn text-white custom-bg shadow-none">LOGIN</button>
      </div>
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


  <?php require('inc/scripts.php') ?>
</body>
</html>
