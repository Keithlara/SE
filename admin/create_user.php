<?php
  require('inc/essentials.php');
  require('inc/db_config.php');
  require('inc/admin_users_table.php');
  requireAdminRole();

  $message = null;
  $message_type = 'error';

  if(!ensureAdminUsersTable()){
    $message = 'Database table `admin_users` is missing and could not be created automatically.';
    $message_type = 'error';
  }

  if(isset($_POST['create_user'])){
    $frm_data = filteration($_POST);
    $username  = $frm_data['username'] ?? '';
    $password  = $_POST['password'] ?? '';
    $role      = $frm_data['role'] ?? '';
    $email     = trim($frm_data['email'] ?? '');

    $valid = true;

    if($username === '' || $password === ''){
      $message = 'Please fill in all required fields.';
      $valid = false;
    }
    else if(!in_array($role, ['admin','staff'], true)){
      $message = 'Invalid role selected.';
      $valid = false;
    }
    else if($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)){
      $message = 'Please enter a valid email address.';
      $valid = false;
    }
    else if(!ensureAdminUsersTable()){
      $message = 'Database table `admin_users` is missing and could not be created automatically.';
      $valid = false;
    }

    if($valid){
      $check_r = select("SELECT `id` FROM `admin_users` WHERE `username`=? LIMIT 1", [$username], "s");
      if($check_r && $check_r->num_rows > 0){
        $message = 'Username already exists. Please choose another.';
        $valid = false;
      }
    }

    if($valid && $email !== ''){
      $email_r = select("SELECT `id` FROM `admin_users` WHERE `email`=? LIMIT 1", [$email], "s");
      if($email_r && $email_r->num_rows > 0){
        $message = 'That email address is already used by another admin account.';
        $valid = false;
      }
    }

    if($valid){
      $hashed    = password_hash($password, PASSWORD_DEFAULT);
      $email_val = $email !== '' ? $email : null;
      $ins_q     = "INSERT INTO `admin_users` (`username`,`password`,`role`,`email`,`created_at`) VALUES (?,?,?,?,CURRENT_TIMESTAMP)";
      $ins_r     = insert($ins_q, [$username,$hashed,$role,$email_val], "ssss");

      if($ins_r == 1){
        $message      = 'System user created successfully.';
        $message_type = 'success';
      }
      else{
        $message = 'Failed to create user. Please try again.';
      }
    }
    else{
      $message_type = 'error';
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Create System User</title>
  <?php require('inc/links.php'); ?>
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4 overflow-hidden">
        <h3 class="mb-4">CREATE SYSTEM USER</h3>

        <?php if($message){ alert($message_type, $message); } ?>

        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <form method="POST" autocomplete="off">
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label class="form-label">Username <span class="text-danger">*</span></label>
                  <input name="username" type="text" class="form-control shadow-none" required
                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                </div>

                <div class="col-md-4 mb-3">
                  <label class="form-label">Email <span class="text-muted small">(for login &amp; password reset)</span></label>
                  <input name="email" type="email" class="form-control shadow-none"
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
                    placeholder="admin@example.com">
                </div>

                <div class="col-md-4 mb-3">
                  <label class="form-label">Password <span class="text-danger">*</span></label>
                  <input name="password" type="password" class="form-control shadow-none" required>
                </div>

                <div class="col-md-4 mb-3">
                  <label class="form-label">Role <span class="text-danger">*</span></label>
                  <select name="role" class="form-select shadow-none" required>
                    <option value="" disabled <?php echo (empty($_POST['role']) ? 'selected' : '') ?>>Select role</option>
                    <option value="admin" <?php echo (($_POST['role'] ?? '') === 'admin' ? 'selected' : '') ?>>Admin</option>
                    <option value="staff" <?php echo (($_POST['role'] ?? '') === 'staff' ? 'selected' : '') ?>>Staff</option>
                  </select>
                </div>

                <div class="col-12">
                  <button name="create_user" type="submit" class="btn btn-dark shadow-none">Create User</button>
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
