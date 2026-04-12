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
  $permission_groups = systemPermissionGroups();
  $staff_users = [];
  $res = mysqli_query($con, "SELECT `id`,`username`,`email`,`created_at` FROM `admin_users` WHERE `role`='staff' ORDER BY `created_at` DESC");
  while ($res && $row = mysqli_fetch_assoc($res)) {
    $row['permissions_raw'] = getAdminAssignedPermissions((int)$row['id']);
    if (empty($row['permissions_raw'])) {
      $row['permissions_raw'] = defaultStaffPermissions();
    }
    $row['permissions'] = expandSystemPermissionCodes($row['permissions_raw']);
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
    .perm-item {
      display: flex;
      gap: 14px;
      align-items: flex-start;
      min-height: 100%;
      border: 1px solid rgba(148,163,184,0.18);
      border-radius: 14px;
      padding: 14px 16px;
      background: rgba(255,255,255,0.92);
      cursor: pointer;
      transition: border-color 0.18s ease, background 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
    }
    .perm-item:hover {
      border-color: rgba(var(--admin-accent-rgb), 0.34);
      box-shadow: 0 10px 24px rgba(15,23,42,0.07);
      transform: translateY(-1px);
    }
    .perm-item.is-enabled {
      border-color: rgba(var(--admin-accent-rgb), 0.42);
      background: rgba(var(--admin-accent-rgb), 0.08);
      box-shadow: 0 12px 28px rgba(var(--admin-accent-rgb), 0.12);
    }
    .perm-toggle {
      width: 1.15rem;
      height: 1.15rem;
      margin-top: 0.15rem !important;
      flex-shrink: 0;
      cursor: pointer;
    }
    .perm-toggle:checked {
      background-color: var(--admin-accent) !important;
      border-color: var(--admin-accent) !important;
    }
    .perm-meta {
      flex: 1 1 auto;
      min-width: 0;
    }
    .perm-title-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      flex-wrap: wrap;
    }
    .perm-status {
      display: inline-flex;
      align-items: center;
      padding: 0.2rem 0.65rem;
      border-radius: 999px;
      font-size: 0.72rem;
      font-weight: 600;
      background: rgba(148,163,184,0.18);
      color: var(--admin-text-muted);
    }
    .perm-item.is-enabled .perm-status {
      background: rgba(var(--admin-accent-rgb), 0.14);
      color: var(--admin-accent);
    }
    .perm-save-note {
      font-size: 0.82rem;
    }
    .perm-group {
      border: 1px solid rgba(148,163,184,0.18);
      border-radius: 16px;
      padding: 16px;
      background: rgba(248,250,252,0.75);
    }
  </style>
</head>
<body class="bg-light">
  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="p-4">
      <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
          <h3 class="mb-1">Staff Permissions</h3>
          <p class="text-muted mb-0">Give staff only the pages they actually need, without exposing the whole panel.</p>
          <p class="text-muted small mb-0 mt-1">You can now allow specific subpages under a module, such as New Bookings and Refund Bookings without giving Booking Records or Booking Calendar.</p>
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
              <span class="badge bg-primary rounded-pill" data-enabled-summary><?php echo count($staff['permissions']) === 1 ? '1 page enabled' : count($staff['permissions']) . ' pages enabled'; ?></span>
            </div>
            <form method="POST" class="permission-form">
              <input type="hidden" name="staff_id" value="<?php echo (int)$staff['id']; ?>">
              <div class="d-flex flex-column gap-3">
                <?php foreach ($permission_groups as $group): ?>
                  <div class="perm-group">
                    <div class="mb-3">
                      <h6 class="mb-1"><?php echo htmlspecialchars($group['label']); ?></h6>
                      <div class="small text-muted"><?php echo htmlspecialchars($group['description']); ?></div>
                    </div>
                    <div class="row g-3">
                      <?php foreach ($group['codes'] as $code): ?>
                        <?php if (!isset($permission_catalog[$code])) { continue; } ?>
                        <?php $config = $permission_catalog[$code]; ?>
                        <?php $is_enabled = in_array($code, $staff['permissions'], true); ?>
                        <div class="col-md-6 col-xl-4">
                          <label class="perm-item permission-option <?php echo $is_enabled ? 'is-enabled' : ''; ?>">
                            <input class="form-check-input perm-toggle" type="checkbox" name="permissions[]" value="<?php echo htmlspecialchars($code); ?>" <?php echo $is_enabled ? 'checked' : ''; ?>>
                            <span class="perm-meta">
                              <span class="perm-title-row">
                                <span class="fw-semibold d-block"><?php echo htmlspecialchars($config['label']); ?></span>
                                <span class="perm-status" data-perm-status><?php echo $is_enabled ? 'Enabled' : 'Disabled'; ?></span>
                              </span>
                              <span class="small text-muted d-block mt-1"><?php echo htmlspecialchars($config['description']); ?></span>
                            </span>
                          </label>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
              <div class="mt-3 d-flex align-items-center flex-wrap gap-2">
                <button type="submit" class="btn btn-primary shadow-none" name="save_permissions">Save Permissions</button>
                <span class="text-muted perm-save-note">Only checked modules will stay available to this staff account.</span>
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
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const forms = document.querySelectorAll('.permission-form');

      const buildSummaryText = function (count) {
        return count + ' page' + (count === 1 ? '' : 's') + ' enabled';
      };

      const syncOptionState = function (option) {
        const checkbox = option.querySelector('.perm-toggle');
        const status = option.querySelector('[data-perm-status]');
        if (!checkbox || !status) {
          return false;
        }

        const enabled = checkbox.checked;
        option.classList.toggle('is-enabled', enabled);
        status.textContent = enabled ? 'Enabled' : 'Disabled';
        return enabled;
      };

      forms.forEach(function (form) {
        const card = form.closest('.perm-card');
        const summary = card ? card.querySelector('[data-enabled-summary]') : null;
        const options = form.querySelectorAll('.permission-option');

        const syncFormState = function () {
          let enabledCount = 0;

          options.forEach(function (option) {
            if (syncOptionState(option)) {
              enabledCount += 1;
            }
          });

          if (summary) {
            summary.textContent = buildSummaryText(enabledCount);
          }
        };

        options.forEach(function (option) {
          const checkbox = option.querySelector('.perm-toggle');
          if (!checkbox) {
            return;
          }

          checkbox.addEventListener('change', syncFormState);
        });

        syncFormState();
      });
    });
  </script>
</body>
</html>
