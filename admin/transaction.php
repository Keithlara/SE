<?php
  require('inc/essentials.php');
  require('inc/db_config.php');
  adminLogin();
  requireAdminPermission('reports.view');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Transaction Management</title>
  <?php require('inc/links.php'); ?>
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4 overflow-hidden">
        <h3 class="mb-4"> Transaction History</h3>

        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">

            <div class="row g-2 mb-3 align-items-end">
              <div class="col-md-2">
                <label class="form-label">From</label>
                <input type="date" id="t_from" class="form-control shadow-none">
              </div>
              <div class="col-md-2">
                <label class="form-label">To</label>
                <input type="date" id="t_to" class="form-control shadow-none">
              </div>
              <div class="col-md-2">
                <label class="form-label">Method</label>
                <input type="text" id="t_method" class="form-control shadow-none" placeholder="e.g. Paytm, Cash">
              </div>
              <div class="col-md-2">
                <label class="form-label">Status</label>
                <select id="t_status" class="form-select shadow-none">
                  <option value="">All</option>
                  <option value="paid">Paid</option>
                  <option value="pending">Pending</option>
                  <option value="refunded">Refunded</option>
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label">Search</label>
                <input type="text" id="t_search" class="form-control shadow-none" placeholder="Guest/Room">
              </div>
              <div class="col-md-2 text-end">
                <a id="t_export_csv" class="btn btn-sm btn-outline-primary me-2" href="#">Export CSV</a>
                <a id="t_export_pdf" class="btn btn-sm btn-outline-danger" href="#">Export PDF</a>
              </div>
            </div>

            <div class="table-responsive">
              <table class="table table-hover border">
                <thead>
                  <tr class="bg-dark text-light">
                    <th>#</th>
                    <th>Guest / Room</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Type</th>
                  </tr>
                </thead>
                <tbody id="table-data">                 
                </tbody>
              </table>
            </div>

            <ul class="pagination" id="table-pagination"></ul>

          </div>
        </div>

      </div>
    </div>
  </div>



  <!-- Assign Room Number modal -->

  <div class="modal fade" id="assign-room" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form id="assign_room_form">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Assign Room</h5>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label fw-bold">Room Number</label>
              <input type="text" name="room_no" class="form-control shadow-none" required>
            </div>
            <span class="badge rounded-pill bg-light text-dark mb-3 text-wrap lh-base">
              Note: Assign Room Number only when user has been arrived!
            </span>
            <input type="hidden" name="booking_id">
          </div>
          <div class="modal-footer">
            <button type="reset" class="btn text-secondary shadow-none" data-bs-dismiss="modal">CANCEL</button>
            <button type="submit" class="btn custom-bg text-white shadow-none">ASSIGN</button>
          </div>
        </div>
      </form>
    </div>
  </div>



  <?php require('inc/scripts.php'); ?>

  <script src="scripts/transactions.js"></script>

</body>
</html>
