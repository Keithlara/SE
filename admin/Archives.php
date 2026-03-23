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
            <!-- Archive Type Tabs -->
            <ul class="nav nav-tabs mb-4" id="archiveTabs" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="bookings-tab" data-bs-toggle="tab" data-bs-target="#bookings" type="button" role="tab" aria-controls="bookings" aria-selected="true" onclick="changeArchiveType('bookings')">
                  <i class="bi bi-journal-text me-1"></i> Bookings
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="rooms-tab" data-bs-toggle="tab" data-bs-target="#rooms" type="button" role="tab" aria-controls="rooms" aria-selected="false" onclick="changeArchiveType('rooms')">
                  <i class="bi bi-house-door me-1"></i> Rooms
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab" aria-controls="users" aria-selected="false" onclick="changeArchiveType('users')">
                  <i class="bi bi-people me-1"></i> Users
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="queries-tab" data-bs-toggle="tab" data-bs-target="#queries" type="button" role="tab" aria-controls="queries" aria-selected="false" onclick="changeArchiveType('queries')">
                  <i class="bi bi-chat-square-text me-1"></i> Queries
                </button>
              </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content">
              <!-- Bookings Tab -->
              <div class="tab-pane fade show active" id="bookings" role="tabpanel" aria-labelledby="bookings-tab">
                <div class="row g-2 mb-3">
                  <div class="col-md-3 ms-auto">
                    <input type="text" id="search_bookings" class="form-control shadow-none" placeholder="Search bookings..." oninput="get_archives('bookings')">
                  </div>
                  <div class="col-md-2">
                    <input type="date" id="date_from_bookings" class="form-control shadow-none" onchange="get_archives('bookings')">
                  </div>
                  <div class="col-md-2">
                    <input type="date" id="date_to_bookings" class="form-control shadow-none" onchange="get_archives('bookings')">
                  </div>
                  <div class="col-md-2">
                    <input type="text" id="guest_bookings" class="form-control shadow-none" placeholder="Guest name" oninput="get_archives('bookings')">
                  </div>
                  <div class="col-md-2">
                    <input type="text" id="room_type_bookings" class="form-control shadow-none" placeholder="Room type" oninput="get_archives('bookings')">
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
                    <tbody id="bookings-data">                  
                    </tbody>
                  </table>
                </div>
                <div class="mt-3">
                  <ul class="pagination" id="bookings-pagination"></ul>
                </div>
              </div>

              <!-- Rooms Tab -->
              <div class="tab-pane fade" id="rooms" role="tabpanel" aria-labelledby="rooms-tab">
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
                    <tbody id="rooms-data">                  
                    </tbody>
                  </table>
                </div>
                <div class="mt-3">
                  <ul class="pagination" id="rooms-pagination"></ul>
                </div>
              </div>
              
              <!-- Users Tab -->
              <div class="tab-pane fade" id="users" role="tabpanel" aria-labelledby="users-tab">
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

              <!-- Queries Tab -->
              <div class="tab-pane fade" id="queries" role="tabpanel" aria-labelledby="queries-tab">
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
                      </tr>
                    </thead>
                    <tbody id="queries-data"></tbody>
                  </table>
                </div>
                <div class="mt-3">
                  <ul class="pagination" id="queries-pagination"></ul>
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



  <!-- Room Details Modal -->
  <div class="modal fade" id="roomDetailsModal" tabindex="-1" aria-labelledby="roomDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="roomDetailsModalLabel">Room Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <!-- Room Images Carousel -->
            <div class="col-md-7">
              <div id="roomCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators" id="carouselIndicators">
                  <!-- Indicators will be added by JavaScript -->
                </div>
                <div class="carousel-inner rounded" id="carouselItems">
                  <!-- Carousel items will be added by JavaScript -->
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
              
              <!-- Room Description -->
              <div class="card mt-3">
                <div class="card-body">
                  <h5 class="card-title">Description</h5>
                  <p class="card-text" id="roomDescription">Loading description...</p>
                </div>
              </div>
              
              <!-- Room Features -->
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
            
            <!-- Room Details -->
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
                        <span>Price: ₱<span id="roomPrice">0</span> / night</span>
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
                  
                  <!-- Facilities -->
                  <h5>Facilities</h5>
                  <div class="mb-3" id="roomFacilities">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                      <span class="visually-hidden">Loading...</span>
                    </div>
                    Loading facilities...
                  </div>
                  
                  <!-- Rating Distribution -->
                  <h5 class="mt-4">Rating Distribution</h5>
                  <div id="ratingDistribution">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                      <span class="visually-hidden">Loading...</span>
                    </div>
                    Loading rating data...
                  </div>
                </div>
              </div>
              
              <!-- Reviews -->
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
  <script src="scripts/archives.js"></script>

</body>
</html>