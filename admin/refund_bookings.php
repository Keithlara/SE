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
  <title>Admin Panel - Refund Bookings</title>
  <?php require('inc/links.php'); ?>
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4 overflow-hidden">
        <h3 class="mb-4">REFUND BOOKINGS</h3>

        <ul class="nav nav-tabs mb-0" id="refundTabs">
          <li class="nav-item">
            <button class="nav-link active fw-semibold" id="tab-pending" onclick="switchTab('pending')">
              <i class="bi bi-clock-history me-1"></i> Pending Refunds
            </button>
          </li>
          <li class="nav-item">
            <button class="nav-link fw-semibold" id="tab-processed" onclick="switchTab('processed')">
              <i class="bi bi-check-circle me-1"></i> Processed Refunds
            </button>
          </li>
        </ul>

        <div class="card border-0 shadow-sm mb-4" style="border-top-left-radius:0;">
          <div class="card-body">

            <div class="text-end mb-4">
              <input type="text" id="search-box" oninput="searchCurrent(this.value)" class="form-control shadow-none w-25 ms-auto" placeholder="Type to search...">
            </div>

            <!-- Pending table -->
            <div id="panel-pending">
              <div class="table-responsive">
                <table class="table table-hover border" style="min-width: 1200px;">
                  <thead>
                    <tr class="bg-dark text-light">
                      <th scope="col">#</th>
                      <th scope="col">User Details</th>
                      <th scope="col">Room Details</th>
                      <th scope="col">Refund Amount</th>
                      <th scope="col">Action</th>
                    </tr>
                  </thead>
                  <tbody id="table-data"></tbody>
                </table>
              </div>
            </div>

            <!-- Processed table -->
            <div id="panel-processed" class="d-none">
              <div class="table-responsive">
                <table class="table table-hover border" style="min-width: 1200px;">
                  <thead>
                    <tr class="bg-success text-light">
                      <th scope="col">#</th>
                      <th scope="col">User Details</th>
                      <th scope="col">Room Details</th>
                      <th scope="col">Refund Amount</th>
                      <th scope="col">Proof</th>
                    </tr>
                  </thead>
                  <tbody id="table-data-processed"></tbody>
                </table>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>



<?php require('inc/scripts.php'); ?>
  <script src="scripts/refund_bookings.js?v=<?php echo filemtime('scripts/refund_bookings.js'); ?>"></script>

  <script>
    let currentTab = 'pending';

    function switchTab(tab) {
      currentTab = tab;
      document.getElementById('tab-pending').classList.toggle('active', tab === 'pending');
      document.getElementById('tab-processed').classList.toggle('active', tab === 'processed');
      document.getElementById('panel-pending').classList.toggle('d-none', tab !== 'pending');
      document.getElementById('panel-processed').classList.toggle('d-none', tab !== 'processed');
      const search = document.getElementById('search-box').value;
      if (tab === 'pending') get_bookings(search);
      else get_processed(search);
    }

    function searchCurrent(val) {
      if (currentTab === 'pending') get_bookings(val);
      else get_processed(val);
    }
  </script>

</body>
</html>
