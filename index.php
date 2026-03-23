<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link  rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css">
  <?php require('inc/links.php'); ?>
  <title><?php echo $settings_r['site_title'] ?> - HOME</title>
  <style>
    /* Homepage specific - remove body padding and black navbar */
    body {
      padding-top: 0 !important;
    }
    
    #nav-bar.navbar-dark-mode {
      background: rgba(0, 0, 0, 0.9) !important;
    }

    /* HERO SECTION */
    .hero-section {
      position: relative;
      height: 100vh;
      width: 100%;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .hero-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.5);
      z-index: 2;
    }

    .swiper-hero {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 1;
    }

    .swiper-hero .swiper-slide {
      width: 100%;
      height: 100%;
    }

    .hero-slide {
      width: 100%;
      height: 100%;
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
    }

    /* Hero carousel navigation */
    .hero-content {
      position: relative;
      z-index: 3;
      text-align: center;
      color: white;
    }

    .hero-title {
      font-size: 4.5rem;
      font-weight: 700;
      margin-bottom: 1rem;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
      font-family: 'Merienda', cursive;
    }

    .hero-subtitle {
      font-size: 1.5rem;
      font-weight: 400;
      margin-bottom: 2.5rem;
      letter-spacing: 3px;
      text-transform: uppercase;
    }

    /* BOOK NOW BUTTON */
    .btn-book-now {
      background: linear-gradient(135deg, #d4af37 0%, #c9a227 50%, #b8960c 100%);
      color: white;
      font-size: 1.3rem;
      font-weight: 600;
      padding: 18px 50px;
      border: none;
      border-radius: 50px;
      text-transform: uppercase;
      letter-spacing: 2px;
      box-shadow: 0 10px 30px rgba(212, 175, 55, 0.4);
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-block;
    }

    .btn-book-now:hover {
      transform: scale(1.05);
      box-shadow: 0 15px 40px rgba(212, 175, 55, 0.6);
      color: white;
      background: linear-gradient(135deg, #e5c048 0%, #d4af37 50%, #c9a227 100%);
    }

    /* NAVBAR - BASE STYLES */
    #nav-bar {
      transition: all 0.3s ease;
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
    }

    /* Default safe fallback - white text */
    #nav-bar .nav-link,
    #nav-bar .navbar-brand {
      color: #fff !important;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    /* DARK MODE - 50% transparent */
    .navbar-dark-mode {
      background: rgba(0, 0, 0, 0.5) !important;
    }

    .navbar-dark-mode .nav-link,
    .navbar-dark-mode .navbar-brand {
      color: #fff !important;
    }

    /* LIGHT MODE - 50% transparent */
    .navbar-light-mode {
      background: rgba(255, 255, 255, 0.5) !important;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .navbar-light-mode .nav-link,
    .navbar-light-mode .navbar-brand,
    #nav-bar.navbar-light-mode .nav-link,
    #nav-bar.navbar-light-mode .navbar-brand {
      color: #000 !important;
      font-weight: 600 !important;
      text-shadow: none;
    }

    .navbar-light-mode .nav-link:hover,
    .navbar-light-mode .navbar-brand:hover,
    #nav-bar.navbar-light-mode .nav-link:hover,
    #nav-bar.navbar-light-mode .navbar-brand:hover {
      color: #333 !important;
    }

    /* USER PROFILE VISIBILITY FIX */
    .navbar .dropdown-toggle,
    .navbar .btn-group .btn {
      color: #fff !important;
      border-color: #fff;
    }

    .navbar-light-mode .dropdown-toggle,
    .navbar-light-mode .btn-group .btn {
      color: #000 !important;
      border-color: #000;
    }

    /* Notification bell visibility */
    .navbar .bi-bell {
      color: #fff;
    }

    .navbar-light-mode .bi-bell {
      color: #000;
    }

    /* LOGIN/REGISTER BUTTONS - STYLED FOR BOTH MODES */
    .btn-auth {
      border-radius: 50px;
      padding: 6px 18px;
      font-weight: 500;
      background: transparent;
      transition: all 0.3s ease;
    }

    .navbar-dark-mode .btn-auth {
      border: 1px solid #fff;
      color: #fff;
      box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
    }

    .navbar-dark-mode .btn-auth:hover {
      background: #fff;
      color: #000;
    }

    .navbar-light-mode .btn-auth {
      border: 1px solid #000;
      color: #000;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .navbar-light-mode .btn-auth:hover {
      background: #000;
      color: #fff;
    }

    /* Mobile - always dark for visibility */
    @media (max-width: 768px) {
      .hero-title {
        font-size: 2.5rem;
      }

      .hero-subtitle {
        font-size: 1rem;
        letter-spacing: 2px;
      }

      #nav-bar {
        background: rgba(0, 0, 0, 0.9) !important;
      }

      #nav-bar .nav-link,
      #nav-bar .navbar-brand,
      #nav-bar .dropdown-toggle,
      #nav-bar .btn-group .btn {
        color: #fff !important;
      }

      #nav-bar .btn-auth {
        border-color: #fff;
        color: #fff;
      }

      .navbar-collapse {
        background: rgba(0, 0, 0, 0.95);
        padding: 15px;
        border-radius: 10px;
        margin-top: 10px;
      }
    }

    /* SMOOTH SCROLL */
    html {
      scroll-behavior: smooth;
    }

    /* FLOATING BOOK NOW BUTTON */
    .book-now-floating {
      position: fixed;
      bottom: 30px;
      left: 50%;
      transform: translateX(-50%);
      z-index: 9999;
      padding: 12px 30px;
      font-size: 18px;
      border-radius: 50px;
      background: #d4af37;
      color: #fff;
      border: none;
      box-shadow: 0 5px 20px rgba(0,0,0,0.3);
      transition: all 0.3s ease;
      animation: pulseGlow 2s infinite;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .book-now-floating:hover {
      transform: translateX(-50%) scale(1.08);
      box-shadow: 0 10px 25px rgba(0,0,0,0.4);
      background: #e5c048;
    }

    /* Pulse Animation */
    @keyframes pulseGlow {
      0% { box-shadow: 0 0 0 0 rgba(212,175,55,0.6); }
      70% { box-shadow: 0 0 0 15px rgba(212,175,55,0); }
      100% { box-shadow: 0 0 0 0 rgba(212,175,55,0); }
    }

    /* GLASSMORPHISM NAVBAR MODES */
    .navbar-dark-mode {
      background: rgba(0,0,0,0.5) !important;
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
    }

    .navbar-light-mode {
      background: rgba(255,255,255,0.85) !important;
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    /* Navbar text visibility */
    .navbar-dark-mode .nav-link,
    .navbar-dark-mode .navbar-brand {
      color: #fff !important;
    }

    .navbar-light-mode .nav-link,
    .navbar-light-mode .navbar-brand {
      color: #000 !important;
    }

    /* Login/Register buttons for each mode */
    .navbar-dark-mode .btn-auth {
      border: 1px solid #fff;
      color: #fff;
      box-shadow: 0 0 10px rgba(255,255,255,0.2);
    }

    .navbar-dark-mode .btn-auth:hover {
      background: #fff;
      color: #000;
    }

    .navbar-light-mode .btn-auth {
      border: 1px solid #000;
      color: #000;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .navbar-light-mode .btn-auth:hover {
      background: #000;
      color: #fff;
    }

    /* Section classes for auto-detect */
    .dark-section {
      background-color: #1a1a1a;
    }

    .light-section {
      background-color: #f8f9fa;
    }
  </style>
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <?php
    // Fetch all carousel images
    $carousel_q = mysqli_query($con, "SELECT * FROM `carousel`");
    $carousel_images = [];
    while($row = mysqli_fetch_assoc($carousel_q)) {
      $carousel_images[] = CAROUSEL_IMG_PATH . $row['image'];
    }
    // Fallback to default image if no carousel images
    if(empty($carousel_images)) {
      $carousel_images[] = 'images/carousel/1.png';
    }
  ?>

  <!-- HERO CAROUSEL SECTION -->
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
    
    <!-- Hero Content (static overlay) -->
    <div class="hero-content">
      <h1 class="hero-title">TRAVELERS PLACE</h1>
      <p class="hero-subtitle">Comfort. Convenience. Relaxation.</p>
    </div>
  </section>

  <!-- Our Rooms -->
  <h2 class="mt-5 pt-4 mb-4 text-center fw-bold h-font">OUR ROOMS</h2>

  <div class="container">
    <div class="row">

      <?php 
            
        $room_res = select("SELECT * FROM `rooms` WHERE `status`=? AND `removed`=? ORDER BY `id` DESC LIMIT 3",[1,0],'ii');

        while($room_data = mysqli_fetch_assoc($room_res))
        {
          // get features of room

          $fea_q = mysqli_query($con,"SELECT f.name FROM `features` f 
            INNER JOIN `room_features` rfea ON f.id = rfea.features_id 
            WHERE rfea.room_id = '$room_data[id]'");

          $features_data = "";
          while($fea_row = mysqli_fetch_assoc($fea_q)){
            $features_data .="<span class='badge rounded-pill bg-light text-dark text-wrap me-1 mb-1'>
              $fea_row[name]
            </span>";
          }

          // get facilities of room

          $fac_q = mysqli_query($con,"SELECT f.name FROM `facilities` f 
            INNER JOIN `room_facilities` rfac ON f.id = rfac.facilities_id 
            WHERE rfac.room_id = '$room_data[id]'");

          $facilities_data = "";
          while($fac_row = mysqli_fetch_assoc($fac_q)){
            $facilities_data .="<span class='badge rounded-pill bg-light text-dark text-wrap me-1 mb-1'>
              $fac_row[name]
            </span>";
          }

          // get thumbnail of image

          $room_thumb = ROOMS_IMG_PATH."thumbnail.jpg";
          $thumb_q = mysqli_query($con,"SELECT * FROM `room_images` 
            WHERE `room_id`='$room_data[id]' 
            AND `thumb`='1'");

          if(mysqli_num_rows($thumb_q)>0){
            $thumb_res = mysqli_fetch_assoc($thumb_q);
            $room_thumb = ROOMS_IMG_PATH.$thumb_res['image'];
          }

          $book_btn = "";

          if(!$settings_r['shutdown']){
            $login=0;
            if(isset($_SESSION['login']) && $_SESSION['login']==true){
              $login=1;
            }

            $book_btn = "<button onclick='checkLoginToBook($login,$room_data[id])' class='btn btn-sm text-white custom-bg shadow-none'>Book Now</button>";
          }

          $rating_q = "SELECT AVG(rating) AS `avg_rating` FROM `rating_review`
            WHERE `room_id`='$room_data[id]' ORDER BY `sr_no` DESC LIMIT 20";

          $rating_res = mysqli_query($con,$rating_q);
          $rating_fetch = mysqli_fetch_assoc($rating_res);

          $rating_data = "";

          if($rating_fetch['avg_rating']!=NULL)
          {
            $rating_data = "<div class='rating mb-4'>
              <h6 class='mb-1'>Rating</h6>
              <span class='badge rounded-pill bg-light'>
            ";

            for($i=0; $i<$rating_fetch['avg_rating']; $i++){
              $rating_data .="<i class='bi bi-star-fill text-warning'></i> ";
            }

            $rating_data .= "</span>
              </div>
            ";
          }

          // print room card

          echo <<<data
            <div class="col-lg-4 col-md-6 my-3">
              <div class="card border-0 shadow" style="max-width: 350px; margin: auto;">
                <img src="$room_thumb" class="card-img-top">
                <div class="card-body">
                  <h5>$room_data[name]</h5>
                  <h6 class="mb-4">₱$room_data[price] per night</h6>
                  <div class="features mb-4">
                    <h6 class="mb-1">Features</h6>
                    $features_data
                  </div>
                  <div class="facilities mb-4">
                    <h6 class="mb-1">Facilities</h6>
                    $facilities_data
                  </div>
                  <div class="guests mb-4">
                    <h6 class="mb-1">Guests</h6>
                    <span class="badge rounded-pill bg-light text-dark text-wrap">
                      $room_data[adult] Adults
                    </span>
                    <span class="badge rounded-pill bg-light text-dark text-wrap">
                      $room_data[children] Children
                    </span>
                  </div>
                  $rating_data
                  <div class="d-flex justify-content-evenly mb-2">
                    $book_btn
                    <a href="room_details.php?id=$room_data[id]" class="btn btn-sm btn-outline-dark shadow-none">More details</a>
                  </div>
                </div>
              </div>
            </div>
          data;

        }

      ?>

      <div class="col-lg-12 text-center mt-5">
        <a href="rooms.php" class="btn btn-sm btn-outline-dark rounded-0 fw-bold shadow-none">More Rooms >>></a>
      </div>
    </div>
  </div>

  <!-- Our Facilities -->

  <h2 class="mt-5 pt-4 mb-4 text-center fw-bold h-font">OUR FACILITIES</h2>

  <div class="container">
    <div class="row justify-content-evenly px-lg-0 px-md-0 px-5">
      <?php 
        $res = mysqli_query($con,"SELECT * FROM `facilities` ORDER BY `id` DESC LIMIT 5");
        $path = FACILITIES_IMG_PATH;

        while($row = mysqli_fetch_assoc($res)){
          echo<<<data
            <div class="col-lg-2 col-md-2 text-center bg-white rounded shadow py-4 my-3">
              <img src="$path$row[icon]" width="60px">
              <h5 class="mt-3">$row[name]</h5>
            </div>
          data;
        }
      ?>

      <div class="col-lg-12 text-center mt-5">
        <a href="facilities.php" class="btn btn-sm btn-outline-dark rounded-0 fw-bold shadow-none">More Facilities >>></a>
      </div>
    </div>
  </div>

  <!-- Testimonials -->

  <h2 class="mt-5 pt-4 mb-4 text-center fw-bold h-font">TESTIMONIALS</h2>

  <div class="container mt-5">
    <div class="swiper swiper-testimonials">
      <div class="swiper-wrapper mb-5">
        <?php

          $review_q = "SELECT rr.*,uc.name AS uname, uc.profile, r.name AS rname FROM `rating_review` rr
            INNER JOIN `user_cred` uc ON rr.user_id = uc.id
            INNER JOIN `rooms` r ON rr.room_id = r.id
            ORDER BY `sr_no` DESC LIMIT 6";

          $review_res = mysqli_query($con,$review_q);
          $img_path = USERS_IMG_PATH;

          if(mysqli_num_rows($review_res)==0){
            echo 'No reviews yet!';
          }
          else
          {
            while($row = mysqli_fetch_assoc($review_res))
            {
              $stars = "<i class='bi bi-star-fill text-warning'></i> ";
              for($i=1; $i<$row['rating']; $i++){
                $stars .= " <i class='bi bi-star-fill text-warning'></i>";
              }

              echo<<<slides
                <div class="swiper-slide bg-white p-4">
                  <div class="profile d-flex align-items-center mb-3">
                    <img src="$img_path$row[profile]" class="rounded-circle" loading="lazy" width="30px">
                    <h6 class="m-0 ms-2">$row[uname]</h6>
                  </div>
                  <p>
                    $row[review]
                  </p>
                  <div class="rating">
                    $stars
                  </div>
                </div>
              slides;
            }
          }
        
        ?>
      </div>
      <div class="swiper-pagination"></div>
    </div>
    <div class="col-lg-12 text-center mt-5">
      <a href="about.php" class="btn btn-sm btn-outline-dark rounded-0 fw-bold shadow-none">Know More >>></a>
    </div>
  </div>

  <!-- Reach us -->

  <h2 class="mt-5 pt-4 mb-4 text-center fw-bold h-font">REACH US</h2>

  <div class="container">
    <div class="row">
      <div class="col-lg-8 col-md-8 p-4 mb-lg-0 mb-3 bg-white rounded">
        <iframe class="w-100 rounded" height="320px" src="<?php echo $contact_r['iframe'] ?>" loading="lazy"></iframe>
      </div>
      <div class="col-lg-4 col-md-4">
        <div class="bg-white p-4 rounded mb-4">
          <h5>Call us</h5>
          <a href="tel: +<?php echo $contact_r['pn1'] ?>" class="d-inline-block mb-2 text-decoration-none text-dark">
            <i class="bi bi-telephone-fill"></i> +<?php echo $contact_r['pn1'] ?>
          </a>
          <br>
          <?php 
            if($contact_r['pn2']!=''){
              echo<<<data
                <a href="tel: +$contact_r[pn2]" class="d-inline-block text-decoration-none text-dark">
                  <i class="bi bi-telephone-fill"></i> +$contact_r[pn2]
                </a>
              data;
            }
          
          ?>
        </div>
        <div class="bg-white p-4 rounded mb-4">
          <h5>Follow us</h5>
          <?php 
            if($contact_r['tw']!=''){
              echo<<<data
                <a href="$contact_r[tw]" class="d-inline-block mb-3">
                  <span class="badge bg-light text-dark fs-6 p-2"> 
                  <i class="bi bi-twitter me-1"></i> Twitter
                  </span>
                </a>
                <br>
              data;
            }
          ?>

          <a href="<?php echo $contact_r['fb'] ?>" class="d-inline-block mb-3">
            <span class="badge bg-light text-dark fs-6 p-2"> 
            <i class="bi bi-facebook me-1"></i> Facebook
            </span>
          </a>
          <br>
          <a href="<?php echo $contact_r['insta'] ?>" class="d-inline-block">
            <span class="badge bg-light text-dark fs-6 p-2"> 
            <i class="bi bi-instagram me-1"></i> Instagram
            </span>
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Password reset modal and code -->

  <div class="modal fade" id="recoveryModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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

  <!-- FLOATING BOOK NOW BUTTON (Only on index.php) -->
  <?php if(basename($_SERVER['PHP_SELF']) == 'index.php'): ?>
  <button class="book-now-floating" id="bookNowBtn">
      <i class="bi bi-calendar-check"></i> BOOK NOW
  </button>
  <?php endif; ?>

  <?php
  
    if(isset($_GET['account_recovery']))
    {
      $data = filteration($_GET);

      $t_date = date("Y-m-d");

      $query = select("SELECT * FROM `user_cred` WHERE `email`=? AND `token`=? AND `t_expire`=? LIMIT 1",
        [$data['email'],$data['token'],$t_date],'sss');

      if(mysqli_num_rows($query)==1)
      {
        echo<<<showModal
          <script>
            var myModal = document.getElementById('recoveryModal');

            myModal.querySelector("input[name='email']").value = '$data[email]';
            myModal.querySelector("input[name='token']").value = '$data[token]';

            var modal = bootstrap.Modal.getOrCreateInstance(myModal);
            modal.show();
          </script>
        showModal;
      }
      else{
        alert("error","Invalid or Expired Link !");
      }

    }

  ?>
  
  <script src="https://unpkg.com/swiper@7/swiper-bundle.min.js"></script>

  <script>
    // NAVBAR SCROLL EFFECT with dark/light mode
    window.addEventListener('scroll', function() {
      const navbar = document.getElementById('nav-bar');
      const isHome = window.location.pathname.includes('index.php') || window.location.pathname === '/' || window.location.pathname === '';
      
      if (window.scrollY > 100) {
        navbar.classList.remove('navbar-dark-mode');
        navbar.classList.add('navbar-light-mode');
      } else if (isHome) {
        navbar.classList.remove('navbar-light-mode');
        navbar.classList.add('navbar-dark-mode');
      }
    });

    // INITIAL NAVBAR STATE
    document.addEventListener('DOMContentLoaded', function() {
      const navbar = document.getElementById('nav-bar');
      const isHome = window.location.pathname.includes('index.php') || window.location.pathname === '/' || window.location.pathname === '';
      
      if (isHome) {
        navbar.classList.add('navbar-dark-mode');
      } else {
        navbar.classList.add('navbar-light-mode');
      }
    });

    // FLOATING BOOK NOW BUTTON LOGIC
    document.addEventListener('DOMContentLoaded', function() {
      const btn = document.getElementById('bookNowBtn');
      if (!btn) return;

      // Click handler for login check
      btn.addEventListener('click', function() {
        const isLoggedIn = <?php echo isset($_SESSION['login']) && $_SESSION['login'] == true ? 'true' : 'false'; ?>;

        if (isLoggedIn) {
          window.location.href = 'rooms.php';
        } else {
          let loginModalEl = document.getElementById('loginModal');
          if (loginModalEl) {
            let modal = new bootstrap.Modal(loginModalEl);
            modal.show();
          }
        }
      });

      // Auto-hide near footer
      const footer = document.querySelector('footer');
      if (footer) {
        window.addEventListener('scroll', function() {
          const rect = footer.getBoundingClientRect();
          if (rect.top < window.innerHeight) {
            btn.style.opacity = '0';
            btn.style.pointerEvents = 'none';
          } else {
            btn.style.opacity = '1';
            btn.style.pointerEvents = 'auto';
          }
        });
      }
    });

    // SECTION-BASED NAVBAR COLOR (Advanced)
    window.addEventListener('scroll', function() {
      const navbar = document.getElementById('nav-bar');
      const sections = document.querySelectorAll('.dark-section, .light-section');
      
      if (sections.length === 0) return;

      let currentSection = null;
      const navbarBottom = 100; // navbar height offset

      sections.forEach(section => {
        const rect = section.getBoundingClientRect();
        if (rect.top <= navbarBottom && rect.bottom >= navbarBottom) {
          currentSection = section;
        }
      });

      if (currentSection) {
        if (currentSection.classList.contains('dark-section')) {
          navbar.classList.add('navbar-dark-mode');
          navbar.classList.remove('navbar-light-mode');
        } else {
          navbar.classList.add('navbar-light-mode');
          navbar.classList.remove('navbar-dark-mode');
        }
      }
    });

    // SWIPER HERO CAROUSEL
    var heroSwiper = new Swiper(".swiper-hero", {
      effect: "fade",
      grabCursor: true,
      loop: true,
      autoplay: {
        delay: 5000,
        disableOnInteraction: false,
      },
    });

    // SWIPER TESTIMONIALS
    var swiper = new Swiper(".swiper-testimonials", {
      effect: "coverflow",
      grabCursor: true,
      centeredSlides: true,
      slidesPerView: "auto",
      slidesPerView: "3",
      loop: true,
      coverflowEffect: {
        rotate: 50,
        stretch: 0,
        depth: 100,
        modifier: 1,
        slideShadows: false,
      },
      pagination: {
        el: ".swiper-pagination",
      },
      breakpoints: {
        320: {
          slidesPerView: 1,
        },
        640: {
          slidesPerView: 1,
        },
        768: {
          slidesPerView: 2,
        },
        1024: {
          slidesPerView: 3,
        },
      }
    });

    // recover account
    
    let recovery_form = document.getElementById('recovery-form');

    recovery_form.addEventListener('submit', (e)=>{
      e.preventDefault();

      let data = new FormData();

      data.append('email',recovery_form.elements['email'].value);
      data.append('token',recovery_form.elements['token'].value);
      data.append('pass',recovery_form.elements['pass'].value);
      data.append('recover_user','');

      var myModal = document.getElementById('recoveryModal');
      var modal = bootstrap.Modal.getInstance(myModal);
      modal.hide();

      let xhr = new XMLHttpRequest();
      xhr.open("POST","ajax/login_register.php",true);

      xhr.onload = function(){
        if(this.responseText == 'failed'){
          alert('error',"Account reset failed!");
        }
        else{
          alert('success',"Account Reset Successful !");
          recovery_form.reset();
        }
      }

      xhr.send(data);
    });

  </script>

</body>
</html>