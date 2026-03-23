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
  <title>Admin Panel - Booking Records</title>
  <?php require('inc/links.php'); ?>
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4 overflow-hidden">
        <h3 class="mb-4">BOOKING RECORDS</h3>

        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
              <div class="d-flex flex-wrap align-items-center gap-2">
                <select id="filter_month" class="form-select shadow-none" onchange="applyFilters()">
                  <option value="">All months</option>
                  <option value="1">January</option>
                  <option value="2">February</option>
                  <option value="3">March</option>
                  <option value="4">April</option>
                  <option value="5">May</option>
                  <option value="6">June</option>
                  <option value="7">July</option>
                  <option value="8">August</option>
                  <option value="9">September</option>
                  <option value="10">October</option>
                  <option value="11">November</option>
                  <option value="12">December</option>
                </select>

                <select id="filter_year" class="form-select shadow-none" onchange="applyFilters()">
                  <option value="">All years</option>
                  <?php
                    $currentYear = (int)date('Y');
                    for($y = $currentYear - 3; $y <= $currentYear + 3; $y++){
                      echo "<option value=\"{$y}\">{$y}</option>";
                    }
                  ?>
                </select>

                <select id="filter_status" class="form-select shadow-none" onchange="applyFilters()">
                  <option value="all">All statuses</option>
                  <option value="booked">Booked</option>
                  <option value="cancelled">Cancelled</option>
                  <option value="payment_failed">Payment failed</option>
                </select>
              </div>

              <input id="search_input" type="text" oninput="get_bookings(this.value)" class="form-control shadow-none w-25" placeholder="Type to search...">
            </div>

            <div class="table-responsive">
              <table class="table table-hover border" style="min-width: 1200px;">
                <thead>
                  <tr class="bg-dark text-light">
                    <th scope="col">#</th>
                    <th scope="col">User Details</th>
                    <th scope="col">Room Details</th>
                    <th scope="col">Booking Amount/Date</th>
                    <th scope="col">Status</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                <tbody id="table-data">
                </tbody>
              </table>
            </div>

            <nav aria-label="Page navigation example">
              <ul id="table-pagination" class="pagination justify-content-end mb-0">
              </ul>
            </nav>

          </div>
        </div>

      </div>
    </div>
  </div>

  <?php require('inc/scripts.php'); ?>

  <script src="scripts/booking_records.js"></script>

</body>
</html>


