<?php 
  // Include loggers
  require_once('activity_logger.php');
  require_once('audit_logger.php');

  //frontend purpose data

  $site_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
  define('SITE_URL', rtrim($site_url, '/') . '/');
  define('ABOUT_IMG_PATH',SITE_URL.'images/about/');
  define('CAROUSEL_IMG_PATH',SITE_URL.'images/carousel/');
  define('FACILITIES_IMG_PATH',SITE_URL.'images/facilities/');
  define('ROOMS_IMG_PATH',SITE_URL.'images/rooms/');
  define('USERS_IMG_PATH',SITE_URL.'images/users/');
  
  // Logging helper functions
  function logAction($action, $details = '') {
      return logActivity($action, $details);
  }
  
  function logError($message, $details = '') {
      return AuditLogger::logSystem('Error: ' . $message, $details);
  }
  
  function logSecurityEvent($action, $details = '') {
      return AuditLogger::logSecurity($action, $details);
  }


  //backend upload process needs this data

  define('UPLOAD_IMAGE_PATH',$_SERVER['DOCUMENT_ROOT'].'/images/');
  define('ABOUT_FOLDER','about/');
  define('CAROUSEL_FOLDER','carousel/');
  define('FACILITIES_FOLDER','facilities/');
  define('ROOMS_FOLDER','rooms/');
  define('USERS_FOLDER','users/');

  // payments toggle (1 = enabled with gateway, 0 = disabled/test mode)
  if(!defined('PAYMENTS_ENABLED')){
    define('PAYMENTS_ENABLED',0);
  }

  // sendgrid api key

  define('SENDGRID_API_KEY',"PASTE YOUR API KEY GENERATED FROM SENDGRID WEBSITE");
  define('SENDGRID_EMAIL',"PUT YOU EMAIL");
  define('SENDGRID_NAME',"ANY NAME");

  // Possible "booking status" values in db = pending, booked, payment failed, cancelled
  
  /**
   * Create a new notification in the database
   * @param mysqli $con Database connection
   * @param int $user_id User ID to notify
   * @param int $booking_id Related booking ID
   * @param string $message Notification message
   * @return bool True on success, false on failure
   */
  function createNotification($con, $user_id, $booking_id, $message) {
      $query = "INSERT INTO notifications (user_id, booking_id, message) VALUES (?, ?, ?)";
      $stmt = $con->prepare($query);
      if (!$stmt) {
          error_log("Prepare failed: " . $con->error);
          return false;
      }
      $stmt->bind_param('iis', $user_id, $booking_id, $message);
      $result = $stmt->execute();
      if (!$result) {
          error_log("Notification creation failed: " . $stmt->error);
      }
      return $result;
  }
  
  // Function to get time ago format
  function timeAgo($datetime) {
      $time = strtotime($datetime);
      $time_difference = time() - $time;
      
      if($time_difference < 1) { return 'just now'; }
      
      $condition = [
          12 * 30 * 24 * 60 * 60  =>  'year',
          30 * 24 * 60 * 60       =>  'month',
          24 * 60 * 60            =>  'day',
          60 * 60                 =>  'hour',
          60                      =>  'minute',
          1                       =>  'second'
      ];
      
      foreach($condition as $secs => $str) {
          $d = $time_difference / $secs;
          if($d >= 1) {
              $t = round($d);
              return $t . ' ' . $str . ($t > 1 ? 's' : '') . ' ago';
          }
      }
      return 'just now';
  }
  
  // Flexible settings helper (supports key/value or column-based schema)
  function settings($con, $key) {
      static $settings_mode = null;
      static $settings_columns = [];

      if($settings_mode === null){
          $columns_res = mysqli_query($con, "SHOW COLUMNS FROM `settings`");
          if($columns_res){
              while($col = mysqli_fetch_assoc($columns_res)){
                  $settings_columns[] = $col['Field'];
              }
          }

          if(in_array('key', $settings_columns) && in_array('value', $settings_columns)){
              $settings_mode = 'key_value';
          } else {
              $settings_mode = 'columns';
          }
      }

      if($settings_mode === 'key_value'){
          $query = "SELECT `value` FROM `settings` WHERE `key` = ? LIMIT 1";
          $stmt = $con->prepare($query);
          if(!$stmt){ return ''; }
          $stmt->bind_param('s', $key);
          $stmt->execute();
          $result = $stmt->get_result();
          
          if($result && $result->num_rows > 0) {
              $row = $result->fetch_assoc();
              return $row['value'];
          }
          return '';
      }

      if(!in_array($key, $settings_columns)){
          return '';
      }

      $query = "SELECT `$key` AS val FROM `settings` WHERE `sr_no` = 1 LIMIT 1";
      $stmt = $con->prepare($query);
      if(!$stmt){ return ''; }
      $stmt->execute();
      $result = $stmt->get_result();

      if($result && $result->num_rows > 0){
          $row = $result->fetch_assoc();
          return $row['val'];
      }
      return '';
  }
  
  // to configure paytm gateway check file 'project folder / inc / paytm / config_paytm.php' 

  function adminLogin()
  {
    if(session_status() === PHP_SESSION_NONE){ session_start(); }
    if(!(isset($_SESSION['adminLogin']) && $_SESSION['adminLogin']==true)){
      echo"<script>
        window.location.href='index.php';
      </script>";
      exit;
    }

    // Default role for legacy logins that don't set a role
    if(!isset($_SESSION['adminRole']) || $_SESSION['adminRole'] === ''){
      $_SESSION['adminRole'] = 'admin';
    }

    // Staff restrictions (apply globally to all admin pages that call adminLogin)
    if(isset($_SESSION['adminRole']) && $_SESSION['adminRole'] === 'staff'){
      $allowed_pages = [
        'dashboard.php',
        'new_bookings.php',
        'booking_records.php',
        'refund_bookings.php',
        'all_time_reports.php',
        'transaction.php',
        'change_password.php',
        'logout.php',
      ];

      $allowed_ajax = [
        'dashboard.php',
        'new_bookings.php',
        'booking_records.php',
        'refund_bookings.php',
        'reports.php',
        'transactions.php',
        'confirm_booking.php',
      ];

      $self = strtolower(str_replace('\\','/', $_SERVER['PHP_SELF'] ?? ''));
      $file = strtolower(basename($self));
      $is_ajax = (strpos($self, '/admin/ajax/') !== false);
      $is_admin_path = (strpos($self, '/admin/') !== false);

      if($is_ajax){
        if(!in_array($file, $allowed_ajax, true)){
          http_response_code(403);
          echo 'Forbidden';
          exit;
        }
      }
      else if($is_admin_path){
        if(!in_array($file, $allowed_pages, true)){
          redirect('dashboard.php');
        }
      }
    }
  }

  // Use on pages that must be Admin-only (e.g. create user)
  function requireAdminRole()
  {
    adminLogin();
    if(($_SESSION['adminRole'] ?? 'admin') !== 'admin'){
      redirect('dashboard.php');
    }
  }

  function redirect($url){
    echo"<script>
      window.location.href='$url';
    </script>";
    exit;
  }

  function alert($type,$msg){    
    $bs_class = ($type == "success") ? "alert-success" : "alert-danger";

    echo <<<alert
      <div class="alert $bs_class alert-dismissible fade show custom-alert" role="alert">
        <strong class="me-3">$msg</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    alert;
  }

  function uploadImage($image,$folder)
  {
    $valid_mime = ['image/jpeg','image/png','image/webp'];
    $img_mime = $image['type'];

    if(!in_array($img_mime,$valid_mime)){
      return 'inv_img'; //invalid image mime or format
    }
    else if(($image['size']/(1024*1024))>10){
      return 'inv_size'; //invalid size greater than 10mb
    }
    else{
      $ext = pathinfo($image['name'],PATHINFO_EXTENSION);
      $rname = 'IMG_'.random_int(11111,99999).".$ext";

      $img_path = UPLOAD_IMAGE_PATH.$folder.$rname;
      if(move_uploaded_file($image['tmp_name'],$img_path)){
        return $rname;
      }
      else{
        return 'upd_failed';
      }
    }
  }

  function deleteImage($image, $folder)
  {
    $path = UPLOAD_IMAGE_PATH.$folder.$image;
    // If file exists, try to delete it; if it doesn't exist, treat as deleted
    if(file_exists($path)){
      return unlink($path);
    }
    return true;
  }

  function uploadSVGImage($image,$folder)
  {
    $valid_mime = ['image/svg+xml'];
    $img_mime = $image['type'];

    if(!in_array($img_mime,$valid_mime)){
      return 'inv_img'; //invalid image mime or format
    }
    else if(($image['size']/(1024*1024))>1){
      return 'inv_size'; //invalid size greater than 1mb
    }
    else{
      $ext = pathinfo($image['name'],PATHINFO_EXTENSION);
      $rname = 'IMG_'.random_int(11111,99999).".$ext";

      $img_path = UPLOAD_IMAGE_PATH.$folder.$rname;
      if(move_uploaded_file($image['tmp_name'],$img_path)){
        return $rname;
      }
      else{
        return 'upd_failed';
      }
    }
  }

  function uploadUserImage($image)
  {
    $valid_mime = ['image/jpeg','image/png','image/webp'];
    $img_mime = $image['type'];

    if(!in_array($img_mime,$valid_mime)){
      return 'inv_img'; //invalid image mime or format
    }
    else
    {
      $ext = pathinfo($image['name'],PATHINFO_EXTENSION);
      $isPng = ($ext == 'png' || $ext == 'PNG');
      $isWebp = ($ext == 'webp' || $ext == 'WEBP');

      $gdAvailable = function_exists('imagejpeg');

      if($gdAvailable){
        $rname = 'IMG_'.random_int(11111,99999).".jpeg";
        $img_path = UPLOAD_IMAGE_PATH.USERS_FOLDER.$rname;

        if($isPng && function_exists('imagecreatefrompng')) {
          $img = imagecreatefrompng($image['tmp_name']);
        }
        else if($isWebp && function_exists('imagecreatefromwebp')) {
          $img = imagecreatefromwebp($image['tmp_name']);
        }
        else if(function_exists('imagecreatefromjpeg')){
          $img = imagecreatefromjpeg($image['tmp_name']);
        }
        else{
          $gdAvailable = false; // fallback if specific loader missing
        }

        if($gdAvailable && imagejpeg($img,$img_path,75)){
          return $rname;
        }
        // if conversion fails, fall through to simple move below
      }

      // Fallback: move the original file without conversion (GD not available)
      $safeExt = strtolower($ext);
      $rname = 'IMG_'.random_int(11111,99999).".$safeExt";
      $img_path = UPLOAD_IMAGE_PATH.USERS_FOLDER.$rname;
      if(move_uploaded_file($image['tmp_name'],$img_path)){
        return $rname;
      }
      else{
        return 'upd_failed';
      }
    }
  }

?>