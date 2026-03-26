<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('inc/links.php'); ?>
  <title><?php echo $settings_r['site_title'] ?> - Book Room</title>
  <style>
    .booking-container {
      max-width: 4000px;
      margin: 0 auto;
    }
    .room-preview {
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    }
    .room-preview img {
      width: 100%;
      height: 550px;
      object-fit: cover;
    }
    .booking-form {
      background: #fff;
      border-radius: 12px;
      padding: 25px;
      box-shadow: 0 2px 12px rgba(0,0,0,0.08);
      height: 100%;
    }
    .form-section {
      margin-bottom: 20px;
    }
    .form-section-title {
      font-size: 0.85rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #6c757d;
      margin-bottom: 8px;
      font-weight: 600;
    }
    .date-inputs {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px;
    }
    .date-inputs input[type="date"] {
      padding: 12px;
      border: 2px solid #e9ecef;
      border-radius: 8px;
      font-size: 0.95rem;
      cursor: pointer;
    }
    .date-inputs input[type="date"]:focus {
      border-color: #2ec1ac;
      outline: none;
    }
    .payment-box {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-radius: 12px;
      padding: 20px;
      color: white;
      margin-top: 20px;
    }
    .payment-box h6 {
      color: white;
      margin-bottom: 15px;
    }
    .qr-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px;
      margin-top: 15px;
    }
    .qr-item {
      background: white;
      border-radius: 8px;
      padding: 10px;
      text-align: center;
    }
    .qr-item img {
      width: 100%;
      max-width: 120px;
      border-radius: 6px;
      cursor: pointer;
    }
    .qr-item small {
      color: #666;
      display: block;
      margin-top: 5px;
    }
    .room-grid-container {
      margin: 15px 0;
      padding: 15px;
      background: #f8f9fa;
      border-radius: 8px;
    }
    .price-display {
      font-size: 1.5rem;
      font-weight: 700;
      color: #2ec1ac;
    }
    .btn-submit {
      background: #2ec1ac;
      border: none;
      padding: 14px 30px;
      font-size: 1rem;
      font-weight: 600;
      border-radius: 8px;
      width: 100%;
    }
    .btn-submit:hover:not(:disabled) {
      background: #279e8c;
    }
    .btn-submit:disabled {
      background: #ccc;
    }
    .guest-info-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px;
    }
    .guest-info-grid .col-12 {
      grid-column: 1 / -1;
    }
    @media (max-width: 992px) {
      .room-preview img {
        height: 250px;
      }
    }
    @media (max-width: 768px) {
      .guest-info-grid {
        grid-template-columns: 1fr;
      }
      .guest-info-grid .col-12 {
        grid-column: 1;
      }
    }
  </style>
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <?php 

    /*
      Check room id from url is present or not
      Shutdown mode is active or not
      User is logged in or not
    */

    if(!isset($_GET['id']) || $settings_r['shutdown']==true){
      redirect('rooms.php');
    }
    else if(!(isset($_SESSION['login']) && $_SESSION['login']==true)){
      redirect('rooms.php');
    }

    // filter and get room and user data

    $data = filteration($_GET);

    $room_res = select("SELECT * FROM `rooms` WHERE `id`=? AND `status`=? AND `removed`=?",[$data['id'],1,0],'iii');

    if(mysqli_num_rows($room_res)==0){
      redirect('rooms.php');
    }

    $room_data = mysqli_fetch_assoc($room_res);

    $_SESSION['room'] = [
      "id" => $room_data['id'],
      "name" => $room_data['name'],
      "price" => $room_data['price'],
      "payment" => null,
      "available" => false,
    ];

    // Fetch room features & facilities for display
    $room_features_q = mysqli_query($con, "SELECT f.name FROM `features` f 
      INNER JOIN `room_features` rf ON f.id = rf.features_id 
      WHERE rf.room_id = '{$room_data['id']}'");
    $room_features = [];
    while($row = mysqli_fetch_assoc($room_features_q)) $room_features[] = $row['name'];

    $room_facilities_q = mysqli_query($con, "SELECT f.name FROM `facilities` f 
      INNER JOIN `room_facilities` rf ON f.id = rf.facilities_id 
      WHERE rf.room_id = '{$room_data['id']}'");
    $room_facilities = [];
    while($row = mysqli_fetch_assoc($room_facilities_q)) $room_facilities[] = $row['name'];


    $user_res = select("SELECT * FROM `user_cred` WHERE `id`=? LIMIT 1", [$_SESSION['uId']], "i");
    $user_data = mysqli_fetch_assoc($user_res);

    if(!$user_data){
      redirect('logout.php');
    }

    if(empty($_SESSION['booking_csrf'])){
      $_SESSION['booking_csrf'] = bin2hex(random_bytes(32));
    }
    $booking_csrf = $_SESSION['booking_csrf'];

    $gcash_number = settings($con, 'payment_gcash_number');
    $maya_number = settings($con, 'payment_maya_number');

  ?>

  <div class="container py-4">
    <div class="booking-container">
      
      <!-- Header -->
      <div class="text-center mb-4">
        <h2 class="fw-bold mb-2">Complete Your Booking</h2>
        <p class="text-muted">Fill in your details and select your dates</p>
      </div>

      <div class="row g-4 align-items-start">
        <!-- Room Preview - Left side (40% - blue area) -->
        <div class="col-lg-5">
          <?php 
            $room_thumb = ROOMS_IMG_PATH."thumbnail.jpg";
            $thumb_q = mysqli_query($con,"SELECT * FROM `room_images`
              WHERE `room_id`='{$room_data['id']}'
              AND `thumb`='1'");

            if(mysqli_num_rows($thumb_q)>0){
              $thumb_res = mysqli_fetch_assoc($thumb_q);
              $room_thumb = ROOMS_IMG_PATH.$thumb_res['image'];
            }
          ?>
          <div class="room-preview">
            <img src="<?php echo $room_thumb; ?>" alt="<?php echo $room_data['name']; ?>">
            <div class="p-3 bg-white">
              <h5 class="fw-bold mb-1"><?php echo $room_data['name']; ?></h5>
              <p class="text-warning fw-bold mb-0" style="font-size:1.1rem;">₱<?php echo number_format($room_data['price']); ?> / night</p>
            </div>
          </div>

          <!-- Room Details Card -->
          <div class="mt-3 bg-white rounded-3 shadow-sm p-3">
            <?php if($room_data['description']): ?>
            <p class="text-muted small mb-3" style="line-height:1.7;"><?php echo htmlspecialchars($room_data['description']); ?></p>
            <?php endif; ?>

            <div class="row g-2 mb-2">
              <div class="col-6">
                <div class="d-flex align-items-center gap-2 text-muted small">
                  <i class="bi bi-people-fill text-warning"></i>
                  <span><?php echo $room_data['adult']; ?> Adults · <?php echo $room_data['children']; ?> Children</span>
                </div>
              </div>
              <div class="col-6">
                <div class="d-flex align-items-center gap-2 text-muted small">
                  <i class="bi bi-rulers text-warning"></i>
                  <span><?php echo $room_data['area']; ?> sqm</span>
                </div>
              </div>
            </div>

            <?php if(!empty($room_features)): ?>
            <div class="mb-2">
              <div class="fw-semibold small mb-1" style="color:#555;">Features</div>
              <div class="d-flex flex-wrap gap-1">
                <?php foreach($room_features as $feat): ?>
                  <span class="badge rounded-pill" style="background:#f0ede6;color:#555;font-weight:500;font-size:0.7rem;"><?php echo htmlspecialchars($feat); ?></span>
                <?php endforeach; ?>
              </div>
            </div>
            <?php endif; ?>

            <?php if(!empty($room_facilities)): ?>
            <div>
              <div class="fw-semibold small mb-1" style="color:#555;">Facilities</div>
              <div class="d-flex flex-wrap gap-1">
                <?php foreach($room_facilities as $fac): ?>
                  <span class="badge rounded-pill" style="background:#eaf4ff;color:#3b7dd8;font-weight:500;font-size:0.7rem;"><?php echo htmlspecialchars($fac); ?></span>
                <?php endforeach; ?>
              </div>
            </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Booking Form - Right side (60% - red area) -->
        <div class="col-lg-7">
          <div class="booking-form">
            <form action="pay_now.php" method="POST" id="booking_form" enctype="multipart/form-data">
              <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($booking_csrf, ENT_QUOTES, 'UTF-8'); ?>">

              <!-- Guest Info - Compact -->
              <div class="form-section mb-2">
                <div class="form-section-title">Guest Information</div>
                <div class="row g-1">
                  <div class="col-sm-4">
                    <label class="form-label small text-muted mb-0">Full Name</label>
                    <input name="name" type="text" value="<?php echo $user_data['name'] ?>" class="form-control form-control-sm" required>
                  </div>
                  <div class="col-sm-4">
                    <label class="form-label small text-muted mb-0">Phone Number</label>
                    <input name="phonenum" type="tel" value="<?php echo $user_data['phonenum'] ?>" class="form-control form-control-sm" required>
                  </div>
                  <div class="col-sm-4">
                    <label class="form-label small text-muted mb-0">Address</label>
                    <textarea name="address" class="form-control form-control-sm" rows="1" required><?php echo $user_data['address'] ?></textarea>
                  </div>
                </div>
              </div>

              <!-- Dates & Room - Side by side -->
              <div class="row g-2 mb-2">
                <div class="col-sm-6">
                  <div class="form-section mb-0">
                    <div class="form-section-title">Check-in / Check-out</div>
                    <div class="row g-1">
                      <div class="col-6">
                        <input name="checkin" id="checkin" onchange="check_availability()" onclick="try{this.showPicker()}catch(e){}" type="date" class="form-control form-control-sm" style="cursor:pointer;" required>
                      </div>
                      <div class="col-6">
                        <input name="checkout" id="checkout" onchange="check_availability()" onclick="try{this.showPicker()}catch(e){}" type="date" class="form-control form-control-sm" style="cursor:pointer;" required>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-section mb-0">
                    <div class="form-section-title">Select Room Number</div>
                    <div id="user-assign-legend" class="legend mb-1" style="font-size: 11px; gap: 8px;"></div>
                    <div id="user-assign-grid" class="seat-grid" style="max-height: 80px; overflow-y: auto; padding: 4px;"></div>
                  </div>
                </div>
              </div>

              <!-- Price -->
              <div class="text-center mb-2">
                <div id="pay_info" class="text-danger small">Select dates to see price</div>
                <div class="spinner-border text-info d-none" id="info_loader" role="status" style="width: 1rem; height: 1rem;">
                  <span class="visually-hidden">Loading...</span>
                </div>
              </div>

              <!-- Upload & Notes - Side by side -->
              <div class="row g-2 mb-2">
                <div class="col-sm-6">
                  <div class="form-section mb-0">
                    <div class="form-section-title">Payment Proof</div>
                    <input type="file" name="billing_proof" id="billing_proof" accept=".jpg,.jpeg,.png,.pdf" class="form-control form-control-sm" required style="display: none;">
                    <label for="billing_proof" class="btn btn-outline-secondary btn-sm w-100" id="upload_label">
                      <i class="bi bi-upload me-1"></i><span id="upload_text">Upload</span>
                    </label>
                    <div id="file_preview" class="mt-1 d-none">
                      <div class="d-flex align-items-center gap-1 p-1 border rounded bg-light">
                        <i class="bi bi-file-earmark-image text-success"></i>
                        <span id="filename_display" class="text-truncate flex-grow-1 small"></span>
                        <span class="badge bg-success">Selected</span>
                      </div>
                      <img id="image_preview" class="img-fluid rounded mt-1 d-none" style="max-height: 60px;">
                    </div>
                    <small class="text-muted">Max 10MB</small>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-section mb-0">
                    <div class="form-section-title">Special Requests</div>
                    <textarea name="booking_note" class="form-control form-control-sm" rows="1" maxlength="500" placeholder="Any requests..."></textarea>
                  </div>
                </div>
              </div>

              <input type="hidden" name="room_no" />

              <!-- Booking Policy -->
              <div class="mb-2 rounded-3 p-3" style="background:#fffbf0;border:1.5px solid #f0c040;">
                <div class="fw-bold small mb-2" style="color:#b8860b;">
                  <i class="bi bi-shield-exclamation me-1"></i> Booking Policy &amp; House Rules
                </div>
                <ul class="mb-0 ps-3" style="font-size:0.78rem;color:#555;line-height:1.8;">
                  <li><strong>50% downpayment</strong> is required to confirm your booking. Upload your payment proof above.</li>
                  <li>The remaining 50% balance is due <strong>upon check-in</strong>.</li>
                  <li>Check-in time is <strong>2:00 PM</strong>; Check-out time is <strong>12:00 PM</strong> (noon).</li>
                  <li><strong>Cancellations</strong> will be refunded <strong>50%</strong> of the total amount paid.</li>
                  <li>Guests are responsible for any damage to room property during their stay.</li>
                  <li><strong>No smoking</strong> inside the rooms. Designated smoking areas are available outside.</li>
                  <li>Please observe <strong>quiet hours from 10:00 PM to 7:00 AM</strong>.</li>
                  <li>A valid <strong>government-issued ID</strong> is required upon check-in.</li>
                </ul>
              </div>

              <!-- Agree checkbox -->
              <div class="form-check mb-2">
                <input class="form-check-input shadow-none" type="checkbox" id="agree_policy" onchange="toggleBookBtn()">
                <label class="form-check-label small" for="agree_policy">
                  I have read and agree to the <strong>booking policy and house rules</strong> above.
                </label>
              </div>

              <button name="pay_now" id="pay_now_btn" class="btn btn-submit text-white btn-sm w-100 mb-2" disabled>
                Complete Booking
              </button>
            </form>

            <!-- Payment Info - Compact -->
            <div class="payment-box p-2">
              <h6 class="small mb-1"><i class="bi bi-wallet2 me-1"></i>Pay via GCash or Maya</h6>
              <div class="row g-1 mb-1">
                <div class="col-6">
                  <small class="d-block text-white-50" style="font-size: 0.7rem;">GCash</small>
                  <span class="small fw-semibold"><?php echo $gcash_number; ?></span>
                </div>
                <div class="col-6">
                  <small class="d-block text-white-50" style="font-size: 0.7rem;">Maya</small>
                  <span class="small fw-semibold"><?php echo $maya_number; ?></span>
                </div>
              </div>
              <div class="qr-grid">
                <div class="qr-item p-1">
                  <a href="#" class="qr-code" data-qr-src="images/qr/GCASH.jpg" data-qr-title="GCash QR">
                    <img src="images/qr/GCASH.jpg" alt="GCash QR" style="max-width: 80px;">
                  </a>
                  <small class="d-block mt-1" style="font-size: 0.7rem;">GCash QR</small>
                </div>
                <div class="qr-item p-1">
                  <a href="#" class="qr-code" data-qr-src="images/qr/MAYA.jpg" data-qr-title="Maya QR">
                    <img src="images/qr/MAYA.jpg" alt="Maya QR" style="max-width: 80px;">
                  </a>
                  <small class="d-block mt-1" style="font-size: 0.7rem;">Maya QR</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- QR Modal -->
  <div class="modal fade" id="qrModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="qrModalLabel">QR Code</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-center">
          <img id="modalQrImage" src="" class="img-fluid" style="max-width: 300px;">
          <div class="mt-3">
            <button class="btn btn-secondary btn-sm" onclick="window.print()">
              <i class="bi bi-printer"></i> Print
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php require('inc/footer.php'); ?>

  <script>

    let booking_form = document.getElementById('booking_form');
    let info_loader = document.getElementById('info_loader');
    let pay_info = document.getElementById('pay_info');
    let grid = document.getElementById('user-assign-grid');
    let legend = document.getElementById('user-assign-legend');

    function toggleBookBtn() {
      const agreed = document.getElementById('agree_policy').checked;
      const roomNo = booking_form.elements['room_no'].value;
      if (agreed && roomNo) {
        booking_form.elements['pay_now'].removeAttribute('disabled');
      } else {
        booking_form.elements['pay_now'].setAttribute('disabled', true);
      }
    }

    function check_availability()
    {
      let checkin_val = booking_form.elements['checkin'].value;
      let checkout_val = booking_form.elements['checkout'].value;

      booking_form.elements['pay_now'].setAttribute('disabled',true);

      if(checkin_val!='' && checkout_val!='')
      {
        pay_info.classList.add('d-none');
        pay_info.classList.replace('text-dark','text-danger');
        info_loader.classList.remove('d-none');

        let data = new FormData();

        data.append('check_availability','');
        data.append('check_in',checkin_val);
        data.append('check_out',checkout_val);

        let xhr = new XMLHttpRequest();
        xhr.open("POST","ajax/confirm_booking.php",true);

        xhr.onload = function()
        {
          let data = JSON.parse(this.responseText);

          if(data.status == 'check_in_out_equal'){
            pay_info.innerText = "You cannot check-out on the same day!";
          }
          else if(data.status == 'check_out_earlier'){
            pay_info.innerText = "Check-out date is earlier than check-in date!";
          }
          else if(data.status == 'check_in_earlier'){
            pay_info.innerText = "Check-in date is earlier than today's date!";
          }
          else if(data.status == 'unavailable'){
            pay_info.innerText = "Room not available for this check-in date!";
            if(grid) grid.innerHTML = '';
          }
          else{
            pay_info.innerHTML = "No. of Days: "+data.days+"<br>Total Amount to Pay: ₱"+data.payment+"<br><small class=\"text-muted\">Select a room number to continue.</small>";
            pay_info.classList.replace('text-danger','text-dark');
            // Require room selection before enabling payment
            booking_form.elements['pay_now'].setAttribute('disabled',true);
            booking_form.elements['room_no'].value = '';
            // Load rooms map for this room and dates
            load_user_rooms_map(checkin_val, checkout_val);
          }

          pay_info.classList.remove('d-none');
          info_loader.classList.add('d-none');
        }

        xhr.send(data);
      }

    }

    function load_user_rooms_map(checkin_val, checkout_val){
      if(!grid) return;
      if(legend){
        legend.className = 'legend';
        legend.innerHTML = '<span class="legend-item"><span class="legend-swatch available"></span><span>Available</span></span>'+
                           '<span class="legend-item"><span class="legend-swatch pending"></span><span>Pending</span></span>'+
                           '<span class="legend-item"><span class="legend-swatch occupied"></span><span>Occupied</span></span>';
      }
      grid.innerHTML = '<div class="text-muted">Loading...</div>';
      let xhr = new XMLHttpRequest();
      xhr.open('GET', 'ajax/rooms_map.php?room_id='+encodeURIComponent(<?php echo (int)$room_data['id']; ?>)+'&check_in='+encodeURIComponent(checkin_val)+'&check_out='+encodeURIComponent(checkout_val), true);
      xhr.onload = function(){
        try{
          const res = JSON.parse(this.responseText);
          render_user_assign_grid(res.room);
        }catch(e){ grid.innerHTML = '<div class="text-danger">Failed to load</div>'; }
      }
      xhr.send();
    }

    function render_user_assign_grid(room){
      if(!grid) return;
      grid.innerHTML = '';
      if(!room || !Array.isArray(room.seats) || room.seats.length===0){
        grid.innerHTML = '<div class="text-muted">No data</div>';
        return;
      }
      const perRow = 10;
      for(let i=0;i<room.seats.length;i+=perRow){
        const row = document.createElement('div');
        row.className = 'seat-row';
        const left = document.createElement('div');
        left.className = 'seat-row-label';
        left.textContent = '';
        row.appendChild(left);
        room.seats.slice(i,i+perRow).forEach((s, idx) => {
          const seat = document.createElement('div');
          const cls = (s.status==='occupied') ? 'occupied' : (s.status==='pending' ? 'pending' : 'available');
          seat.className = 'seat ' + cls;
          seat.textContent = String(i + idx + 1);
          seat.title = (cls==='available') ? 'Click to choose' : cls;
          seat.style.cursor = (cls==='available') ? 'pointer' : 'not-allowed';
          if(cls==='available'){
            seat.addEventListener('click', function(){
              Array.from(grid.querySelectorAll('.seat.selected')).forEach(el=>el.classList.remove('selected'));
              seat.classList.add('selected');
              booking_form.elements['room_no'].value = seat.textContent;
              toggleBookBtn();
            });
          }
          row.appendChild(seat);
        });
        grid.appendChild(row);
      }
    }

  </script>

  <script>
    // QR Code Modal Functionality
    document.addEventListener('DOMContentLoaded', function() {
      const qrModalEl = document.getElementById('qrModal');
      const modalQrImage = document.getElementById('modalQrImage');
      const qrModalLabel = document.getElementById('qrModalLabel');

      // Create ONE shared instance — never create a new one on each click
      const qrModal = new bootstrap.Modal(qrModalEl, { backdrop: true, keyboard: true });

      // Ensure backdrop is fully removed whenever the modal is hidden
      qrModalEl.addEventListener('hidden.bs.modal', function () {
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
      });

      // When a QR code is clicked, update content then reuse the same instance
      document.querySelectorAll('.qr-code').forEach(item => {
        item.addEventListener('click', function(e) {
          e.preventDefault();
          modalQrImage.src = this.getAttribute('data-qr-src');
          modalQrImage.alt = this.getAttribute('data-qr-title');
          qrModalLabel.textContent = this.getAttribute('data-qr-title');
          qrModal.show();
        });
      });
    });

    // File upload preview
    document.getElementById('billing_proof').addEventListener('change', function(e) {
      const file = e.target.files[0];
      const previewDiv = document.getElementById('file_preview');
      const filenameDisplay = document.getElementById('filename_display');
      const imagePreview = document.getElementById('image_preview');
      const uploadText = document.getElementById('upload_text');
      const uploadLabel = document.getElementById('upload_label');
      
      if (file) {
        filenameDisplay.textContent = file.name;
        previewDiv.classList.remove('d-none');
        uploadText.textContent = 'Change File';
        uploadLabel.classList.remove('btn-outline-secondary');
        uploadLabel.classList.add('btn-outline-success');
        
        // Show image preview if it's an image
        if (file.type.startsWith('image/')) {
          const reader = new FileReader();
          reader.onload = function(e) {
            imagePreview.src = e.target.result;
            imagePreview.classList.remove('d-none');
          };
          reader.readAsDataURL(file);
        } else {
          imagePreview.classList.add('d-none');
        }
      }
    });

    // Check URL parameters for booking success
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('booking') === 'success') {
      Swal.fire({
        icon: 'success',
        title: 'Booking Successful!',
        text: 'Your booking has been submitted successfully. You will receive a confirmation soon.',
        confirmButtonText: 'View My Bookings',
        confirmButtonColor: '#2ec1ac',
        showCancelButton: true,
        cancelButtonText: 'Book Another Room',
        cancelButtonColor: '#6c757d'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = 'bookings.php';
        } else {
          window.location.href = 'rooms.php';
        }
      });
    }
  </script>

</body>
</html>