<?php
require('inc/essentials.php');
require('inc/db_config.php');
adminLogin();

header('Location: Archives.php?tab=users');
exit;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Archived Users</title>
  <?php require('inc/links.php'); ?>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <style>
    .user-image {
      width: 50px;
      height: 50px;
      object-fit: cover;
      border-radius: 50%;
    }
  </style>
</head>
<body class="bg-light">
  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4 overflow-hidden">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h3 class="mb-0">Archived Users</h3>
          <a href="users.php" class="btn btn-dark">
            <i class="bi bi-arrow-left"></i> Back to Active Users
          </a>
        </div>

        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <div class="text-muted">
                Showing archived user accounts
              </div>
              <div class="input-group" style="width: 300px;">
                <input type="text" id="search_input" class="form-control shadow-none" placeholder="Search users...">
                <button class="btn btn-outline-secondary" type="button" id="search_btn">
                  <i class="bi bi-search"></i>
                </button>
              </div>
            </div>

            <div class="table-responsive">
              <table class="table table-hover border" id="archiveTable">
                <thead>
                  <tr class="bg-dark text-light">
                    <th>#</th>
                    <th>User</th>
                    <th>Contact</th>
                    <th>Status</th>
                    <th>Archived On</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody id="table-data"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Restore Confirmation Modal -->
  <div class="modal fade" id="restoreModal" tabindex="-1" aria-labelledby="restoreModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="restoreModalLabel">Restore User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to restore this user account?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-success" id="confirmRestore">Restore User</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  
  <script>
    let archiveTable;
    let selectedUserId = null;

    // Load archived users
    function loadArchivedUsers() {
      $.ajax({
        url: 'ajax/archived_users.php',
        type: 'POST',
        data: { get_archived_users: 1 },
        success: function(response) {
          $('#table-data').html(response);
          
          if (!$.fn.DataTable.isDataTable('#archiveTable')) {
            archiveTable = $('#archiveTable').DataTable({
              "pageLength": 10,
              "order": [[4, 'desc']], // Sort by archived date by default
              "columnDefs": [
                { "orderable": false, "targets": [5] } // Make actions column not sortable
              ]
            });
          }
        }
      });
    }

    // Restore user
    function restoreUser(userId) {
      selectedUserId = userId;
      $('#restoreModal').modal('show');
    }

    // Confirm restore
    $('#confirmRestore').click(function() {
      if (!selectedUserId) return;
      
      $.ajax({
        url: 'ajax/archived_users.php',
        type: 'POST',
        data: { 
          restore_user: 1,
          user_id: selectedUserId
        },
        success: function(response) {
          if (response == 1) {
            alert('success', 'User restored successfully');
            loadArchivedUsers();
          } else {
            alert('error', 'Failed to restore user');
          }
          $('#restoreModal').modal('hide');
        }
      });
    });

    // Search functionality
    $('#search_btn').click(function() {
      if (archiveTable) {
        archiveTable.search($('#search_input').val()).draw();
      }
    });

    // Press Enter to search
    $('#search_input').keypress(function(e) {
      if (e.which == 13) {
        if (archiveTable) {
          archiveTable.search($(this).val()).draw();
        }
        return false;
      }
    });

    // Load data on page load
    $(document).ready(function() {
      loadArchivedUsers();
    });
  </script>

  <?php require('inc/scripts.php'); ?>
</body>
</html>
