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
    
    // Function to convert datetime to time ago format
    function time_elapsed_string($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);
        
        $string = [
            'y' => 'year',
            'm' => 'month',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        ];
        
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }
    
    $is_shutdown = mysqli_fetch_assoc(mysqli_query($con,"SELECT `shutdown` FROM `settings`"));

    $current_bookings = mysqli_fetch_assoc(mysqli_query($con,"SELECT 
      COUNT(CASE WHEN booking_status='booked' AND arrival=0 THEN 1 END) AS `new_bookings`,
      COUNT(CASE WHEN booking_status='cancelled' AND refund=0 THEN 1 END) AS `refund_bookings`
      FROM `booking_order`"));

    $unread_queries = mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(sr_no) AS `count`
      FROM `user_queries` WHERE `seen`=0"));

    $unread_reviews = mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(sr_no) AS `count`
      FROM `rating_review` WHERE `seen`=0"));
    
    $current_users = mysqli_fetch_assoc(mysqli_query($con,"SELECT 
      COUNT(id) AS `total`,
      COUNT(CASE WHEN `status`=1 THEN 1 END) AS `active`,
      COUNT(CASE WHEN `status`=0 THEN 1 END) AS `inactive`,
      COUNT(CASE WHEN `is_verified`=0 THEN 1 END) AS `unverified`
      FROM `user_cred`"));  
  
  ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4 overflow-hidden">
        
        <div class="d-flex align-items-center justify-content-between mb-4">
          <h3 class="fw-bold text-primary"><i class="bi bi-speedometer2 me-2"></i>DASHBOARD OVERVIEW</h3>
          <?php 
            if($is_shutdown['shutdown']){
              echo<<<data
                <h6 class="badge bg-danger py-2 px-3 rounded">Shutdown Mode is Active!</h6>
              data;
            }
          ?>
        </div>

        <!-- Rooms Map Section -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <h5 class="fw-bold text-dark section-title">
                <i class="bi bi-grid-3x3-gap-fill me-2"></i>Occupancy Today
              </h5>
              <button class="btn btn-sm btn-outline-secondary" onclick="load_today_occupancy()">
                <i class="bi bi-arrow-clockwise"></i> Refresh
              </button>
            </div>
            <div class="card border-0 shadow-sm">
              <div class="card-body">
                <div class="fw-bold mb-2">Rooms Map</div>
                <div class="legend mb-3">
                  <span class="legend-item"><span class="legend-swatch available"></span><span>Available</span></span>
                  <span class="legend-item"><span class="legend-swatch pending"></span><span>Pending</span></span>
                  <span class="legend-item"><span class="legend-swatch occupied"></span><span>Occupied</span></span>
                </div>
                <div id="occ-grid-dashboard" class="seat-grid">
                  <div class="text-muted">Loading room data...</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
          <!-- Total Bookings Card -->
          <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h6 class="text-uppercase text-muted mb-1">Total Bookings</h6>
                    <h2 class="mb-0 fw-bold"><?php 
                      $total_bookings = mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(*) as count FROM `booking_order`"));
                      echo $total_bookings['count'];
                    ?></h2>
                  </div>
                  <div class="icon-shape bg-primary bg-opacity-10 text-primary rounded-circle p-3">
                    <i class="bi bi-calendar-check fs-2"></i>
                  </div>
                </div>
                <div class="mt-3">
                  <a href="new_bookings.php" class="small text-decoration-none">
                    View all bookings <i class="bi bi-arrow-right"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- New Bookings Card -->
          <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h6 class="text-uppercase text-muted mb-1">New Bookings</h6>
                    <h2 class="mb-0 fw-bold text-warning"><?php echo $current_bookings['new_bookings']; ?></h2>
                  </div>
                  <div class="icon-shape bg-warning bg-opacity-10 text-warning rounded-circle p-3">
                    <i class="bi bi-bell fs-2"></i>
                  </div>
                </div>
                <div class="mt-3">
                  <a href="new_bookings.php" class="small text-decoration-none">
                    Manage new bookings <i class="bi bi-arrow-right"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- Total Users Card -->
          <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h6 class="text-uppercase text-muted mb-1">Total Users</h6>
                    <h2 class="mb-0 fw-bold text-success"><?php echo $current_users['total']; ?></h2>
                    <div class="small mt-1">
                      <span class="text-success"><?php echo $current_users['active']; ?> active</span> • 
                      <span class="text-danger"><?php echo $current_users['inactive']; ?> inactive</span>
                    </div>
                  </div>
                  <div class="icon-shape bg-success bg-opacity-10 text-success rounded-circle p-3">
                    <i class="bi bi-people fs-2"></i>
                  </div>
                </div>
                <div class="mt-3">
                  <a href="users.php" class="small text-decoration-none">
                    Manage users <i class="bi bi-arrow-right"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- Pending Actions Card -->
          <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h6 class="text-uppercase text-muted mb-1">Pending Actions</h6>
                    <div class="d-flex flex-column">
                      <div class="mb-1">
                        <span class="badge bg-warning text-dark me-2"><?php echo $unread_queries['count']; ?></span>
                        <span class="small">Unread Queries</span>
                      </div>
                      <div class="mb-1">
                        <span class="badge bg-info text-dark me-2"><?php echo $unread_reviews['count']; ?></span>
                        <span class="small">New Reviews</span>
                      </div>
                      <div>
                        <span class="badge bg-danger me-2"><?php echo $current_bookings['refund_bookings']; ?></span>
                        <span class="small">Refund Requests</span>
                      </div>
                    </div>
                  </div>
                  <div class="icon-shape bg-danger bg-opacity-10 text-danger rounded-circle p-3">
                    <i class="bi bi-exclamation-triangle fs-2"></i>
                  </div>
                </div>
                <div class="mt-3">
                  <a href="#" class="small text-decoration-none">
                    View all actions <i class="bi bi-arrow-right"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Activity & Quick Stats -->
        <div class="row g-4 mb-4">
          <!-- Recent Activity -->
          <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-header bg-white border-0 py-3">
                <h6 class="m-0 fw-bold"><i class="bi bi-activity me-2"></i>Recent Activity</h6>
              </div>
              <div class="card-body p-0">
                <?php
                // Get recent activities
                $recent_activities = [];
                $recent_activities_query = mysqli_query($con, "SELECT * FROM `activity_logs` ORDER BY `created_at` DESC LIMIT 5");
                if($recent_activities_query) {
                    while($row = mysqli_fetch_assoc($recent_activities_query)) {
                        $recent_activities[] = $row;
                    }
                }
                // Time ago calculation is handled by the function at the top of the file
                
                if(!empty($recent_activities)):
                  echo '<div class="list-group list-group-flush">';
                  foreach($recent_activities as $activity) {
                    $time_ago = time_elapsed_string($activity['created_at']);
                    $icon = strpos($activity['action'], 'Failed') !== false ? 'text-danger bi-x-circle' : 'text-success bi-check-circle';
                    echo "
                    <div class='list-group-item border-0 py-3'>
                      <div class='d-flex align-items-center'>
                        <div class='icon-shape bg-light rounded-circle p-2 me-3'>
                          <i class='bi $icon fs-5'></i>
                        </div>
                        <div class='flex-grow-1'>
                          <div class='d-flex justify-content-between align-items-center'>
                            <h6 class='mb-0'>{$activity['action']}</h6>
                            <small class='text-muted'>{$time_ago}</small>
                          </div>
                          <p class='mb-0 small text-muted'>{$activity['details']}</p>
                          <small class='text-muted'><i class='bi bi-person me-1'></i>{$activity['user_name']}</small>
                        </div>
                      </div>
                    </div>";
                  }
                  echo '</div>';
                else:
                  echo '<div class="text-center p-4">
                    <div class="text-muted mb-2">
                      <i class="bi bi-inbox fs-1"></i>
                    </div>
                    <p class="mb-0">No recent activities found</p>
                  </div>';
                endif;
                ?>
                <div class="card-footer bg-white border-0 py-3">
                  <a href="activity_logs.php" class="btn btn-sm btn-outline-primary w-100">View All Activities</a>
                </div>
              </div>
            </div>
          </div>

          <!-- Quick Stats -->
          <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-header bg-white border-0 py-3">
                <h6 class="m-0 fw-bold"><i class="bi bi-lightning-charge me-2"></i>Quick Stats</h6>
              </div>
              <div class="card-body p-0">
                <div class="list-group list-group-flush">
                  <?php
                  // Get today's date range
                  $today_start = date('Y-m-d 00:00:00');
                  $today_end = date('Y-m-d 23:59:59');
                  
                  // Today's bookings
                  $today_bookings = mysqli_fetch_assoc(mysqli_query($con, "
                    SELECT COUNT(*) as count FROM `booking_order` 
                    WHERE `datentime` BETWEEN '$today_start' AND '$today_end'"));
                  
                  // Pending reviews
                  $pending_reviews = mysqli_fetch_assoc(mysqli_query($con, "
                    SELECT COUNT(*) as count FROM `rating_review` 
                    WHERE `seen` = 0"));
                    
                  // Unread messages
                  $unread_messages = mysqli_fetch_assoc(mysqli_query($con, "
                    SELECT COUNT(*) as count FROM `user_queries` 
                    WHERE `seen` = 0"));
                  ?>
                  
                  <div class='list-group-item border-0 py-3'>
                    <div class='d-flex justify-content-between align-items-center mb-1'>
                      <span class='fw-medium'>Today's Bookings</span>
                      <span class='badge bg-primary rounded-pill'><?php echo $today_bookings['count']; ?></span>
                    </div>
                    <div class='progress' style='height: 6px;'>
                      <div class='progress-bar' role='progressbar' style='width: 75%' aria-valuenow='75' aria-valuemin='0' aria-valuemax='100'></div>
                    </div>
                  </div>
                  
                  <div class='list-group-item border-0 py-3'>
                    <div class='d-flex justify-content-between align-items-center mb-1'>
                      <span class='fw-medium'>Pending Reviews</span>
                      <span class='badge bg-warning text-dark rounded-pill'><?php echo $pending_reviews['count']; ?></span>
                    </div>
                    <div class='progress' style='height: 6px;'>
                      <div class='progress-bar bg-warning' role='progressbar' style='width: 45%' aria-valuenow='45' aria-valuemin='0' aria-valuemax='100'></div>
                    </div>
                  </div>
                  
                  <div class='list-group-item border-0 py-3'>
                    <div class='d-flex justify-content-between align-items-center mb-1'>
                      <span class='fw-medium'>Unread Messages</span>
                      <span class='badge bg-info text-dark rounded-pill'><?php echo $unread_messages['count']; ?></span>
                    </div>
                    <div class='progress' style='height: 6px;'>
                      <div class='progress-bar bg-info' role='progressbar' style='width: 60%' aria-valuenow='60' aria-valuemin='0' aria-valuemax='100'></div>
                    </div>
                  </div>
                  
                  <div class='list-group-item border-0 py-3'>
                    <div class='d-flex justify-content-between align-items-center mb-1'>
                      <span class='fw-medium'>System Status</span>
                      <span class='badge bg-success rounded-pill'>Online</span>
                    </div>
                    <div class='progress' style='height: 6px;'>
                      <div class='progress-bar bg-success' role='progressbar' style='width: 95%' aria-valuenow='95' aria-valuemin='0' aria-valuemax='100'></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>


      </div>
    </div>
  </div>

  <?php require('inc/scripts.php'); ?>
  
  <style>
    /* Custom styles for the enhanced dashboard */
    .icon-shape {
      width: 50px;
      height: 50px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }
    
    .card {
      transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
      border-radius: 0.75rem;
      overflow: hidden;
    }
    
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08) !important;
    }
    
    .progress {
      border-radius: 10px;
      background-color: #f0f2f5;
    }
    
    .progress-bar {
      border-radius: 10px;
    }
    
    .list-group-item {
      border-left: 0;
      border-right: 0;
    }
    
    .list-group-item:first-child {
      border-top: 0;
    }
    
    .list-group-item:last-child {
      border-bottom: 0;
    }
    
    /* Animation for stats */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .stats-card {
      animation: fadeInUp 0.6s ease-out forwards;
      opacity: 0;
    }
    
    .stats-card:nth-child(1) { animation-delay: 0.1s; }
    .stats-card:nth-child(2) { animation-delay: 0.2s; }
    .stats-card:nth-child(3) { animation-delay: 0.3s; }
    .stats-card:nth-child(4) { animation-delay: 0.4s; }
    
    /* Custom scrollbar */
    .activity-feed {
      max-height: 400px;
      overflow-y: auto;
    }
    
    .activity-feed::-webkit-scrollbar {
      width: 6px;
    }
    
    .activity-feed::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 10px;
    }
    
    .activity-feed::-webkit-scrollbar-thumb {
      background: #888;
      border-radius: 10px;
    }
    
    .activity-feed::-webkit-scrollbar-thumb:hover {
      background: #555;
    }
  </style>
  
  <script>
    // Add animation class to stats cards
    document.addEventListener('DOMContentLoaded', function() {
      const statsCards = document.querySelectorAll('.stats-card');
      statsCards.forEach(card => {
        card.style.opacity = '1';
      });
      
      // Initialize tooltips
      const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
      });
    });
    
    // Time ago function for activity feed
    function timeAgo(date) {
      const seconds = Math.floor((new Date() - new Date(date)) / 1000);
      let interval = Math.floor(seconds / 31536000);
      
      if (interval > 1) return interval + ' years ago';
      if (interval === 1) return '1 year ago';
      
      interval = Math.floor(seconds / 2592000);
      if (interval > 1) return interval + ' months ago';
      if (interval === 1) return '1 month ago';
      
      interval = Math.floor(seconds / 86400);
      if (interval > 1) return interval + ' days ago';
      if (interval === 1) return '1 day ago';
      
      interval = Math.floor(seconds / 3600);
      if (interval > 1) return interval + ' hours ago';
      if (interval === 1) return '1 hour ago';
      
      interval = Math.floor(seconds / 60);
      if (interval > 1) return interval + ' minutes ago';
      if (interval === 1) return '1 minute ago';
      
      return 'just now';
    }
    
    // Update all time-ago elements
    document.querySelectorAll('.time-ago').forEach(element => {
      const timestamp = element.getAttribute('data-time');
      if (timestamp) {
        element.textContent = timeAgo(timestamp);
      }
    });
  </script>
  
  <?php require('inc/scripts.php'); ?>
  <script src="scripts/dashboard.js?v=<?php echo filemtime('scripts/dashboard.js'); ?>"></script>
</body>
</html>