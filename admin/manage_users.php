<?php
  require('inc/essentials.php');
  require('inc/db_config.php');
  require('inc/admin_users_table.php');
  requireAdminRole();

  $message = null;
  $message_type = 'error';
  $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'list';

  if(!ensureAdminUsersTable()){
    $message = 'Database table `admin_users` is missing and could not be created automatically.';
    $message_type = 'error';
  }

  // ── DELETE ──────────────────────────────────────────────────────────────────
  if(isset($_POST['delete_user'])){
    $user_id = (int)($_POST['user_id'] ?? 0);
    $current_id = (int)($_SESSION['adminId'] ?? 0);
    if($user_id <= 0){ $message = 'Invalid user selected.'; }
    else if($user_id === $current_id){ $message = 'You cannot delete your own account.'; }
    else{
      $r = select("SELECT `id`,`role` FROM `admin_users` WHERE `id`=? LIMIT 1", [$user_id], "i");
      if(!$r || $r->num_rows !== 1){ $message = 'Account not found.'; }
      else{
        $row = mysqli_fetch_assoc($r);
        if(($row['role'] ?? '') !== 'staff'){ $message = 'Only staff accounts can be deleted.'; }
        else{
          $ok = delete("DELETE FROM `admin_users` WHERE `id`=? LIMIT 1", [$user_id], "i");
          $message = ($ok === 1) ? 'Staff account deleted successfully.' : 'Delete failed.';
          $message_type = ($ok === 1) ? 'success' : 'error';
        }
      }
    }
    $active_tab = 'list';
  }

  // ── EDIT ─────────────────────────────────────────────────────────────────────
  if(isset($_POST['edit_user'])){
    $frm_data = filteration($_POST);
    $user_id  = (int)($frm_data['user_id'] ?? 0);
    $username = $frm_data['username'] ?? '';
    $role     = $frm_data['role'] ?? '';
    $new_pass = $_POST['password'] ?? '';

    if($user_id <= 0 || $username === ''){ $message = 'Please provide a valid username.'; }
    else if(!in_array($role, ['admin','staff'], true)){ $message = 'Invalid role selected.'; }
    else{
      $exists = select("SELECT `id` FROM `admin_users` WHERE `id`=? LIMIT 1", [$user_id], "i");
      if(!$exists || $exists->num_rows !== 1){ $message = 'Account not found.'; }
      else{
        $dup = select("SELECT `id` FROM `admin_users` WHERE `username`=? AND `id`<>? LIMIT 1", [$username,$user_id], "si");
        if($dup && $dup->num_rows > 0){ $message = 'Username already exists.'; }
        else{
          if(trim($new_pass) !== ''){
            $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
            $ok = update("UPDATE `admin_users` SET `username`=?, `password`=?, `role`=? WHERE `id`=? LIMIT 1",
              [$username,$hashed,$role,$user_id], "sssi");
          } else {
            $ok = update("UPDATE `admin_users` SET `username`=?, `role`=? WHERE `id`=? LIMIT 1",
              [$username,$role,$user_id], "ssi");
          }
          $message = ($ok === 1) ? 'Account updated successfully.' : 'No changes made or update failed.';
          $message_type = ($ok === 1) ? 'success' : 'error';
        }
      }
    }
    $active_tab = 'list';
  }

  // ── CREATE ───────────────────────────────────────────────────────────────────
  if(isset($_POST['create_user'])){
    $frm_data = filteration($_POST);
    $username = $frm_data['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $role     = $frm_data['role'] ?? '';
    $email    = trim($frm_data['email'] ?? '');
    $valid    = true;

    if($username === '' || $password === ''){ $message = 'Please fill in all required fields.'; $valid = false; }
    else if(!in_array($role, ['admin','staff'], true)){ $message = 'Invalid role selected.'; $valid = false; }
    else if($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)){ $message = 'Please enter a valid email address.'; $valid = false; }

    if($valid){
      $check_r = select("SELECT `id` FROM `admin_users` WHERE `username`=? LIMIT 1", [$username], "s");
      if($check_r && $check_r->num_rows > 0){ $message = 'Username already exists.'; $valid = false; }
    }
    if($valid && $email !== ''){
      $email_r = select("SELECT `id` FROM `admin_users` WHERE `email`=? LIMIT 1", [$email], "s");
      if($email_r && $email_r->num_rows > 0){ $message = 'Email already used by another account.'; $valid = false; }
    }

    if($valid){
      $hashed    = password_hash($password, PASSWORD_DEFAULT);
      $email_val = $email !== '' ? $email : null;
      $ins_r = insert("INSERT INTO `admin_users` (`username`,`password`,`role`,`email`,`created_at`) VALUES (?,?,?,?,CURRENT_TIMESTAMP)",
        [$username,$hashed,$role,$email_val], "ssss");
      $message = ($ins_r == 1) ? "System user \"$username\" created successfully." : 'Failed to create user.';
      $message_type = ($ins_r == 1) ? 'success' : 'error';
    } else { $message_type = 'error'; }
    $active_tab = ($message_type === 'success') ? 'list' : 'create';
  }

  // ── CHANGE PASSWORD ───────────────────────────────────────────────────────────
  if(isset($_POST['change_pass'])){
    $current = $_POST['current_password'] ?? '';
    $new     = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if($current === '' || $new === '' || $confirm === ''){ $message = 'Please fill in all fields.'; $message_type = 'error'; }
    else if($new !== $confirm){ $message = 'New password and confirm password do not match.'; $message_type = 'error'; }
    else{
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
        } else { $message = 'Account not found.'; $message_type = 'error'; }
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
        } else { $message = 'Account not found.'; $message_type = 'error'; }
      }
    }
    $active_tab = 'password';
  }

  // ── FETCH USER LIST ───────────────────────────────────────────────────────────
  $admin_count = 0;
  $staff_count = 0;
  $users = [];

  if(ensureAdminUsersTable()){
    $stats = mysqli_query($con, "SELECT `role`, COUNT(*) AS c FROM `admin_users` GROUP BY `role`");
    if($stats){
      while($s = mysqli_fetch_assoc($stats)){
        if($s['role'] === 'admin') $admin_count = (int)$s['c'];
        if($s['role'] === 'staff') $staff_count = (int)$s['c'];
      }
    }
    $res = mysqli_query($con, "SELECT `id`,`username`,`role`,`email`,`created_at` FROM `admin_users` ORDER BY `created_at` DESC");
    if($res){ while($row = mysqli_fetch_assoc($res)) $users[] = $row; }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Users Management</title>
  <?php require('inc/links.php'); ?>
</head>
<body>

  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="p-4">

      <!-- Page Header -->
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
        <div>
    <h3 class="mb-1"><i class="bi bi-person-badge me-2 text-primary"></i>Users Management</h3>
          <p class="text-muted mb-0" style="font-size:0.85rem;">Manage system users, create accounts, and update passwords.</p>
        </div>
        <div class="d-flex gap-2">
          <div class="stat-chip">
            <span class="stat-num"><?php echo $admin_count; ?></span>
            <span class="stat-label">Admins</span>
          </div>
          <div class="stat-chip stat-chip-blue">
            <span class="stat-num"><?php echo $staff_count; ?></span>
            <span class="stat-label">Staff</span>
          </div>
        </div>
      </div>

      <?php if($message){ alert($message_type, $message); } ?>

      <!-- Card with Tabs -->
      <div class="card">
        <div class="card-body">

          <!-- Tabs -->
          <ul class="nav nav-tabs mb-0" id="userTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link <?php echo ($active_tab==='list')?'active':''; ?>" data-bs-toggle="tab" data-bs-target="#tab-list" type="button" role="tab">
                <i class="bi bi-people me-1"></i> User List
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link <?php echo ($active_tab==='create')?'active':''; ?>" data-bs-toggle="tab" data-bs-target="#tab-create" type="button" role="tab">
                <i class="bi bi-person-plus me-1"></i> Create User
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link <?php echo ($active_tab==='password')?'active':''; ?>" data-bs-toggle="tab" data-bs-target="#tab-password" type="button" role="tab">
                <i class="bi bi-key me-1"></i> Change Password
              </button>
            </li>
          </ul>

          <!-- Tab Content -->
          <div class="tab-content pt-3" id="userTabsContent">

            <!-- TAB 1: User List -->
            <div class="tab-pane fade <?php echo ($active_tab==='list')?'show active':''; ?>" id="tab-list" role="tabpanel">
              <div class="d-flex align-items-center gap-2 mb-3">
                <div class="flex-grow-1">
                  <input id="userSearch" type="text" class="form-control shadow-none" placeholder="Search by username...">
                </div>
                <div class="text-muted small text-nowrap">
                  <span id="visibleCount"><?php echo count($users); ?></span> of <?php echo count($users); ?> accounts
                </div>
              </div>

              <div class="table-responsive">
                <table class="table table-hover border-0 align-middle" id="usersTable">
                  <thead>
                    <tr>
                      <th style="width:60px;">ID</th>
                      <th>Username</th>
                      <th>Email</th>
                      <th style="width:110px;">Role</th>
                      <th style="width:190px;">Created</th>
                      <th style="width:180px;">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if(count($users) === 0): ?>
                      <tr><td colspan="6" class="text-center text-muted py-4">No accounts found.</td></tr>
                    <?php else: ?>
                      <?php foreach($users as $u): ?>
                        <?php
                          $uid      = (int)($u['id'] ?? 0);
                          $uname    = $u['username'] ?? '';
                          $uemail   = $u['email'] ?? '';
                          $urole    = $u['role'] ?? '';
                          $created  = $u['created_at'] ?? '';
                          $is_self  = ($uid === (int)($_SESSION['adminId'] ?? 0));
                          $role_badge = ($urole === 'admin')
                            ? '<span class="badge bg-danger">Admin</span>'
                            : '<span class="badge bg-primary">Staff</span>';
                          $can_delete = (!$is_self && $urole === 'staff');
                        ?>
                        <tr data-username="<?php echo htmlspecialchars(strtolower($uname)); ?>">
                          <td class="text-muted"><?php echo $uid; ?></td>
                          <td class="fw-semibold"><?php echo htmlspecialchars($uname); ?>
                            <?php if($is_self): ?><span class="badge bg-secondary ms-1" style="font-size:0.7rem;">You</span><?php endif; ?>
                          </td>
                          <td class="text-muted"><?php echo $uemail ? htmlspecialchars($uemail) : '<span class="text-muted">—</span>'; ?></td>
                          <td><?php echo $role_badge; ?></td>
                          <td class="text-muted" style="font-size:0.82rem;"><?php echo htmlspecialchars($created); ?></td>
                          <td>
                            <button type="button" class="btn btn-sm btn-outline-primary shadow-none me-1"
                              data-bs-toggle="modal" data-bs-target="#editUserModal"
                              data-user-id="<?php echo $uid; ?>"
                              data-username="<?php echo htmlspecialchars($uname); ?>"
                              data-role="<?php echo htmlspecialchars($urole); ?>">
                              <i class="bi bi-pencil"></i> Edit
                            </button>
                            <form method="POST" class="d-inline delete-user-form" data-username="<?php echo htmlspecialchars($uname); ?>">
                              <input type="hidden" name="user_id" value="<?php echo $uid; ?>">
                              <button type="submit" name="delete_user"
                                class="btn btn-sm btn-outline-danger shadow-none"
                                <?php echo $can_delete ? '' : 'disabled'; ?>>
                                <i class="bi bi-trash"></i>
                              </button>
                            </form>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
              <p class="text-muted small mt-2">Only staff accounts can be deleted. You cannot delete your current account.</p>
            </div>

            <!-- TAB 2: Create User -->
            <div class="tab-pane fade <?php echo ($active_tab==='create')?'show active':''; ?>" id="tab-create" role="tabpanel">
              <form method="POST" autocomplete="off">
                <div class="row g-3">
                  <div class="col-md-4">
                    <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                    <input name="username" type="text" class="form-control shadow-none" required
                      value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label fw-semibold">Email <span class="text-muted fw-normal">(optional)</span></label>
                    <input name="email" type="email" class="form-control shadow-none" placeholder="admin@example.com"
                      value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                    <input name="password" type="password" class="form-control shadow-none" required>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                    <select name="role" class="form-select shadow-none" required>
                      <option value="" disabled <?php echo (empty($_POST['role']) ? 'selected' : ''); ?>>Select role</option>
                      <option value="admin" <?php echo (($_POST['role'] ?? '') === 'admin' ? 'selected' : ''); ?>>Admin</option>
                      <option value="staff" <?php echo (($_POST['role'] ?? '') === 'staff' ? 'selected' : ''); ?>>Staff</option>
                    </select>
                  </div>
                  <div class="col-12 mt-2">
                    <button name="create_user" type="submit" class="btn btn-primary shadow-none">
                      <i class="bi bi-person-plus me-1"></i> Create User
                    </button>
                  </div>
                </div>
              </form>
            </div>

            <!-- TAB 3: Change Password -->
            <div class="tab-pane fade <?php echo ($active_tab==='password')?'show active':''; ?>" id="tab-password" role="tabpanel">
              <p class="text-muted mb-3" style="font-size:0.88rem;">Change your own admin password. Leave new password fields blank to cancel.</p>
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
                  <div class="col-12 mt-2">
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

    </div>
  </div>

  <!-- Edit Modal -->
  <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow">
        <form method="POST" autocomplete="off">
          <div class="modal-header border-0 pb-0">
            <h5 class="modal-title fw-bold">Edit Account</h5>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="user_id" id="edit_user_id">
            <div class="mb-3">
              <label class="form-label fw-semibold">Username</label>
              <input type="text" class="form-control shadow-none" name="username" id="edit_username" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Role</label>
              <select class="form-select shadow-none" name="role" id="edit_role" required>
                <option value="admin">Admin</option>
                <option value="staff">Staff</option>
              </select>
            </div>
            <div class="mb-1">
              <label class="form-label fw-semibold">New Password <span class="text-muted fw-normal">(leave blank to keep)</span></label>
              <input type="password" class="form-control shadow-none" name="password" id="edit_password">
            </div>
          </div>
          <div class="modal-footer border-0 pt-0">
            <button type="button" class="btn btn-light shadow-none" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" name="edit_user" class="btn btn-primary shadow-none">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <style>
    .stat-chip {
      display: flex; flex-direction: column; align-items: center;
      background: #fee2e2; color: #b91c1c;
      border-radius: 10px; padding: 8px 18px; min-width: 70px;
    }
    .stat-chip-blue { background: #dbeafe; color: #1d4ed8; }
    .stat-num { font-size: 1.4rem; font-weight: 700; line-height: 1; }
    .stat-label { font-size: 0.72rem; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase; }
  </style>

  <?php require('inc/scripts.php'); ?>
  <script>
    // Fill edit modal
    document.getElementById('editUserModal').addEventListener('show.bs.modal', function(e){
      const btn = e.relatedTarget;
      document.getElementById('edit_user_id').value = btn.getAttribute('data-user-id') || '';
      document.getElementById('edit_username').value = btn.getAttribute('data-username') || '';
      document.getElementById('edit_role').value = btn.getAttribute('data-role') || 'staff';
      document.getElementById('edit_password').value = '';
    });

    // Search filter
    const searchInput = document.getElementById('userSearch');
    const usersTable  = document.getElementById('usersTable');
    const visibleEl   = document.getElementById('visibleCount');

    function applyFilter(){
      const q = (searchInput.value || '').trim().toLowerCase();
      let visible = 0;
      Array.from(usersTable.querySelectorAll('tbody tr')).forEach(tr => {
        if(!tr.hasAttribute('data-username')) return;
        const show = (q === '' || tr.getAttribute('data-username').includes(q));
        tr.style.display = show ? '' : 'none';
        if(show) visible++;
      });
      if(visibleEl) visibleEl.textContent = String(visible);
    }
    if(searchInput) searchInput.addEventListener('input', applyFilter);

    // Delete confirmation
    document.addEventListener('submit', function(e){
      const form = e.target.closest('.delete-user-form');
      if(!form) return;
      const btn = form.querySelector('button[type="submit"]');
      if(btn && btn.disabled) return;
      e.preventDefault();
      const uname = form.getAttribute('data-username') || 'this staff account';
      confirmDelete(`Delete staff account "${uname}"? This cannot be undone.`, () => form.submit());
    });
  </script>
</body>
</html>
