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
  <title>Admin Panel - User Login Logs</title>
  <?php require('inc/links.php'); ?>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <style>
    .dataTables_length, .dataTables_filter {
      margin-bottom: 1rem;
    }
    .status-success { color: #198754; font-weight: 500; }
    .status-failed { color: #dc3545; font-weight: 500; }
  </style>
</head>
<body class="bg-light">
  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4 overflow-hidden">
        <h3 class="mb-4">USER LOGIN LOGS</h3>

        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover border" id="login-logs-table">
                <thead class="bg-dark text-light">
                  <tr>
                    <th>#</th>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Status</th>
                    <th>IP Address</th>
                    <th>User Agent</th>
                    <th>Timestamp</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $con = $GLOBALS['con'];
                  $query = "SELECT * FROM `activity_logs` 
                           WHERE `action` LIKE 'Login%' 
                           ORDER BY `created_at` DESC";
                  $res = mysqli_query($con, $query);
                  $i = 1;
                  while($row = mysqli_fetch_assoc($res)) {
                    $statusClass = strpos($row['action'], 'Failed') !== false ? 'status-failed' : 'status-success';
                    echo "
                    <tr>
                      <td>$i</td>
                      <td>" . ($row['user_id'] ?: 'N/A') . "</td>
                      <td>" . htmlspecialchars($row['user_name']) . "</td>
                      <td class='$statusClass'>" . htmlspecialchars($row['action']) . "</td>
                      <td>" . htmlspecialchars($row['ip_address']) . "</td>
                      <td title='" . htmlspecialchars($row['user_agent']) . "'>" . 
                         (strlen($row['user_agent']) > 30 ? 
                          substr(htmlspecialchars($row['user_agent']), 0, 30) . '...' : 
                          htmlspecialchars($row['user_agent'])) . 
                      "</td>
                      <td>" . date('M d, Y h:i A', strtotime($row['created_at'])) . "</td>
                    </tr>
                    ";
                    $i++;
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php require('inc/scripts.php'); ?>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  <script>
    $(document).ready(function() {
      $('#login-logs-table').DataTable({
        "order": [[6, "desc"]], // Sort by timestamp column (index 6) in descending order
        "pageLength": 25,
        "responsive": true,
        "language": {
          "search": "_INPUT_",
          "searchPlaceholder": "Search logs..."
        }
      });
    });
  </script>
</body>
</html>
