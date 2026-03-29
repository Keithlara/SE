<?php
  if (session_status() === PHP_SESSION_NONE) { session_start(); }
  require('inc/essentials.php');
  require('inc/db_config.php');
  require('inc/admin_users_table.php');

  ensureAdminUsersTable();

  if(isset($_SESSION['adminLogin']) && $_SESSION['adminLogin']==true){
    redirect('dashboard.php');
  }

  $token = trim($_GET['token'] ?? '');
  $email = trim($_GET['email'] ?? '');
  $message = '';
  $msg_type = 'danger';
  $valid_token = false;
  $user_row = null;

  if($token === '' || $email === ''){
    $message = 'Invalid or missing reset link. Please request a new one.';
  } else {
    $res = select(
      "SELECT `id`,`username` FROM `admin_users`
       WHERE `email`=? AND `reset_token`=? AND `reset_expires` > NOW() LIMIT 1",
      [$email, $token], 'ss'
    );
    if($res && $res->num_rows === 1){
      $valid_token = true;
      $user_row = mysqli_fetch_assoc($res);
    } else {
      $message = 'This reset link is invalid or has expired. Please request a new one.';
    }
  }

  if($valid_token && isset($_POST['do_reset'])){
    $new_pass  = $_POST['new_pass'] ?? '';
    $conf_pass = $_POST['conf_pass'] ?? '';
    if(strlen($new_pass) < 6){
      $message = 'Password must be at least 6 characters.';
      $msg_type = 'danger';
    } else if($new_pass !== $conf_pass){
      $message = 'Passwords do not match.';
      $msg_type = 'danger';
    } else {
      $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
      $upd = "UPDATE `admin_users` SET `password`=?, `reset_token`=NULL, `reset_expires`=NULL WHERE `id`=?";
      update($upd, [$hashed, $user_row['id']], 'si');
      $message = 'Password reset successfully! You can now log in.';
      $msg_type = 'success';
      $valid_token = false;
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin – Reset Password</title>
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
      <h4 class="mb-0">RESET PASSWORD</h4>
    </div>
    <div class="p-4">
      <?php if($message): ?>
        <div class="alert alert-<?php echo $msg_type; ?> text-start"><?php echo htmlspecialchars($message); ?></div>
      <?php endif; ?>

      <?php if($valid_token): ?>
      <p class="text-muted small mb-3">Hello <strong><?php echo htmlspecialchars($user_row['username']); ?></strong>, enter a new password below.</p>
      <form method="POST" action="reset_admin_password.php?token=<?php echo urlencode($token); ?>&email=<?php echo urlencode($email); ?>">
        <div class="mb-3">
          <input name="new_pass" type="password" required minlength="6" class="form-control shadow-none text-center" placeholder="New password">
        </div>
        <div class="mb-3">
          <input name="conf_pass" type="password" required minlength="6" class="form-control shadow-none text-center" placeholder="Confirm new password">
        </div>
        <button name="do_reset" type="submit" class="btn btn-dark shadow-none w-100">Set New Password</button>
      </form>
      <?php elseif($msg_type === 'success'): ?>
        <a href="index.php" class="btn btn-dark shadow-none w-100">Go to Login</a>
      <?php else: ?>
        <a href="forgot_password.php" class="btn btn-dark shadow-none w-100">Request New Link</a>
      <?php endif; ?>

      <div class="mt-3">
        <a href="index.php" class="text-secondary small text-decoration-none">&larr; Back to Login</a>
      </div>
    </div>
  </div>

  <?php require('inc/scripts.php') ?>
</body>
</html>
