<?php
  require('inc/essentials.php');
  require('inc/db_config.php');
  require('inc/admin_users_table.php');
  requireAdminRole();
  requireAdminPermission('permissions.manage');

  $message = '';
  $message_type = 'success';

  if (isset($_POST['save_permissions'])) {
    $staff_id = (int)($_POST['staff_id'] ?? 0);
    $codes = $_POST['permissions'] ?? [];

    $staff_res = select("SELECT `id`,`role`,`username` FROM `admin_users` WHERE `id`=? LIMIT 1", [$staff_id], 'i');
    if ($staff_res && mysqli_num_rows($staff_res) === 1) {
      $staff = mysqli_fetch_assoc($staff_res);
      if (($staff['role'] ?? '') !== 'staff') {
        $message = 'Only staff accounts can be updated here.';
        $message_type = 'error';
      } else {
        if (saveAdminPermissionAssignments($staff_id, is_array($codes) ? $codes : [])) {
          $message = 'Permissions updated for ' . $staff['username'] . '.';
        } else {
          $message = 'Unable to update staff permissions.';
          $message_type = 'error';
        }
      }
    } else {
      $message = 'Staff account not found.';
      $message_type = 'error';
    }
  }

  $permission_catalog = systemPermissionCatalog();
  $staff_users = [];
  $res = mysqli_query($con, "SELECT `id`,`username`,`email`,`created_at` FROM `admin_users` WHERE `role`='staff' ORDER BY `created_at` DESC");
  while ($res && $row = mysqli_fetch_assoc($res)) {
    $row['permissions'] = getAdminAssignedPermissions((int)$row['id']);
    if (empty($row['permissions'])) {
      $row['permissions'] = defaultStaffPermissions();
    }
    $staff_users[] = $row;
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Staff Permissions</title>
  <?php require('inc/links.php'); ?>
  <style>
    .perm-card { border-radius: 18px; border: 1px solid rgba(148,163,184,0.18); background: rgba(255,255,255,0.96); box-shadow: 0 14px 32px rgba(15,23,42,0.05); }
    .perm-item { border: 1px solid rgba(148,163,184,0.18); border-radius: 14px; padding: 12px 14px; background: rgba(255,255,255,0.92); }
  </style>
</head>
<body class="bg-light">
  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="p-4">
      <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
          <h3 class="mb-1">Staff Permissions</h3>
          <p class="text-muted mb-0">Give staff only the modules they actually need, without exposing the whole panel.</p>
        </div>
      </div>

      <?php if ($message !== '') { alert($message_type, $message); } ?>

      <div class="d-flex flex-column gap-4">
        <?php foreach ($staff_users as $staff): ?>
          <div class="perm-card p-4">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
              <div>
                <h5 class="mb-1"><?php echo htmlspecialchars($staff['username']); ?></h5>
                <div class="text-muted small"><?php echo htmlspecialchars($staff['email'] ?: 'No email'); ?> • Created <?php echo date('M d, Y', strtotime($staff['created_at'])); ?></div>
              </div>
              <span class="badge bg-primary rounded-pill"><?php echo count($staff['permissions']); ?> modules enabled</span>
            </div>
            <form method="POST">
              <input type="hidden" name="staff_id" value="<?php echo (int)$staff['id']; ?>">
              <div class="row g-3">
                <?php foreach ($permission_catalog as $code => $config): ?>
                  <div class="col-md-6 col-xl-4">
                    <label class="perm-item d-flex gap-3 align-items-start">
                      <input class="form-check-input mt-1" type="checkbox" name="permissions[]" value="<?php echo htmlspecialchars($code); ?>" <?php echo in_array($code, $staff['permissions'], true) ? 'checked' : ''; ?>>
                      <span>
                        <span class="fw-semibold d-block"><?php echo htmlspecialchars($config['label']); ?></span>
                        <span class="small text-muted"><?php echo htmlspecialchars($config['description']); ?></span>
                      </span>
                    </label>
                  </div>
                <?php endforeach; ?>
              </div>
              <div class="mt-3">
                <button type="submit" class="btn btn-primary shadow-none" name="save_permissions">Save Permissions</button>
              </div>
            </form>
          </div>
        <?php endforeach; ?>
        <?php if (empty($staff_users)): ?>
          <div class="perm-card p-4 text-muted">No staff accounts found yet.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <?php require('inc/scripts.php'); ?>
</body>
</html>
