<?php
require('admin/inc/db_config.php');
require('admin/inc/essentials.php');

$tokenValid  = false;
$tokenEmail  = '';
$tokenValue  = '';
$errorMsg    = '';
$siteName    = defined('SITE_NAME') ? SITE_NAME : 'Travelers Place';

if (isset($_GET['account_recovery'], $_GET['email'], $_GET['token'])) {
    $data   = filteration($_GET);
    $tEmail = $data['email'];
    $tToken = $data['token'];
    $today  = date('Y-m-d');

    $query = select(
        "SELECT `id` FROM `user_cred` WHERE `email`=? AND `token`=? AND `t_expire`>=? AND `status`=1 LIMIT 1",
        [$tEmail, $tToken, $today],
        'sss'
    );

    if (mysqli_num_rows($query) === 1) {
        $tokenValid = true;
        $tokenEmail = $tEmail;
        $tokenValue = $tToken;
    } else {
        $errorMsg = 'This password reset link is invalid or has expired. Please request a new one.';
    }
} else {
    $errorMsg = 'Invalid request. No reset token found.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password - <?php echo htmlspecialchars($siteName); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body { background: #f3f4f6; min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Poppins', Arial, sans-serif; }
    .card { max-width: 460px; width: 100%; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.10); border: none; }
    .card-header { background: #1a1a2e; padding: 28px 32px; text-align: center; }
    .card-header h1 { color: #c8a951; margin: 0; font-size: 24px; font-weight: 700; }
    .card-header p  { color: #d1d5db; margin: 4px 0 0; font-size: 13px; }
    .card-body { padding: 36px 32px; background: #fff; }
    .form-label { font-weight: 500; color: #374151; }
    .form-control:focus { border-color: #c8a951; box-shadow: 0 0 0 3px rgba(200,169,81,0.18); }
    .btn-submit { background: #1a1a2e; color: #c8a951; border: none; border-radius: 8px; padding: 10px 0; font-weight: 600; width: 100%; font-size: 15px; transition: opacity .2s; }
    .btn-submit:hover { opacity: .85; color: #c8a951; }
    .back-link { display: block; text-align: center; margin-top: 16px; color: #6b7280; font-size: 14px; text-decoration: none; }
    .back-link:hover { color: #111827; }
    .icon-circle { width: 64px; height: 64px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 16px; }
    .icon-error { background: #fee2e2; color: #991b1b; }
  </style>
</head>
<body>
  <div class="card">
    <div class="card-header">
      <h1><?php echo htmlspecialchars($siteName); ?></h1>
      <p>Reset Your Password</p>
    </div>
    <div class="card-body">
      <?php if ($tokenValid): ?>
        <p class="text-muted mb-4" style="font-size:14px;">Enter your new password below. Make sure it's at least 8 characters long.</p>
        <form id="reset-form">
          <input type="hidden" name="email"  value="<?php echo htmlspecialchars($tokenEmail); ?>">
          <input type="hidden" name="token"  value="<?php echo htmlspecialchars($tokenValue); ?>">
          <input type="hidden" name="recover_user">
          <div class="mb-3">
            <label class="form-label">New Password</label>
            <input type="password" name="pass" id="newPass" required minlength="8" class="form-control shadow-none" autocomplete="new-password">
          </div>
          <div class="mb-4">
            <label class="form-label">Confirm New Password</label>
            <input type="password" name="cpass" id="confPass" required minlength="8" class="form-control shadow-none" autocomplete="new-password">
          </div>
          <button type="submit" class="btn-submit">RESET PASSWORD</button>
        </form>
        <a href="index.php" class="back-link"><i class="bi bi-arrow-left me-1"></i>Back to Homepage</a>
      <?php else: ?>
        <div class="text-center">
          <div class="icon-circle icon-error mx-auto"><i class="bi bi-x-lg"></i></div>
          <h5 class="mb-2" style="color:#991b1b;">Link Invalid or Expired</h5>
          <p class="text-muted" style="font-size:14px;"><?php echo htmlspecialchars($errorMsg); ?></p>
          <a href="index.php" class="btn-submit text-decoration-none d-inline-block mt-2" style="width:auto;padding:10px 28px;">Back to Homepage</a>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const resetForm = document.getElementById('reset-form');
    if (resetForm) {
      resetForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const pass  = document.getElementById('newPass').value;
        const cpass = document.getElementById('confPass').value;

        if (pass !== cpass) {
          Swal.fire({ icon: 'error', title: 'Passwords do not match', text: 'Please make sure both passwords are the same.', confirmButtonColor: '#1a1a2e' });
          return;
        }
        if (pass.length < 8) {
          Swal.fire({ icon: 'error', title: 'Password too short', text: 'Password must be at least 8 characters.', confirmButtonColor: '#1a1a2e' });
          return;
        }

        const data = new FormData(resetForm);
        const btn  = resetForm.querySelector('button[type=submit]');
        btn.disabled = true;
        btn.textContent = 'Resetting...';

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'ajax/login_register.php', true);
        xhr.onload = function () {
          btn.disabled = false;
          btn.textContent = 'RESET PASSWORD';

          const responseText = this.responseText.trim();

          if (responseText === '1') {
            Swal.fire({
              icon: 'success',
              title: 'Password Reset Successful!',
              text: 'Your password has been updated. You can now log in with your new password.',
              confirmButtonColor: '#1a1a2e',
              confirmButtonText: 'Go to Homepage'
            }).then(() => { window.location.href = 'index.php'; });
          } else if (responseText === 'pass_mismatch') {
            Swal.fire({ icon: 'error', title: 'Passwords do not match', text: 'Please make sure both passwords are the same.', confirmButtonColor: '#1a1a2e' });
          } else if (responseText === 'invalid_token') {
            Swal.fire({ icon: 'error', title: 'Reset Link Expired', text: 'This reset link is no longer valid. Please request a new password reset email.', confirmButtonColor: '#1a1a2e' });
          } else {
            Swal.fire({ icon: 'error', title: 'Reset Failed', text: responseText || 'Your reset link may have expired. Please request a new one.', confirmButtonColor: '#1a1a2e' });
          }
        };
        xhr.onerror = function () {
          btn.disabled = false;
          btn.textContent = 'RESET PASSWORD';
          Swal.fire({ icon: 'error', title: 'Network Error', text: 'Could not connect to the server. Please try again.', confirmButtonColor: '#1a1a2e' });
        };
        xhr.send(data);
      });
    }
  </script>
</body>
</html>
