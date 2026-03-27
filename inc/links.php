<?php
  if(ob_get_level() === 0) ob_start();
  if(session_status() !== PHP_SESSION_ACTIVE){
    session_start();
  }
  date_default_timezone_set("Asia/Kolkata");

  require_once('admin/inc/db_config.php');
  require_once('admin/inc/essentials.php');
  
  $contact_q = "SELECT * FROM `contact_details` WHERE `sr_no`=?";
  $settings_q = "SELECT * FROM `settings` WHERE `sr_no`=?";
  $values = [1];
  $contact_r = mysqli_fetch_assoc(select($contact_q,$values,'i'));
  $settings_r = mysqli_fetch_assoc(select($settings_q,$values,'i'));
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<link href="https://fonts.googleapis.com/css2?family=Merienda:wght@400;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
<link rel="stylesheet" href="css/common.css?v=<?php echo filemtime('css/common.css'); ?>">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php
  if($settings_r['shutdown']){
    echo<<<alertbar
      <div class='shutdown-alert bg-danger text-center p-2 fw-bold text-white' style="position: fixed; top: 0; left: 0; right: 0; z-index: 10000;">
        <i class="bi bi-exclamation-triangle-fill"></i>
        Bookings are temporarily closed!
      </div>
      <style>
        body { padding-top: 40px !important; }
        #nav-bar { top: 40px !important; }
      </style>
    alertbar;
  }
?>
