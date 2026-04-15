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
  <title>Admin Panel - New Bookings</title>
  <?php require('inc/links.php'); ?>
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4 overflow-hidden">
        <ul class="nav nav-tabs mb-4" id="bookingTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending-bookings" type="button" role="tab">
              Pending Bookings
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="assign-tab" data-bs-toggle="tab" data-bs-target="#assign-rooms" type="button" role="tab">
              Assign Rooms
            </button>
          </li>
        </ul>

        <div class="tab-content" id="bookingTabsContent">
          <!-- Pending Bookings Tab -->
          <div class="tab-pane fade show active" id="pending-bookings" role="tabpanel" aria-labelledby="pending-tab">
            <div class="card border-0 shadow-sm mb-4">
              <div class="card-body">
                <div class="text-end mb-4">
                  <input type="text" oninput="get_bookings(this.value, 'pending')" class="form-control shadow-none w-25 ms-auto" placeholder="Search pending bookings...">
                </div>
                <div class="table-responsive">
                  <table class="table table-hover border">
                    <thead>
                      <tr class="bg-dark text-light">
                        <th scope="col">#</th>
                        <th scope="col">User Details</th>
                        <th scope="col">Room Details</th>
                        <th scope="col">Booking Details</th>
                        <th scope="col">Action</th>
                      </tr>
                    </thead>
                    <tbody id="pending-bookings-data"></tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <!-- Assign Rooms Tab -->
          <div class="tab-pane fade" id="assign-rooms" role="tabpanel" aria-labelledby="assign-tab">
            <div class="card border-0 shadow-sm">
              <div class="card-body">
                <div class="text-end mb-4">
                  <input type="text" oninput="get_bookings(this.value, 'confirmed')" class="form-control shadow-none w-25 ms-auto" placeholder="Search confirmed bookings...">
                </div>
                <div class="table-responsive">
                  <table class="table table-hover border">
                    <thead>
                      <tr class="bg-dark text-light">
                        <th scope="col">#</th>
                        <th scope="col">User Details</th>
                        <th scope="col">Room Details</th>
                        <th scope="col">Booking Details</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                      </tr>
                    </thead>
                    <tbody id="assign-rooms-data"></tbody>
                  </table>
                </div>
              </div>
            </div>
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
              <label class="form-label fw-bold">Pick Room</label>
              <div id="assign-legend" class="legend mb-2">
                <span class="legend-item"><span class="legend-swatch available"></span><span>Available</span></span>
                <span class="legend-item"><span class="legend-swatch pending"></span><span>Pending</span></span>
                <span class="legend-item"><span class="legend-swatch occupied"></span><span>Occupied</span></span>
              </div>
              <div id="assign-grid" class="seat-grid"></div>
            </div>
            <span class="badge rounded-pill bg-light text-dark mb-3 text-wrap lh-base">
              Note: Assign Room Number only when user has been arrived!
            </span>
            <input type="hidden" name="booking_id">
            <input type="hidden" name="room_no">
          </div>
          <div class="modal-footer">
            <button type="reset" class="btn text-secondary shadow-none" data-bs-dismiss="modal">CANCEL</button>
            <button type="submit" class="btn custom-bg text-white shadow-none">ASSIGN</button>
          </div>
        </div>
      </form>
    </div>
</div>



  <!-- Payment proof modal -->
  <div class="modal fade" id="payment-proof-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Payment Proof</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="payment-proof-viewer" class="text-center"></div>
        </div>
        <div class="modal-footer">
          <a id="payment-proof-download" href="#" target="_blank" class="btn btn-outline-primary shadow-none" rel="noopener">
            <i class="bi bi-download me-1"></i> Download
          </a>
          <button type="button" class="btn btn-secondary shadow-none" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>


<?php require('inc/scripts.php'); ?>

  <script src="scripts/new_bookings.js?v=<?php echo filemtime('scripts/new_bookings.js'); ?>"></script>

</body>
</html>