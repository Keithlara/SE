<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('inc/links.php'); ?>
  <title><?php echo $settings_r['site_title'] ?> - Book Room</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <style>
    .booking-container {
      max-width: 4000px;
      margin: 0 auto;
    }
    .room-preview {
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 2px 12px rgba(0,0,0,0.08);
      background: #fff;
    }
    .room-preview .carousel-item {
      background: #fff;
    }
    .room-preview-media {
      height: 550px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #fff;
      overflow: hidden;
    }
    .room-preview-image {
      width: 100%;
      height: 100%;
      object-fit: contain;
      object-position: center;
      display: block;
    }
    .room-preview .carousel-control-prev,
    .room-preview .carousel-control-next {
      width: 12%;
    }
    .room-preview .carousel-control-prev-icon,
    .room-preview .carousel-control-next-icon {
      background-color: rgba(15, 23, 42, 0.38);
      border-radius: 999px;
      background-size: 55% 55%;
      width: 2.4rem;
      height: 2.4rem;
    }
    .room-preview .carousel-indicators {
      margin-bottom: 0.8rem;
    }
    .room-preview .carousel-indicators button {
      width: 9px;
      height: 9px;
      border-radius: 999px;
      border: none;
      background-color: rgba(255,255,255,0.85);
    }
    .room-preview .carousel-indicators .active {
      background-color: #2ec1ac;
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
    /* ── Date Picker Cards ── */
    .date-card-row {
      display: flex;
      gap: 0;
      border: 2px solid #d0d5dd;
      border-radius: 12px;
      overflow: hidden;
      background: #fff;
      cursor: pointer;
      transition: border-color 0.2s, box-shadow 0.2s;
    }
    .date-card-row:hover {
      border-color: #2ec1ac;
      box-shadow: 0 0 0 3px rgba(46,193,172,0.12);
    }
    .date-card-row.has-focus {
      border-color: #2ec1ac;
      box-shadow: 0 0 0 3px rgba(46,193,172,0.15);
    }
    .date-card {
      flex: 1;
      padding: 10px 12px;
      position: relative;
      min-width: 0;
    }
    .date-card + .date-card {
      border-left: 1.5px solid #e5e7eb;
    }
    .date-card .dc-label {
      font-size: 0.62rem;
      font-weight: 700;
      letter-spacing: 0.08em;
      color: #888;
      text-transform: uppercase;
      display: flex;
      align-items: center;
      gap: 4px;
      margin-bottom: 2px;
    }
    .date-card .dc-label i { font-size: 0.75rem; color: #2ec1ac; }
    .date-card .dc-date {
      font-size: 1rem;
      font-weight: 700;
      color: #1a1a2e;
      line-height: 1.2;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .date-card .dc-date.placeholder { color: #aab0be; font-weight: 400; font-size: 0.85rem; }
    .date-card .dc-day {
      font-size: 0.7rem;
      color: #888;
      margin-top: 1px;
    }
    /* hide the real date inputs visually but keep them accessible */
    .date-card input[type="date"],
    .date-card input[type="text"] {
      position: absolute;
      opacity: 0;
      width: 1px;
      height: 1px;
      top: 0; left: 0;
      pointer-events: none;
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
    .booking-stepper {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 12px;
      margin-bottom: 22px;
    }
    .booking-step-chip {
      border: 1px solid #dbe5f0;
      background: #f8fafc;
      border-radius: 16px;
      padding: 14px 16px;
      display: flex;
      align-items: center;
      gap: 12px;
      text-align: left;
      width: 100%;
      transition: all 0.18s ease;
      color: #475569;
    }
    .booking-step-chip.is-clickable {
      cursor: pointer;
    }
    .booking-step-chip.is-active {
      border-color: #2ec1ac;
      background: rgba(46,193,172,0.08);
      box-shadow: 0 10px 24px rgba(46,193,172,0.12);
      color: #0f172a;
    }
    .booking-step-chip.is-complete {
      border-color: rgba(46,193,172,0.28);
      background: rgba(46,193,172,0.05);
    }
    .booking-step-chip-number {
      width: 34px;
      height: 34px;
      border-radius: 999px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: #e2e8f0;
      color: #475569;
      font-weight: 700;
      flex-shrink: 0;
    }
    .booking-step-chip.is-active .booking-step-chip-number,
    .booking-step-chip.is-complete .booking-step-chip-number {
      background: #2ec1ac;
      color: #fff;
    }
    .booking-step-chip-title {
      font-size: 0.95rem;
      font-weight: 700;
      display: block;
      color: inherit;
    }
    .booking-step-chip-note {
      font-size: 0.75rem;
      color: #64748b;
      display: block;
      margin-top: 2px;
      line-height: 1.4;
    }
    .wizard-head {
      margin-bottom: 18px;
    }
    .wizard-head-step {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 6px 10px;
      border-radius: 999px;
      background: rgba(46,193,172,0.1);
      color: #117a6f;
      font-size: 0.78rem;
      font-weight: 700;
      letter-spacing: 0.04em;
      text-transform: uppercase;
    }
    .wizard-head-title {
      font-size: 1.4rem;
      font-weight: 700;
      color: #0f172a;
      margin: 12px 0 6px;
    }
    .wizard-head-copy {
      color: #64748b;
      font-size: 0.93rem;
      margin-bottom: 0;
    }
    .booking-step-panel {
      display: none;
      animation: wizardFadeIn 0.22s ease;
    }
    .booking-step-panel.is-active {
      display: block;
    }
    .step-section-card {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 18px;
      padding: 18px;
      margin-bottom: 16px;
    }
    .step-section-note {
      font-size: 0.83rem;
      color: #64748b;
      margin-bottom: 12px;
      line-height: 1.6;
    }
    .promo-code-row {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      align-items: flex-start;
    }
    .promo-code-row .promo-input-col {
      flex: 1 1 220px;
      min-width: 0;
    }
    .promo-code-row .promo-btn-col {
      flex: 0 0 124px;
      width: 124px;
    }
    .promo-code-row #promo_code_input {
      min-height: 46px;
    }
    .promo-code-row .btn {
      width: 100%;
      height: 46px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      white-space: nowrap;
      font-size: 0.95rem;
      padding: 0 10px;
    }
    @media (max-width: 575.98px) {
      .promo-code-row .promo-btn-col {
        flex: 1 1 100%;
        width: 100%;
      }
    }
    .step-summary-card {
      background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
      border: 1px solid #dbe5f0;
      border-radius: 18px;
      padding: 18px;
      margin-bottom: 16px;
    }
    .wizard-controls {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 12px;
      margin-top: 12px;
      padding-top: 16px;
      border-top: 1px solid #e5e7eb;
    }
    .wizard-controls .btn {
      min-width: 170px;
    }
    .wizard-progress-copy {
      font-size: 0.82rem;
      color: #64748b;
      margin: 0;
      flex: 1 1 100%;
      max-width: 100%;
      text-align: left;
      line-height: 1.55;
      order: -1;
    }
    .wizard-back-btn {
      border-radius: 12px;
      padding: 11px 16px;
      font-weight: 600;
      flex: 1 1 220px;
    }
    .wizard-next-btn {
      border-radius: 12px;
      padding: 11px 18px;
      font-weight: 600;
      background: #2ec1ac;
      border: none;
      color: #fff;
      flex: 1 1 220px;
    }
    .wizard-next-btn:hover {
      background: #279e8c;
      color: #fff;
    }
    .wizard-snapshot-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 10px 16px;
    }
    .wizard-snapshot-item span {
      display: block;
      font-size: 0.72rem;
      color: #64748b;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      margin-bottom: 4px;
      font-weight: 700;
    }
    .wizard-snapshot-item strong {
      color: #0f172a;
      font-size: 0.95rem;
      font-weight: 700;
    }
    .payment-box.step-payment-box {
      margin-top: 0;
      margin-bottom: 16px;
    }
    @keyframes wizardFadeIn {
      from {
        opacity: 0;
        transform: translateY(8px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    .guest-info-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px;
    }
    .guest-info-grid .col-12 {
      grid-column: 1 / -1;
    }
    .extras-pricing-hint {
      font-size: 0.78rem;
      color: #64748b;
      margin-top: 6px;
      line-height: 1.5;
    }
    .extra-card-note {
      display: block;
      font-size: 0.72rem;
      color: #64748b;
      margin-top: 4px;
      line-height: 1.45;
    }
    @media (max-width: 992px) {
      .room-preview-media {
        height: 320px;
      }
    }
    @media (max-width: 768px) {
      .room-preview-media {
        height: 250px;
      }
      .booking-stepper {
        grid-template-columns: 1fr;
      }
      .wizard-controls {
        flex-direction: column;
        align-items: stretch;
      }
      .wizard-progress-copy {
        text-align: left;
      }
      .wizard-snapshot-grid {
        grid-template-columns: 1fr;
      }
      .guest-info-grid {
        grid-template-columns: 1fr;
      }
      .guest-info-grid .col-12 {
        grid-column: 1;
      }
    }
    .flatpickr-calendar {
      z-index: 9999 !important;
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
    else if(isset($_SESSION['is_verified']) && $_SESSION['is_verified'] == 0){
      // Unverified users cannot access the booking page
      $_SESSION['booking_blocked'] = true;
      redirect('profile.php');
    }
  ?>

  <?php

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

    // Fetch active extras
    $extras_q = mysqli_query($con, "SELECT * FROM `extras` WHERE `status`=1 ORDER BY `id` ASC");
    $extras_list = [];
    while($row = mysqli_fetch_assoc($extras_q)) $extras_list[] = $row;

    // Fetch booking rules from settings
    $rules_row = mysqli_fetch_assoc(mysqli_query($con, "SELECT `booking_rules` FROM `settings` WHERE `sr_no`=1 LIMIT 1"));
    $booking_rules = $rules_row['booking_rules'] ?? '';
    $rules_lines = $booking_rules ? array_filter(array_map('trim', explode("\n", $booking_rules))) : [
      '50% downpayment is required to confirm your booking. Upload your payment proof above.',
      'The remaining 50% balance is due upon check-in.',
      'Check-in time is 2:00 PM; Check-out time is 12:00 PM (noon).',
      'Cancellations will be refunded 50% of the total amount paid.',
      'Guests are responsible for any damage to room property during their stay.',
      'No smoking inside the rooms. Designated smoking areas are available outside.',
      'Please observe quiet hours from 10:00 PM to 7:00 AM.',
      'A valid government-issued ID is required upon check-in.',
    ];


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
            $room_images = [];
            $img_q = mysqli_query($con,"SELECT * FROM `room_images`
              WHERE `room_id`='{$room_data['id']}'
              ORDER BY `thumb` DESC, `sr_no` ASC");

            if($img_q && mysqli_num_rows($img_q)>0){
              while($img_res = mysqli_fetch_assoc($img_q)){
                $room_images[] = ROOMS_IMG_PATH.$img_res['image'];
              }
            }

            if(empty($room_images)){
              $room_images[] = ROOMS_IMG_PATH."thumbnail.jpg";
            }
            $has_room_carousel = count($room_images) > 1;
          ?>
          <div class="room-preview">
            <div id="bookingRoomCarousel" class="carousel slide"
                 data-bs-touch="true"
                 data-bs-wrap="true"
                 <?php echo $has_room_carousel ? 'data-bs-ride="carousel" data-bs-interval="3200" data-bs-pause="hover"' : 'data-bs-ride="false"'; ?>>
              <?php if($has_room_carousel): ?>
              <div class="carousel-indicators">
                <?php foreach($room_images as $idx => $_img): ?>
                  <button type="button" data-bs-target="#bookingRoomCarousel" data-bs-slide-to="<?php echo $idx; ?>" class="<?php echo $idx === 0 ? 'active' : ''; ?>" <?php echo $idx === 0 ? 'aria-current="true"' : ''; ?> aria-label="Slide <?php echo $idx + 1; ?>"></button>
                <?php endforeach; ?>
              </div>
              <?php endif; ?>
              <div class="carousel-inner">
                <?php foreach($room_images as $idx => $room_image): ?>
                  <div class="carousel-item <?php echo $idx === 0 ? 'active' : ''; ?>">
                    <div class="room-preview-media">
                      <img src="<?php echo $room_image; ?>" class="room-preview-image" alt="<?php echo htmlspecialchars($room_data['name'], ENT_QUOTES); ?>">
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
              <?php if($has_room_carousel): ?>
              <button class="carousel-control-prev" type="button" data-bs-target="#bookingRoomCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#bookingRoomCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
              </button>
              <?php endif; ?>
            </div>
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
            <div class="booking-stepper" id="booking_stepper">
              <button type="button" class="booking-step-chip is-active is-clickable" data-step-chip="1" onclick="attemptBookingStepChange(1)">
                <span class="booking-step-chip-number">1</span>
                <span>
                  <span class="booking-step-chip-title">Stay Details</span>
                  <span class="booking-step-chip-note">Guest, dates, and room</span>
                </span>
              </button>
              <button type="button" class="booking-step-chip" data-step-chip="2" onclick="attemptBookingStepChange(2)">
                <span class="booking-step-chip-number">2</span>
                <span>
                  <span class="booking-step-chip-title">Extras &amp; Notes</span>
                  <span class="booking-step-chip-note">Optional add-ons first</span>
                </span>
              </button>
              <button type="button" class="booking-step-chip" data-step-chip="3" onclick="attemptBookingStepChange(3)">
                <span class="booking-step-chip-number">3</span>
                <span>
                  <span class="booking-step-chip-title">Payment &amp; Total</span>
                  <span class="booking-step-chip-note">Pay, upload, and review total</span>
                </span>
              </button>
              <button type="button" class="booking-step-chip" data-step-chip="4" onclick="attemptBookingStepChange(4)">
                <span class="booking-step-chip-number">4</span>
                <span>
                  <span class="booking-step-chip-title">Review &amp; Confirm</span>
                  <span class="booking-step-chip-note">Final check before submit</span>
                </span>
              </button>
            </div>

            <div class="wizard-head">
              <div class="wizard-head-step" id="wizard_step_badge">Step 1 of 4</div>
              <h4 class="wizard-head-title" id="wizard_step_title">Stay Details</h4>
              <p class="wizard-head-copy" id="wizard_step_copy">Start with the guest information, stay dates, and room number in one clean step so the rest of the booking feels lighter.</p>
            </div>

            <form action="pay_now.php" method="POST" id="booking_form" enctype="multipart/form-data">
              <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($booking_csrf, ENT_QUOTES, 'UTF-8'); ?>">
              <input type="hidden" name="room_no" />

              <div class="booking-step-panel is-active" data-step-panel="1">
                <div class="step-section-card">
                  <div class="form-section-title">Guest Information</div>
                  <p class="step-section-note">Start with the guest details first, then choose the stay dates and room number right below.</p>
                  <div class="guest-info-grid">
                    <div>
                      <label class="form-label small text-muted mb-1">Full Name</label>
                      <input name="name" type="text" value="<?php echo $user_data['name'] ?>" class="form-control" required>
                    </div>
                    <div>
                      <label class="form-label small text-muted mb-1">Phone Number</label>
                      <input name="phonenum" type="tel" value="<?php echo $user_data['phonenum'] ?>" class="form-control" required>
                    </div>
                    <div class="col-12">
                      <label class="form-label small text-muted mb-1">Address</label>
                      <textarea name="address" class="form-control" rows="2" required><?php echo $user_data['address'] ?></textarea>
                    </div>
                  </div>
                </div>

                <div class="row g-3 mb-3">
                  <div class="col-sm-6">
                    <div class="step-section-card h-100">
                      <div class="form-section-title">Check-in / Check-out</div>
                      <p class="step-section-note">Pick your stay dates first, then the system will load room availability for those dates.</p>
                      <div class="date-card-row" id="date-card-row" onclick="openDateCard(event)">
                        <div class="date-card" id="checkin-card">
                          <div class="dc-label"><i class="bi bi-calendar-check"></i> Check-in</div>
                          <div class="dc-date placeholder" id="checkin-display">Select date</div>
                          <div class="dc-day" id="checkin-day"></div>
                          <input name="checkin" id="checkin" type="text" required readonly>
                        </div>
                        <div class="d-flex align-items-center px-1" style="color:#ccc;font-size:0.9rem;">
                          <i class="bi bi-arrow-right"></i>
                        </div>
                        <div class="date-card" id="checkout-card">
                          <div class="dc-label"><i class="bi bi-calendar-x"></i> Check-out</div>
                          <div class="dc-date placeholder" id="checkout-display">Select date</div>
                          <div class="dc-day" id="checkout-day"></div>
                          <input name="checkout" id="checkout" type="text" required readonly>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="step-section-card h-100">
                      <div class="form-section-title">Select Room Number</div>
                      <p class="step-section-note">Only available room numbers will be clickable here once your dates are valid.</p>
                      <div id="user-assign-legend" class="legend mb-2" style="font-size: 11px; gap: 8px;"></div>
                      <div id="user-assign-grid" class="seat-grid" style="max-height: 110px; overflow-y: auto; padding: 6px;"></div>
                    </div>
                  </div>
                </div>

                <div class="step-summary-card mb-3 text-center">
                  <div class="form-section-title mb-2 text-start">Estimated Stay Cost</div>
                  <div id="pay_info" class="text-danger small">Select dates to see price</div>
                  <div class="spinner-border text-info d-none" id="info_loader" role="status" style="width: 1rem; height: 1rem;">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                </div>

                <div class="wizard-controls">
                  <div class="wizard-progress-copy">Step 1 now keeps the guest profile, stay dates, estimated price, and room selection together so you can finish the main booking details in one pass.</div>
                  <button type="button" class="btn btn-primary wizard-next-btn" onclick="nextBookingStep(1)">Continue to Extras</button>
                </div>
              </div>

              <div class="booking-step-panel" data-step-panel="2">
              <?php if(!empty($extras_list)): ?>
              <div class="mb-3 rounded-3 p-3" style="background:#f0f8ff;border:1.5px solid #90caf9;">
                <div class="fw-bold small mb-2" style="color:#1565c0;">
                  <i class="bi bi-plus-circle me-1"></i> Add-on Extras
                  <span class="text-muted fw-normal ms-1">(charged each night of your stay)</span>
                </div>
                <div class="extras-pricing-hint" id="extras_duration_hint">Select stay dates to see the real total cost of each extra.</div>
                <div class="row g-2" id="extras-section">
                  <?php foreach($extras_list as $extra): ?>
                  <div class="col-sm-6">
                    <div class="d-flex align-items-center gap-2 p-2 rounded h-100" style="background:#fff;border:1px solid #e0e0e0;">
                      <input type="checkbox" class="form-check-input shadow-none extra-check"
                        id="extra_<?php echo $extra['id']; ?>"
                        data-extra-id="<?php echo $extra['id']; ?>"
                        data-extra-name="<?php echo htmlspecialchars($extra['name']); ?>"
                        data-extra-price="<?php echo $extra['price']; ?>"
                        onchange="updateExtrasTotal()">
                      <div class="flex-grow-1" style="min-width:0;">
                        <label for="extra_<?php echo $extra['id']; ?>" class="fw-semibold small mb-0 d-block" style="cursor:pointer;">
                          <?php echo htmlspecialchars($extra['name']); ?>
                          <span class="text-success ms-1">+&#8369;<?php echo number_format($extra['price'],2); ?>/night</span>
                        </label>
                        <?php if($extra['description']): ?>
                          <span class="text-muted" style="font-size:0.7rem;"><?php echo htmlspecialchars($extra['description']); ?></span>
                        <?php endif; ?>
                        <span class="extra-card-note" id="extra_total_note_<?php echo $extra['id']; ?>">Charged each night of your stay.</span>
                      </div>
                      <div class="d-flex align-items-center gap-1" style="opacity:0.3;" id="qty_wrap_<?php echo $extra['id']; ?>">
                        <button type="button" class="btn btn-sm btn-outline-secondary shadow-none px-1 py-0" style="font-size:0.8rem;line-height:1.4;"
                          onclick="changeQty(<?php echo $extra['id']; ?>,-1)">-</button>
                        <input type="number" min="1" max="10" value="1"
                          id="qty_<?php echo $extra['id']; ?>"
                          class="form-control form-control-sm shadow-none text-center px-1 extra-qty"
                          style="width:40px;font-size:0.8rem;"
                          onchange="updateExtrasTotal()" disabled>
                        <button type="button" class="btn btn-sm btn-outline-secondary shadow-none px-1 py-0" style="font-size:0.8rem;line-height:1.4;"
                          onclick="changeQty(<?php echo $extra['id']; ?>,1)">+</button>
                      </div>
                    </div>
                  </div>
                  <?php endforeach; ?>
                </div>
                <div class="text-end mt-2 small fw-semibold" id="extras-total-line" style="display:none!important;color:#1565c0;"></div>
                <input type="hidden" name="extras_json" id="extras_json" value="[]">
              </div>
              <?php endif; ?>

              <div class="row g-3 mb-3">
                <div class="col-sm-6">
                  <div class="step-section-card h-100">
                    <div class="form-section-title">Special Requests</div>
                    <p class="step-section-note">Optional notes for room setup, arrival reminders, or anything staff should know.</p>
                    <textarea name="booking_note" class="form-control" rows="6" maxlength="500" placeholder="Any requests..."></textarea>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="step-section-card h-100">
                    <div class="form-section-title">Promo Code</div>
                    <p class="step-section-note">Apply a valid promo before you continue to the payment step.</p>
                    <div class="promo-code-row">
                      <div class="promo-input-col">
                        <input type="text" class="form-control" id="promo_code_input" placeholder="Enter promo code">
                        <div id="promo_feedback" class="small text-muted mt-1"></div>
                      </div>
                      <div class="promo-btn-col">
                        <button type="button" class="btn btn-outline-primary" onclick="applyPromoCode()">Apply Promo</button>
                      </div>
                    </div>
                    <input type="hidden" name="promo_code" id="promo_code_hidden" value="">
                  </div>
                </div>
              </div>

              <div class="wizard-controls">
                <button type="button" class="btn btn-light border wizard-back-btn" onclick="prevBookingStep(2)">Back</button>
                <div class="wizard-progress-copy">Step 2 keeps the optional parts together first, so guests can finish add-ons, requests, and promo choices before any payment upload appears.</div>
                <button type="button" class="btn btn-primary wizard-next-btn" onclick="nextBookingStep(2)">Continue to Payment</button>
              </div>
              </div>

              <div class="booking-step-panel" data-step-panel="3">
              <div class="step-summary-card mb-3">
                <div class="form-section-title mb-2">Final Total Summary</div>
                <p class="step-section-note">This step combines your payment upload and the final total, so you can review the amount right before sending the proof.</p>
                <div id="final_pay_info" class="text-danger small">Select dates to see total summary</div>
              </div>

              <div class="payment-box p-3 step-payment-box" id="payment_info_box">
                <h6 class="small mb-2"><i class="bi bi-wallet2 me-1"></i>Pay via GCash or Maya</h6>
                <div class="row g-2 mb-2">
                  <div class="col-6">
                    <small class="d-block text-white-50" style="font-size: 0.72rem;">GCash</small>
                    <span class="small fw-semibold"><?php echo $gcash_number; ?></span>
                  </div>
                  <div class="col-6">
                    <small class="d-block text-white-50" style="font-size: 0.72rem;">Maya</small>
                    <span class="small fw-semibold"><?php echo $maya_number; ?></span>
                  </div>
                </div>
                <div class="qr-grid">
                  <div class="qr-item p-1">
                    <a href="#" class="qr-code" data-qr-src="images/qr/GCASH.jpg" data-qr-title="GCash QR">
                      <img src="images/qr/GCASH.jpg" alt="GCash QR" style="max-width: 86px;">
                    </a>
                    <small class="d-block mt-1" style="font-size: 0.72rem;">GCash QR</small>
                  </div>
                  <div class="qr-item p-1">
                    <a href="#" class="qr-code" data-qr-src="images/qr/MAYA.jpg" data-qr-title="Maya QR">
                      <img src="images/qr/MAYA.jpg" alt="Maya QR" style="max-width: 86px;">
                    </a>
                    <small class="d-block mt-1" style="font-size: 0.72rem;">Maya QR</small>
                  </div>
                </div>
              </div>

              <div class="step-section-card mb-3">
                <div class="form-section-title">Payment Proof</div>
                <p class="step-section-note">After paying through GCash or Maya, upload your screenshot or PDF proof here before the final review.</p>
                <input type="file" name="billing_proof" id="billing_proof" accept=".jpg,.jpeg,.png,.pdf" class="form-control form-control-sm" required style="display: none;">
                <label for="billing_proof" class="btn btn-outline-secondary w-100" id="upload_label">
                  <i class="bi bi-upload me-1"></i><span id="upload_text">Upload payment proof</span>
                </label>
                <div id="file_preview" class="mt-2 d-none">
                  <div class="d-flex align-items-center gap-2 p-2 border rounded bg-light">
                    <i class="bi bi-file-earmark-image text-success"></i>
                    <span id="filename_display" class="text-truncate flex-grow-1 small"></span>
                    <span class="badge bg-success">Selected</span>
                  </div>
                  <img id="image_preview" class="img-fluid rounded mt-2 d-none" style="max-height: 100px;">
                </div>
                <small class="text-muted d-block mt-2">Accepted: JPG, PNG, or PDF up to 10MB.</small>
              </div>

              <?php if(false): ?>
              <!-- Add-on Extras -->
              <?php if(!empty($extras_list)): ?>
              <div class="mb-3 rounded-3 p-3" style="background:#f0f8ff;border:1.5px solid #90caf9;">
                <div class="fw-bold small mb-2" style="color:#1565c0;">
                  <i class="bi bi-plus-circle me-1"></i> Add-on Extras
                  <span class="text-muted fw-normal ms-1">(charged each night of your stay)</span>
                </div>
                <div class="extras-pricing-hint" id="extras_duration_hint">Select stay dates to see the real total cost of each extra.</div>
                <div class="row g-2" id="extras-section">
                  <?php foreach($extras_list as $extra): ?>
                  <div class="col-sm-6">
                    <div class="d-flex align-items-center gap-2 p-2 rounded h-100" style="background:#fff;border:1px solid #e0e0e0;">
                      <input type="checkbox" class="form-check-input shadow-none extra-check"
                        id="extra_<?php echo $extra['id']; ?>"
                        data-extra-id="<?php echo $extra['id']; ?>"
                        data-extra-name="<?php echo htmlspecialchars($extra['name']); ?>"
                        data-extra-price="<?php echo $extra['price']; ?>"
                        onchange="updateExtrasTotal()">
                      <div class="flex-grow-1" style="min-width:0;">
                        <label for="extra_<?php echo $extra['id']; ?>" class="fw-semibold small mb-0 d-block" style="cursor:pointer;">
                          <?php echo htmlspecialchars($extra['name']); ?>
                          <span class="text-success ms-1">+₱<?php echo number_format($extra['price'],2); ?>/night</span>
                        </label>
                        <?php if($extra['description']): ?>
                          <span class="text-muted" style="font-size:0.7rem;"><?php echo htmlspecialchars($extra['description']); ?></span>
                        <?php endif; ?>
                        <span class="extra-card-note" id="extra_total_note_<?php echo $extra['id']; ?>">Charged each night of your stay.</span>
                      </div>
                      <div class="d-flex align-items-center gap-1" style="opacity:0.3;" id="qty_wrap_<?php echo $extra['id']; ?>">
                        <button type="button" class="btn btn-sm btn-outline-secondary shadow-none px-1 py-0" style="font-size:0.8rem;line-height:1.4;"
                          onclick="changeQty(<?php echo $extra['id']; ?>,-1)">−</button>
                        <input type="number" min="1" max="10" value="1"
                          id="qty_<?php echo $extra['id']; ?>"
                          class="form-control form-control-sm shadow-none text-center px-1 extra-qty"
                          style="width:40px;font-size:0.8rem;"
                          onchange="updateExtrasTotal()" disabled>
                        <button type="button" class="btn btn-sm btn-outline-secondary shadow-none px-1 py-0" style="font-size:0.8rem;line-height:1.4;"
                          onclick="changeQty(<?php echo $extra['id']; ?>,1)">+</button>
                      </div>
                    </div>
                  </div>
                  <?php endforeach; ?>
                </div>
                <div class="text-end mt-2 small fw-semibold" id="extras-total-line" style="display:none!important;color:#1565c0;"></div>
                <input type="hidden" name="extras_json" id="extras_json" value="[]">
              </div>
              <?php endif; ?>

              <div class="mb-3 rounded-3 p-3" style="background:#f8fafc;border:1.5px solid #dbeafe;">
                <div class="fw-bold small mb-2" style="color:#1d4ed8;">
                  <i class="bi bi-tag me-1"></i> Promo Code
                </div>
                <div class="promo-code-row">
                  <div class="promo-input-col">
                    <input type="text" class="form-control" id="promo_code_input" placeholder="Enter promo code">
                    <div id="promo_feedback" class="small text-muted mt-1">Have a valid discount code? Apply it before completing your booking.</div>
                  </div>
                  <div class="promo-btn-col">
                    <button type="button" class="btn btn-outline-primary" onclick="applyPromoCode()">Apply Promo</button>
                  </div>
                </div>
                <input type="hidden" name="promo_code" id="promo_code_hidden" value="">
              </div>

              <?php endif; ?>
              <div class="wizard-controls">
                <button type="button" class="btn btn-light border wizard-back-btn" onclick="prevBookingStep(3)">Back</button>
                <div class="wizard-progress-copy">Step 3 is now the only payment step, so the QR codes, proof upload, and final total stay together in one place.</div>
                <button type="button" class="btn btn-primary wizard-next-btn" onclick="nextBookingStep(3)">Continue to Review</button>
              </div>
              </div>

              <div class="booking-step-panel" data-step-panel="4">
              <div class="step-summary-card mb-3">
                <div class="form-section-title mb-2">Booking Snapshot</div>
                <div class="wizard-snapshot-grid">
                  <div class="wizard-snapshot-item">
                    <span>Guest</span>
                    <strong id="snapshot_guest_name"><?php echo htmlspecialchars($user_data['name']); ?></strong>
                  </div>
                  <div class="wizard-snapshot-item">
                    <span>Phone</span>
                    <strong id="snapshot_phone"><?php echo htmlspecialchars($user_data['phonenum']); ?></strong>
                  </div>
                  <div class="wizard-snapshot-item">
                    <span>Stay</span>
                    <strong id="snapshot_stay">Select dates</strong>
                  </div>
                  <div class="wizard-snapshot-item">
                    <span>Room No.</span>
                    <strong id="snapshot_room">Not selected</strong>
                  </div>
                  <div class="wizard-snapshot-item">
                    <span>Total</span>
                    <strong id="snapshot_total">Waiting for dates</strong>
                  </div>
                  <div class="wizard-snapshot-item">
                    <span>Proof</span>
                    <strong id="snapshot_proof">Not uploaded yet</strong>
                  </div>
                </div>
              </div>

              <!-- Booking Policy (from DB) -->
              <div class="mb-3 rounded-3 p-3" style="background:#fffbf0;border:1.5px solid #f0c040;">
                <div class="fw-bold small mb-2" style="color:#b8860b;">
                  <i class="bi bi-shield-exclamation me-1"></i> Booking Policy &amp; House Rules
                </div>
                <ul class="mb-0 ps-3" style="font-size:0.78rem;color:#555;line-height:1.8;">
                  <?php foreach($rules_lines as $rule): ?>
                    <li><?php echo htmlspecialchars($rule); ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>

              <!-- Agree checkbox -->
              <div class="form-check mb-3">
                <input class="form-check-input shadow-none" type="checkbox" id="agree_policy" onchange="toggleBookBtn()">
                <label class="form-check-label small" for="agree_policy">
                  I have read and agree to the <strong>booking policy and house rules</strong> above.
                </label>
              </div>

              <div class="wizard-controls">
                <button type="button" class="btn btn-light border wizard-back-btn" onclick="prevBookingStep(4)">Back</button>
                <div class="wizard-progress-copy">Final step: do one last review of the booking snapshot, confirm the rules, and submit when everything looks right.</div>
                <button name="pay_now" id="pay_now_btn" class="btn btn-submit text-white wizard-next-btn" disabled>
                  Complete Booking
                </button>
              </div>
              </div>
            </form>

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

  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

  <script>

    let booking_form = document.getElementById('booking_form');
    let info_loader = document.getElementById('info_loader');
    let pay_info = document.getElementById('pay_info');
    let final_pay_info = document.getElementById('final_pay_info');
    let grid = document.getElementById('user-assign-grid');
    let legend = document.getElementById('user-assign-legend');
    let fpCheckin, fpCheckout;
    let currentBookingStep = 1;
    let unlockedBookingStep = 1;
    const bookingStepMeta = {
      1: {
        badge: 'Step 1 of 4',
        title: 'Stay Details',
        copy: 'Fill in the guest information, stay dates, and room number in one clean step so the rest of the booking feels faster.'
      },
      2: {
        badge: 'Step 2 of 4',
        title: 'Extras and Notes',
        copy: 'Add optional extras, special requests, or a promo code before you move to the payment step.'
      },
      3: {
        badge: 'Step 3 of 4',
        title: 'Payment and Total',
        copy: 'Use the QR codes, review the final total, and upload your payment proof in one step.'
      },
      4: {
        badge: 'Step 4 of 4',
        title: 'Review and Confirm',
        copy: 'Do one last review of the booking snapshot, confirm the policy, and submit when everything looks right.'
      }
    };

    function updateWizardHead(step) {
      const meta = bookingStepMeta[step] || bookingStepMeta[1];
      const badge = document.getElementById('wizard_step_badge');
      const title = document.getElementById('wizard_step_title');
      const copy = document.getElementById('wizard_step_copy');
      if (badge) badge.textContent = meta.badge;
      if (title) title.textContent = meta.title;
      if (copy) copy.textContent = meta.copy;
    }

    function setBookingStep(step) {
      currentBookingStep = step;
      document.querySelectorAll('[data-step-panel]').forEach(panel => {
        panel.classList.toggle('is-active', Number(panel.dataset.stepPanel) === step);
      });
      document.querySelectorAll('[data-step-chip]').forEach(chip => {
        const chipStep = Number(chip.dataset.stepChip);
        chip.classList.toggle('is-active', chipStep === step);
        chip.classList.toggle('is-complete', chipStep < step);
        chip.classList.toggle('is-clickable', chipStep <= unlockedBookingStep);
      });
      updateWizardHead(step);
      updateBookingSnapshot();
    }

    function validateGuestStep() {
      const fields = [
        booking_form.elements['name'],
        booking_form.elements['phonenum'],
        booking_form.elements['address']
      ];

      for (const field of fields) {
        if (!field || String(field.value).trim() !== '') continue;
        field.focus();
        field.reportValidity();
        return false;
      }

      return true;
    }

    function validateBookingDatesStep() {
      const checkin = booking_form.elements['checkin'].value;
      const checkout = booking_form.elements['checkout'].value;
      const roomNo = booking_form.elements['room_no'].value;

      if (!checkin) {
        pay_info.textContent = 'Please select a check-in date first.';
        pay_info.classList.remove('d-none', 'text-dark');
        pay_info.classList.add('text-danger');
        if (fpCheckin) fpCheckin.open();
        return false;
      }

      if (!checkout) {
        pay_info.textContent = 'Please select a check-out date first.';
        pay_info.classList.remove('d-none', 'text-dark');
        pay_info.classList.add('text-danger');
        if (fpCheckout) fpCheckout.open();
        return false;
      }

      if (!roomNo) {
        pay_info.textContent = 'Please choose an available room number to continue.';
        pay_info.classList.remove('d-none', 'text-dark');
        pay_info.classList.add('text-danger');
        return false;
      }

      return true;
    }

    function validateStayDetailsStep() {
      return validateGuestStep() && validateBookingDatesStep();
    }

    function notifyStepIssue(message) {
      if (window.Swal && typeof Swal.fire === 'function') {
        Swal.fire({
          icon: 'warning',
          title: 'One More Step',
          text: message,
          confirmButtonColor: '#2ec1ac'
        });
      } else {
        alert(message);
      }
    }

    function validatePaymentStep() {
      const proofInput = document.getElementById('billing_proof');
      if (proofInput && proofInput.files && proofInput.files.length > 0) {
        return true;
      }

      notifyStepIssue('Upload your payment proof before continuing to the final review.');
      const uploadLabel = document.getElementById('upload_label');
      if (uploadLabel) {
        uploadLabel.scrollIntoView({ behavior: 'smooth', block: 'center' });
        uploadLabel.classList.add('border', 'border-danger');
        setTimeout(() => {
          uploadLabel.classList.remove('border', 'border-danger');
        }, 1400);
      }
      return false;
    }

    function validateBookingStep(step) {
      if (step === 1) return validateStayDetailsStep();
      if (step === 2) return true;
      if (step === 3) return validatePaymentStep();
      return true;
    }

    function attemptBookingStepChange(step) {
      if (step === currentBookingStep) return;

      if (step < currentBookingStep) {
        setBookingStep(step);
        return;
      }

      for (let index = currentBookingStep; index < step; index++) {
        if (!validateBookingStep(index)) return;
        unlockedBookingStep = Math.max(unlockedBookingStep, index + 1);
      }

      setBookingStep(step);
    }

    function nextBookingStep(step) {
      if (!validateBookingStep(step)) return;
      unlockedBookingStep = Math.max(unlockedBookingStep, step + 1);
      setBookingStep(Math.min(4, step + 1));
    }

    function prevBookingStep(step) {
      setBookingStep(Math.max(1, step - 1));
    }

    function updateBookingSnapshot() {
      const guestName = document.getElementById('snapshot_guest_name');
      const phone = document.getElementById('snapshot_phone');
      const stay = document.getElementById('snapshot_stay');
      const room = document.getElementById('snapshot_room');
      const total = document.getElementById('snapshot_total');
      const proof = document.getElementById('snapshot_proof');

      if (guestName) {
        guestName.textContent = booking_form.elements['name'].value.trim() || 'Not provided';
      }
      if (phone) {
        phone.textContent = booking_form.elements['phonenum'].value.trim() || 'Not provided';
      }

      if (stay) {
        const checkinText = document.getElementById('checkin-display')?.textContent || '';
        const checkoutText = document.getElementById('checkout-display')?.textContent || '';
        stay.textContent = (checkinText !== 'Select date' && checkoutText !== 'Select date' && checkinText && checkoutText)
          ? `${checkinText} to ${checkoutText}`
          : 'Select dates';
      }

      if (room) {
        room.textContent = booking_form.elements['room_no'].value || 'Not selected';
      }

      if (total) {
        if (_billing.days > 0) {
          const { extrasTotalPerNight } = getExtrasInfo();
          const subtotal = _billing.room_total + (extrasTotalPerNight * _billing.days);
          const discount = Math.min(_promo.discount || 0, subtotal);
          total.textContent = formatPeso(Math.max(0, subtotal - discount));
        } else {
          total.textContent = 'Waiting for dates';
        }
      }

      if (proof) {
        const uploadedFile = document.getElementById('billing_proof')?.files?.[0];
        proof.textContent = uploadedFile ? uploadedFile.name : 'Not uploaded yet';
      }
    }

    function toggleBookBtn() {
      const agreed = document.getElementById('agree_policy').checked;
      const roomNo = booking_form.elements['room_no'].value;
      if (agreed && roomNo) {
        booking_form.elements['pay_now'].removeAttribute('disabled');
      } else {
        booking_form.elements['pay_now'].setAttribute('disabled', true);
      }
      updateBookingSnapshot();
    }

    // Tracks current availability data for billing breakdown
    let _billing = { days: 0, price_night: 0, room_total: 0 };
    let _promo = { code: '', discount: 0 };

    function formatPeso(n) {
      return '₱' + Number(n).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function formatPesoSafe(n) {
      return '\u20B1' + Number(n).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    formatPeso = formatPesoSafe;

    function setPromoFeedback(message, tone = 'muted') {
      const feedback = document.getElementById('promo_feedback');
      if (!feedback) return;

      const toneMap = {
        muted: 'text-muted',
        success: 'text-success',
        danger: 'text-danger',
        warning: 'text-warning'
      };

      feedback.className = 'small mt-1 ' + (toneMap[tone] || toneMap.muted);
      feedback.textContent = message;
    }

    function clearPromoSelection(message = '', preserveInput = false) {
      _promo = { code: '', discount: 0 };

      const hidden = document.getElementById('promo_code_hidden');
      const input = document.getElementById('promo_code_input');
      if (hidden) hidden.value = '';
      if (input && !preserveInput) input.value = '';

      setPromoFeedback(
        message || 'Have a valid discount code? Apply it before completing your booking.',
        message ? 'warning' : 'muted'
      );
      updateBookingSnapshot();
    }

    function setBillingPanelsMessage(message) {
      [pay_info, final_pay_info].forEach(panel => {
        if (!panel) return;
        panel.textContent = message;
        panel.classList.remove('d-none', 'text-dark');
        panel.classList.add('text-danger');
      });
    }

    function setBillingPanelsHtml(html) {
      [pay_info, final_pay_info].forEach(panel => {
        if (!panel) return;
        panel.innerHTML = html;
        panel.classList.remove('d-none', 'text-danger');
        panel.classList.add('text-dark');
      });
    }

    function resetBillingState(message = 'Select dates to see price') {
      _billing = { days: 0, price_night: 0, room_total: 0 };
      setBillingPanelsMessage(message);
      booking_form.elements['room_no'].value = '';
      if (grid) grid.innerHTML = '';
      updateExtraPricingHints();
      toggleBookBtn();
      updateBookingSnapshot();
    }

    function getCurrentSubtotal() {
      const { extrasTotalPerNight } = getExtrasInfo();
      return _billing.room_total + (extrasTotalPerNight * _billing.days);
    }

    function getExtrasInfo() {
      const checks = document.querySelectorAll('.extra-check');
      let extras = [];
      let extrasTotalPerNight = 0;
      checks.forEach(cb => {
        const qtyInp = document.getElementById('qty_' + cb.dataset.extraId);
        if (cb.checked) {
          const qty = parseInt(qtyInp.value) || 1;
          const price = parseFloat(cb.dataset.extraPrice);
          extrasTotalPerNight += price * qty;
          extras.push({ id: cb.dataset.extraId, name: cb.dataset.extraName, qty, unit_price: price });
        }
      });
      return { extras, extrasTotalPerNight };
    }

    function updateExtraPricingHints() {
      const durationHint = document.getElementById('extras_duration_hint');
      const checks = document.querySelectorAll('.extra-check');
      const nightsLabel = _billing.days === 1 ? 'night' : 'nights';

      if (durationHint) {
        durationHint.textContent = _billing.days > 0
          ? `Current stay: ${_billing.days} ${nightsLabel}. Extras are charged once for every night of the stay.`
          : 'Select stay dates to see the real total cost of each extra.';
      }

      checks.forEach(cb => {
        const note = document.getElementById('extra_total_note_' + cb.dataset.extraId);
        const qtyInp = document.getElementById('qty_' + cb.dataset.extraId);
        if (!note) return;

        const qty = cb.checked ? (parseInt(qtyInp?.value, 10) || 1) : 1;
        const price = parseFloat(cb.dataset.extraPrice || '0');

        if (_billing.days > 0) {
          const stayTotal = price * qty * _billing.days;
          note.textContent = cb.checked
            ? `Selected total: ${formatPeso(stayTotal)} for ${_billing.days} ${nightsLabel}.`
            : `Would add ${formatPeso(price * _billing.days)} for ${_billing.days} ${nightsLabel}.`;
        } else {
          note.textContent = cb.checked
            ? `${formatPeso(price * qty)} per night. Select stay dates to see the final total.`
            : 'Charged each night of your stay.';
        }
      });
    }

    function renderBillingBreakdown() {
      if (_billing.days <= 0) {
        updateExtraPricingHints();
        updateBookingSnapshot();
        return;
      }
      const { extrasTotalPerNight } = getExtrasInfo();
      const extrasTotal = extrasTotalPerNight * _billing.days;
      const subtotal    = _billing.room_total + extrasTotal;
      const discount    = Math.min(_promo.discount || 0, subtotal);
      const grandTotal  = Math.max(0, subtotal - discount);
      const downpayment = Math.ceil(grandTotal / 2);
      const balance     = grandTotal - downpayment;
      const selectedRoom = booking_form.elements['room_no'].value;
      const extrasLabel = `Add-ons (${_billing.days} ${_billing.days === 1 ? 'night' : 'nights'})`;
      const summaryNote = selectedRoom
        ? `Room ${selectedRoom} is selected for this booking. Add-ons are charged per night.`
        : 'Select a room number to continue. Add-ons are charged per night.';

      const summaryHtml = `
        <div class="text-start rounded p-2 mt-1" style="background:#f8f9fa;border:1px solid #dee2e6;font-size:0.85rem;">
          <div class="d-flex justify-content-between"><span class="text-muted">Duration</span><span>${_billing.days} night${_billing.days > 1 ? 's' : ''}</span></div>
          <div class="d-flex justify-content-between"><span class="text-muted">Room charge</span><span>${formatPeso(_billing.room_total)}</span></div>
          ${extrasTotal > 0 ? `<div class="d-flex justify-content-between"><span class="text-muted">${extrasLabel}</span><span>+${formatPeso(extrasTotal)}</span></div>` : ''}
          ${discount > 0 ? `<div class="d-flex justify-content-between" style="color:#047857;"><span>Promo discount${_promo.code ? ` (${_promo.code})` : ''}</span><span>-${formatPeso(discount)}</span></div>` : ''}
          <hr class="my-1">
          <div class="d-flex justify-content-between fw-bold"><span>Total</span><span>${formatPeso(grandTotal)}</span></div>
          <div class="d-flex justify-content-between" style="color:#b8860b;font-weight:600;"><span>Downpayment (50%)</span><span>${formatPeso(downpayment)}</span></div>
          <div class="d-flex justify-content-between text-muted" style="font-size:0.78rem;"><span>Balance at check-in</span><span>${formatPeso(balance)}</span></div>
          <div class="text-muted mt-1" style="font-size:0.75rem;"><i class="bi bi-info-circle me-1"></i>${summaryNote}</div>
        </div>
      `;
      setBillingPanelsHtml(summaryHtml);
      updateExtraPricingHints();
      updateBookingSnapshot();
    }

    async function applyPromoCode() {
      const input = document.getElementById('promo_code_input');
      const hidden = document.getElementById('promo_code_hidden');
      if (!input || !hidden) return;

      const code = input.value.trim().toUpperCase();
      if (_billing.days <= 0 || _billing.room_total <= 0) {
        setPromoFeedback('Select valid stay dates first before applying a promo code.', 'warning');
        return;
      }

      if (code === '') {
        clearPromoSelection('Enter a promo code first.', true);
        return;
      }

      setPromoFeedback('Checking promo code...', 'muted');

      try {
        const response = await fetch('ajax/validate_promo.php', {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'Accept': 'application/json'
          },
          body: new URLSearchParams({
            code: code,
            subtotal: getCurrentSubtotal()
          }).toString()
        });

        const responseText = await response.text();
        let data;

        try {
          data = JSON.parse(responseText);
        } catch (error) {
          throw new Error(responseText.trim() || 'We could not validate the promo code right now. Please try again.');
        }

        if (data.status === 'success') {
          _promo = {
            code: data.promo_code || code,
            discount: Number(data.discount || 0)
          };
          hidden.value = _promo.code;
          input.value = _promo.code;
          setPromoFeedback(`${_promo.code} applied. You saved ${formatPeso(_promo.discount)} on this booking.`, 'success');
          renderBillingBreakdown();
        } else {
          hidden.value = '';
          _promo = { code: '', discount: 0 };
          setPromoFeedback(data.message || 'Promo code could not be applied.', 'danger');
          updateBookingSnapshot();
        }
      } catch (error) {
        hidden.value = '';
        _promo = { code: '', discount: 0 };
        setPromoFeedback(error.message || 'We could not validate the promo code right now. Please try again.', 'danger');
        updateBookingSnapshot();
      }
    }

    // ── EXTRAS ──
    function changeQty(extraId, delta) {
      const cb = document.getElementById('extra_' + extraId);
      if (!cb || !cb.checked) return;
      const inp = document.getElementById('qty_' + extraId);
      let val = parseInt(inp.value) + delta;
      if (val < 1) val = 1;
      if (val > 10) val = 10;
      inp.value = val;
      updateExtrasTotal();
    }

    function updateExtrasTotal() {
      const checks = document.querySelectorAll('.extra-check');
      let extras = [];
      let extrasTotalPerNight = 0;

      checks.forEach(cb => {
        const wrap = document.getElementById('qty_wrap_' + cb.dataset.extraId);
        const qtyInp = document.getElementById('qty_' + cb.dataset.extraId);
        if (cb.checked) {
          wrap.style.opacity = '1';
          qtyInp.disabled = false;
          const qty = parseInt(qtyInp.value) || 1;
          const price = parseFloat(cb.dataset.extraPrice);
          extrasTotalPerNight += price * qty;
          extras.push({ id: cb.dataset.extraId, name: cb.dataset.extraName, qty, unit_price: price });
        } else {
          wrap.style.opacity = '0.3';
          qtyInp.disabled = true;
          qtyInp.value = 1;
        }
      });

      // Store extras JSON for form submission
      const jsonInput = document.getElementById('extras_json');
      if (jsonInput) jsonInput.value = JSON.stringify(extras);

      // Hide the old extras-total-line (breakdown is now inside renderBillingBreakdown)
      const totalLine = document.getElementById('extras-total-line');
      if (totalLine) totalLine.style.setProperty('display', 'none', 'important');

      if (_promo.code) {
        clearPromoSelection('Promo selection was cleared because your add-ons changed. Apply it again to refresh the discount.');
      }

      // Re-render billing breakdown with new extras
      if (_billing.days > 0) {
        renderBillingBreakdown();
      } else {
        updateExtraPricingHints();
        updateBookingSnapshot();
      }
    }

    // ── DATE CARD LOGIC (Flatpickr) ──
    const DAYS   = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    const MONTHS = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

    function updateDateDisplay(field, dateObj) {
      const disp = document.getElementById(field + '-display');
      const day  = document.getElementById(field + '-day');
      if (dateObj) {
        disp.textContent = MONTHS[dateObj.getMonth()] + ' ' + dateObj.getDate() + ', ' + dateObj.getFullYear();
        disp.classList.remove('placeholder');
        day.textContent  = DAYS[dateObj.getDay()];
      } else {
        disp.textContent = 'Select date';
        disp.classList.add('placeholder');
        day.textContent  = '';
      }
    }

    document.addEventListener('DOMContentLoaded', function() {
      const today = new Date();
      today.setHours(0,0,0,0);

      const dateRow = document.getElementById('date-card-row');

      fpCheckout = flatpickr('#checkout', {
        dateFormat: 'Y-m-d',
        minDate: today,
        disableMobile: true,
        positionElement: dateRow,
        onChange: function(selectedDates, dateStr) {
          updateDateDisplay('checkout', selectedDates[0] || null);
          document.getElementById('date-card-row').classList.remove('has-focus');
          check_availability();
          updateBookingSnapshot();
        },
        onOpen: function() {
          document.getElementById('date-card-row').classList.add('has-focus');
        },
        onClose: function() {
          document.getElementById('date-card-row').classList.remove('has-focus');
        }
      });

      fpCheckin = flatpickr('#checkin', {
        dateFormat: 'Y-m-d',
        minDate: today,
        disableMobile: true,
        positionElement: dateRow,
        onChange: function(selectedDates, dateStr) {
          updateDateDisplay('checkin', selectedDates[0] || null);
          if (selectedDates[0]) {
            // advance checkout min date
            fpCheckout.set('minDate', dateStr);
            const curOut = fpCheckout.selectedDates[0];
            if (!curOut || curOut <= selectedDates[0]) {
              fpCheckout.clear();
              updateDateDisplay('checkout', null);
              // Auto-open checkout picker
              setTimeout(() => fpCheckout.open(), 120);
            }
          }
          document.getElementById('date-card-row').classList.remove('has-focus');
          check_availability();
          updateBookingSnapshot();
        },
        onOpen: function() {
          document.getElementById('date-card-row').classList.add('has-focus');
        },
        onClose: function() {
          document.getElementById('date-card-row').classList.remove('has-focus');
        }
      });

      ['name', 'phonenum', 'address'].forEach(fieldName => {
        const field = booking_form.elements[fieldName];
        if (field) field.addEventListener('input', updateBookingSnapshot);
      });

      const bookingCarouselEl = document.getElementById('bookingRoomCarousel');
      if (bookingCarouselEl && window.bootstrap && bookingCarouselEl.querySelectorAll('.carousel-item').length > 1) {
        bootstrap.Carousel.getOrCreateInstance(bookingCarouselEl, {
          interval: 3200,
          ride: 'carousel',
          touch: true,
          pause: 'hover',
          wrap: true
        });
      }

      setBookingStep(1);
      updateExtraPricingHints();
      updateBookingSnapshot();
    });

    function openDateCard(e) {
      const row = document.getElementById('date-card-row');
      const rect = row.getBoundingClientRect();
      const midX = rect.left + rect.width / 2;
      if (e.clientX < midX) {
        fpCheckin.open();
      } else {
        fpCheckout.open();
      }
    }

    document.addEventListener('click', function(e) {
      if (!e.target.closest('#date-card-row') && !e.target.closest('.flatpickr-calendar')) {
        document.getElementById('date-card-row').classList.remove('has-focus');
      }
    });

    function check_availability()
    {
      let checkin_val = booking_form.elements['checkin'].value;
      let checkout_val = booking_form.elements['checkout'].value;

      booking_form.elements['pay_now'].setAttribute('disabled',true);

      if(checkin_val!='' && checkout_val!='')
      {
        [pay_info, final_pay_info].forEach(panel => {
          if (!panel) return;
          panel.classList.add('d-none');
          panel.classList.remove('text-dark');
          panel.classList.add('text-danger');
        });
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
            resetBillingState("You cannot check-out on the same day!");
            if (_promo.code) clearPromoSelection('Promo selection was cleared because the selected dates are invalid.');
          }
          else if(data.status == 'check_out_earlier'){
            resetBillingState("Check-out date is earlier than check-in date!");
            if (_promo.code) clearPromoSelection('Promo selection was cleared because the selected dates are invalid.');
          }
          else if(data.status == 'check_in_earlier'){
            resetBillingState("Check-in date is earlier than today's date!");
            if (_promo.code) clearPromoSelection('Promo selection was cleared because the selected dates are invalid.');
          }
          else if(data.status == 'unavailable'){
            resetBillingState("Room not available for this check-in date!");
            if (_promo.code) clearPromoSelection('Promo selection was cleared because room availability changed.');
          }
          else{
            // Store billing state for breakdown rendering
            const nextRoomTotal = data.room_total || data.payment;
            const billingChanged =
              _billing.days !== data.days ||
              _billing.room_total !== nextRoomTotal;

            _billing.days        = data.days;
            _billing.price_night = data.price_night || (data.payment / data.days);
            _billing.room_total  = nextRoomTotal;
            if (billingChanged && _promo.code) {
              clearPromoSelection('Promo selection was cleared because your stay dates changed. Apply it again for the updated total.');
            }
            renderBillingBreakdown();
            // Require room selection before enabling payment
            booking_form.elements['pay_now'].setAttribute('disabled',true);
            booking_form.elements['room_no'].value = '';
            // Load rooms map for this room and dates
            load_user_rooms_map(checkin_val, checkout_val);
            updateBookingSnapshot();
          }

          [pay_info, final_pay_info].forEach(panel => {
            if (!panel) return;
            panel.classList.remove('d-none');
          });
          info_loader.classList.add('d-none');
          updateBookingSnapshot();
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
              updateBookingSnapshot();
            });
          }
          row.appendChild(seat);
        });
        grid.appendChild(row);
      }
    }

    const promoInput = document.getElementById('promo_code_input');
    if (promoInput) {
      promoInput.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
          event.preventDefault();
          applyPromoCode();
        }
      });
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
      } else {
        previewDiv.classList.add('d-none');
        imagePreview.classList.add('d-none');
        uploadText.textContent = 'Upload payment proof';
        uploadLabel.classList.remove('btn-outline-success');
        uploadLabel.classList.add('btn-outline-secondary');
      }

      updateBookingSnapshot();
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
