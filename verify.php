<?php
require('admin/inc/db_config.php');
require('admin/inc/essentials.php');

$status  = '';
$message = '';

if (isset($_GET['email_confirmation'])) {
    $data = filteration($_GET);

    $query = select(
        "SELECT * FROM `user_cred` WHERE `email`=? AND `token`=? LIMIT 1",
        [$data['email'], $data['token']],
        'ss'
    );

    if (mysqli_num_rows($query) === 1) {
        $fetch = mysqli_fetch_assoc($query);

        if ($fetch['is_verified'] == 1) {
            $status  = 'info';
            $message = 'Your email is already verified. You can log in now.';
        } else {
            $updated = update(
                "UPDATE `user_cred` SET `is_verified`=1, `token`=NULL WHERE `id`=?",
                [$fetch['id']],
                'i'
            );
            if ($updated) {
                $status  = 'success';
                $message = 'Email verified successfully! Your account is now active. You can log in now.';
            } else {
                $status  = 'error';
                $message = 'Verification failed due to a server error. Please try again or contact support.';
            }
        }
    } else {
        $status  = 'error';
        $message = 'Invalid or expired verification link. Please register again or contact support.';
    }
} else {
    $status  = 'error';
    $message = 'Invalid request.';
}

$siteName = defined('SITE_NAME') ? SITE_NAME : 'Travelers Place';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Email Verification – <?php echo htmlspecialchars($siteName); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
  <style>
    body { background: #f3f4f6; min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Poppins', Arial, sans-serif; }
    .card { max-width: 480px; width: 100%; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.10); border: none; }
    .card-header { background: #1a1a2e; padding: 28px 32px; text-align: center; }
    .card-header h1 { color: #c8a951; margin: 0; font-size: 24px; font-weight: 700; }
    .card-header p  { color: #d1d5db; margin: 4px 0 0; font-size: 13px; }
    .card-body { padding: 40px 32px; background: #fff; text-align: center; }
    .icon-circle { width: 72px; height: 72px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 32px; margin-bottom: 20px; }
    .icon-success { background: #d1fae5; color: #065f46; }
    .icon-error   { background: #fee2e2; color: #991b1b; }
    .icon-info    { background: #dbeafe; color: #1e40af; }
    h2 { font-size: 20px; font-weight: 600; color: #111827; margin-bottom: 10px; }
    p  { color: #4b5563; font-size: 15px; }
    .btn-home { background: #1a1a2e; color: #c8a951; border: none; border-radius: 8px; padding: 10px 28px; font-weight: 600; text-decoration: none; display: inline-block; margin-top: 12px; transition: opacity .2s; }
    .btn-home:hover { opacity: .85; color: #c8a951; }
  </style>
</head>
<body>
  <div class="card">
    <div class="card-header">
      <h1><?php echo htmlspecialchars($siteName); ?></h1>
      <p>Email Verification</p>
    </div>
    <div class="card-body">
      <?php if ($status === 'success'): ?>
        <div class="icon-circle icon-success"><i class="bi bi-check-lg"></i></div>
        <h2>Verification Successful!</h2>
        <p><?php echo htmlspecialchars($message); ?></p>
      <?php elseif ($status === 'info'): ?>
        <div class="icon-circle icon-info"><i class="bi bi-info-lg"></i></div>
        <h2>Already Verified</h2>
        <p><?php echo htmlspecialchars($message); ?></p>
      <?php else: ?>
        <div class="icon-circle icon-error"><i class="bi bi-x-lg"></i></div>
        <h2>Verification Failed</h2>
        <p><?php echo htmlspecialchars($message); ?></p>
      <?php endif; ?>
      <a href="index.php" class="btn-home">Go to Homepage</a>
    </div>
  </div>
</body>
</html>
