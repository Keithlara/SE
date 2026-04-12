<?php
require('inc/essentials.php');
require('inc/db_config.php');
adminLogin();
requireAdminPermission('reports.all_time');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - All Time Reports</title>
  <?php require('inc/links.php'); ?>
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4 overflow-hidden">
        <h3 class="mb-4">Reports</h3>

        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">

            <div class="row g-2 mb-3 align-items-end">
              <div class="col-md-2">
                <label class="form-label">Granularity</label>
                <select id="granularity" class="form-select shadow-none" onchange="load_report()">
                  <option value="daily">Daily</option>
                  <option value="monthly">Monthly</option>
                  <option value="yearly">Yearly</option>
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label">From</label>
                <input type="date" id="from" class="form-control shadow-none" onchange="load_report()">
              </div>
              <div class="col-md-2">
                <label class="form-label">To</label>
                <input type="date" id="to" class="form-control shadow-none" onchange="load_report()">
              </div>
              <div class="col-md-6 text-end">
                <a id="export-csv" class="btn btn-sm btn-outline-primary me-2" href="#">Export CSV</a>
                <a id="export-pdf" class="btn btn-sm btn-outline-danger" href="#">Export PDF</a>
              </div>
            </div>

            <div class="row g-3">
              <div class="col-md-3"><div class="p-3 border rounded"><div class="text-muted">Reservations</div><div id="metric-reservations" class="fs-4 fw-bold">0</div></div></div>
              <div class="col-md-3"><div class="p-3 border rounded"><div class="text-muted">Cancelled</div><div id="metric-cancelled" class="fs-4 fw-bold">0</div></div></div>
              <div class="col-md-3"><div class="p-3 border rounded"><div class="text-muted">Revenue</div><div id="metric-revenue" class="fs-4 fw-bold">₱0</div></div></div>
              <div class="col-md-3"><div class="p-3 border rounded"><div class="text-muted">Occupancy</div><div id="metric-occupancy" class="fs-4 fw-bold">0%</div></div></div>
            </div>

            <div class="row g-3 mt-1">
              <div class="col-md-3"><div class="p-3 border rounded"><div class="text-muted">Refund Rate</div><div id="metric-refund-rate" class="fs-5 fw-bold">0%</div></div></div>
              <div class="col-md-3"><div class="p-3 border rounded"><div class="text-muted">Repeat Guests</div><div id="metric-repeat-guests" class="fs-5 fw-bold">0</div></div></div>
              <div class="col-md-3"><div class="p-3 border rounded"><div class="text-muted">Top Room</div><div id="metric-top-room" class="fw-bold">No bookings yet</div></div></div>
              <div class="col-md-3"><div class="p-3 border rounded"><div class="text-muted">Top Add-on</div><div id="metric-top-extra" class="fw-bold">None</div></div></div>
            </div>

            <hr>
            <div class="row">
              <div class="col-md-8">
                <canvas id="lineChart" height="120"></canvas>
              </div>
              <div class="col-md-4">
                <canvas id="pieChart" height="120"></canvas>
              </div>
            </div>

              <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                  <div class="row g-2 mb-3 align-items-end">
                    <div class="col-md-3">
                      <label class="form-label">Occupancy Date</label>
                      <input type="date" id="occ-date" class="form-control shadow-none">
                    </div>
                    <div class="col-md-9 text-end">
                      <button class="btn btn-sm btn-outline-secondary" onclick="trigger_occupancy_load()">Load</button>
                    </div>
                  </div>
                  <div class="fw-bold mb-2">Rooms Map</div>
                  <div class="legend mb-2">
                    <span class="legend-item"><span class="legend-swatch available"></span><span>Available</span></span>
                    <span class="legend-item"><span class="legend-swatch pending"></span><span>Pending</span></span>
                    <span class="legend-item"><span class="legend-swatch occupied"></span><span>Occupied</span></span>
                  </div>
                  <div id="occ-grid-reports" class="seat-grid"></div>
                </div>
              </div>

          
          </div>
        </div>

      </div>
    </div>
  </div>







  <?php require('inc/scripts.php'); ?>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="scripts/reports.js?v=<?php echo filemtime('scripts/reports.js'); ?>"></script>

</body>
</html>
