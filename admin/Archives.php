<?php
  require('inc/essentials.php');
  require('inc/db_config.php');
  adminLogin();

  $allowed_archive_tabs = ['bookings', 'rooms', 'users', 'queries', 'tickets', 'transactions', 'reviews'];
  $active_archive_tab = strtolower(trim((string)($_GET['tab'] ?? 'bookings')));
  if (!in_array($active_archive_tab, $allowed_archive_tabs, true)) {
    $active_archive_tab = 'bookings';
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Archives</title>
  <?php require('inc/links.php'); ?>
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4 overflow-hidden">
        <h3 class="mb-4"> Archives</h3>

        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">
            <ul class="nav nav-tabs mb-4" id="archiveTabs" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link <?php echo $active_archive_tab === 'bookings' ? 'active' : ''; ?>" id="bookings-tab" data-bs-toggle="tab" data-bs-target="#bookings" type="button" role="tab" aria-controls="bookings" aria-selected="<?php echo $active_archive_tab === 'bookings' ? 'true' : 'false'; ?>" onclick="changeArchiveType('bookings')">
                  <i class="bi bi-journal-text me-1"></i> Bookings
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link <?php echo $active_archive_tab === 'rooms' ? 'active' : ''; ?>" id="rooms-tab" data-bs-toggle="tab" data-bs-target="#rooms" type="button" role="tab" aria-controls="rooms" aria-selected="<?php echo $active_archive_tab === 'rooms' ? 'true' : 'false'; ?>" onclick="changeArchiveType('rooms')">
                  <i class="bi bi-house-door me-1"></i> Rooms
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link <?php echo $active_archive_tab === 'users' ? 'active' : ''; ?>" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab" aria-controls="users" aria-selected="<?php echo $active_archive_tab === 'users' ? 'true' : 'false'; ?>" onclick="changeArchiveType('users')">
                  <i class="bi bi-people me-1"></i> Users
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link <?php echo $active_archive_tab === 'queries' ? 'active' : ''; ?>" id="queries-tab" data-bs-toggle="tab" data-bs-target="#queries" type="button" role="tab" aria-controls="queries" aria-selected="<?php echo $active_archive_tab === 'queries' ? 'true' : 'false'; ?>" onclick="changeArchiveType('queries')">
                  <i class="bi bi-chat-square-text me-1"></i> Queries
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link <?php echo $active_archive_tab === 'tickets' ? 'active' : ''; ?>" id="tickets-tab" data-bs-toggle="tab" data-bs-target="#tickets" type="button" role="tab" aria-controls="tickets" aria-selected="<?php echo $active_archive_tab === 'tickets' ? 'true' : 'false'; ?>" onclick="changeArchiveType('tickets')">
                  <i class="bi bi-life-preserver me-1"></i> Support
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link <?php echo $active_archive_tab === 'transactions' ? 'active' : ''; ?>" id="transactions-tab" data-bs-toggle="tab" data-bs-target="#transactions" type="button" role="tab" aria-controls="transactions" aria-selected="<?php echo $active_archive_tab === 'transactions' ? 'true' : 'false'; ?>" onclick="changeArchiveType('transactions')">
                  <i class="bi bi-receipt me-1"></i> Transactions
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link <?php echo $active_archive_tab === 'reviews' ? 'active' : ''; ?>" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab" aria-controls="reviews" aria-selected="<?php echo $active_archive_tab === 'reviews' ? 'true' : 'false'; ?>" onclick="changeArchiveType('reviews')">
                  <i class="bi bi-star-half me-1"></i> Reviews
                </button>
              </li>
            </ul>

            <div class="tab-content">
              <div class="tab-pane fade <?php echo $active_archive_tab === 'bookings' ? 'show active' : ''; ?>" id="bookings" role="tabpanel" aria-labelledby="bookings-tab">
                <div class="row g-2 mb-3">
                  <div class="col-md-3">
                    <input type="text" id="search_bookings" class="form-control shadow-none" placeholder="Search bookings..." oninput="refreshArchive('bookings')">
                  </div>
                  <div class="col-md-2">
                    <input type="date" id="date_from_bookings" class="form-control shadow-none" onchange="refreshArchive('bookings')">
                  </div>
                  <div class="col-md-2">
                    <input type="date" id="date_to_bookings" class="form-control shadow-none" onchange="refreshArchive('bookings')">
                  </div>
                  <div class="col-md-2">
                    <input type="text" id="guest_bookings" class="form-control shadow-none" placeholder="Guest name" oninput="refreshArchive('bookings')">
                  </div>
                  <div class="col-md-2">
                    <input type="text" id="room_type_bookings" class="form-control shadow-none" placeholder="Room type" oninput="refreshArchive('bookings')">
                  </div>
                  <div class="col-md-3 text-md-end">
                    <div class="btn-group w-100 w-md-auto">
                      <button type="button" class="btn btn-outline-secondary" onclick="downloadArchiveExport('csv')">
                        <i class="bi bi-filetype-csv me-1"></i> CSV
                      </button>
                      <button type="button" class="btn btn-outline-secondary" onclick="downloadArchiveExport('pdf')">
                        <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                      </button>
                    </div>
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="table table-hover border" style="min-width: 1200px;">
                    <thead>
                      <tr class="bg-dark text-light">
                        <th scope="col">#</th>
                        <th scope="col">User Details</th>
                        <th scope="col">Room Details</th>
                        <th scope="col">Booking Details</th>
                        <th scope="col">Action</th>
                      </tr>
                    </thead>
                    <tbody id="bookings-data"></tbody>
                  </table>
                </div>
                <div class="mt-3">
                  <ul class="pagination" id="bookings-pagination"></ul>
                </div>
              </div>

              <div class="tab-pane fade <?php echo $active_archive_tab === 'rooms' ? 'show active' : ''; ?>" id="rooms" role="tabpanel" aria-labelledby="rooms-tab">
                <div class="row g-2 mb-3">
                  <div class="col-md-4 ms-auto">
                    <input type="text" id="search_rooms" class="form-control shadow-none" placeholder="Search archived rooms..." oninput="refreshArchive('rooms')">
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="table table-hover border">
                    <thead>
                      <tr class="bg-dark text-light">
                        <th scope="col">#</th>
                        <th scope="col">Room Name</th>
                        <th scope="col">Area</th>
                        <th scope="col">Price</th>
                        <th scope="col">Archived On</th>
                        <th scope="col">Action</th>
                      </tr>
                    </thead>
                    <tbody id="rooms-data"></tbody>
                  </table>
                </div>
                <div class="mt-3">
                  <ul class="pagination" id="rooms-pagination"></ul>
                </div>
              </div>

              <div class="tab-pane fade <?php echo $active_archive_tab === 'users' ? 'show active' : ''; ?>" id="users" role="tabpanel" aria-labelledby="users-tab">
                <div class="row g-2 mb-3">
                  <div class="col-md-4 ms-auto">
                    <input type="text" id="search_users" class="form-control shadow-none" placeholder="Search archived users..." oninput="refreshArchive('users')">
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="table table-hover border">
                    <thead>
                      <tr class="bg-dark text-light">
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Phone</th>
                        <th scope="col">Archived On</th>
                        <th scope="col">Action</th>
                      </tr>
                    </thead>
                    <tbody id="users-data"></tbody>
                  </table>
                </div>
                <div class="mt-3">
                  <ul class="pagination" id="users-pagination"></ul>
                </div>
              </div>

              <div class="tab-pane fade <?php echo $active_archive_tab === 'queries' ? 'show active' : ''; ?>" id="queries" role="tabpanel" aria-labelledby="queries-tab">
                <div class="row g-2 mb-3">
                  <div class="col-md-4 ms-auto">
                    <input type="text" id="search_queries" class="form-control shadow-none" placeholder="Search archived queries..." oninput="refreshArchive('queries')">
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="table table-hover border">
                    <thead>
                      <tr class="bg-dark text-light">
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Subject</th>
                        <th scope="col">Message</th>
                        <th scope="col">Archived On</th>
                        <th scope="col">Action</th>
                      </tr>
                    </thead>
                    <tbody id="queries-data"></tbody>
                  </table>
                </div>
                <div class="mt-3">
                  <ul class="pagination" id="queries-pagination"></ul>
                </div>
              </div>

              <div class="tab-pane fade <?php echo $active_archive_tab === 'tickets' ? 'show active' : ''; ?>" id="tickets" role="tabpanel" aria-labelledby="tickets-tab">
                <div class="row g-2 mb-3">
                  <div class="col-md-4 ms-auto">
                    <input type="text" id="search_tickets" class="form-control shadow-none" placeholder="Search archived tickets..." oninput="refreshArchive('tickets')">
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="table table-hover border">
                    <thead>
                      <tr class="bg-dark text-light">
                        <th scope="col">#</th>
                        <th scope="col">Ticket</th>
                        <th scope="col">Category</th>
                        <th scope="col">Status</th>
                        <th scope="col">Replies</th>
                        <th scope="col">Archived On</th>
                        <th scope="col">Action</th>
                      </tr>
                    </thead>
                    <tbody id="tickets-data"></tbody>
                  </table>
                </div>
                <div class="mt-3">
                  <ul class="pagination" id="tickets-pagination"></ul>
                </div>
              </div>

              <div class="tab-pane fade <?php echo $active_archive_tab === 'transactions' ? 'show active' : ''; ?>" id="transactions" role="tabpanel" aria-labelledby="transactions-tab">
                <div class="row g-2 mb-3">
                  <div class="col-md-4 ms-auto">
                    <input type="text" id="search_transactions" class="form-control shadow-none" placeholder="Search archived transactions..." oninput="refreshArchive('transactions')">
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="table table-hover border">
                    <thead>
                      <tr class="bg-dark text-light">
                        <th scope="col">#</th>
                        <th scope="col">Guest / Room</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Method / Type</th>
                        <th scope="col">Status</th>
                        <th scope="col">Archived On</th>
                        <th scope="col">Action</th>
                      </tr>
                    </thead>
                    <tbody id="transactions-data"></tbody>
                  </table>
                </div>
                <div class="mt-3">
                  <ul class="pagination" id="transactions-pagination"></ul>
                </div>
              </div>

              <div class="tab-pane fade <?php echo $active_archive_tab === 'reviews' ? 'show active' : ''; ?>" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                <div class="row g-2 mb-3">
                  <div class="col-md-4 ms-auto">
                    <input type="text" id="search_reviews" class="form-control shadow-none" placeholder="Search archived reviews..." oninput="refreshArchive('reviews')">
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="table table-hover border">
                    <thead>
                      <tr class="bg-dark text-light">
                        <th scope="col">#</th>
                        <th scope="col">Room ID</th>
                        <th scope="col">User ID</th>
                        <th scope="col">Rating</th>
                        <th scope="col">Review</th>
                        <th scope="col">Archived On</th>
                        <th scope="col">Action</th>
                      </tr>
                    </thead>
                    <tbody id="reviews-data"></tbody>
                  </table>
                </div>
                <div class="mt-3">
                  <ul class="pagination" id="reviews-pagination"></ul>
                </div>
              </div>

            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <script>
    window.initialArchiveTab = '<?php echo $active_archive_tab; ?>';
  </script>

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

  <div class="modal fade" id="roomDetailsModal" tabindex="-1" aria-labelledby="roomDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="roomDetailsModalLabel">Room Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-7">
              <div id="roomCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators" id="carouselIndicators"></div>
                <div class="carousel-inner rounded" id="carouselItems">
                  <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                      <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading room details...</p>
                  </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#roomCarousel" data-bs-slide="prev">
                  <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#roomCarousel" data-bs-slide="next">
                  <span class="carousel-control-next-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Next</span>
                </button>
              </div>

              <div class="card mt-3">
                <div class="card-body">
                  <h5 class="card-title">Description</h5>
                  <p class="card-text" id="roomDescription">Loading description...</p>
                </div>
              </div>

              <div class="card mt-3">
                <div class="card-body">
                  <h5 class="card-title">Features</h5>
                  <div id="roomFeatures">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                      <span class="visually-hidden">Loading...</span>
                    </div>
                    Loading features...
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-5">
              <div class="card">
                <div class="card-body">
                  <h3 class="card-title" id="roomName">Room Name</h3>
                  <div class="d-flex align-items-center mb-3">
                    <div class="text-warning me-2" id="roomRating">
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-half"></i>
                    </div>
                    <span class="text-muted" id="reviewCount">(0 reviews)</span>
                  </div>

                  <div class="row mb-3">
                    <div class="col-6">
                      <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-arrows-angle-expand me-2 text-primary"></i>
                        <span>Area: <span id="roomArea">0</span> sq.ft</span>
                      </div>
                      <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-people me-2 text-primary"></i>
                        <span>Adults: <span id="roomAdults">0</span></span>
                      </div>
                      <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-people me-2 text-primary"></i>
                        <span>Children: <span id="roomChildren">0</span></span>
                      </div>
                    </div>
                    <div class="col-6">
                      <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-tag me-2 text-primary"></i>
                        <span>Price: &#8369;<span id="roomPrice">0</span> / night</span>
                      </div>
                      <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-calendar-check me-2 text-primary"></i>
                        <span>Archived on: <span id="archivedDate">-</span></span>
                      </div>
                      <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle me-2 text-primary"></i>
                        <span id="roomStatus">Status: -</span>
                      </div>
                    </div>
                  </div>

                  <hr>

                  <h5>Facilities</h5>
                  <div class="mb-3" id="roomFacilities">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                      <span class="visually-hidden">Loading...</span>
                    </div>
                    Loading facilities...
                  </div>

                  <h5 class="mt-4">Rating Distribution</h5>
                  <div id="ratingDistribution">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                      <span class="visually-hidden">Loading...</span>
                    </div>
                    Loading rating data...
                  </div>
                </div>
              </div>

              <div class="card mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h5 class="mb-0">Reviews</h5>
                  <span class="badge bg-primary rounded-pill" id="reviewBadge">0</span>
                </div>
                <div class="card-body" id="roomReviews" style="max-height: 300px; overflow-y: auto;">
                  <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                      <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading reviews...</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="restoreRoomBtn">
            <i class="bi bi-arrow-counterclockwise"></i> Restore Room
          </button>
        </div>
      </div>
    </div>
  </div>

  <?php require('inc/scripts.php'); ?>
  <script src="scripts/archives.js?v=<?php echo filemtime('scripts/archives.js'); ?>"></script>

</body>
</html>
