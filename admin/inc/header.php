<?php
  $admin_role = $_SESSION['adminRole'] ?? 'admin';
  $admin_name = $_SESSION['adminName'] ?? 'Admin';
  $current_page = basename($_SERVER['PHP_SELF']);

  function is_active($pages){
    $cur = basename($_SERVER['PHP_SELF']);
    return in_array($cur, (array)$pages) ? 'active' : '';
  }
  function group_open($pages){
    $cur = basename($_SERVER['PHP_SELF']);
    return in_array($cur, (array)$pages) ? 'show' : '';
  }

  $booking_pages  = ['new_bookings.php','refund_bookings.php','booking_records.php'];
  $report_pages   = ['all_time_reports.php','transaction.php'];
  $user_pages     = ['users.php','user_queries.php','manage_users.php','create_user.php','change_password.php'];
  $content_pages  = ['rooms.php','features_facilities.php','extras.php','carousel.php','rate_review.php'];
  $utility_pages  = ['archives.php','backup_restore.php','activity_logs.php','settings.php'];
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
    <div class="dropdown">
      <button class="profile-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        <div class="profile-avatar"><i class="bi bi-person-fill"></i></div>
        <span class="profile-name d-none d-md-inline"><?php echo htmlspecialchars($admin_name); ?></span>
        <span class="role-badge"><?php echo ucfirst($admin_role); ?></span>
      </button>
      <ul class="dropdown-menu dropdown-menu-end profile-dropdown">
        <li><h6 class="dropdown-header">Signed in as <strong><?php echo htmlspecialchars($admin_name); ?></strong></h6></li>
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

      <!-- Dashboard (standalone) -->
      <div class="sb-section-label">Main</div>
      <a href="dashboard.php" class="sb-link <?php echo is_active('dashboard.php'); ?>">
        <i class="bi bi-speedometer2"></i><span>Dashboard</span>
      </a>

      <!-- Bookings dropdown -->
      <div class="sb-section-label">Bookings</div>
      <button class="sb-group-toggle <?php echo group_open($booking_pages) ? 'open' : ''; ?>"
        data-bs-toggle="collapse" data-bs-target="#grp-bookings"
        aria-expanded="<?php echo group_open($booking_pages) ? 'true' : 'false'; ?>">
        <span class="sb-toggle-left"><i class="bi bi-calendar2-week"></i><span>Bookings</span></span>
        <i class="bi bi-chevron-down sb-caret"></i>
      </button>
      <div class="collapse <?php echo group_open($booking_pages); ?>" id="grp-bookings">
        <div class="sb-submenu">
          <a href="new_bookings.php" class="sb-link sb-sub <?php echo is_active('new_bookings.php'); ?>">
            <i class="bi bi-calendar-plus"></i><span>New Bookings</span>
          </a>
          <a href="refund_bookings.php" class="sb-link sb-sub <?php echo is_active('refund_bookings.php'); ?>">
            <i class="bi bi-arrow-counterclockwise"></i><span>Refund Bookings</span>
          </a>
          <a href="booking_records.php" class="sb-link sb-sub <?php echo is_active('booking_records.php'); ?>">
            <i class="bi bi-journal-text"></i><span>Booking Records</span>
          </a>
        </div>
      </div>

      <!-- Reports dropdown -->
      <div class="sb-section-label">Reports</div>
      <button class="sb-group-toggle <?php echo group_open($report_pages) ? 'open' : ''; ?>"
        data-bs-toggle="collapse" data-bs-target="#grp-reports"
        aria-expanded="<?php echo group_open($report_pages) ? 'true' : 'false'; ?>">
        <span class="sb-toggle-left"><i class="bi bi-bar-chart-line"></i><span>Reports</span></span>
        <i class="bi bi-chevron-down sb-caret"></i>
      </button>
      <div class="collapse <?php echo group_open($report_pages); ?>" id="grp-reports">
        <div class="sb-submenu">
          <a href="all_time_reports.php" class="sb-link sb-sub <?php echo is_active('all_time_reports.php'); ?>">
            <i class="bi bi-graph-up"></i><span>All Time Reports</span>
          </a>
          <a href="transaction.php" class="sb-link sb-sub <?php echo is_active('transaction.php'); ?>">
            <i class="bi bi-receipt"></i><span>Transactions</span>
          </a>
        </div>
      </div>

      <?php if($admin_role === 'admin'): ?>

        <!-- Users dropdown -->
        <div class="sb-section-label">Users</div>
        <button class="sb-group-toggle <?php echo group_open($user_pages) ? 'open' : ''; ?>"
          data-bs-toggle="collapse" data-bs-target="#grp-users"
          aria-expanded="<?php echo group_open($user_pages) ? 'true' : 'false'; ?>">
          <span class="sb-toggle-left"><i class="bi bi-people"></i><span>Users</span></span>
          <i class="bi bi-chevron-down sb-caret"></i>
        </button>
        <div class="collapse <?php echo group_open($user_pages); ?>" id="grp-users">
          <div class="sb-submenu">
            <a href="users.php" class="sb-link sb-sub <?php echo is_active('users.php'); ?>">
              <i class="bi bi-person-lines-fill"></i><span>User Accounts</span>
            </a>
            <a href="user_queries.php" class="sb-link sb-sub <?php echo is_active('user_queries.php'); ?>">
              <i class="bi bi-chat-left-text"></i><span>User Queries</span>
            </a>
            <a href="manage_users.php" class="sb-link sb-sub <?php echo is_active(['manage_users.php','create_user.php']); ?>">
              <i class="bi bi-shield-person"></i><span>System Users</span>
            </a>
          </div>
        </div>

        <!-- Content dropdown -->
        <div class="sb-section-label">Content</div>
        <button class="sb-group-toggle <?php echo group_open($content_pages) ? 'open' : ''; ?>"
          data-bs-toggle="collapse" data-bs-target="#grp-content"
          aria-expanded="<?php echo group_open($content_pages) ? 'true' : 'false'; ?>">
          <span class="sb-toggle-left"><i class="bi bi-grid"></i><span>Content</span></span>
          <i class="bi bi-chevron-down sb-caret"></i>
        </button>
        <div class="collapse <?php echo group_open($content_pages); ?>" id="grp-content">
          <div class="sb-submenu">
            <a href="rooms.php" class="sb-link sb-sub <?php echo is_active('rooms.php'); ?>">
              <i class="bi bi-door-open"></i><span>Manage Rooms</span>
            </a>
            <a href="features_facilities.php" class="sb-link sb-sub <?php echo is_active('features_facilities.php'); ?>">
              <i class="bi bi-stars"></i><span>Features & Facilities</span>
            </a>
            <a href="extras.php" class="sb-link sb-sub <?php echo is_active('extras.php'); ?>">
              <i class="bi bi-plus-square"></i><span>Extras & Rules</span>
            </a>
            <a href="carousel.php" class="sb-link sb-sub <?php echo is_active('carousel.php'); ?>">
              <i class="bi bi-images"></i><span>Carousel</span>
            </a>
            <a href="rate_review.php" class="sb-link sb-sub <?php echo is_active('rate_review.php'); ?>">
              <i class="bi bi-star-half"></i><span>Ratings & Reviews</span>
            </a>
          </div>
        </div>

        <!-- Utilities dropdown -->
        <div class="sb-section-label">Utilities</div>
        <button class="sb-group-toggle <?php echo group_open($utility_pages) ? 'open' : ''; ?>"
          data-bs-toggle="collapse" data-bs-target="#grp-utilities"
          aria-expanded="<?php echo group_open($utility_pages) ? 'true' : 'false'; ?>">
          <span class="sb-toggle-left"><i class="bi bi-tools"></i><span>Utilities</span></span>
          <i class="bi bi-chevron-down sb-caret"></i>
        </button>
        <div class="collapse <?php echo group_open($utility_pages); ?>" id="grp-utilities">
          <div class="sb-submenu">
            <a href="archives.php" class="sb-link sb-sub <?php echo is_active('archives.php'); ?>">
              <i class="bi bi-archive"></i><span>Archives</span>
            </a>
            <a href="backup_restore.php" class="sb-link sb-sub <?php echo is_active('backup_restore.php'); ?>">
              <i class="bi bi-hdd-rack"></i><span>Backup & Restore</span>
            </a>
            <a href="activity_logs.php" class="sb-link sb-sub <?php echo is_active('activity_logs.php'); ?>">
              <i class="bi bi-list-check"></i><span>Activity Logs</span>
            </a>
            <a href="settings.php" class="sb-link sb-sub <?php echo is_active('settings.php'); ?>">
              <i class="bi bi-gear"></i><span>Settings</span>
            </a>
          </div>
        </div>

      <?php else: ?>
        <div class="sb-section-label">Account</div>
        <a href="change_password.php" class="sb-link <?php echo is_active('change_password.php'); ?>">
          <i class="bi bi-key"></i><span>Change Password</span>
        </a>
      <?php endif; ?>

    </nav>

    <div class="sidebar-footer">
      <a href="logout.php" id="admin-logout" class="sb-link sidebar-logout">
        <i class="bi bi-box-arrow-right"></i><span>Log Out</span>
      </a>
    </div>
  </div>
</div>

<!-- Mobile overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<style>
  :root {
    --sidebar-bg: #0f172a;
    --sidebar-hover: rgba(255,255,255,0.07);
    --sidebar-active: #1e3a5f;
    --sidebar-text: #cbd5e1;
    --sidebar-text-active: #fff;
    --sidebar-label: #475569;
    --navbar-bg: rgba(15,23,42,0.97);
    --sidebar-width: 240px;
    --navbar-height: 60px;
    --accent: #3b82f6;
  }

  body {
    background: #f1f5f9 !important;
    padding-top: var(--navbar-height) !important;
    padding-left: var(--sidebar-width) !important;
  }

  /* ── TOP NAVBAR ── */
  .admin-top-navbar {
    position: fixed;
    top: 0; left: 0; right: 0;
    height: var(--navbar-height);
    background: var(--navbar-bg);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(255,255,255,0.05);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px 0 18px;
    z-index: 1050;
    box-shadow: 0 2px 12px rgba(0,0,0,0.35);
  }
  .top-navbar-left { display: flex; align-items: center; gap: 14px; }
  .top-navbar-right { display: flex; align-items: center; }

  .sidebar-toggle {
    background: none; border: none; color: #cbd5e1;
    font-size: 22px; line-height: 1; cursor: pointer;
    padding: 5px 8px; border-radius: 6px; display: none;
  }
  .sidebar-toggle:hover { background: rgba(255,255,255,0.08); color: #fff; }

  .brand-name {
    color: #fff; font-size: 1.22rem; font-weight: 700; letter-spacing: 0.3px;
  }

  .profile-btn {
    display: flex; align-items: center; gap: 8px;
    background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 8px;
    padding: 7px 14px;
    color: #cbd5e1;
    cursor: pointer;
    font-size: 0.875rem;
    transition: background 0.2s;
  }
  .profile-btn:hover, .profile-btn:focus { background: rgba(255,255,255,0.13); color: #fff; outline: none; box-shadow: none; }
  .profile-btn::after { border-color: #94a3b8 transparent transparent; }
  .profile-avatar {
    width: 30px; height: 30px; background: var(--accent);
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    font-size: 14px; color: #fff; flex-shrink: 0;
  }
  .role-badge {
    background: rgba(59,130,246,0.18);
    color: #93c5fd;
    border-radius: 20px;
    padding: 2px 9px;
    font-size: 0.72rem;
    font-weight: 600;
  }

  .profile-dropdown {
    background: #1e293b !important;
    border: 1px solid rgba(255,255,255,0.08) !important;
    border-radius: 12px !important;
    box-shadow: 0 10px 40px rgba(0,0,0,0.4) !important;
    min-width: 210px;
    padding: 6px !important;
  }
  .profile-dropdown .dropdown-header { color: #94a3b8; font-size: 0.78rem; }
  .profile-dropdown .dropdown-divider { border-color: rgba(255,255,255,0.08); }
  .profile-dropdown .dropdown-item {
    color: #cbd5e1; border-radius: 8px; font-size: 0.875rem; padding: 9px 12px;
  }
  .profile-dropdown .dropdown-item:hover { background: rgba(255,255,255,0.08); color: #fff; }
  .profile-dropdown .dropdown-item.text-danger { color: #f87171 !important; }
  .profile-dropdown .dropdown-item.text-danger:hover { background: rgba(248,113,113,0.1); }

  /* ── SIDEBAR ── */
  .admin-sidebar {
    position: fixed;
    top: var(--navbar-height); left: 0; bottom: 0;
    width: var(--sidebar-width);
    background: var(--sidebar-bg);
    overflow-y: auto; overflow-x: hidden;
    z-index: 1040;
    display: flex; flex-direction: column;
    border-right: 1px solid rgba(255,255,255,0.04);
    scrollbar-width: thin;
    scrollbar-color: rgba(255,255,255,0.08) transparent;
  }
  .admin-sidebar::-webkit-scrollbar { width: 3px; }
  .admin-sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 2px; }

  .sidebar-inner { display: flex; flex-direction: column; height: 100%; padding: 8px 10px 0; }
  .sidebar-nav { flex: 1; }

  /* Section labels */
  .sb-section-label {
    color: var(--sidebar-label);
    font-size: 0.67rem;
    font-weight: 700;
    letter-spacing: 1.3px;
    text-transform: uppercase;
    padding: 18px 10px 5px;
  }

  /* Shared link base */
  .sb-link {
    display: flex;
    align-items: center;
    gap: 11px;
    color: var(--sidebar-text);
    text-decoration: none;
    padding: 11px 12px;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    margin-bottom: 1px;
    white-space: nowrap;
    overflow: hidden;
    transition: background 0.18s, color 0.18s;
  }
  .sb-link i { font-size: 16px; flex-shrink: 0; width: 18px; text-align: center; }
  .sb-link:hover { background: var(--sidebar-hover); color: #fff; }
  .sb-link.active {
    background: var(--sidebar-active);
    color: #fff;
    box-shadow: inset 3px 0 0 var(--accent);
  }

  /* Sub-links (inside dropdowns) — slightly indented */
  .sb-submenu { padding-left: 6px; }
  .sb-link.sb-sub { padding: 10px 12px 10px 14px; font-size: 0.85rem; }

  /* Group toggle button */
  .sb-group-toggle {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    background: none;
    border: none;
    color: var(--sidebar-text);
    padding: 11px 12px;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    margin-bottom: 1px;
    transition: background 0.18s, color 0.18s;
  }
  .sb-toggle-left { display: flex; align-items: center; gap: 11px; }
  .sb-toggle-left i { font-size: 16px; width: 18px; text-align: center; }
  .sb-group-toggle:hover { background: var(--sidebar-hover); color: #fff; }
  .sb-group-toggle.open { color: #fff; }

  /* Caret rotation */
  .sb-caret {
    font-size: 11px;
    transition: transform 0.25s ease;
  }
  .sb-group-toggle[aria-expanded="true"] .sb-caret,
  .sb-group-toggle.open .sb-caret { transform: rotate(180deg); }

  /* Sidebar footer */
  .sidebar-footer {
    padding: 10px 0 14px;
    border-top: 1px solid rgba(255,255,255,0.06);
    margin-top: 10px;
  }
  .sidebar-logout { color: #f87171 !important; }
  .sidebar-logout:hover { background: rgba(248,113,113,0.08) !important; }

  /* Mobile overlay */
  .sidebar-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.5); z-index: 1035;
    backdrop-filter: blur(2px);
  }

  /* ── MAIN CONTENT CARDS ── */
  #main-content .card {
    background: #fff;
    border: none !important;
    border-radius: 14px;
    box-shadow: 0 2px 16px rgba(0,0,0,0.07);
  }
  #main-content .card-body { padding: 1.5rem; }
  #main-content h3 { font-size: 1.2rem; font-weight: 700; color: #0f172a; }

  .btn-primary { background: #3b82f6; border: none; border-radius: 8px; }
  .btn-primary:hover { background: #2563eb; }
  .btn-danger { border-radius: 8px; }
  .btn-dark { background: #0f172a; border-color: #0f172a; border-radius: 8px; }
  .btn-dark:hover { background: #1e293b; border-color: #1e293b; }

  #main-content .table { color: #374151; }
  #main-content .table thead { background: #0f172a; color: #fff; }
  #main-content .table tbody tr:hover { background: rgba(59,130,246,0.04); }

  .nav-tabs .nav-link {
    border-radius: 20px !important; margin-right: 6px;
    color: #64748b; border: none !important;
    padding: 7px 18px; font-size: 0.875rem; font-weight: 500;
  }
  .nav-tabs .nav-link:hover { background: #e2e8f0; color: #0f172a; }
  .nav-tabs .nav-link.active { background: #3b82f6 !important; color: #fff !important; border: none !important; }
  .nav-tabs { border-bottom: 1px solid #e2e8f0 !important; padding-bottom: 10px; margin-bottom: 20px !important; }

  /* ── RESPONSIVE ── */
  @media (max-width: 991px) {
    body { padding-left: 0 !important; }
    .sidebar-toggle { display: flex !important; align-items: center; justify-content: center; }
    .admin-sidebar { left: calc(-1 * var(--sidebar-width)); transition: left 0.25s ease; }
    .admin-sidebar.open { left: 0; }
    .sidebar-overlay.open { display: block; }
  }
</style>

<script>
  (function(){
    const toggle  = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('dashboard-menu');
    const overlay = document.getElementById('sidebarOverlay');
    if(!toggle || !sidebar) return;

    function openSidebar(){ sidebar.classList.add('open'); overlay.classList.add('open'); }
    function closeSidebar(){ sidebar.classList.remove('open'); overlay.classList.remove('open'); }

    toggle.addEventListener('click', function(){
      sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
    });
    overlay.addEventListener('click', closeSidebar);

    // Sync caret state with Bootstrap collapse events
    document.querySelectorAll('.sb-group-toggle').forEach(function(btn){
      var targetId = btn.getAttribute('data-bs-target');
      var target = document.querySelector(targetId);
      if(!target) return;
      target.addEventListener('show.bs.collapse', function(){ btn.setAttribute('aria-expanded','true'); });
      target.addEventListener('hide.bs.collapse', function(){ btn.setAttribute('aria-expanded','false'); });
    });
  })();
</script>
