<?php
  // Session is started by auto_prepend_file before any output
  if (!(isset($_SESSION['login']) && $_SESSION['login'] === true) || !isset($_SESSION['uId'])) {
    header('Location: index.php');
    exit;
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('inc/links.php'); ?>
  <title>Notifications - <?php echo htmlspecialchars($settings_r['site_title'] ?? 'Travelers Place'); ?></title>
  <style>
    body { padding-top: 70px; }

    .notif-page-item {
      display: flex;
      align-items: flex-start;
      gap: 14px;
      padding: 16px 20px;
      border-radius: 12px;
      background: #fff;
      margin-bottom: 10px;
      border: 1px solid #e9ecef;
      transition: background 0.2s, box-shadow 0.2s;
      cursor: pointer;
    }
    .notif-page-item:hover {
      background: #f8f9fa;
      box-shadow: 0 2px 10px rgba(0,0,0,0.07);
    }
    .notif-page-item.unread {
      background: linear-gradient(135deg, #f0f4ff 0%, #e8f0fe 100%);
      border-left: 4px solid #0d6efd;
    }
    .notif-icon-circle {
      width: 46px;
      height: 46px;
      border-radius: 50%;
      background: rgba(13,110,253,0.10);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.25rem;
      flex-shrink: 0;
    }
    .notif-body { flex: 1; min-width: 0; }
    .notif-msg {
      font-size: 0.97rem;
      color: #212529;
      line-height: 1.5;
      word-break: break-word;
    }
    .notif-meta {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-top: 5px;
      flex-wrap: wrap;
    }
    .notif-time { font-size: 0.8rem; color: #6c757d; }
    .notif-status-badge {
      font-size: 0.72rem;
      padding: 2px 9px;
      border-radius: 20px;
      font-weight: 600;
      text-transform: capitalize;
    }
    .notif-unread-dot {
      width: 9px;
      height: 9px;
      border-radius: 50%;
      background: #0d6efd;
      flex-shrink: 0;
      margin-top: 6px;
    }
    .empty-state {
      text-align: center;
      padding: 60px 20px;
      color: #6c757d;
    }
    .empty-state i { font-size: 3.5rem; margin-bottom: 15px; display: block; }
    #notif-loading { text-align: center; padding: 40px; color: #6c757d; }
  </style>
</head>
<body>
  <?php require('inc/header.php'); ?>

  <div class="container py-4" style="max-width: 760px;">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
      <div>
        <h4 class="mb-0 fw-bold">
          <i class="bi bi-bell-fill me-2 text-primary"></i>Notifications
        </h4>
        <small class="text-muted" id="notif-summary">Loading...</small>
      </div>
      <button id="mark-all-read-btn" class="btn btn-outline-primary btn-sm shadow-none"
              style="display:none;" onclick="markAllNotificationsAsRead()">
        <i class="bi bi-check-all me-1"></i>Mark All as Read
      </button>
    </div>

    <div id="notifications-list">
      <div id="notif-loading">
        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
        Loading notifications...
      </div>
    </div>
  </div>

  <?php require('inc/footer.php'); ?>
</body>
</html>
