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
  <title>Admin Panel - Activity Logs</title>
  <?php require('inc/links.php'); ?>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <style>
    .dataTables_length, .dataTables_filter {
      margin-bottom: 1rem;
    }
  </style>
</head>
<body class="bg-light">
  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4 overflow-hidden">
        <h3 class="mb-4">ACTIVITY LOGS</h3>

        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover border" id="logs-table">
                <thead>
                  <tr class="bg-dark text-light">
                    <th>#</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Details</th>
                    <th>Timestamp</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $res = selectAll('activity_logs ORDER BY created_at DESC');
                  $i = 1;
                  while($row = mysqli_fetch_assoc($res)) {
                    echo "
                    <tr>
                      <td>$i</td>
                      <td>" . htmlspecialchars($row['user_name']) . "</td>
                      <td>" . htmlspecialchars($row['action']) . "</td>
                      <td>" . htmlspecialchars($row['details']) . "</td>
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
      $('#logs-table').DataTable({
        order: [[4, 'desc']], // Sort by timestamp by default
        pageLength: 25,
        responsive: true
      });
    });
  </script>
</body>
</html>
