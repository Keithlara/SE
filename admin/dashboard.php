<?php
  require('inc/essentials.php');
  require('inc/db_config.php');
  adminLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Dashboard</title>
  <?php require('inc/links.php'); ?>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body class="bg-light">

  <?php
    require('inc/header.php');

    function time_elapsed_string($datetime) {
      $now  = new DateTime;
      $ago  = new DateTime($datetime);
      $diff = $now->diff($ago);
      $parts = [
        'y' => 'year', 'm' => 'month', 'd' => 'day',
        'h' => 'hour', 'i' => 'minute', 's' => 'second',
      ];
      foreach ($parts as $k => &$v) {
        if ($diff->$k) { $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : ''); }
        else { unset($parts[$k]); }
      }
      $parts = array_slice($parts, 0, 1);
      return $parts ? implode(', ', $parts) . ' ago' : 'just now';
    }

    $is_shutdown      = mysqli_fetch_assoc(mysqli_query($con, "SELECT `shutdown` FROM `settings`"));
    $current_bookings = mysqli_fetch_assoc(mysqli_query($con, "
      SELECT
        COUNT(CASE WHEN booking_status='booked' AND arrival=0 AND COALESCE(booking_source,'online') <> 'walk_in' THEN 1 END) AS new_bookings,
        COUNT(CASE WHEN booking_status='cancelled' AND refund=0 THEN 1 END) AS refund_bookings
      FROM booking_order"));
    $unread_queries   = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(sr_no) AS count FROM user_queries WHERE seen=0"));
    $unread_reviews   = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(sr_no) AS count FROM rating_review WHERE seen=0"));
    $current_users    = mysqli_fetch_assoc(mysqli_query($con, "
      SELECT COUNT(id) AS total,
        COUNT(CASE WHEN status=1 THEN 1 END) AS active,
        COUNT(CASE WHEN status=0 THEN 1 END) AS inactive
      FROM user_cred"));
    $total_bookings   = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS count FROM booking_order"));
    $today_bookings   = mysqli_fetch_assoc(mysqli_query($con, "
      SELECT COUNT(*) AS count FROM booking_order
      WHERE DATE(datentime)=CURDATE()"));
    $revenue          = mysqli_fetch_assoc(mysqli_query($con, "
      SELECT
        COALESCE(SUM(trans_amt),0) AS total_revenue,
        COALESCE(SUM(CASE WHEN MONTH(datentime)=MONTH(CURDATE()) AND YEAR(datentime)=YEAR(CURDATE()) THEN trans_amt ELSE 0 END),0) AS month_revenue
      FROM booking_order WHERE booking_status='booked'"));
    $today_checkins   = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS count FROM booking_order WHERE booking_status='booked' AND DATE(check_in)=CURDATE()"));
    $today_checkouts  = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS count FROM booking_order WHERE booking_status='booked' AND DATE(check_out)=CURDATE()"));

    $chart_labels = [];
    $chart_data   = [];
    for ($i = 6; $i >= 0; $i--) {
      $chart_labels[] = date('D', strtotime("-$i days"));
      $row = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS c FROM booking_order WHERE DATE(datentime)=DATE(NOW() - INTERVAL $i DAY)"));
      $chart_data[] = (int)$row['c'];
    }
    $admin_name = $_SESSION['adminName'] ?? $_SESSION['adminUsername'] ?? 'Admin';
  ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4">

        <div class="welcome-banner mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
          <div>
            <div class="welcome-eyebrow">Travelers Place</div>
            <h4 class="welcome-title mb-1">Welcome back, <?php echo htmlspecialchars($admin_name); ?></h4>
            <div class="welcome-date">
              <i class="bi bi-calendar3 me-1"></i><?php echo date('l, F j, Y'); ?>
              &nbsp;&middot;&nbsp;
              <i class="bi bi-clock me-1"></i><span id="live-clock"></span>
            </div>
          </div>
          <div class="d-flex align-items-center gap-2 flex-wrap">
            <?php if ($is_shutdown['shutdown']): ?>
              <span class="dash-badge dash-badge-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>Shutdown Mode</span>
            <?php else: ?>
              <span class="dash-badge dash-badge-success"><i class="bi bi-circle-fill me-1" style="font-size:.5rem;vertical-align:middle"></i>System Online</span>
            <?php endif; ?>
            <a href="all_time_reports.php" class="dash-badge dash-badge-outline"><i class="bi bi-bar-chart-line me-1"></i>View Reports</a>
          </div>
        </div>

        <div class="row g-3 mb-4">

          <div class="col-sm-6 col-xl-3">
            <div class="kpi-card kpi-blue">
              <div class="kpi-deco"><i class="bi bi-calendar-check"></i></div>
              <div class="kpi-label">Total Bookings</div>
              <div class="kpi-value"><?php echo $total_bookings['count']; ?></div>
              <a href="booking_records.php" class="kpi-link">View all <i class="bi bi-arrow-right"></i></a>
            </div>
          </div>

          <div class="col-sm-6 col-xl-3">
            <div class="kpi-card kpi-amber">
              <div class="kpi-deco"><i class="bi bi-hourglass-split"></i></div>
              <div class="kpi-label">Pending Approval</div>
              <div class="kpi-value"><?php echo $current_bookings['new_bookings']; ?></div>
              <a href="new_bookings.php" class="kpi-link">Manage <i class="bi bi-arrow-right"></i></a>
            </div>
          </div>

          <div class="col-sm-6 col-xl-3">
            <div class="kpi-card kpi-green">
              <div class="kpi-deco"><i class="bi bi-people-fill"></i></div>
              <div class="kpi-label">Total Users</div>
              <div class="kpi-value"><?php echo $current_users['total']; ?></div>
              <span class="kpi-link"><?php echo $current_users['active']; ?> active</span>
            </div>
          </div>

          <div class="col-sm-6 col-xl-3">
            <div class="kpi-card kpi-red">
              <div class="kpi-deco"><i class="bi bi-arrow-counterclockwise"></i></div>
              <div class="kpi-label">Refund Requests</div>
              <div class="kpi-value"><?php echo $current_bookings['refund_bookings']; ?></div>
              <a href="refund_bookings.php" class="kpi-link">Review <i class="bi bi-arrow-right"></i></a>
            </div>
          </div>

        </div>

        <div class="row g-3 mb-4">

          <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-header bg-white border-0 py-2 d-flex align-items-center justify-content-between">
                <span class="fw-semibold"><i class="bi bi-grid-3x3-gap-fill me-2 text-primary"></i>Room Occupancy &mdash; Today</span>
                <button class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="load_today_occupancy()">
                  <i class="bi bi-arrow-clockwise"></i> Refresh
                </button>
              </div>
              <div class="card-body pt-2 dashboard-panel-body">
                <div class="legend mb-2">
                  <span class="legend-item"><span class="legend-swatch available"></span><span>Available</span></span>
                  <span class="legend-item"><span class="legend-swatch pending"></span><span>Pending</span></span>
                  <span class="legend-item"><span class="legend-swatch occupied"></span><span>Occupied</span></span>
                </div>
                <div id="occ-grid-dashboard" class="seat-grid">
                  <div class="text-muted small">Loading room data...</div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
                <span class="fw-semibold"><i class="bi bi-graph-up-arrow me-2 text-primary"></i>Bookings &mdash; Last 7 Days</span>
                <div class="d-flex gap-3">
                  <div class="text-center">
                    <div class="fw-bold text-primary" style="font-size:1.1rem">&#8369;<?php echo number_format($revenue['month_revenue']); ?></div>
                    <div class="text-muted" style="font-size:.7rem">This Month</div>
                  </div>
                  <div class="vr"></div>
                  <div class="text-center">
                    <div class="fw-bold text-success" style="font-size:1.1rem">&#8369;<?php echo number_format($revenue['total_revenue']); ?></div>
                    <div class="text-muted" style="font-size:.7rem">All-Time</div>
                  </div>
                </div>
              </div>
              <div class="card-body pt-0 pb-3 dashboard-panel-body">
                <canvas id="bookingChart" height="145"></canvas>
              </div>
            </div>
          </div>

        </div>

        <div class="row g-3">

          <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-header bg-white border-0 py-3">
                <span class="fw-semibold"><i class="bi bi-door-open me-2 text-success"></i>Today's Movement</span>
              </div>
              <div class="card-body d-flex flex-column gap-3">
                <div class="movement-row movement-checkin">
                  <div class="movement-icon"><i class="bi bi-box-arrow-in-right"></i></div>
                  <div>
                    <div class="fw-semibold"><?php echo $today_checkins['count']; ?> Check-ins</div>
                    <div class="text-muted small">Expected today</div>
                  </div>
                </div>
                <div class="movement-row movement-checkout">
                  <div class="movement-icon"><i class="bi bi-box-arrow-right"></i></div>
                  <div>
                    <div class="fw-semibold"><?php echo $today_checkouts['count']; ?> Check-outs</div>
                    <div class="text-muted small">Expected today</div>
                  </div>
                </div>
                <div class="movement-row movement-new">
                  <div class="movement-icon"><i class="bi bi-plus-circle"></i></div>
                  <div>
                    <div class="fw-semibold"><?php echo $today_bookings['count']; ?> New Bookings</div>
                    <div class="text-muted small">Created today</div>
                  </div>
                </div>
                <div class="movement-row movement-users">
                  <div class="movement-icon"><i class="bi bi-person-check"></i></div>
                  <div>
                    <div class="fw-semibold"><?php echo $current_users['active']; ?> Active Users</div>
                    <div class="text-muted small">of <?php echo $current_users['total']; ?> total</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
                <span class="fw-semibold"><i class="bi bi-activity me-2 text-primary"></i>Recent Activity</span>
                <a href="activity_logs.php" class="btn btn-sm btn-outline-primary py-0 px-2">View all</a>
              </div>
              <div class="card-body py-2 px-3">
                <?php
                  $activities = [];
                  $res = mysqli_query($con, "SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT 8");
                  if ($res) while ($row = mysqli_fetch_assoc($res)) $activities[] = $row;

                  if (!empty($activities)):
                    echo '<div class="timeline">';
                    foreach ($activities as $a):
                      $failed  = stripos($a['action'], 'fail') !== false || stripos($a['action'], 'error') !== false;
                      $dotClass = $failed ? 'tl-dot-danger' : 'tl-dot-success';
                      $ago = time_elapsed_string($a['created_at']);
                      echo "
                      <div class='tl-item'>
                        <div class='tl-dot {$dotClass}'></div>
                        <div class='tl-body'>
                          <div class='d-flex justify-content-between align-items-start gap-2'>
                            <span class='fw-medium small'>{$a['action']}</span>
                            <span class='tl-time'>{$ago}</span>
                          </div>
                          <div class='tl-detail'>{$a['details']}</div>
                          <div class='tl-user'><i class='bi bi-person me-1'></i>{$a['user_name']}</div>
                        </div>
                      </div>";
                    endforeach;
                    echo '</div>';
                  else:
                    echo '<div class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>No recent activities
                          </div>';
                  endif;
                ?>
              </div>
            </div>
          </div>

        </div>

      </div>
    </div>
  </div>

  <?php require('inc/scripts.php'); ?>
  <script src="scripts/dashboard.js?v=<?php echo filemtime('scripts/dashboard.js'); ?>"></script>

  <script>
    function updateClock() {
      const now = new Date();
      document.getElementById('live-clock').textContent =
        now.toLocaleTimeString('en-PH', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }
    updateClock(); setInterval(updateClock, 1000);

    const ctx = document.getElementById('bookingChart').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: <?php echo json_encode($chart_labels); ?>,
        datasets: [{
          label: 'Bookings',
          data: <?php echo json_encode($chart_data); ?>,
          backgroundColor: 'rgba(13,110,253,0.15)',
          borderColor: 'rgba(13,110,253,0.85)',
          borderWidth: 2,
          borderRadius: 6,
          borderSkipped: false,
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, ticks: { precision: 0, font: { size: 11 } }, grid: { color: 'rgba(0,0,0,.04)' } },
          x: { ticks: { font: { size: 11 } }, grid: { display: false } }
        }
      }
    });
  </script>

  <style>
    .welcome-banner {
      background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 50%, #6610f2 100%);
      border-radius: 1rem;
      padding: 1.5rem 1.75rem;
      color: #fff;
    }
    .welcome-eyebrow { font-size: .72rem; letter-spacing: .1em; text-transform: uppercase; opacity: .75; margin-bottom: .15rem; }
    .welcome-title { font-size: 1.35rem; font-weight: 700; color: #ffffff; }
    .welcome-date { font-size: .82rem; opacity: .8; }
    .dash-badge {
      display: inline-flex; align-items: center; font-size: .75rem; font-weight: 600;
      padding: .35rem .85rem; border-radius: 50px;
    }
    .dash-badge-success { background: rgba(255,255,255,.2); color:#fff; }
    .dash-badge-danger { background: rgba(220,53,69,.8); color:#fff; }
    .dash-badge-outline { background: rgba(255,255,255,.15); color:#fff; border: 1px solid rgba(255,255,255,.4); text-decoration:none; }
    .dash-badge-outline:hover { background: rgba(255,255,255,.25); color:#fff; }

    .kpi-card {
      position: relative; border-radius: 1rem; padding: 1.25rem 1.25rem 1rem;
      color: #fff; overflow: hidden; height: 100%;
      transition: transform .18s, box-shadow .18s;
    }
    .kpi-card:hover { transform: translateY(-4px); box-shadow: 0 .6rem 1.6rem rgba(0,0,0,.18) !important; }
    .kpi-deco {
      position: absolute; right: -10px; top: -10px;
      font-size: 5rem; opacity: .12; line-height: 1;
    }
    .kpi-label { font-size: .72rem; text-transform: uppercase; letter-spacing: .08em; opacity: .85; margin-bottom: .2rem; }
    .kpi-value { font-size: 2.4rem; font-weight: 800; line-height: 1; margin-bottom: .5rem; }
    .kpi-link { font-size: .78rem; color: rgba(255,255,255,.85); text-decoration: none; }
    .kpi-link:hover { color: #fff; }
    .kpi-blue { background: linear-gradient(135deg,#0d6efd,#0a58ca); box-shadow: 0 4px 20px rgba(13,110,253,.35); }
    .kpi-amber { background: linear-gradient(135deg,#fd7e14,#e8620a); box-shadow: 0 4px 20px rgba(253,126,20,.35); }
    .kpi-green { background: linear-gradient(135deg,#198754,#146c43); box-shadow: 0 4px 20px rgba(25,135,84,.35); }
    .kpi-red { background: linear-gradient(135deg,#dc3545,#b02a37); box-shadow: 0 4px 20px rgba(220,53,69,.35); }

    .movement-row {
      display: flex; align-items: center; gap: .85rem;
      padding: .75rem 1rem; border-radius: .6rem;
    }
    .movement-icon {
      width: 38px; height: 38px; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: 1rem; flex-shrink: 0;
    }
    .movement-checkin { background: #d1f0e0; }
    .movement-checkin .movement-icon { background:#198754; color:#fff; }
    .movement-checkout { background: #fff3cd; }
    .movement-checkout .movement-icon { background:#fd7e14; color:#fff; }
    .movement-new { background: #e7f1ff; }
    .movement-new .movement-icon { background:#0d6efd; color:#fff; }
    .movement-users { background: #ede7f6; }
    .movement-users .movement-icon { background:#6f42c1; color:#fff; }

    .timeline { padding-left: .5rem; }
    .tl-item { display: flex; gap: .75rem; padding: .5rem 0; position: relative; }
    .tl-item:not(:last-child)::before {
      content: ''; position: absolute; left: .44rem; top: 1.4rem; bottom: -.3rem;
      width: 2px; background: #e9ecef;
    }
    .tl-dot { width: .9rem; height: .9rem; border-radius: 50%; flex-shrink: 0; margin-top: .3rem; }
    .tl-dot-success { background: #198754; }
    .tl-dot-danger { background: #dc3545; }
    .tl-body { flex: 1; min-width: 0; }
    .tl-time { font-size: .7rem; color: #adb5bd; white-space: nowrap; }
    .tl-detail { font-size: .76rem; color: #6c757d; }
    .tl-user { font-size: .72rem; color: #adb5bd; }

    .card { border-radius: .85rem; overflow: hidden; }
    .dashboard-panel-body { min-height: 250px; }

    @media (max-width: 991.98px) {
      .dashboard-panel-body { min-height: auto; }
    }
  </style>

</body>
</html>

