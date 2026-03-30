<?php
  require('inc/essentials.php');
  require('inc/db_config.php');
  require('inc/admin_users_table.php');
  adminLogin();

  $message = null;
  $message_type = 'error';

  if(isset($_POST['change_pass'])){
    $current = $_POST['current_password'] ?? '';
    $new     = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if($current === '' || $new === '' || $confirm === ''){
      $message = 'Please fill in all fields.'; $message_type = 'error';
    } else if($new !== $confirm){
      $message = 'New password and confirm password do not match.'; $message_type = 'error';
    } else {
      $auth_source = $_SESSION['adminAuthSource'] ?? 'admin_users';
      if($auth_source === 'admin_cred'){
        $res = select("SELECT `admin_pass` FROM `admin_cred` WHERE `sr_no`=? LIMIT 1", [$_SESSION['adminId']], "i");
        if($res && $res->num_rows === 1){
          $row = mysqli_fetch_assoc($res);
          if($current !== $row['admin_pass']){ $message = 'Current password is incorrect.'; $message_type = 'error'; }
          else{
            $ok = update("UPDATE `admin_cred` SET `admin_pass`=? WHERE `sr_no`=? LIMIT 1", [$new, $_SESSION['adminId']], "si");
            $message = ($ok === 1) ? 'Password updated successfully.' : 'No changes made.';
            $message_type = ($ok === 1) ? 'success' : 'error';
          }
        } else { $message = 'Account not found.'; }
      } else {
        $res = select("SELECT `password` FROM `admin_users` WHERE `id`=? LIMIT 1", [$_SESSION['adminId']], "i");
        if($res && $res->num_rows === 1){
          $row = mysqli_fetch_assoc($res);
          if(!password_verify($current, $row['password'])){ $message = 'Current password is incorrect.'; $message_type = 'error'; }
          else{
            $hashed = password_hash($new, PASSWORD_DEFAULT);
            $ok = update("UPDATE `admin_users` SET `password`=? WHERE `id`=? LIMIT 1", [$hashed, $_SESSION['adminId']], "si");
            $message = ($ok === 1) ? 'Password updated successfully.' : 'Update failed.';
            $message_type = ($ok === 1) ? 'success' : 'error';
          }
        } else { $message = 'Account not found.'; }
      }
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Change Password</title>
  <?php require('inc/links.php'); ?>
</head>
<body>
  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="p-4">
      <h3 class="mb-4"><i class="bi bi-key me-2 text-primary"></i>Change Password</h3>

      <?php if($message){ alert($message_type, $message); } ?>

      <div class="card">
        <div class="card-body">
          <form method="POST" autocomplete="off">
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label fw-semibold">Current Password</label>
                <input name="current_password" type="password" class="form-control shadow-none" required>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">New Password</label>
                <input name="new_password" type="password" class="form-control shadow-none" required>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">Confirm New Password</label>
                <input name="confirm_password" type="password" class="form-control shadow-none" required>
              </div>
              <div class="col-12">
                <button name="change_pass" type="submit" class="btn btn-primary shadow-none">
                  <i class="bi bi-key me-1"></i> Update Password
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <?php require('inc/scripts.php'); ?>
</body>
</html>
