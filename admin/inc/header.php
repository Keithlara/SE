<?php
  $admin_role = $_SESSION['adminRole'] ?? 'admin';
  $admin_name = $_SESSION['adminName'] ?? 'Admin';
  $admin_new_bookings_count = 0;
  $admin_refund_requests_count = 0;
  $admin_support_count = 0;
  $is_super_admin = ($admin_role === 'admin');

  $can_dashboard = function_exists('currentAdminCan') ? currentAdminCan('dashboard.view') : true;
  $can_bookings = function_exists('currentAdminCan') ? currentAdminCan('bookings.manage') : true;
  $can_calendar = function_exists('currentAdminCan') ? currentAdminCan('calendar.manage') : true;
  $can_support = function_exists('currentAdminCan') ? currentAdminCan('support.manage') : true;
  $can_reports = function_exists('currentAdminCan') ? currentAdminCan('reports.view') : true;
  $can_email_logs = function_exists('currentAdminCan') ? currentAdminCan('email_logs.view') : true;
  $can_users = $is_super_admin && (function_exists('currentAdminCan') ? currentAdminCan('users.manage') : true);
  $can_permissions = $is_super_admin && (function_exists('currentAdminCan') ? currentAdminCan('permissions.manage') : true);
  $can_content = $is_super_admin && (function_exists('currentAdminCan') ? currentAdminCan('content.manage') : true);
  $can_promos = $is_super_admin && (function_exists('currentAdminCan') ? currentAdminCan('promos.manage') : true);
  $can_utilities = $is_super_admin && (function_exists('currentAdminCan') ? currentAdminCan('utilities.manage') : true);

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

    if(function_exists('getSupportTicketUnreadCountForAdmin')){
      $admin_support_count = getSupportTicketUnreadCountForAdmin();
    }
  }

  $admin_total_attention_count =
    (($can_bookings ? $admin_new_bookings_count + $admin_refund_requests_count : 0)) +
    ($can_support ? $admin_support_count : 0);

  function is_active($pages){
    $cur = basename($_SERVER['PHP_SELF']);
    return in_array($cur, (array)$pages) ? 'active' : '';
  }

  // Groups for auto-expand
  $grp_bookings  = ['new_bookings.php','refund_bookings.php','booking_records.php','booking_calendar.php'];
  $grp_service   = ['support_center.php','user_queries.php'];
  $grp_reports   = ['all_time_reports.php','transaction.php'];
  $grp_access    = ['users.php','manage_users.php','create_user.php','staff_permissions.php'];
  $grp_content   = ['rooms.php','features_facilities.php','extras.php','carousel.php','rate_review.php','promo_codes.php'];
  $grp_utilities = ['archives.php','Archives.php','backup_restore.php','activity_logs.php','settings.php','change_password.php','manual.php'];

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
      <?php if($can_bookings): ?>
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
      <?php endif; ?>
      <?php if($can_support): ?>
        <a href="support_center.php" class="admin-alert-link" title="Unread support tickets">
          <i class="bi bi-life-preserver"></i>
          <span>Support</span>
          <?php if($admin_support_count > 0): ?>
            <span class="admin-count-badge"><?php echo $admin_support_count; ?></span>
          <?php endif; ?>
        </a>
      <?php endif; ?>
    </div>
    <div class="dropdown" data-bs-auto-close="outside">
      <button class="profile-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        <div class="profile-avatar"><i class="bi bi-person-fill"></i></div>
        <span class="profile-name d-none d-md-inline"><?php echo htmlspecialchars($admin_name); ?></span>
        <span class="role-badge"><?php echo ucfirst($admin_role); ?></span>
        <?php if($admin_total_attention_count > 0): ?>
          <span class="admin-profile-count"><?php echo $admin_total_attention_count; ?></span>
        <?php endif; ?>
      </button>
      <div class="dropdown-menu dropdown-menu-end profile-dropdown theme-dropdown-menu">
        <h6 class="dropdown-header">Signed in as <strong><?php echo htmlspecialchars($admin_name); ?></strong></h6>
        <hr class="dropdown-divider">
        <?php if($can_bookings): ?>
          <a class="dropdown-item d-flex justify-content-between align-items-center" href="new_bookings.php">
            <span><i class="bi bi-calendar-plus me-2"></i>New Bookings</span>
            <?php if($admin_new_bookings_count > 0): ?>
              <span class="admin-count-badge"><?php echo $admin_new_bookings_count; ?></span>
            <?php endif; ?>
          </a>
          <a class="dropdown-item d-flex justify-content-between align-items-center" href="refund_bookings.php">
            <span><i class="bi bi-arrow-counterclockwise me-2"></i>Refund Requests</span>
            <?php if($admin_refund_requests_count > 0): ?>
              <span class="admin-count-badge"><?php echo $admin_refund_requests_count; ?></span>
            <?php endif; ?>
          </a>
        <?php endif; ?>
        <?php if($can_support): ?>
          <a class="dropdown-item d-flex justify-content-between align-items-center" href="support_center.php">
            <span><i class="bi bi-life-preserver me-2"></i>Service Center</span>
            <?php if($admin_support_count > 0): ?>
              <span class="admin-count-badge"><?php echo $admin_support_count; ?></span>
            <?php endif; ?>
          </a>
        <?php endif; ?>
        <hr class="dropdown-divider">

        <div class="profile-inline-panel" id="profileAppearancePanel">
          <button type="button" class="dropdown-item profile-inline-toggle" id="profileAppearanceToggle" onclick="toggleAdminAppearancePanel(event)" aria-expanded="false">
            <span class="profile-inline-main">
              <i class="bi bi-palette me-2"></i>Appearance
            </span>
            <span class="profile-inline-meta" id="profileThemeSummary">Light · Ocean Blue</span>
            <i class="bi bi-chevron-down profile-inline-chevron"></i>
          </button>
          <div class="profile-inline-body" id="profileAppearanceBody">
            <div class="profile-inline-section-label">Mode</div>
            <div class="theme-mode-grid compact-mode-grid">
              <button type="button" class="theme-mode-choice compact-mode-choice" data-admin-mode-choice="light" aria-pressed="false">
                <i class="bi bi-sun-fill"></i>
                <span>Light</span>
              </button>
              <button type="button" class="theme-mode-choice compact-mode-choice" data-admin-mode-choice="dark" aria-pressed="false">
                <i class="bi bi-moon-stars-fill"></i>
                <span>Dark</span>
              </button>
            </div>

            <div class="profile-inline-section-label mt-2">Theme</div>
            <div class="theme-color-grid compact-theme-grid">
              <button type="button" class="theme-color-choice compact-theme-choice" data-admin-theme-choice="ocean" aria-pressed="false">
                <span class="theme-color-swatch ocean"></span>
                <span class="compact-theme-name">Ocean</span>
              </button>
              <button type="button" class="theme-color-choice compact-theme-choice" data-admin-theme-choice="emerald" aria-pressed="false">
                <span class="theme-color-swatch emerald"></span>
                <span class="compact-theme-name">Emerald</span>
              </button>
              <button type="button" class="theme-color-choice compact-theme-choice" data-admin-theme-choice="sunset" aria-pressed="false">
                <span class="theme-color-swatch sunset"></span>
                <span class="compact-theme-name">Sunset</span>
              </button>
              <button type="button" class="theme-color-choice compact-theme-choice" data-admin-theme-choice="plum" aria-pressed="false">
                <span class="theme-color-swatch plum"></span>
                <span class="compact-theme-name">Plum</span>
              </button>
            </div>
          </div>
        </div>

        <hr class="dropdown-divider">
        <a class="dropdown-item" href="change_password.php"><i class="bi bi-key me-2"></i>Change Password</a>
        <a class="dropdown-item text-danger" href="logout.php" id="admin-logout"><i class="bi bi-box-arrow-right me-2"></i>Log Out</a>
      </div>
    </div>
  </div>
</nav>

<!-- SIDEBAR -->
<div id="dashboard-menu" class="admin-sidebar">
  <div class="sidebar-inner">

    <nav class="sidebar-nav">

      <!-- Dashboard — always visible direct link -->
      <?php if($can_dashboard): ?>
        <a href="dashboard.php" class="sidebar-link <?php echo is_active('dashboard.php'); ?>">
          <i class="bi bi-speedometer2"></i><span>Dashboard</span>
        </a>
      <?php endif; ?>

      <?php if($can_bookings || $can_calendar): ?>
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
            <?php if($can_bookings): ?>
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
            <?php endif; ?>
            <?php if($can_calendar): ?>
              <a href="booking_calendar.php" class="sidebar-sublink <?php echo is_active('booking_calendar.php'); ?>">
                <i class="bi bi-calendar3"></i><span>Booking Calendar</span>
              </a>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>

      <?php if($can_support): ?>
        <button class="sidebar-grp-btn <?php echo grp_btn_active($grp_service); ?>"
                type="button" data-bs-toggle="collapse" data-bs-target="#grp-service"
                aria-expanded="<?php echo grp_open($grp_service) ? 'true' : 'false'; ?>">
          <i class="bi bi-life-preserver"></i>
          <span>Service</span>
          <?php if($admin_support_count > 0): ?>
            <span class="admin-count-badge"><?php echo $admin_support_count; ?></span>
          <?php endif; ?>
          <i class="bi bi-chevron-down chevron"></i>
        </button>
        <div class="collapse <?php echo grp_open($grp_service); ?>" id="grp-service">
          <div class="sidebar-subnav">
            <a href="support_center.php" class="sidebar-sublink <?php echo is_active('support_center.php'); ?>">
              <i class="bi bi-headset"></i><span>Service Center</span>
              <?php if($admin_support_count > 0): ?>
                <span class="admin-count-badge"><?php echo $admin_support_count; ?></span>
              <?php endif; ?>
            </a>
          </div>
        </div>
      <?php endif; ?>

      <?php if($can_reports): ?>
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
      <?php endif; ?>

      <?php if($can_users || $can_permissions): ?>
        <button class="sidebar-grp-btn <?php echo grp_btn_active($grp_access); ?>"
                type="button" data-bs-toggle="collapse" data-bs-target="#grp-access"
                aria-expanded="<?php echo grp_open($grp_access) ? 'true' : 'false'; ?>">
          <i class="bi bi-people"></i>
          <span>Access</span>
          <i class="bi bi-chevron-down chevron"></i>
        </button>
        <div class="collapse <?php echo grp_open($grp_access); ?>" id="grp-access">
          <div class="sidebar-subnav">
            <?php if($can_users): ?>
              <a href="users.php" class="sidebar-sublink <?php echo is_active('users.php'); ?>">
                <i class="bi bi-person-lines-fill"></i><span>User Accounts</span>
              </a>
              <a href="manage_users.php" class="sidebar-sublink <?php echo is_active(['manage_users.php','create_user.php']); ?>">
                <i class="bi bi-shield-person"></i><span>System Users</span>
              </a>
            <?php endif; ?>
            <?php if($can_permissions): ?>
              <a href="staff_permissions.php" class="sidebar-sublink <?php echo is_active('staff_permissions.php'); ?>">
                <i class="bi bi-sliders"></i><span>Staff Permissions</span>
              </a>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>

      <?php if($can_content || $can_promos): ?>
        <button class="sidebar-grp-btn <?php echo grp_btn_active($grp_content); ?>"
                type="button" data-bs-toggle="collapse" data-bs-target="#grp-content"
                aria-expanded="<?php echo grp_open($grp_content) ? 'true' : 'false'; ?>">
          <i class="bi bi-grid"></i>
          <span>Content</span>
          <i class="bi bi-chevron-down chevron"></i>
        </button>
        <div class="collapse <?php echo grp_open($grp_content); ?>" id="grp-content">
          <div class="sidebar-subnav">
            <?php if($can_content): ?>
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
            <?php endif; ?>
            <?php if($can_promos): ?>
              <a href="promo_codes.php" class="sidebar-sublink <?php echo is_active('promo_codes.php'); ?>">
                <i class="bi bi-tags"></i><span>Promo Codes</span>
              </a>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>

      <?php if($can_utilities): ?>
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
            <a href="manual.php" class="sidebar-sublink <?php echo is_active('manual.php'); ?>">
              <i class="bi bi-journal-bookmark"></i><span>Admin Manual</span>
            </a>
            <a href="settings.php" class="sidebar-sublink <?php echo is_active('settings.php'); ?>">
              <i class="bi bi-gear"></i><span>Settings</span>
            </a>
          </div>
        </div>
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
  function closeAdminAppearancePanel(){
    const panel = document.getElementById('profileAppearancePanel');
    const body = document.getElementById('profileAppearanceBody');
    const toggle = document.getElementById('profileAppearanceToggle');
    if(panel) panel.classList.remove('is-open');
    if(body) body.classList.remove('is-open');
    if(toggle) toggle.setAttribute('aria-expanded', 'false');
  }

  function toggleAdminAppearancePanel(event){
    if(event){
      event.preventDefault();
      event.stopPropagation();
    }

    const panel = document.getElementById('profileAppearancePanel');
    const body = document.getElementById('profileAppearanceBody');
    const toggle = document.getElementById('profileAppearanceToggle');
    if(!panel || !body || !toggle) return false;

    const shouldOpen = !panel.classList.contains('is-open');
    panel.classList.toggle('is-open', shouldOpen);
    body.classList.toggle('is-open', shouldOpen);
    toggle.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
    return false;
  }

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

    document.querySelectorAll('.profile-dropdown .dropdown-item, .profile-dropdown .theme-mode-choice, .profile-dropdown .theme-color-choice').forEach(function(item){
      item.addEventListener('click', function(event){
        if(event.target.closest('#profileAppearanceToggle') || event.target.closest('#profileAppearanceBody')){
          event.stopPropagation();
        }
      });
    });

    document.querySelectorAll('.admin-top-navbar .dropdown[data-bs-auto-close="outside"]').forEach(function(dropdown){
      dropdown.addEventListener('hide.bs.dropdown', closeAdminAppearancePanel);
    });
  })();
</script>
