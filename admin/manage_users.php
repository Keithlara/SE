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

  // Delete (staff only, never self)
  if(isset($_POST['delete_user'])){
    if(!ensureAdminUsersTable()){
      $message = 'Database table `admin_users` is missing and could not be created automatically.';
      $message_type = 'error';
    }
    else{
      $user_id = (int)($_POST['user_id'] ?? 0);
      $current_id = (int)($_SESSION['adminId'] ?? 0);

      if($user_id <= 0){
        $message = 'Invalid user selected.';
        $message_type = 'error';
      }
      else if($user_id === $current_id){
        $message = 'You cannot delete the currently logged-in account.';
        $message_type = 'error';
      }
      else{
        $r = select("SELECT `id`,`role` FROM `admin_users` WHERE `id`=? LIMIT 1", [$user_id], "i");
        if(!$r || $r->num_rows !== 1){
          $message = 'Account not found.';
          $message_type = 'error';
        }
        else{
          $row = mysqli_fetch_assoc($r);
          if(($row['role'] ?? '') !== 'staff'){
            $message = 'Only staff accounts can be deleted.';
            $message_type = 'error';
          }
          else{
            $ok = delete("DELETE FROM `admin_users` WHERE `id`=? LIMIT 1", [$user_id], "i");
            if($ok === 1){
              $message = 'Staff account deleted successfully.';
              $message_type = 'success';
            }
            else{
              $message = 'Delete failed. Please try again.';
              $message_type = 'error';
            }
          }
        }
      }
    }
  }

  // Edit (username + role required; password optional)
  if(isset($_POST['edit_user'])){
    if(!ensureAdminUsersTable()){
      $message = 'Database table `admin_users` is missing and could not be created automatically.';
      $message_type = 'error';
    }
    else{
      $frm_data = filteration($_POST);
      $user_id = (int)($frm_data['user_id'] ?? 0);
      $username = $frm_data['username'] ?? '';
      $role = $frm_data['role'] ?? '';
      $new_password = $_POST['password'] ?? '';

      if($user_id <= 0 || $username === ''){
        $message = 'Please provide a valid username.';
        $message_type = 'error';
      }
      else if(!in_array($role, ['admin','staff'], true)){
        $message = 'Invalid role selected.';
        $message_type = 'error';
      }
      else{
        // ensure account exists
        $exists = select("SELECT `id` FROM `admin_users` WHERE `id`=? LIMIT 1", [$user_id], "i");
        if(!$exists || $exists->num_rows !== 1){
          $message = 'Account not found.';
          $message_type = 'error';
        }
        else{
          // unique username (excluding same id)
          $dup = select("SELECT `id` FROM `admin_users` WHERE `username`=? AND `id`<>? LIMIT 1", [$username,$user_id], "si");
          if($dup && $dup->num_rows > 0){
            $message = 'Username already exists. Please choose another.';
            $message_type = 'error';
          }
          else{
            if(trim($new_password) !== ''){
              $hashed = password_hash($new_password, PASSWORD_DEFAULT);
              $ok = update("UPDATE `admin_users` SET `username`=?, `password`=?, `role`=? WHERE `id`=? LIMIT 1",
                [$username,$hashed,$role,$user_id],
                "sssi"
              );
            }
            else{
              $ok = update("UPDATE `admin_users` SET `username`=?, `role`=? WHERE `id`=? LIMIT 1",
                [$username,$role,$user_id],
                "ssi"
              );
            }

            if($ok === 1){
              $message = 'Account updated successfully.';
              $message_type = 'success';
            }
            else{
              $message = 'No changes made or update failed.';
              $message_type = 'error';
            }
          }
        }
      }
    }
  }

  // Stats + list
  $admin_count = 0;
  $staff_count = 0;
  $users = [];

  if(ensureAdminUsersTable()){
    $stats = mysqli_query($con, "SELECT `role`, COUNT(*) AS c FROM `admin_users` GROUP BY `role`");
    if($stats){
      while($s = mysqli_fetch_assoc($stats)){
        if(($s['role'] ?? '') === 'admin'){ $admin_count = (int)$s['c']; }
        if(($s['role'] ?? '') === 'staff'){ $staff_count = (int)$s['c']; }
      }
    }

    $res = mysqli_query($con, "SELECT `id`,`username`,`role`,`created_at` FROM `admin_users` ORDER BY `created_at` DESC");
    if($res){
      while($row = mysqli_fetch_assoc($res)){
        $users[] = $row;
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
  <title>Admin Panel - Manage System Users</title>
  <?php require('inc/links.php'); ?>
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4 overflow-hidden">

        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
          <h3 class="mb-0">MANAGE SYSTEM USERS</h3>
          <a href="create_user.php" class="btn btn-dark shadow-none">Create New Account</a>
        </div>

        <?php
          if($message){
            alert($message_type, $message);
          }
        ?>

        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <div class="card border-0 shadow-sm">
              <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                  <div class="text-muted small">Total Admin Accounts</div>
                  <div class="fs-4 fw-semibold"><?php echo (int)$admin_count; ?></div>
                </div>
                <span class="badge bg-danger">Admin</span>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card border-0 shadow-sm">
              <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                  <div class="text-muted small">Total Staff Accounts</div>
                  <div class="fs-4 fw-semibold"><?php echo (int)$staff_count; ?></div>
                </div>
                <span class="badge bg-primary">Staff</span>
              </div>
            </div>
          </div>
        </div>

        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="row align-items-center g-2 mb-3">
              <div class="col-md-6">
                <input id="userSearch" type="text" class="form-control shadow-none" placeholder="Search by username...">
              </div>
              <div class="col-md-6 text-md-end small text-muted">
                Showing <span id="visibleCount"><?php echo count($users); ?></span> of <?php echo count($users); ?> accounts
              </div>
            </div>

            <div class="table-responsive">
              <table class="table table-hover border text-center align-middle" id="usersTable">
                <thead class="bg-dark text-light">
                  <tr>
                    <th scope="col" style="width: 90px;">ID</th>
                    <th scope="col">Username</th>
                    <th scope="col" style="width: 140px;">Role</th>
                    <th scope="col" style="width: 220px;">Date Created</th>
                    <th scope="col" style="width: 200px;">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if(count($users) === 0): ?>
                    <tr>
                      <td colspan="5" class="text-muted py-4">No accounts found.</td>
                    </tr>
                  <?php else: ?>
                    <?php foreach($users as $u): ?>
                      <?php
                        $uid = (int)($u['id'] ?? 0);
                        $uname = $u['username'] ?? '';
                        $urole = $u['role'] ?? '';
                        $created = $u['created_at'] ?? '';
                        $is_self = ($uid === (int)($_SESSION['adminId'] ?? 0));
                        $role_badge = ($urole === 'admin')
                          ? '<span class="badge bg-danger">Admin</span>'
                          : '<span class="badge bg-primary">Staff</span>';
                        $can_delete = (!$is_self && $urole === 'staff');
                      ?>
                      <tr data-username="<?php echo htmlspecialchars(strtolower($uname)); ?>">
                        <td><?php echo $uid; ?></td>
                        <td class="text-break"><?php echo htmlspecialchars($uname); ?></td>
                        <td><?php echo $role_badge; ?></td>
                        <td><?php echo htmlspecialchars($created); ?></td>
                        <td>
                          <button
                            type="button"
                            class="btn btn-sm btn-outline-dark shadow-none"
                            data-bs-toggle="modal"
                            data-bs-target="#editUserModal"
                            data-user-id="<?php echo $uid; ?>"
                            data-username="<?php echo htmlspecialchars($uname); ?>"
                            data-role="<?php echo htmlspecialchars($urole); ?>"
                          >Edit</button>

                          <form method="POST" class="d-inline delete-user-form" data-username="<?php echo htmlspecialchars($uname); ?>">
                            <input type="hidden" name="user_id" value="<?php echo $uid; ?>">
                            <button
                              type="submit"
                              name="delete_user"
                              class="btn btn-sm btn-outline-danger shadow-none"
                              <?php echo $can_delete ? '' : 'disabled'; ?>
                            >Delete</button>
                          </form>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>

            <div class="small text-muted mt-2">
              Note: Only staff accounts can be deleted. You cannot delete your current account.
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Edit Modal -->
  <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <form method="POST" autocomplete="off">
          <div class="modal-header">
            <h5 class="modal-title">Edit Account</h5>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="user_id" id="edit_user_id">

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Username</label>
                <input type="text" class="form-control shadow-none" name="username" id="edit_username" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Role</label>
                <select class="form-select shadow-none" name="role" id="edit_role" required>
                  <option value="admin">Admin</option>
                  <option value="staff">Staff</option>
                </select>
              </div>
              <div class="col-12 mb-3">
                <label class="form-label">New Password (leave blank to keep existing)</label>
                <input type="password" class="form-control shadow-none" name="password" id="edit_password">
              </div>
            </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light shadow-none" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" name="edit_user" class="btn btn-dark shadow-none">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php require('inc/scripts.php'); ?>
  <script>
    // Fill edit modal
    const editModal = document.getElementById('editUserModal');
    editModal.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      document.getElementById('edit_user_id').value = button.getAttribute('data-user-id') || '';
      document.getElementById('edit_username').value = button.getAttribute('data-username') || '';
      document.getElementById('edit_role').value = button.getAttribute('data-role') || 'staff';
      document.getElementById('edit_password').value = '';
    });

    // Search filter (username)
    const searchInput = document.getElementById('userSearch');
    const table = document.getElementById('usersTable');
    const visibleCountEl = document.getElementById('visibleCount');

    function applyFilter() {
      const q = (searchInput.value || '').trim().toLowerCase();
      let visible = 0;

      Array.from(table.querySelectorAll('tbody tr')).forEach(tr => {
        // ignore "no accounts" row
        if (!tr.hasAttribute('data-username')) return;
        const u = tr.getAttribute('data-username') || '';
        const show = (q === '' || u.includes(q));
        tr.style.display = show ? '' : 'none';
        if (show) visible++;
      });

      if (visibleCountEl) visibleCountEl.textContent = String(visible);
    }

    searchInput.addEventListener('input', applyFilter);
    applyFilter();

    // SweetAlert confirmation for deleting staff accounts
    document.addEventListener('submit', function(e){
      const form = e.target.closest('.delete-user-form');
      if(!form){ return; }
      // If button is disabled, let normal validation handle it
      const submitBtn = form.querySelector('button[type="submit"]');
      if(submitBtn && submitBtn.disabled){ return; }

      e.preventDefault();
      const uname = form.getAttribute('data-username') || 'this staff account';
      confirmDelete(
        `Delete staff account "${uname}"? This action cannot be undone.`,
        function(){
          form.submit();
        }
      );
    });
  </script>
</body>
</html>

