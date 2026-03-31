<?php
  $admin_role = $_SESSION['adminRole'] ?? 'admin';
  $admin_name = $_SESSION['adminName'] ?? 'Admin';
  $admin_new_bookings_count = 0;
  $admin_refund_requests_count = 0;

  if(isset($con) && $con instanceof mysqli){
    $adminCountsRes = mysqli_query($con, "
      SELECT
        COUNT(CASE WHEN booking_status='pending' THEN 1 END) AS pending_bookings,
        COUNT(CASE WHEN booking_status='cancelled' AND refund=0 THEN 1 END) AS pending_refunds
      FROM booking_order
    ");
    if($adminCountsRes){
      $adminCounts = mysqli_fetch_assoc($adminCountsRes);
      $admin_new_bookings_count = (int)($adminCounts['pending_bookings'] ?? 0);
      $admin_refund_requests_count = (int)($adminCounts['pending_refunds'] ?? 0);
    }
  }

  $admin_total_attention_count = $admin_new_bookings_count + $admin_refund_requests_count;

  function is_active($pages){
    $cur = basename($_SERVER['PHP_SELF']);
    return in_array($cur, (array)$pages) ? 'active' : '';
  }

  // Groups for auto-expand
  $grp_bookings  = ['new_bookings.php','refund_bookings.php','booking_records.php'];
  $grp_reports   = ['all_time_reports.php','transaction.php'];
  $grp_users     = ['users.php','user_queries.php','manage_users.php','create_user.php','change_password.php'];
  $grp_content   = ['rooms.php','features_facilities.php','extras.php','carousel.php','rate_review.php'];
  $grp_utilities = ['archives.php','Archives.php','backup_restore.php','activity_logs.php','settings.php'];

  function grp_open($pages){
    return in_array(basename($_SERVER['PHP_SELF']), $pages) ? 'show' : '';
  }
  function grp_btn_active($pages){
    return in_array(basename($_SERVER['PHP_SELF']), $pages) ? 'grp-active' : '';
  }
?>

<!-- TOP NAVBAR -->
<nav class="admin-top-navbar">
  <div class="top-navbar-left">
    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
      <i class="bi bi-list"></i>
    </button>
    <span class="brand-name h-font">Travelers Place</span>
  </div>
  <div class="top-navbar-right">
    <div class="admin-alert-links d-none d-md-flex">
      <a href="new_bookings.php" class="admin-alert-link" title="Pending new bookings">
        <i class="bi bi-calendar-plus"></i>
        <span>Bookings</span>
        <?php if($admin_new_bookings_count > 0): ?>
          <span class="admin-count-badge"><?php echo $admin_new_bookings_count; ?></span>
        <?php endif; ?>
      </a>
      <a href="refund_bookings.php" class="admin-alert-link" title="Pending refund requests">
        <i class="bi bi-arrow-counterclockwise"></i>
        <span>Refunds</span>
        <?php if($admin_refund_requests_count > 0): ?>
          <span class="admin-count-badge"><?php echo $admin_refund_requests_count; ?></span>
        <?php endif; ?>
      </a>
    </div>
    <div class="dropdown">
      <button class="theme-picker-btn dropdown-toggle" type="button" id="adminThemePicker" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="theme-current-pill">
          <span class="theme-current-swatch"></span>
          <span class="theme-current-label d-none d-xl-inline" id="currentThemeLabel">Ocean Blue</span>
        </span>
      </button>
      <div class="dropdown-menu dropdown-menu-end theme-dropdown-menu" aria-labelledby="adminThemePicker">
        <div class="theme-dropdown-section">
          <div class="theme-dropdown-title">Mode</div>
          <div class="theme-dropdown-subtitle">Switch between a bright workspace and a darker after-hours view.</div>
          <div class="theme-mode-grid">
            <button type="button" class="theme-mode-choice" data-admin-mode-choice="light" aria-pressed="false">
              <i class="bi bi-sun-fill"></i>
              <span>Light</span>
            </button>
            <button type="button" class="theme-mode-choice" data-admin-mode-choice="dark" aria-pressed="false">
              <i class="bi bi-moon-stars-fill"></i>
              <span>Dark</span>
            </button>
          </div>
        </div>

        <div class="theme-dropdown-section">
          <div class="theme-dropdown-title">Color Theme</div>
          <div class="theme-dropdown-subtitle">Pick the accent and shell color set for the admin and staff workspace.</div>
          <div class="theme-color-grid">
            <button type="button" class="theme-color-choice" data-admin-theme-choice="ocean" aria-pressed="false">
              <span class="theme-color-swatch ocean"></span>
              <span class="theme-color-copy">
                <span class="theme-color-name">Ocean Blue</span>
                <span class="theme-color-note">Clean and balanced default</span>
              </span>
            </button>
            <button type="button" class="theme-color-choice" data-admin-theme-choice="emerald" aria-pressed="false">
              <span class="theme-color-swatch emerald"></span>
              <span class="theme-color-copy">
                <span class="theme-color-name">Emerald</span>
                <span class="theme-color-note">Fresh and calmer on the eyes</span>
              </span>
            </button>
            <button type="button" class="theme-color-choice" data-admin-theme-choice="sunset" aria-pressed="false">
              <span class="theme-color-swatch sunset"></span>
              <span class="theme-color-copy">
                <span class="theme-color-name">Sunset</span>
                <span class="theme-color-note">Warm and more energetic</span>
              </span>
            </button>
            <button type="button" class="theme-color-choice" data-admin-theme-choice="plum" aria-pressed="false">
              <span class="theme-color-swatch plum"></span>
              <span class="theme-color-copy">
                <span class="theme-color-name">Plum</span>
                <span class="theme-color-note">Cool violet with a softer contrast</span>
              </span>
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="dropdown">
      <button class="profile-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        <div class="profile-avatar"><i class="bi bi-person-fill"></i></div>
        <span class="profile-name d-none d-md-inline"><?php echo htmlspecialchars($admin_name); ?></span>
        <span class="role-badge"><?php echo ucfirst($admin_role); ?></span>
        <?php if($admin_total_attention_count > 0): ?>
          <span class="admin-profile-count"><?php echo $admin_total_attention_count; ?></span>
        <?php endif; ?>
      </button>
      <ul class="dropdown-menu dropdown-menu-end profile-dropdown">
        <li><h6 class="dropdown-header">Signed in as <strong><?php echo htmlspecialchars($admin_name); ?></strong></h6></li>
        <li><hr class="dropdown-divider"></li>
        <li>
          <a class="dropdown-item d-flex justify-content-between align-items-center" href="new_bookings.php">
            <span><i class="bi bi-calendar-plus me-2"></i>New Bookings</span>
            <?php if($admin_new_bookings_count > 0): ?>
              <span class="admin-count-badge"><?php echo $admin_new_bookings_count; ?></span>
            <?php endif; ?>
          </a>
        </li>
        <li>
          <a class="dropdown-item d-flex justify-content-between align-items-center" href="refund_bookings.php">
            <span><i class="bi bi-arrow-counterclockwise me-2"></i>Refund Requests</span>
            <?php if($admin_refund_requests_count > 0): ?>
              <span class="admin-count-badge"><?php echo $admin_refund_requests_count; ?></span>
            <?php endif; ?>
          </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="change_password.php"><i class="bi bi-key me-2"></i>Change Password</a></li>
        <li><a class="dropdown-item text-danger" href="logout.php" id="admin-logout"><i class="bi bi-box-arrow-right me-2"></i>Log Out</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- SIDEBAR -->
<div id="dashboard-menu" class="admin-sidebar">
  <div class="sidebar-inner">

    <nav class="sidebar-nav">

      <!-- Dashboard — always visible direct link -->
      <a href="dashboard.php" class="sidebar-link <?php echo is_active('dashboard.php'); ?>">
        <i class="bi bi-speedometer2"></i><span>Dashboard</span>
      </a>

      <!-- BOOKINGS GROUP -->
      <button class="sidebar-grp-btn <?php echo grp_btn_active($grp_bookings); ?>"
              type="button" data-bs-toggle="collapse" data-bs-target="#grp-bookings"
              aria-expanded="<?php echo grp_open($grp_bookings) ? 'true' : 'false'; ?>">
        <i class="bi bi-calendar2-week"></i>
        <span>Bookings</span>
        <?php if($admin_total_attention_count > 0): ?>
          <span class="admin-count-badge"><?php echo $admin_total_attention_count; ?></span>
        <?php endif; ?>
        <i class="bi bi-chevron-down chevron"></i>
      </button>
      <div class="collapse <?php echo grp_open($grp_bookings); ?>" id="grp-bookings">
        <div class="sidebar-subnav">
          <a href="new_bookings.php" class="sidebar-sublink <?php echo is_active('new_bookings.php'); ?>">
            <i class="bi bi-calendar-plus"></i><span>New Bookings</span>
            <?php if($admin_new_bookings_count > 0): ?>
              <span class="admin-count-badge"><?php echo $admin_new_bookings_count; ?></span>
            <?php endif; ?>
          </a>
          <a href="refund_bookings.php" class="sidebar-sublink <?php echo is_active('refund_bookings.php'); ?>">
            <i class="bi bi-arrow-counterclockwise"></i><span>Refund Bookings</span>
            <?php if($admin_refund_requests_count > 0): ?>
              <span class="admin-count-badge"><?php echo $admin_refund_requests_count; ?></span>
            <?php endif; ?>
          </a>
          <a href="booking_records.php" class="sidebar-sublink <?php echo is_active('booking_records.php'); ?>">
            <i class="bi bi-journal-text"></i><span>Booking Records</span>
          </a>
        </div>
      </div>

      <!-- REPORTS GROUP -->
      <button class="sidebar-grp-btn <?php echo grp_btn_active($grp_reports); ?>"
              type="button" data-bs-toggle="collapse" data-bs-target="#grp-reports"
              aria-expanded="<?php echo grp_open($grp_reports) ? 'true' : 'false'; ?>">
        <i class="bi bi-bar-chart-line"></i>
        <span>Reports</span>
        <i class="bi bi-chevron-down chevron"></i>
      </button>
      <div class="collapse <?php echo grp_open($grp_reports); ?>" id="grp-reports">
        <div class="sidebar-subnav">
          <a href="all_time_reports.php" class="sidebar-sublink <?php echo is_active('all_time_reports.php'); ?>">
            <i class="bi bi-graph-up"></i><span>All Time Reports</span>
          </a>
          <a href="transaction.php" class="sidebar-sublink <?php echo is_active('transaction.php'); ?>">
            <i class="bi bi-receipt"></i><span>Transactions</span>
          </a>
        </div>
      </div>

      <?php if($admin_role === 'admin'): ?>

        <!-- USERS GROUP -->
        <button class="sidebar-grp-btn <?php echo grp_btn_active($grp_users); ?>"
                type="button" data-bs-toggle="collapse" data-bs-target="#grp-users"
                aria-expanded="<?php echo grp_open($grp_users) ? 'true' : 'false'; ?>">
          <i class="bi bi-people"></i>
          <span>Users</span>
          <i class="bi bi-chevron-down chevron"></i>
        </button>
        <div class="collapse <?php echo grp_open($grp_users); ?>" id="grp-users">
          <div class="sidebar-subnav">
            <a href="users.php" class="sidebar-sublink <?php echo is_active('users.php'); ?>">
              <i class="bi bi-person-lines-fill"></i><span>User Accounts</span>
            </a>
            <a href="user_queries.php" class="sidebar-sublink <?php echo is_active('user_queries.php'); ?>">
              <i class="bi bi-chat-left-text"></i><span>User Queries</span>
            </a>
            <a href="manage_users.php" class="sidebar-sublink <?php echo is_active(['manage_users.php','create_user.php']); ?>">
              <i class="bi bi-shield-person"></i><span>System Users</span>
            </a>
          </div>
        </div>

        <!-- CONTENT GROUP -->
        <button class="sidebar-grp-btn <?php echo grp_btn_active($grp_content); ?>"
                type="button" data-bs-toggle="collapse" data-bs-target="#grp-content"
                aria-expanded="<?php echo grp_open($grp_content) ? 'true' : 'false'; ?>">
          <i class="bi bi-grid"></i>
          <span>Content</span>
          <i class="bi bi-chevron-down chevron"></i>
        </button>
        <div class="collapse <?php echo grp_open($grp_content); ?>" id="grp-content">
          <div class="sidebar-subnav">
            <a href="rooms.php" class="sidebar-sublink <?php echo is_active('rooms.php'); ?>">
              <i class="bi bi-door-open"></i><span>Manage Rooms</span>
            </a>
            <a href="features_facilities.php" class="sidebar-sublink <?php echo is_active('features_facilities.php'); ?>">
              <i class="bi bi-stars"></i><span>Features & Facilities</span>
            </a>
            <a href="extras.php" class="sidebar-sublink <?php echo is_active('extras.php'); ?>">
              <i class="bi bi-plus-square"></i><span>Extras & Rules</span>
            </a>
            <a href="carousel.php" class="sidebar-sublink <?php echo is_active('carousel.php'); ?>">
              <i class="bi bi-images"></i><span>Carousel</span>
            </a>
            <a href="rate_review.php" class="sidebar-sublink <?php echo is_active('rate_review.php'); ?>">
              <i class="bi bi-star-half"></i><span>Ratings & Reviews</span>
            </a>
          </div>
        </div>

        <!-- UTILITIES GROUP -->
        <button class="sidebar-grp-btn <?php echo grp_btn_active($grp_utilities); ?>"
                type="button" data-bs-toggle="collapse" data-bs-target="#grp-utilities"
                aria-expanded="<?php echo grp_open($grp_utilities) ? 'true' : 'false'; ?>">
          <i class="bi bi-tools"></i>
          <span>Utilities</span>
          <i class="bi bi-chevron-down chevron"></i>
        </button>
        <div class="collapse <?php echo grp_open($grp_utilities); ?>" id="grp-utilities">
          <div class="sidebar-subnav">
            <a href="Archives.php" class="sidebar-sublink <?php echo is_active(['archives.php','Archives.php']); ?>">
              <i class="bi bi-archive"></i><span>Archives</span>
            </a>
            <a href="backup_restore.php" class="sidebar-sublink <?php echo is_active('backup_restore.php'); ?>">
              <i class="bi bi-hdd-rack"></i><span>Backup & Restore</span>
            </a>
            <a href="activity_logs.php" class="sidebar-sublink <?php echo is_active('activity_logs.php'); ?>">
              <i class="bi bi-list-check"></i><span>Activity Logs</span>
            </a>
            <a href="settings.php" class="sidebar-sublink <?php echo is_active('settings.php'); ?>">
              <i class="bi bi-gear"></i><span>Settings</span>
            </a>
          </div>
        </div>

      <?php else: ?>

        <!-- Staff: change password only -->
        <a href="change_password.php" class="sidebar-link <?php echo is_active('change_password.php'); ?>">
          <i class="bi bi-key"></i><span>Change Password</span>
        </a>

      <?php endif; ?>

    </nav>

    <div class="sidebar-footer">
      <a href="logout.php" id="admin-logout" class="sidebar-link sidebar-logout">
        <i class="bi bi-box-arrow-right"></i><span>Log Out</span>
      </a>
    </div>

  </div>
</div>

<!-- Overlay for mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>


<script>
  (function(){
    const toggle  = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('dashboard-menu');
    const overlay = document.getElementById('sidebarOverlay');
    if(!toggle || !sidebar) return;

    function openSidebar()  { sidebar.classList.add('open');    overlay.classList.add('open'); }
    function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('open'); }

    toggle.addEventListener('click', function(){
      sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
    });
    overlay.addEventListener('click', closeSidebar);

    // Sync aria-expanded when Bootstrap collapse fires
    sidebar.querySelectorAll('[data-bs-toggle="collapse"]').forEach(function(btn){
      const target = document.querySelector(btn.getAttribute('data-bs-target'));
      if(!target) return;
      target.addEventListener('show.bs.collapse',  function(){ btn.setAttribute('aria-expanded','true'); });
      target.addEventListener('hide.bs.collapse',  function(){ btn.setAttribute('aria-expanded','false'); });
    });
  })();
</script>
