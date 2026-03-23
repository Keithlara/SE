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
        COUNT(CASE WHEN booking_status='booked'    AND arrival=0  THEN 1 END) AS new_bookings,
        COUNT(CASE WHEN booking_status='cancelled' AND refund=0   THEN 1 END) AS refund_bookings
      FROM booking_order"));
    $unread_queries   = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(sr_no) AS count FROM user_queries   WHERE seen=0"));
    $unread_reviews   = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(sr_no) AS count FROM rating_review  WHERE seen=0"));
    $current_users    = mysqli_fetch_assoc(mysqli_query($con, "
      SELECT COUNT(id) AS total,
        COUNT(CASE WHEN status=1     THEN 1 END) AS active,
        COUNT(CASE WHEN status=0     THEN 1 END) AS inactive
      FROM user_cred"));
    $total_bookings   = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS count FROM booking_order"));
    $today_bookings   = mysqli_fetch_assoc(mysqli_query($con, "
      SELECT COUNT(*) AS count FROM booking_order
      WHERE DATE(datentime)=CURDATE()"));
  ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4">

        <!-- Page heading -->
        <div class="d-flex align-items-center justify-content-between mb-4">
          <h4 class="fw-bold text-primary mb-0"><i class="bi bi-speedometer2 me-2"></i>Dashboard</h4>
          <?php if ($is_shutdown['shutdown']): ?>
            <span class="badge bg-danger py-2 px-3">Shutdown Mode Active</span>
          <?php endif; ?>
        </div>

        <!-- TOP ROW: Rooms Map (left) + Alerts (right) -->
        <div class="row g-3 mb-4">

          <!-- Rooms Map -->
          <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-header bg-white border-0 py-2 d-flex align-items-center justify-content-between">
                <span class="fw-semibold"><i class="bi bi-grid-3x3-gap-fill me-2 text-primary"></i>Room Occupancy — Today</span>
                <button class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="load_today_occupancy()">
                  <i class="bi bi-arrow-clockwise"></i> Refresh
                </button>
              </div>
              <div class="card-body pt-2">
                <div class="legend mb-2">
                  <span class="legend-item"><span class="legend-swatch available"></span><span>Available</span></span>
                  <span class="legend-item"><span class="legend-swatch pending"></span><span>Pending</span></span>
                  <span class="legend-item"><span class="legend-swatch occupied"></span><span>Occupied</span></span>
                </div>
                <div id="occ-grid-dashboard" class="seat-grid">
                  <div class="text-muted small">Loading room data…</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Alerts & Quick Actions -->
          <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-header bg-white border-0 py-2">
                <span class="fw-semibold"><i class="bi bi-bell me-2 text-warning"></i>Needs Attention</span>
              </div>
              <div class="card-body p-0">
                <a href="new_bookings.php" class="alert-row d-flex align-items-center justify-content-between px-3 py-3 border-bottom text-decoration-none text-dark">
                  <div class="d-flex align-items-center gap-2">
                    <div class="alert-icon bg-warning bg-opacity-10 text-warning rounded-circle">
                      <i class="bi bi-calendar-plus"></i>
                    </div>
                    <span class="small fw-medium">New Bookings</span>
                  </div>
                  <span class="badge bg-warning text-dark rounded-pill"><?php echo $current_bookings['new_bookings']; ?></span>
                </a>
                <a href="refund_bookings.php" class="alert-row d-flex align-items-center justify-content-between px-3 py-3 border-bottom text-decoration-none text-dark">
                  <div class="d-flex align-items-center gap-2">
                    <div class="alert-icon bg-danger bg-opacity-10 text-danger rounded-circle">
                      <i class="bi bi-arrow-counterclockwise"></i>
                    </div>
                    <span class="small fw-medium">Refund Requests</span>
                  </div>
                  <span class="badge bg-danger rounded-pill"><?php echo $current_bookings['refund_bookings']; ?></span>
                </a>
                <a href="user_queries.php" class="alert-row d-flex align-items-center justify-content-between px-3 py-3 border-bottom text-decoration-none text-dark">
                  <div class="d-flex align-items-center gap-2">
                    <div class="alert-icon bg-info bg-opacity-10 text-info rounded-circle">
                      <i class="bi bi-chat-dots"></i>
                    </div>
                    <span class="small fw-medium">Unread Queries</span>
                  </div>
                  <span class="badge bg-info text-dark rounded-pill"><?php echo $unread_queries['count']; ?></span>
                </a>
                <a href="rate_review.php" class="alert-row d-flex align-items-center justify-content-between px-3 py-3 border-bottom text-decoration-none text-dark">
                  <div class="d-flex align-items-center gap-2">
                    <div class="alert-icon bg-purple bg-opacity-10 text-purple rounded-circle">
                      <i class="bi bi-star"></i>
                    </div>
                    <span class="small fw-medium">New Reviews</span>
                  </div>
                  <span class="badge bg-secondary rounded-pill"><?php echo $unread_reviews['count']; ?></span>
                </a>
                <div class="d-flex align-items-center justify-content-between px-3 py-3">
                  <div class="d-flex align-items-center gap-2">
                    <div class="alert-icon bg-success bg-opacity-10 text-success rounded-circle">
                      <i class="bi bi-calendar-day"></i>
                    </div>
                    <span class="small fw-medium">Today's Bookings</span>
                  </div>
                  <span class="badge bg-success rounded-pill"><?php echo $today_bookings['count']; ?></span>
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- STATS CARDS ROW -->
        <div class="row g-3 mb-4">
          <div class="col-sm-6 col-xl-3">
            <div class="stat-card card border-0 shadow-sm">
              <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary rounded-circle">
                  <i class="bi bi-calendar-check fs-4"></i>
                </div>
                <div>
                  <div class="text-muted small text-uppercase">Total Bookings</div>
                  <div class="fw-bold fs-4 lh-1"><?php echo $total_bookings['count']; ?></div>
                  <a href="booking_records.php" class="small text-decoration-none">View all <i class="bi bi-arrow-right"></i></a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-sm-6 col-xl-3">
            <div class="stat-card card border-0 shadow-sm">
              <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning rounded-circle">
                  <i class="bi bi-hourglass-split fs-4"></i>
                </div>
                <div>
                  <div class="text-muted small text-uppercase">Pending Bookings</div>
                  <div class="fw-bold fs-4 lh-1"><?php echo $current_bookings['new_bookings']; ?></div>
                  <a href="new_bookings.php" class="small text-decoration-none">Manage <i class="bi bi-arrow-right"></i></a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-sm-6 col-xl-3">
            <div class="stat-card card border-0 shadow-sm">
              <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success rounded-circle">
                  <i class="bi bi-people fs-4"></i>
                </div>
                <div>
                  <div class="text-muted small text-uppercase">Total Users</div>
                  <div class="fw-bold fs-4 lh-1"><?php echo $current_users['total']; ?></div>
                  <span class="small text-success"><?php echo $current_users['active']; ?> active</span>
                </div>
              </div>
            </div>
          </div>

          <div class="col-sm-6 col-xl-3">
            <div class="stat-card card border-0 shadow-sm">
              <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger rounded-circle">
                  <i class="bi bi-arrow-counterclockwise fs-4"></i>
                </div>
                <div>
                  <div class="text-muted small text-uppercase">Refund Requests</div>
                  <div class="fw-bold fs-4 lh-1"><?php echo $current_bookings['refund_bookings']; ?></div>
                  <a href="refund_bookings.php" class="small text-decoration-none">Review <i class="bi bi-arrow-right"></i></a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- RECENT ACTIVITY (full width) -->
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
            <span class="fw-semibold"><i class="bi bi-activity me-2 text-primary"></i>Recent Activity</span>
            <a href="activity_logs.php" class="btn btn-sm btn-outline-primary py-0 px-2">View all</a>
          </div>
          <div class="card-body p-0">
            <?php
              $activities = [];
              $res = mysqli_query($con, "SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT 8");
              if ($res) while ($row = mysqli_fetch_assoc($res)) $activities[] = $row;

              if (!empty($activities)):
                echo '<ul class="list-group list-group-flush">';
                foreach ($activities as $a):
                  $failed = stripos($a['action'], 'fail') !== false || stripos($a['action'], 'error') !== false;
                  $icon   = $failed ? 'bi-x-circle text-danger' : 'bi-check-circle text-success';
                  $ago    = time_elapsed_string($a['created_at']);
                  echo "
                  <li class='list-group-item border-0 py-2 px-3'>
                    <div class='d-flex align-items-start gap-3'>
                      <i class='bi {$icon} mt-1'></i>
                      <div class='flex-grow-1 min-width-0'>
                        <div class='d-flex justify-content-between align-items-center flex-wrap gap-1'>
                          <span class='fw-medium small'>{$a['action']}</span>
                          <span class='text-muted' style='font-size:.75rem;white-space:nowrap'>{$ago}</span>
                        </div>
                        <div class='text-muted text-truncate' style='font-size:.78rem'>{$a['details']}</div>
                        <div style='font-size:.75rem' class='text-muted'><i class='bi bi-person me-1'></i>{$a['user_name']}</div>
                      </div>
                    </div>
                  </li>";
                endforeach;
                echo '</ul>';
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

  <?php require('inc/scripts.php'); ?>
  <script src="scripts/dashboard.js?v=<?php echo filemtime('scripts/dashboard.js'); ?>"></script>

  <style>
    .stat-card { border-radius: .75rem; transition: transform .15s, box-shadow .15s; }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 .4rem 1.2rem rgba(0,0,0,.09) !important; }
    .stat-icon { width:52px; height:52px; flex-shrink:0; display:inline-flex; align-items:center; justify-content:center; }
    .alert-icon { width:34px; height:34px; flex-shrink:0; display:inline-flex; align-items:center; justify-content:center; font-size:.9rem; }
    .alert-row:hover { background:#f8f9fa; }
    .card { border-radius: .75rem; overflow: hidden; }
    .list-group-item { border-left:0; border-right:0; }
    .list-group-item:first-child { border-top:0; }
    .list-group-item:last-child  { border-bottom:0; }
    .min-width-0 { min-width: 0; }
  </style>

</body>
</html>
