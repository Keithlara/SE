<?php
  require('inc/essentials.php');
  require('inc/db_config.php');
  require('inc/admin_users_table.php');
  adminLogin();

  $message = null;
  $message_type = 'error';

  if(isset($_POST['change_pass'])){
    $frm_data = filteration($_POST);
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if($current === '' || $new === '' || $confirm === ''){
      $message = 'Please fill in all fields.';
      $message_type = 'error';
    }
    else if($new !== $confirm){
      $message = 'New password and confirm password do not match.';
      $message_type = 'error';
    }
    else{
      $auth_source = $_SESSION['adminAuthSource'] ?? 'admin_users';

      if($auth_source === 'admin_cred'){
        $q = "SELECT `admin_pass` FROM `admin_cred` WHERE `sr_no`=? LIMIT 1";
        $res = select($q, [$_SESSION['adminId']], "i");
        if($res && $res->num_rows === 1){
          $row = mysqli_fetch_assoc($res);
          if($current !== $row['admin_pass']){
            $message = 'Current password is incorrect.';
            $message_type = 'error';
          }
          else{
            $u = "UPDATE `admin_cred` SET `admin_pass`=? WHERE `sr_no`=? LIMIT 1";
            $ok = update($u, [$new, $_SESSION['adminId']], "si");
            if($ok === 1){
              $message = 'Password updated successfully.';
              $message_type = 'success';
            }
            else{
              $message = 'No changes made (same password) or update failed.';
              $message_type = 'error';
            }
          }
        }
        else{
          $message = 'Account not found.';
          $message_type = 'error';
        }
      }
      else{
        if(!ensureAdminUsersTable()){
          $message = 'Database table `admin_users` is missing and could not be created automatically.';
          $message_type = 'error';
        }
        else{
        $q = "SELECT `password` FROM `admin_users` WHERE `id`=? LIMIT 1";
        $res = select($q, [$_SESSION['adminId']], "i");
        if($res && $res->num_rows === 1){
          $row = mysqli_fetch_assoc($res);
          if(!password_verify($current, $row['password'])){
            $message = 'Current password is incorrect.';
            $message_type = 'error';
          }
          else{
            $hashed = password_hash($new, PASSWORD_DEFAULT);
            $u = "UPDATE `admin_users` SET `password`=? WHERE `id`=? LIMIT 1";
            $ok = update($u, [$hashed, $_SESSION['adminId']], "si");
            if($ok === 1){
              $message = 'Password updated successfully.';
              $message_type = 'success';
            }
            else{
              $message = 'No changes made or update failed.';
              $message_type = 'error';
            }
          }
        }
        else{
          $message = 'Account not found.';
          $message_type = 'error';
        }
        }
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
  <title>Admin Panel - Change Password</title>
  <?php require('inc/links.php'); ?>
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4 overflow-hidden">
        <h3 class="mb-4">CHANGE PASSWORD</h3>

        <?php
          if($message){
            alert($message_type, $message);
          }
        ?>

        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <form method="POST" autocomplete="off">
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label class="form-label">Current Password</label>
                  <input name="current_password" type="password" class="form-control shadow-none" required>
                </div>

                <div class="col-md-4 mb-3">
                  <label class="form-label">New Password</label>
                  <input name="new_password" type="password" class="form-control shadow-none" required>
                </div>

                <div class="col-md-4 mb-3">
                  <label class="form-label">Confirm New Password</label>
                  <input name="confirm_password" type="password" class="form-control shadow-none" required>
                </div>

                <div class="col-12">
                  <button name="change_pass" type="submit" class="btn btn-dark shadow-none">Update Password</button>
                </div>
              </div>
            </form>
          </div>
        </div>

      </div>
    </div>
  </div>

  <?php require('inc/scripts.php'); ?>
</body>
</html>

