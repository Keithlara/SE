<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css">
  <?php require('inc/links.php'); ?>
  <title><?php echo $settings_r['site_title'] ?> - HOME</title>

  <style>
    :root {
      --gold: #d4af37;
      --gold-light: #e5c048;
      --gold-dark: #b8960c;
      --dark: #111111;
      --dark2: #1a1a1a;
      --dark3: #222222;
    }

    html { scroll-behavior: smooth; }

    body { padding-top: 0 !important; background: #fff; }

    /* ── NAVBAR ── */
    #nav-bar {
      transition: all 0.35s ease;
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
    }
    .navbar-dark-mode {
      background: rgba(0,0,0,0.45) !important;
    }
    .navbar-dark-mode .nav-link,
    .navbar-dark-mode .navbar-brand { color: #fff !important; font-weight: 500; }

    .navbar-light-mode {
      background: rgba(255,255,255,0.92) !important;
      box-shadow: 0 2px 16px rgba(0,0,0,0.08);
    }
    .navbar-light-mode .nav-link,
    .navbar-light-mode .navbar-brand { color: #111 !important; font-weight: 600; }

    .navbar .dropdown-toggle,
    .navbar .btn-group .btn { color: #fff !important; border-color: rgba(255,255,255,0.6); }
    .navbar-light-mode .dropdown-toggle,
    .navbar-light-mode .btn-group .btn { color: #111 !important; border-color: rgba(0,0,0,0.3); }

    .navbar .bi-bell { color: #fff; }
    .navbar-light-mode .bi-bell { color: #111; }

    .btn-auth {
      border-radius: 50px;
      padding: 6px 20px;
      font-weight: 500;
      background: transparent;
      transition: all 0.3s ease;
    }
    .navbar-dark-mode .btn-auth { border: 1.5px solid rgba(255,255,255,0.7); color: #fff; }
    .navbar-dark-mode .btn-auth:hover { background: #fff; color: #111; }
    .navbar-light-mode .btn-auth { border: 1.5px solid #111; color: #111; }
    .navbar-light-mode .btn-auth:hover { background: #111; color: #fff; }

    @media (max-width: 768px) {
      #nav-bar { background: rgba(0,0,0,0.92) !important; }
      #nav-bar .nav-link, #nav-bar .navbar-brand,
      #nav-bar .dropdown-toggle, #nav-bar .btn-group .btn { color: #fff !important; }
      #nav-bar .btn-auth { border-color: #fff; color: #fff; }
      .navbar-collapse { background: rgba(0,0,0,0.97); padding: 15px; border-radius: 12px; margin-top: 10px; }
    }

    /* ── HERO ── */
    .hero-section {
      position: relative;
      height: 100vh;
      min-height: 600px;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .hero-section::before {
      content: '';
      position: absolute; inset: 0;
      background: linear-gradient(to bottom, rgba(0,0,0,0.55) 0%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0.65) 100%);
      z-index: 2;
    }
    .swiper-hero { position: absolute; inset: 0; z-index: 1; }
    .swiper-hero .swiper-slide, .hero-slide { width: 100%; height: 100%; }
    .hero-slide {
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      transform: scale(1.05);
      transition: transform 8s ease;
    }
    .swiper-slide-active .hero-slide { transform: scale(1); }

    .hero-content {
      position: relative;
      z-index: 3;
      text-align: center;
      color: #fff;
      padding: 0 20px;
    }
    .hero-eyebrow {
      font-size: 0.8rem;
      letter-spacing: 6px;
      text-transform: uppercase;
      color: var(--gold);
      margin-bottom: 1rem;
      font-weight: 500;
    }
    .hero-title {
      font-size: clamp(2.8rem, 7vw, 5.5rem);
      font-weight: 700;
      margin-bottom: 1rem;
      font-family: 'Merienda', cursive;
      text-shadow: 0 4px 20px rgba(0,0,0,0.4);
      line-height: 1.1;
    }
    .hero-divider {
      width: 60px;
      height: 2px;
      background: var(--gold);
      margin: 1.2rem auto;
    }
    .hero-subtitle {
      font-size: clamp(0.9rem, 2vw, 1.15rem);
      letter-spacing: 4px;
      text-transform: uppercase;
      margin-bottom: 2.5rem;
      opacity: 0.9;
    }
    .hero-actions { display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; }
    .btn-gold {
      background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
      color: #fff;
      font-weight: 600;
      padding: 14px 38px;
      border: none;
      border-radius: 50px;
      text-transform: uppercase;
      letter-spacing: 2px;
      font-size: 0.9rem;
      box-shadow: 0 8px 25px rgba(212,175,55,0.45);
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-block;
    }
    .btn-gold:hover {
      transform: translateY(-2px);
      box-shadow: 0 14px 35px rgba(212,175,55,0.6);
      color: #fff;
      background: linear-gradient(135deg, var(--gold-light) 0%, var(--gold) 100%);
    }
    .btn-outline-gold {
      background: transparent;
      color: #fff;
      font-weight: 600;
      padding: 13px 36px;
      border: 1.5px solid rgba(255,255,255,0.7);
      border-radius: 50px;
      text-transform: uppercase;
      letter-spacing: 2px;
      font-size: 0.9rem;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-block;
    }
    .btn-outline-gold:hover { background: rgba(255,255,255,0.15); border-color: #fff; color: #fff; transform: translateY(-2px); }

    /* Hero scroll indicator */
    .hero-scroll {
      position: absolute;
      bottom: 32px;
      left: 50%;
      transform: translateX(-50%);
      z-index: 4;
      display: flex;
      flex-direction: column;
      align-items: center;
      color: rgba(255,255,255,0.7);
      font-size: 0.7rem;
      letter-spacing: 2px;
      text-transform: uppercase;
      gap: 6px;
      animation: scrollBounce 2s infinite;
    }
    @keyframes scrollBounce {
      0%, 100% { transform: translateX(-50%) translateY(0); }
      50% { transform: translateX(-50%) translateY(6px); }
    }
    .hero-scroll .scroll-line {
      width: 1px; height: 40px;
      background: linear-gradient(to bottom, rgba(255,255,255,0.7), transparent);
    }

    /* ── STATS STRIP ── */
    .stats-strip {
      background: var(--dark);
      padding: 28px 0;
    }
    .stat-item { text-align: center; padding: 0 20px; }
    .stat-item .stat-number {
      font-size: 2rem;
      font-weight: 700;
      color: var(--gold);
      font-family: 'Merienda', cursive;
      line-height: 1;
    }
    .stat-item .stat-label {
      font-size: 0.75rem;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: rgba(255,255,255,0.55);
      margin-top: 4px;
    }
    .stat-divider {
      width: 1px;
      background: rgba(255,255,255,0.12);
      align-self: stretch;
    }

    /* ── SECTION COMMON ── */
    .section-label {
      font-size: 0.75rem;
      letter-spacing: 5px;
      text-transform: uppercase;
      color: var(--gold);
      font-weight: 600;
      margin-bottom: 10px;
    }
    .section-title {
      font-size: clamp(1.8rem, 4vw, 2.6rem);
      font-weight: 700;
      font-family: 'Merienda', cursive;
      color: var(--dark);
      margin-bottom: 0;
    }
    .section-title-white { color: #fff; }
    .section-divider {
      width: 48px;
      height: 3px;
      background: var(--gold);
      margin: 16px 0 0 0;
      border-radius: 2px;
    }
    .section-divider-center { margin: 16px auto 0; }

    /* ── ROOMS ── */
    .rooms-section { background: #f8f7f5; padding: 90px 0; }
    .room-card {
      background: #fff;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 4px 24px rgba(0,0,0,0.07);
      transition: transform 0.35s ease, box-shadow 0.35s ease;
    }
    .room-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 16px 48px rgba(0,0,0,0.13);
    }
    .room-card-img-wrap {
      position: relative;
      overflow: hidden;
      height: 220px;
    }
    .room-card-img-wrap img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.6s ease;
    }
    .room-card:hover .room-card-img-wrap img { transform: scale(1.06); }
    .room-price-badge {
      position: absolute;
      bottom: 14px;
      right: 14px;
      background: var(--gold);
      color: #fff;
      font-weight: 700;
      font-size: 0.85rem;
      padding: 6px 14px;
      border-radius: 50px;
      letter-spacing: 0.5px;
    }
    .room-card-body { padding: 22px 22px 20px; }
    .room-card-body h5 {
      font-weight: 700;
      font-size: 1.15rem;
      color: var(--dark);
      margin-bottom: 14px;
    }
    .room-tag {
      display: inline-block;
      font-size: 0.7rem;
      font-weight: 600;
      padding: 4px 10px;
      border-radius: 50px;
      background: #f0ede6;
      color: #555;
      margin: 2px;
      letter-spacing: 0.3px;
    }
    .room-meta {
      font-size: 0.8rem;
      color: #888;
      margin-top: 12px;
    }
    .room-actions {
      display: flex;
      gap: 10px;
      margin-top: 18px;
    }
    .btn-book {
      flex: 1;
      background: var(--dark);
      color: #fff;
      border: none;
      border-radius: 50px;
      padding: 9px 0;
      font-size: 0.82rem;
      font-weight: 600;
      letter-spacing: 0.5px;
      transition: background 0.25s;
      cursor: pointer;
    }
    .btn-book:hover { background: var(--gold); color: #fff; }
    .btn-details {
      flex: 1;
      background: transparent;
      color: var(--dark);
      border: 1.5px solid #ddd;
      border-radius: 50px;
      padding: 9px 0;
      font-size: 0.82rem;
      font-weight: 600;
      letter-spacing: 0.5px;
      text-decoration: none;
      text-align: center;
      transition: all 0.25s;
    }
    .btn-details:hover { border-color: var(--dark); color: var(--dark); }

    /* ── WHY CHOOSE US ── */
    .why-section { background: var(--dark); padding: 90px 0; }
    .why-card {
      text-align: center;
      padding: 36px 24px;
      border-radius: 16px;
      background: rgba(255,255,255,0.04);
      border: 1px solid rgba(255,255,255,0.07);
      transition: all 0.3s ease;
    }
    .why-card:hover {
      background: rgba(212,175,55,0.08);
      border-color: rgba(212,175,55,0.3);
      transform: translateY(-4px);
    }
    .why-icon {
      width: 64px;
      height: 64px;
      background: rgba(212,175,55,0.12);
      border: 1.5px solid rgba(212,175,55,0.35);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
      font-size: 1.6rem;
      color: var(--gold);
      transition: all 0.3s ease;
    }
    .why-card:hover .why-icon { background: var(--gold); color: #fff; border-color: var(--gold); }
    .why-card h5 { color: #fff; font-weight: 700; font-size: 1rem; margin-bottom: 10px; }
    .why-card p { color: rgba(255,255,255,0.5); font-size: 0.87rem; line-height: 1.7; margin: 0; }

    /* ── FACILITIES ── */
    .facilities-section { background: #fff; padding: 90px 0; }
    .facility-card {
      text-align: center;
      padding: 32px 16px;
      border-radius: 14px;
      border: 1px solid #eee;
      background: #fff;
      transition: all 0.3s ease;
    }
    .facility-card:hover {
      border-color: var(--gold);
      box-shadow: 0 8px 32px rgba(212,175,55,0.12);
      transform: translateY(-4px);
    }
    .facility-card img { width: 52px; height: 52px; object-fit: contain; }
    .facility-card h6 { margin-top: 14px; font-weight: 700; font-size: 0.9rem; color: var(--dark); }

    /* ── TESTIMONIALS ── */
    .testimonials-section { background: #f8f7f5; padding: 90px 0; }
    .swiper-testimonials .swiper-slide {
      background: #fff;
      border-radius: 16px;
      padding: 32px 28px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.06);
      border: 1px solid #eee;
    }
    .review-stars { color: var(--gold); font-size: 0.9rem; margin-bottom: 14px; }
    .review-text { color: #444; font-size: 0.9rem; line-height: 1.8; margin-bottom: 20px; font-style: italic; }
    .review-author { display: flex; align-items: center; gap: 12px; }
    .review-author img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--gold); }
    .review-author-info .name { font-weight: 700; font-size: 0.88rem; color: var(--dark); }
    .review-author-info .room { font-size: 0.75rem; color: #999; }

    /* ── REACH US ── */
    .contact-section { background: var(--dark); padding: 90px 0; }
    .contact-card {
      background: rgba(255,255,255,0.05);
      border: 1px solid rgba(255,255,255,0.08);
      border-radius: 14px;
      padding: 28px;
      height: 100%;
    }
    .contact-card h5 { color: var(--gold); font-weight: 700; font-size: 0.85rem; letter-spacing: 3px; text-transform: uppercase; margin-bottom: 16px; }
    .contact-card a, .contact-card span { color: rgba(255,255,255,0.7); text-decoration: none; font-size: 0.9rem; transition: color 0.2s; }
    .contact-card a:hover { color: var(--gold); }
    .contact-social-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: rgba(255,255,255,0.06);
      border: 1px solid rgba(255,255,255,0.1);
      border-radius: 50px;
      padding: 8px 16px;
      color: rgba(255,255,255,0.75);
      text-decoration: none;
      font-size: 0.85rem;
      transition: all 0.25s;
      margin: 4px 0;
    }
    .contact-social-badge:hover { background: rgba(212,175,55,0.15); border-color: var(--gold); color: var(--gold); }
    .map-wrap { border-radius: 14px; overflow: hidden; height: 100%; min-height: 300px; }
    .map-wrap iframe { width: 100%; height: 100%; border: none; display: block; }

    /* ── CTA SECTION ── */
    .cta-section {
      background: linear-gradient(135deg, var(--gold-dark) 0%, var(--gold) 50%, var(--gold-light) 100%);
      padding: 80px 0;
      text-align: center;
    }
    .cta-section h2 { color: #fff; font-family: 'Merienda', cursive; font-size: clamp(1.8rem, 4vw, 2.8rem); margin-bottom: 12px; }
    .cta-section p { color: rgba(255,255,255,0.85); font-size: 1rem; margin-bottom: 32px; }
    .btn-cta {
      background: #fff;
      color: var(--gold-dark);
      font-weight: 700;
      padding: 16px 48px;
      border-radius: 50px;
      font-size: 0.95rem;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      text-decoration: none;
      display: inline-block;
      transition: all 0.3s ease;
      box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    }
    .btn-cta:hover { transform: translateY(-3px); box-shadow: 0 14px 40px rgba(0,0,0,0.2); color: var(--gold-dark); }

    /* ── VIEW MORE LINK ── */
    .view-more-link {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 0.8rem;
      font-weight: 700;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: var(--dark);
      text-decoration: none;
      border-bottom: 2px solid var(--gold);
      padding-bottom: 2px;
      transition: all 0.25s;
    }
    .view-more-link:hover { color: var(--gold); gap: 12px; }
    .view-more-link-white { color: #fff; border-color: var(--gold); }
    .view-more-link-white:hover { color: var(--gold); }

    /* Swiper pagination gold */
    .swiper-pagination-bullet-active { background: var(--gold) !important; }

    /* ── FLOATING BOOK NOW ── */
    .book-now-floating {
      position: fixed;
      bottom: 28px;
      right: 28px;
      z-index: 9999;
      padding: 14px 28px;
      font-size: 0.88rem;
      font-weight: 700;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      border-radius: 50px;
      background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
      color: #fff;
      border: none;
      box-shadow: 0 6px 24px rgba(0,0,0,0.25);
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 8px;
      animation: pulseGold 2.5s infinite;
    }
    .book-now-floating:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 32px rgba(0,0,0,0.35);
      background: linear-gradient(135deg, var(--gold-light) 0%, var(--gold) 100%);
    }
    @keyframes pulseGold {
      0%   { box-shadow: 0 0 0 0 rgba(212,175,55,0.5); }
      70%  { box-shadow: 0 0 0 14px rgba(212,175,55,0); }
      100% { box-shadow: 0 0 0 0 rgba(212,175,55,0); }
    }
  </style>
</head>
<body>

  <?php require('inc/header.php'); ?>

  <?php
    $carousel_q = mysqli_query($con, "SELECT * FROM `carousel`");
    $carousel_images = [];
    while($row = mysqli_fetch_assoc($carousel_q)) {
      $carousel_images[] = CAROUSEL_IMG_PATH . $row['image'];
    }
    if(empty($carousel_images)) { $carousel_images[] = 'images/carousel/1.png'; }
  ?>

  <!-- ══ HERO ══ -->
  <section class="hero-section">
    <div class="swiper swiper-hero">
      <div class="swiper-wrapper">
        <?php foreach($carousel_images as $img): ?>
          <div class="swiper-slide">
            <div class="hero-slide" style="background-image: url('<?php echo $img; ?>');"></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="hero-content">
      <p class="hero-eyebrow">Welcome to</p>
      <h1 class="hero-title">Travelers Place</h1>
      <div class="hero-divider"></div>
      <p class="hero-subtitle">Comfort &nbsp;·&nbsp; Convenience &nbsp;·&nbsp; Relaxation</p>
      <div class="hero-actions">
        <?php
          $login = (isset($_SESSION['login']) && $_SESSION['login']) ? 1 : 0;
          if(!$settings_r['shutdown']):
        ?>
          <button onclick="checkLoginToBook(<?php echo $login; ?>, 0)" class="btn-gold">
            <i class="bi bi-calendar-check me-2"></i>Book Now
          </button>
        <?php endif; ?>
        <a href="rooms.php" class="btn-outline-gold">View Rooms</a>
      </div>
    </div>

    <div class="hero-scroll">
      <div class="scroll-line"></div>
      <span>Scroll</span>
    </div>
  </section>

  <!-- ══ STATS STRIP ══ -->
  <div class="stats-strip">
    <div class="container">
      <div class="d-flex justify-content-center align-items-center flex-wrap gap-0 row-gap-3">
        <div class="stat-item">
          <div class="stat-number">7</div>
          <div class="stat-label">Rooms Available</div>
        </div>
        <div class="stat-divider d-none d-md-block mx-4" style="height:48px;"></div>
        <div class="stat-item">
          <div class="stat-number">2PM</div>
          <div class="stat-label">Check-in Time</div>
        </div>
        <div class="stat-divider d-none d-md-block mx-4" style="height:48px;"></div>
        <div class="stat-item">
          <div class="stat-number">12PM</div>
          <div class="stat-label">Check-out Time</div>
        </div>
        <div class="stat-divider d-none d-md-block mx-4" style="height:48px;"></div>
        <div class="stat-item">
          <div class="stat-number">24/7</div>
          <div class="stat-label">Guest Support</div>
        </div>
        <div class="stat-divider d-none d-md-block mx-4" style="height:48px;"></div>
        <div class="stat-item">
          <div class="stat-number">★ 5</div>
          <div class="stat-label">Star Experience</div>
        </div>
      </div>
    </div>
  </div>

  <!-- ══ OUR ROOMS ══ -->
  <section class="rooms-section">
    <div class="container">
      <div class="row mb-5 align-items-end">
        <div class="col">
          <p class="section-label">Accommodations</p>
          <h2 class="section-title">Our Rooms</h2>
          <div class="section-divider"></div>
        </div>
        <div class="col-auto">
          <a href="rooms.php" class="view-more-link">All Rooms <i class="bi bi-arrow-right"></i></a>
        </div>
      </div>

      <div class="row g-4">
        <?php
          $room_res = select("SELECT * FROM `rooms` WHERE `status`=? AND `removed`=? ORDER BY `id` DESC LIMIT 3",[1,0],'ii');
          while($room_data = mysqli_fetch_assoc($room_res)):

            $fea_q = mysqli_query($con,"SELECT f.name FROM `features` f INNER JOIN `room_features` rfea ON f.id=rfea.features_id WHERE rfea.room_id='$room_data[id]'");
            $features_data = "";
            while($fea_row = mysqli_fetch_assoc($fea_q))
              $features_data .= "<span class='room-tag'>{$fea_row['name']}</span>";

            $fac_q = mysqli_query($con,"SELECT f.name FROM `facilities` f INNER JOIN `room_facilities` rfac ON f.id=rfac.facilities_id WHERE rfac.room_id='$room_data[id]'");
            $facilities_data = "";
            while($fac_row = mysqli_fetch_assoc($fac_q))
              $facilities_data .= "<span class='room-tag'>{$fac_row['name']}</span>";

            $room_thumb = ROOMS_IMG_PATH."thumbnail.jpg";
            $thumb_q = mysqli_query($con,"SELECT * FROM `room_images` WHERE `room_id`='$room_data[id]' AND `thumb`='1'");
            if(mysqli_num_rows($thumb_q)>0){
              $thumb_res = mysqli_fetch_assoc($thumb_q);
              $room_thumb = ROOMS_IMG_PATH.$thumb_res['image'];
            }

            $rating_q = "SELECT AVG(rating) AS avg_rating FROM `rating_review` WHERE `room_id`='$room_data[id]'";
            $rating_res = mysqli_query($con,$rating_q);
            $rating_fetch = mysqli_fetch_assoc($rating_res);
            $stars = "";
            if($rating_fetch['avg_rating']){
              for($i=0;$i<round($rating_fetch['avg_rating']);$i++) $stars .= "<i class='bi bi-star-fill'></i>";
              for($i=round($rating_fetch['avg_rating']);$i<5;$i++) $stars .= "<i class='bi bi-star'></i>";
            }

            $book_btn = "";
            if(!$settings_r['shutdown']){
              $login2 = (isset($_SESSION['login']) && $_SESSION['login']) ? 1 : 0;
              $book_btn = "<button onclick='checkLoginToBook($login2,$room_data[id])' class='btn-book'>Book Now</button>";
            }
        ?>
        <div class="col-lg-4 col-md-6">
          <div class="room-card">
            <div class="room-card-img-wrap">
              <img src="<?php echo $room_thumb; ?>" alt="<?php echo $room_data['name']; ?>" loading="lazy">
              <span class="room-price-badge">₱<?php echo number_format($room_data['price']); ?>/night</span>
            </div>
            <div class="room-card-body">
              <h5><?php echo $room_data['name']; ?></h5>
              <?php if($stars): ?><div class="review-stars mb-2"><?php echo $stars; ?></div><?php endif; ?>
              <?php if($features_data): ?>
                <div class="mb-2"><?php echo $features_data; ?></div>
              <?php endif; ?>
              <?php if($facilities_data): ?>
                <div class="mb-2"><?php echo $facilities_data; ?></div>
              <?php endif; ?>
              <div class="room-meta">
                <i class="bi bi-people me-1"></i><?php echo $room_data['adult']; ?> Adults
                &nbsp;·&nbsp; <?php echo $room_data['children']; ?> Children
              </div>
              <div class="room-actions">
                <?php echo $book_btn; ?>
                <a href="room_details.php?id=<?php echo $room_data['id']; ?>" class="btn-details">Details</a>
              </div>
            </div>
          </div>
        </div>
        <?php endwhile; ?>
      </div>
    </div>
  </section>

  <!-- ══ WHY CHOOSE US ══ -->
  <section class="why-section">
    <div class="container">
      <div class="text-center mb-5">
        <p class="section-label">Our Promise</p>
        <h2 class="section-title section-title-white">Why Choose Us</h2>
        <div class="section-divider section-divider-center"></div>
      </div>
      <div class="row g-4">
        <div class="col-lg-3 col-md-6">
          <div class="why-card">
            <div class="why-icon"><i class="bi bi-gem"></i></div>
            <h5>Premium Comfort</h5>
            <p>Thoughtfully furnished rooms designed for a restful and memorable stay every time.</p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="why-card">
            <div class="why-icon"><i class="bi bi-geo-alt"></i></div>
            <h5>Prime Location</h5>
            <p>Conveniently situated so you're always close to what matters most during your trip.</p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="why-card">
            <div class="why-icon"><i class="bi bi-shield-check"></i></div>
            <h5>Safe &amp; Secure</h5>
            <p>Your safety is our top priority. Enjoy peace of mind with round-the-clock security.</p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="why-card">
            <div class="why-icon"><i class="bi bi-headset"></i></div>
            <h5>24/7 Support</h5>
            <p>Our dedicated team is always ready to help — any hour, any day, any need.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ══ FACILITIES ══ -->
  <section class="facilities-section">
    <div class="container">
      <div class="row mb-5 align-items-end">
        <div class="col">
          <p class="section-label">Amenities</p>
          <h2 class="section-title">Our Facilities</h2>
          <div class="section-divider"></div>
        </div>
        <div class="col-auto">
          <a href="facilities.php" class="view-more-link">All Facilities <i class="bi bi-arrow-right"></i></a>
        </div>
      </div>
      <div class="row g-3 justify-content-center">
        <?php
          $res = mysqli_query($con,"SELECT * FROM `facilities` ORDER BY `id` DESC LIMIT 6");
          $path = FACILITIES_IMG_PATH;
          while($row = mysqli_fetch_assoc($res)):
        ?>
        <div class="col-6 col-md-4 col-lg-2">
          <div class="facility-card">
            <img src="<?php echo $path.$row['icon']; ?>" alt="<?php echo $row['name']; ?>" loading="lazy">
            <h6><?php echo $row['name']; ?></h6>
          </div>
        </div>
        <?php endwhile; ?>
      </div>
    </div>
  </section>

  <!-- ══ TESTIMONIALS ══ -->
  <section class="testimonials-section">
    <div class="container">
      <div class="text-center mb-5">
        <p class="section-label">Guest Stories</p>
        <h2 class="section-title">What Our Guests Say</h2>
        <div class="section-divider section-divider-center"></div>
      </div>

      <?php
        $review_q = "SELECT rr.*, uc.name AS uname, uc.profile, r.name AS rname FROM `rating_review` rr
          INNER JOIN `user_cred` uc ON rr.user_id=uc.id
          INNER JOIN `rooms` r ON rr.room_id=r.id
          ORDER BY `sr_no` DESC LIMIT 6";
        $review_res = mysqli_query($con,$review_q);
        $img_path = USERS_IMG_PATH;
        $has_reviews = mysqli_num_rows($review_res) > 0;
      ?>

      <?php if(!$has_reviews): ?>
        <p class="text-center text-muted">No reviews yet — be the first to share your experience!</p>
      <?php else: ?>
        <div class="swiper swiper-testimonials">
          <div class="swiper-wrapper pb-4">
            <?php while($row = mysqli_fetch_assoc($review_res)):
              $stars = "";
              for($i=0;$i<$row['rating'];$i++) $stars .= "<i class='bi bi-star-fill'></i>";
              for($i=$row['rating'];$i<5;$i++) $stars .= "<i class='bi bi-star'></i>";
            ?>
            <div class="swiper-slide">
              <div class="review-stars"><?php echo $stars; ?></div>
              <p class="review-text">"<?php echo htmlspecialchars($row['review']); ?>"</p>
              <div class="review-author">
                <img src="<?php echo $img_path.$row['profile']; ?>" alt="<?php echo $row['uname']; ?>" loading="lazy">
                <div class="review-author-info">
                  <div class="name"><?php echo htmlspecialchars($row['uname']); ?></div>
                  <div class="room"><?php echo htmlspecialchars($row['rname']); ?></div>
                </div>
              </div>
            </div>
            <?php endwhile; ?>
          </div>
          <div class="swiper-pagination mt-3"></div>
        </div>
      <?php endif; ?>

      <div class="text-center mt-5">
        <a href="about.php" class="view-more-link">Read More <i class="bi bi-arrow-right"></i></a>
      </div>
    </div>
  </section>

  <!-- ══ CTA BANNER ══ -->
  <section class="cta-section">
    <div class="container">
      <h2>Ready for an Unforgettable Stay?</h2>
      <p>Book your room today and experience comfort like never before.</p>
      <?php if(!$settings_r['shutdown']): ?>
        <button onclick="checkLoginToBook(<?php echo $login; ?>, 0)" class="btn-cta">
          <i class="bi bi-calendar-check me-2"></i>Reserve Your Room
        </button>
      <?php endif; ?>
    </div>
  </section>

  <!-- ══ REACH US ══ -->
  <section class="contact-section">
    <div class="container">
      <div class="text-center mb-5">
        <p class="section-label">Location &amp; Contact</p>
        <h2 class="section-title section-title-white">Find Us</h2>
        <div class="section-divider section-divider-center"></div>
      </div>

      <div class="row g-4 align-items-stretch">
        <div class="col-lg-8">
          <div class="map-wrap">
            <iframe src="<?php echo $contact_r['iframe']; ?>" loading="lazy" allowfullscreen></iframe>
          </div>
        </div>
        <div class="col-lg-4 d-flex flex-column gap-3">
          <div class="contact-card">
            <h5><i class="bi bi-telephone me-2"></i>Call Us</h5>
            <a href="tel:+<?php echo $contact_r['pn1']; ?>" class="d-block mb-1">
              <i class="bi bi-telephone-fill me-1" style="color:var(--gold);"></i>+<?php echo $contact_r['pn1']; ?>
            </a>
            <?php if($contact_r['pn2']!=''): ?>
              <a href="tel:+<?php echo $contact_r['pn2']; ?>" class="d-block">
                <i class="bi bi-telephone-fill me-1" style="color:var(--gold);"></i>+<?php echo $contact_r['pn2']; ?>
              </a>
            <?php endif; ?>
          </div>
          <div class="contact-card">
            <h5><i class="bi bi-share me-2"></i>Follow Us</h5>
            <div class="d-flex flex-column gap-2">
              <?php if($contact_r['fb']!=''): ?>
                <a href="<?php echo $contact_r['fb']; ?>" class="contact-social-badge" target="_blank">
                  <i class="bi bi-facebook" style="color:#1877f2;"></i> Facebook
                </a>
              <?php endif; ?>
              <?php if($contact_r['insta']!=''): ?>
                <a href="<?php echo $contact_r['insta']; ?>" class="contact-social-badge" target="_blank">
                  <i class="bi bi-instagram" style="color:#e1306c;"></i> Instagram
                </a>
              <?php endif; ?>
              <?php if($contact_r['tw']!=''): ?>
                <a href="<?php echo $contact_r['tw']; ?>" class="contact-social-badge" target="_blank">
                  <i class="bi bi-twitter" style="color:#1da1f2;"></i> Twitter
                </a>
              <?php endif; ?>
            </div>
          </div>
          <div class="contact-card">
            <h5><i class="bi bi-envelope me-2"></i>Write to Us</h5>
            <a href="contact.php" class="contact-social-badge d-inline-flex">
              <i class="bi bi-send" style="color:var(--gold);"></i> Send a Message
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Password reset modal -->
  <div class="modal fade" id="recoveryModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="recovery-form">
          <div class="modal-header">
            <h5 class="modal-title d-flex align-items-center">
              <i class="bi bi-shield-lock fs-3 me-2"></i> Set up New Password
            </h5>
          </div>
          <div class="modal-body">
            <div class="mb-4">
              <label class="form-label">New Password</label>
              <input type="password" name="pass" required class="form-control shadow-none">
              <input type="hidden" name="email">
              <input type="hidden" name="token">
            </div>
            <div class="mb-2 text-end">
              <button type="button" class="btn shadow-none me-2" data-bs-dismiss="modal">CANCEL</button>
              <button type="submit" class="btn btn-dark shadow-none">SUBMIT</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php require('inc/footer.php'); ?>

  <!-- Floating Book Now -->
  <?php if(basename($_SERVER['PHP_SELF']) == 'index.php'): ?>
  <button class="book-now-floating" id="bookNowBtn">
    <i class="bi bi-calendar-check"></i> Book Now
  </button>
  <?php endif; ?>

  <?php
    if(isset($_GET['account_recovery'])){
      $data = filteration($_GET);
      $t_date = date("Y-m-d");
      $query = select("SELECT * FROM `user_cred` WHERE `email`=? AND `token`=? AND `t_expire`=? LIMIT 1",
        [$data['email'],$data['token'],$t_date],'sss');
      if(mysqli_num_rows($query)==1){
        echo "<script>
          var myModal = document.getElementById('recoveryModal');
          myModal.querySelector(\"input[name='email']\").value = '$data[email]';
          myModal.querySelector(\"input[name='token']\").value = '$data[token]';
          bootstrap.Modal.getOrCreateInstance(myModal).show();
        </script>";
      } else {
        alert("error","Invalid or Expired Link !");
      }
    }
  ?>

  <script src="https://unpkg.com/swiper@7/swiper-bundle.min.js"></script>
  <script>
    // Navbar scroll behaviour
    window.addEventListener('scroll', function () {
      const navbar = document.getElementById('nav-bar');
      const isHome = ['/', '', '/index.php'].some(p => window.location.pathname === p || window.location.pathname.endsWith('index.php'));
      if (window.scrollY > 80) {
        navbar.classList.remove('navbar-dark-mode');
        navbar.classList.add('navbar-light-mode');
      } else if (isHome) {
        navbar.classList.remove('navbar-light-mode');
        navbar.classList.add('navbar-dark-mode');
      }
    });
    document.getElementById('nav-bar').classList.add('navbar-dark-mode');

    // Hero swiper
    const heroSwiper = new Swiper('.swiper-hero', {
      loop: true,
      speed: 1200,
      autoplay: { delay: 5000, disableOnInteraction: false },
      effect: 'fade',
      fadeEffect: { crossFade: true },
    });

    // Testimonials swiper
    new Swiper('.swiper-testimonials', {
      loop: true,
      speed: 700,
      autoplay: { delay: 4500, disableOnInteraction: false },
      slidesPerView: 1,
      spaceBetween: 24,
      pagination: { el: '.swiper-pagination', clickable: true },
      breakpoints: {
        768:  { slidesPerView: 2 },
        1024: { slidesPerView: 3 },
      }
    });

    // Floating book now → scroll to top / trigger book
    document.getElementById('bookNowBtn')?.addEventListener('click', function () {
      <?php $login3 = (isset($_SESSION['login']) && $_SESSION['login']) ? 1 : 0; ?>
      checkLoginToBook(<?php echo $login3; ?>, 0);
    });
  </script>

</body>
</html>
