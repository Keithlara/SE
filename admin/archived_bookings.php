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
  <title>Admin Panel - Archived Bookings</title>
  <?php require('inc/links.php'); ?>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4 overflow-hidden">
        <h3 class="mb-4">ARCHIVED BOOKINGS</h3>

        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">
            <div class="text-end mb-4">
              <input type="text" id="search_input" class="form-control shadow-none w-25 ms-auto" placeholder="Type to search...">
            </div>

            <div class="table-responsive">
              <table class="table table-hover border" style="min-width: 1200px;" id="archiveTable">
                <thead>
                  <tr class="bg-dark text-light">
                    <th scope="col">#</th>
                    <th scope="col">Order ID</th>
                    <th scope="col">User Details</th>
                    <th scope="col">Room Details</th>
                    <th scope="col">Booking Details</th>
                    <th scope="col">Status</th>
                  </tr>
                </thead>
                <tbody id="table-data">                 
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  
  <script>
    let archiveTable;

    function get_archived_bookings() {
      let xhr = new XMLHttpRequest();
      xhr.open("POST", "ajax/archived_bookings.php", true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      
      xhr.onload = function() {
        document.getElementById('table-data').innerHTML = this.responseText;
        if (!$.fn.DataTable.isDataTable('#archiveTable')) {
          archiveTable = $('#archiveTable').DataTable({
            "pageLength": 10,
            "order": [[0, 'desc']],
            "columnDefs": [
              { "orderable": false, "targets": [5] }
            ]
          });
        }
      }
      
      xhr.send('get_archived_bookings');
    }

    // Search functionality
    document.getElementById('search_input').addEventListener('input', function() {
      if (archiveTable) {
        archiveTable.search(this.value).draw();
      }
    });

    // Load archived bookings on page load
    window.onload = function() {
      get_archived_bookings();
    };
  </script>

  <?php require('inc/scripts.php'); ?>
</body>
</html>
