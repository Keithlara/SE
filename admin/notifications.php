<?php
  require('inc/essentials.php');
  require('inc/db_config.php');
  adminLogin();
  requireAdminPermission('support.manage');

  if (function_exists('ensureAppSchema')) {
    ensureAppSchema();
  }

  $search = trim((string)($_GET['search'] ?? ''));
  $like = '%' . $search . '%';

  $notifications = [];
  $res = select(
    "SELECT n.*, uc.name AS guest_name, bo.order_id
     FROM `notifications` n
     LEFT JOIN `user_cred` uc ON uc.id = n.user_id
     LEFT JOIN `booking_order` bo ON bo.booking_id = n.booking_id
     WHERE n.is_archived = 0
       AND (n.message LIKE ? OR n.type LIKE ? OR COALESCE(uc.name,'') LIKE ? OR COALESCE(bo.order_id,'') LIKE ?)
     ORDER BY n.created_at DESC, n.id DESC",
    [$like, $like, $like, $like],
    'ssss'
  );

  while ($row = mysqli_fetch_assoc($res)) {
    $notifications[] = $row;
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Notifications</title>
  <?php require('inc/links.php'); ?>
</head>
<body class="bg-light">
  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4 overflow-hidden">
        <h3 class="mb-4">NOTIFICATIONS</h3>

        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">
            <form class="mb-4">
              <div class="row g-2">
                <div class="col-md-5 ms-auto">
                  <input type="text" name="search" class="form-control shadow-none" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search guest, type, booking, or message...">
                </div>
                <div class="col-md-auto">
                  <button class="btn btn-dark shadow-none" type="submit">Search</button>
                </div>
              </div>
            </form>

            <div class="table-responsive">
              <table class="table table-hover border align-middle">
                <thead>
                  <tr class="bg-dark text-light">
                    <th>#</th>
                    <th>Guest</th>
                    <th>Booking</th>
                    <th>Message</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($notifications)): ?>
                    <tr>
                      <td colspan="7" class="text-center py-4 text-muted">No notifications found.</td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($notifications as $index => $notification): ?>
                      <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($notification['guest_name'] ?: ('User #' . (int)$notification['user_id'])); ?></td>
                        <td><?php echo !empty($notification['order_id']) ? htmlspecialchars($notification['order_id']) : '-'; ?></td>
                        <td style="min-width: 320px;"><?php echo nl2br(htmlspecialchars($notification['message'])); ?></td>
                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars(ucfirst($notification['type'])); ?></span></td>
                        <td><?php echo date('M d, Y h:i A', strtotime($notification['created_at'])); ?></td>
                        <td>
                          <button type="button" class="btn btn-warning btn-sm shadow-none" onclick="archiveNotification(<?php echo (int)$notification['id']; ?>)">
                            <i class="bi bi-archive-fill"></i>
                          </button>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php require('inc/scripts.php'); ?>
  <script>
    function archiveNotification(id) {
      Swal.fire({
        title: 'Archive notification?',
        text: 'This notification will move to Archives and can be restored later.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, archive it',
        cancelButtonText: 'Cancel',
        reverseButtons: true
      }).then((result) => {
        if (!result.isConfirmed) return;

        const formData = new FormData();
        formData.append('action', 'archive');
        formData.append('type', 'notification');
        formData.append('id', id);

        fetch('ajax/archive.php', { method: 'POST', body: formData })
          .then(r => r.json())
          .then(data => {
            if (data.status === 'success') {
              Swal.fire({ icon: 'success', title: 'Archived', text: data.message, timer: 1800, showConfirmButton: false })
                .then(() => window.location.reload());
            } else {
              Swal.fire('Error', data.message || 'Failed to archive notification.', 'error');
            }
          })
          .catch(() => Swal.fire('Error', 'Failed to archive notification.', 'error'));
      });
    }
  </script>
</body>
</html>
