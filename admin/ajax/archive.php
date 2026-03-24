<?php

require('../inc/db_config.php');
require('../inc/essentials.php');

// Ensure only logged-in admins can access this endpoint
adminLogin();

// Force JSON responses and hide PHP errors from output
ini_set('display_errors', 0);
ini_set('log_errors', 1);
header('Content-Type: application/json; charset=utf-8');

// Ensure fatals still return JSON (prevents "Unexpected end of JSON")
ob_start();
register_shutdown_function(function () {
  $err = error_get_last();
  if (!$err) return;

  $fatal_types = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR];
  if (!in_array($err['type'], $fatal_types, true)) return;

  if (ob_get_length()) {
    ob_clean();
  }

  http_response_code(500);
  echo json_encode([
    'status'  => 'error',
    'message' => 'Server error while processing request',
  ]);
});

function send_json($status, $message = '', $extra = [])
{
  if (ob_get_length()) {
    ob_clean();
  }

  $payload = array_merge([
    'status'  => $status,
    'message' => $message,
  ], $extra);

  echo json_encode($payload);
  exit;
}

// Normalize simple action names into internal flags
if (isset($_POST['action'])) {
  switch ($_POST['action']) {
    case 'list_archives':
      // Used by archives.js to load tab data
      $_POST['list_archives'] = 1;
      break;

    case 'restore':
      $type = $_POST['type'] ?? 'booking';
      if ($type === 'booking') {
        $_POST['restore_record'] = 1;
        if (isset($_POST['id'])) {
          $_POST['booking_id'] = $_POST['id'];
        }
      } elseif ($type === 'room') {
        $_POST['restore_room'] = 1;
      } elseif ($type === 'user') {
        $_POST['restore_user_archive'] = 1;
      }
      break;

    case 'delete':
      // Handled later by explicit type checks
      break;
  }
}

  // Ensure archive tables exist (idempotent)
  function ensure_archive_tables()
  {
    $con = $GLOBALS['con'];

    $tables = [
      "CREATE TABLE IF NOT EXISTS `archived_booking_order` (
        `booking_id` int(11) NOT NULL,
        `user_id` int(11) NOT NULL,
        `room_id` int(11) NOT NULL,
        `check_in` date NOT NULL,
        `check_out` date NOT NULL,
        `arrival` int(11) NOT NULL DEFAULT 0,
        `refund` int(11) DEFAULT NULL,
        `booking_status` varchar(100) NOT NULL DEFAULT 'pending',
        `order_id` varchar(150) NOT NULL,
        `trans_id` varchar(200) DEFAULT NULL,
        `trans_amt` int(11) NOT NULL,
        `trans_status` varchar(100) NOT NULL DEFAULT 'pending',
        `trans_resp_msg` varchar(200) DEFAULT NULL,
        `rate_review` int(11) DEFAULT NULL,
        `datentime` datetime NOT NULL DEFAULT current_timestamp(),
        `archived_at` datetime NOT NULL DEFAULT current_timestamp()
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
      "CREATE TABLE IF NOT EXISTS `archived_booking_details` (
        `sr_no` int(11) NOT NULL,
        `booking_id` int(11) NOT NULL,
        `room_name` varchar(100) NOT NULL,
        `price` int(11) NOT NULL,
        `total_pay` int(11) NOT NULL,
        `room_no` varchar(100) DEFAULT NULL,
        `user_name` varchar(100) NOT NULL,
        `phonenum` varchar(100) NOT NULL,
        `address` varchar(150) NOT NULL,
        `booking_note` text DEFAULT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
      "CREATE TABLE IF NOT EXISTS `archived_rooms` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `room_id` int(11) NOT NULL,
        `name` varchar(150) NOT NULL,
        `area` int(11) NOT NULL,
        `price` int(11) NOT NULL,
        `quantity` int(11) NOT NULL DEFAULT 1,
        `adult` int(11) NOT NULL DEFAULT 1,
        `children` int(11) NOT NULL DEFAULT 0,
        `description` mediumtext NOT NULL,
        `status` tinyint NOT NULL DEFAULT 1,
        `removed` tinyint NOT NULL DEFAULT 1,
        `archived_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `room_id` (`room_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
      "CREATE TABLE IF NOT EXISTS `archived_room_images` (
        `id` int(11) NOT NULL,
        `room_id` int(11) NOT NULL,
        `image` varchar(200) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
      "CREATE TABLE IF NOT EXISTS `archived_room_features` (
        `id` int(11) NOT NULL,
        `room_id` int(11) NOT NULL,
        `features_id` int(11) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
      "CREATE TABLE IF NOT EXISTS `archived_room_facilities` (
        `id` int(11) NOT NULL,
        `room_id` int(11) NOT NULL,
        `facilities_id` int(11) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
      "CREATE TABLE IF NOT EXISTS `archived_ratings_reviews` (
        `id` int(11) NOT NULL,
        `room_id` int(11) NOT NULL,
        `user_id` int(11) NOT NULL,
        `rating` int(11) NOT NULL,
        `review` text NOT NULL,
        `datentime` datetime NOT NULL DEFAULT current_timestamp()
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    ];

    foreach ($tables as $sql) {
      if (!mysqli_query($con, $sql)) {
        error_log('Error creating archive tables: ' . mysqli_error($con));
        send_json('error', 'Failed to initialize archive tables');
      }
    }
  }

  ensure_archive_tables();

  // Get room details with images, features, and facilities
  function get_archived_room_details($room_id) {
    global $con;
    
    // Get room basic info
    $room = [];
    $room_query = "SELECT * FROM `archived_rooms` WHERE `id` = ?";
    $room_stmt = mysqli_prepare($con, $room_query);
    mysqli_stmt_bind_param($room_stmt, 'i', $room_id);
    mysqli_stmt_execute($room_stmt);
    $room_result = mysqli_stmt_get_result($room_stmt);
    
    if (mysqli_num_rows($room_result) > 0) {
      $room = mysqli_fetch_assoc($room_result);
      
      // Get room images
      $images_query = "SELECT * FROM `archived_room_images` WHERE `room_id` = ?";
      $images_stmt = mysqli_prepare($con, $images_query);
      mysqli_stmt_bind_param($images_stmt, 'i', $room_id);
      mysqli_stmt_execute($images_stmt);
      $images_result = mysqli_stmt_get_result($images_stmt);
      $room['images'] = [];
      
      while ($image = mysqli_fetch_assoc($images_result)) {
        $room['images'][] = $image;
      }
      
      // Get room features
      $features_query = "SELECT f.* FROM `archived_room_features` rf 
                        JOIN `features` f ON rf.features_id = f.id 
                        WHERE rf.room_id = ?";
      $features_stmt = mysqli_prepare($con, $features_query);
      mysqli_stmt_bind_param($features_stmt, 'i', $room_id);
      mysqli_stmt_execute($features_stmt);
      $features_result = mysqli_stmt_get_result($features_stmt);
      $room['features'] = [];
      
      while ($feature = mysqli_fetch_assoc($features_result)) {
        $room['features'][] = $feature;
      }
      
      // Get room facilities
      $facilities_query = "SELECT f.* FROM `archived_room_facilities` rf 
                          JOIN `facilities` f ON rf.facilities_id = f.id 
                          WHERE rf.room_id = ?";
      $facilities_stmt = mysqli_prepare($con, $facilities_query);
      mysqli_stmt_bind_param($facilities_stmt, 'i', $room_id);
      mysqli_stmt_execute($facilities_stmt);
      $facilities_result = mysqli_stmt_get_result($facilities_stmt);
      $room['facilities'] = [];
      
      while ($facility = mysqli_fetch_assoc($facilities_result)) {
        $room['facilities'][] = $facility;
      }
      
      // Get room ratings and reviews
      $ratings_query = "SELECT r.*, u.name as user_name, u.profile as user_profile 
                       FROM `archived_ratings_reviews` r
                       LEFT JOIN `user_cred` u ON r.user_id = u.id
                       WHERE r.room_id = ?
                       ORDER BY r.datentime DESC";
      $ratings_stmt = mysqli_prepare($con, $ratings_query);
      mysqli_stmt_bind_param($ratings_stmt, 'i', $room['id']);
      mysqli_stmt_execute($ratings_stmt);
      $ratings_result = mysqli_stmt_get_result($ratings_stmt);
      $room['ratings'] = [];
      
      while ($rating = mysqli_fetch_assoc($ratings_result)) {
        $room['ratings'][] = $rating;
      }
      
      // Calculate average rating
      $avg_rating = 0;
      if (count($room['ratings']) > 0) {
        $total_rating = array_sum(array_column($room['ratings'], 'rating'));
        $avg_rating = $total_rating / count($room['ratings']);
      }
      $room['avg_rating'] = round($avg_rating, 1);
    }
    
    return $room;
  }

  function build_filters_where($frm_data, &$params, &$types)
  {
    $where = [];

    if (!empty($frm_data['date_from']) && !empty($frm_data['date_to'])) {
      $where[] = "DATE(bo.datentime) BETWEEN ? AND ?";
      $params[] = $frm_data['date_from'];
      $params[] = $frm_data['date_to'];
      $types  .= 'ss';
    }

    if (!empty($frm_data['guest'])) {
      $where[] = "bd.user_name LIKE ?";
      $params[] = "%" . $frm_data['guest'] . "%";
      $types  .= 's';
    }

    if (!empty($frm_data['room_type'])) {
      $where[] = "bd.room_name LIKE ?";
      $params[] = "%" . $frm_data['room_type'] . "%";
      $types  .= 's';
    }

    if (!empty($frm_data['transaction_type'])) {
      $where[] = "bo.trans_status = ?";
      $params[] = $frm_data['transaction_type'];
      $types  .= 's';
    }

    if (!empty($frm_data['search'])) {
      $where[] = "(bo.order_id LIKE ? OR bd.phonenum LIKE ? OR bd.user_name LIKE ?)";
      $params[] = "%" . $frm_data['search'] . "%";
      $params[] = "%" . $frm_data['search'] . "%";
      $params[] = "%" . $frm_data['search'] . "%";
      $types  .= 'sss';
    }

    return (count($where) > 0) ? (" WHERE " . implode(" AND ", $where)) : '';
  }

  if (isset($_POST['list_archives'])) {
    global $con;
    $frm_data = filteration($_POST);

    $type = $frm_data['type'] ?? 'bookings';
    $limit = (isset($frm_data['limit']) && (int)$frm_data['limit'] > 0) ? (int)$frm_data['limit'] : 10;
    $page  = (isset($frm_data['page']) && (int)$frm_data['page'] > 0) ? (int)$frm_data['page'] : 1;
    $start = ($page - 1) * $limit;

    // Handle each archive type separately
    if ($type === 'bookings') {
      $params = [];
      $types  = '';
      $where  = build_filters_where($frm_data, $params, $types);

      $base_query = "FROM `archived_booking_order` bo 
                     INNER JOIN `archived_booking_details` bd 
                     ON bo.booking_id = bd.booking_id" . $where;

      if ($types === '') {
        $count_res = mysqli_query($con, "SELECT COUNT(*) as total " . $base_query);
      } else {
        $count_res = select("SELECT COUNT(*) as total " . $base_query, $params, $types);
      }

      if (!$count_res) {
        error_log('Archive count query failed (bookings): ' . mysqli_error($con));
        send_json('error', 'Failed to load archives');
      }

      $total_rows = (int)mysqli_fetch_assoc($count_res)['total'];

      if ($total_rows === 0) {
        send_json('success', '', [
          'html'       => "<tr><td colspan='5' class='text-center text-muted'>No Data Found!</td></tr>",
          'pagination' => '',
        ]);
      }

      if ($types === '') {
        $limit_query = mysqli_query($con, "SELECT bo.*, bd.* " . $base_query . " ORDER BY bo.booking_id DESC LIMIT $start,$limit");
      } else {
        $limit_query = select("SELECT bo.*, bd.* " . $base_query . " ORDER BY bo.booking_id DESC LIMIT $start,$limit", $params, $types);
      }

      if (!$limit_query) {
        error_log('Archive list query failed (bookings): ' . mysqli_error($con));
        send_json('error', 'Failed to load archives');
      }

      $i          = $start + 1;
      $table_data = '';

      while ($data = mysqli_fetch_assoc($limit_query)) {
        $date       = date("d-m-Y", strtotime($data['datentime']));
        $booking_id = (int)$data['booking_id'];

        $table_data .= "
          <tr>
            <td>{$i}</td>
            <td>
              <span class='badge bg-primary'>Order ID: " . htmlspecialchars($data['order_id']) . "</span><br>
              <b>Name:</b> " . htmlspecialchars($data['user_name']) . "<br>
              <b>Phone No:</b> " . htmlspecialchars($data['phonenum']) . "
            </td>
            <td>
              <b>Room:</b> " . htmlspecialchars($data['room_name']) . "<br>
              <b>Price:</b> ₱" . htmlspecialchars($data['price']) . "
            </td>
            <td>
              <b>Amount:</b> ₱" . htmlspecialchars($data['trans_amt']) . "<br>
              <b>Date:</b> {$date}
            </td>
            <td>
              <button type='button' onclick='restore({$booking_id}, \"booking\")' class='btn btn-success btn-sm fw-bold shadow-none' title='Restore booking'>
                <i class='bi bi-arrow-counterclockwise'></i>
              </button>
            </td>
          </tr>
        ";
        $i++;
      }

      $pagination = '';
      if ($total_rows > $limit) {
        $total_pages = (int)ceil($total_rows / $limit);

        if ($page !== 1) {
          $pagination .= "<li class='page-item'><button onclick='change_page(1)' class='page-link shadow-none'>First</button></li>";
        }

        $disabled = ($page === 1) ? 'disabled' : '';
        $prev     = $page - 1;
        $pagination .= "<li class='page-item {$disabled}'><button onclick='change_page({$prev})' class='page-link shadow-none'>Prev</button></li>";

        $disabled = ($page === $total_pages) ? 'disabled' : '';
        $next     = $page + 1;
        $pagination .= "<li class='page-item {$disabled}'><button onclick='change_page({$next})' class='page-link shadow-none'>Next</button></li>";

        if ($page !== $total_pages) {
          $pagination .= "<li class='page-item'><button onclick='change_page({$total_pages})' class='page-link shadow-none'>Last</button></li>";
        }
      }

      send_json('success', '', [
        'html'       => $table_data,
        'pagination' => $pagination,
      ]);
    }

    // Rooms archive listing
    if ($type === 'rooms') {
      $count_res = mysqli_query($con, "SELECT COUNT(*) as total FROM `archived_rooms`");
      if (!$count_res) {
        error_log('Archive count query failed (rooms): ' . mysqli_error($con));
        send_json('error', 'Failed to load room archives');
      }

      $total_rows = (int)mysqli_fetch_assoc($count_res)['total'];

      if ($total_rows === 0) {
        send_json('success', '', [
          'html'       => "<tr><td colspan='6' class='text-center text-muted'>No archived rooms found</td></tr>",
          'pagination' => '',
        ]);
      }

      $limit_query = mysqli_query(
        $con,
        "SELECT * FROM `archived_rooms` ORDER BY `archived_at` DESC LIMIT {$start},{$limit}"
      );

      if (!$limit_query) {
        error_log('Archive list query failed (rooms): ' . mysqli_error($con));
        send_json('error', 'Failed to load room archives');
      }

      $i          = $start + 1;
      $table_data = '';

      while ($row = mysqli_fetch_assoc($limit_query)) {
        $archived_on = date('d-m-Y H:i', strtotime($row['archived_at']));

        $table_data .= "
          <tr>
            <td>{$i}</td>
            <td>" . htmlspecialchars($row['name'] ?? $row['room_name'] ?? '') . "</td>
            <td>" . htmlspecialchars($row['area'] ?? '') . " sq.ft</td>
            <td>₱" . htmlspecialchars($row['price'] ?? '') . "</td>
            <td>{$archived_on}</td>
            <td>
              <button type='button' onclick='restore(" . (int)$row['id'] . ", \"room\")' class='btn btn-success btn-sm fw-bold shadow-none me-1' title='Restore room'>
                <i class='bi bi-arrow-counterclockwise'></i>
              </button>
              <button type='button' onclick='permanentDelete(" . (int)$row['id'] . ", \"room\")' class='btn btn-danger btn-sm fw-bold shadow-none' title='Delete permanently'>
                <i class='bi bi-trash'></i>
              </button>
            </td>
          </tr>
        ";
        $i++;
      }

      $pagination = '';
      if ($total_rows > $limit) {
        $total_pages = (int)ceil($total_rows / $limit);

        if ($page !== 1) {
          $pagination .= "<li class='page-item'><button onclick='change_page(1)' class='page-link shadow-none'>First</button></li>";
        }

        $disabled = ($page === 1) ? 'disabled' : '';
        $prev     = $page - 1;
        $pagination .= "<li class='page-item {$disabled}'><button onclick='change_page({$prev})' class='page-link shadow-none'>Prev</button></li>";

        $disabled = ($page === $total_pages) ? 'disabled' : '';
        $next     = $page + 1;
        $pagination .= "<li class='page-item {$disabled}'><button onclick='change_page({$next})' class='page-link shadow-none'>Next</button></li>";

        if ($page !== $total_pages) {
          $pagination .= "<li class='page-item'><button onclick='change_page({$total_pages})' class='page-link shadow-none'>Last</button></li>";
        }
      }

      send_json('success', '', [
        'html'       => $table_data,
        'pagination' => $pagination,
      ]);
    }

    // Users archive listing
    if ($type === 'users') {
      $count_res = mysqli_query($con, "SELECT COUNT(*) as total FROM `archived_user_cred`");
      if (!$count_res) {
        error_log('Archive count query failed (users): ' . mysqli_error($con));
        send_json('error', 'Failed to load user archives');
      }

      $total_rows = (int)mysqli_fetch_assoc($count_res)['total'];

      if ($total_rows === 0) {
        send_json('success', '', [
          'html'       => "<tr><td colspan='5' class='text-center text-muted'>No archived users found</td></tr>",
          'pagination' => '',
        ]);
      }

      $limit_query = mysqli_query(
        $con,
        "SELECT * FROM `archived_user_cred` ORDER BY `archived_at` DESC LIMIT {$start},{$limit}"
      );

      if (!$limit_query) {
        error_log('Archive list query failed (users): ' . mysqli_error($con));
        send_json('error', 'Failed to load user archives');
      }

      $i          = $start + 1;
      $table_data = '';

      while ($row = mysqli_fetch_assoc($limit_query)) {
        $archived_on = date('d-m-Y H:i', strtotime($row['archived_at']));

        $table_data .= "
          <tr>
            <td>{$i}</td>
            <td>" . htmlspecialchars($row['name']) . "</td>
            <td>" . htmlspecialchars($row['email']) . "</td>
            <td>" . htmlspecialchars($row['phonenum']) . "</td>
            <td>{$archived_on}</td>
            <td>
              <button type='button' onclick='restore(" . (int)$row['id'] . ", \"user\")' class='btn btn-success btn-sm fw-bold shadow-none me-1' title='Restore user'>
                <i class='bi bi-arrow-counterclockwise'></i>
              </button>
              <button type='button' onclick='permanentDelete(" . (int)$row['id'] . ", \"user\")' class='btn btn-danger btn-sm fw-bold shadow-none' title='Delete permanently'>
                <i class='bi bi-trash'></i>
              </button>
            </td>
          </tr>
        ";
        $i++;
      }

      $pagination = '';
      if ($total_rows > $limit) {
        $total_pages = (int)ceil($total_rows / $limit);

        if ($page !== 1) {
          $pagination .= "<li class='page-item'><button onclick='change_page(1)' class='page-link shadow-none'>First</button></li>";
        }

        $disabled = ($page === 1) ? 'disabled' : '';
        $prev     = $page - 1;
        $pagination .= "<li class='page-item {$disabled}'><button onclick='change_page({$prev})' class='page-link shadow-none'>Prev</button></li>";

        $disabled = ($page === $total_pages) ? 'disabled' : '';
        $next     = $page + 1;
        $pagination .= "<li class='page-item {$disabled}'><button onclick='change_page({$next})' class='page-link shadow-none'>Next</button></li>";

        if ($page !== $total_pages) {
          $pagination .= "<li class='page-item'><button onclick='change_page({$total_pages})' class='page-link shadow-none'>Last</button></li>";
        }
      }

      send_json('success', '', [
        'html'       => $table_data,
        'pagination' => $pagination,
      ]);
    }

    // Simple listing for archived user queries
    if ($type === 'queries') {
      $count_res = mysqli_query($con, "SELECT COUNT(*) as total FROM `user_queries` WHERE `is_archived` = 1");
      if (!$count_res) {
        error_log('Archive count query failed (queries): ' . mysqli_error($con));
        send_json('error', 'Failed to load query archives');
      }

      $total_rows = (int)mysqli_fetch_assoc($count_res)['total'];

      if ($total_rows === 0) {
        send_json('success', '', [
          'html'       => "<tr><td colspan='6' class='text-center text-muted'>No archived queries found</td></tr>",
          'pagination' => '',
        ]);
      }

      $limit_query = mysqli_query(
        $con,
        "SELECT * FROM `user_queries` WHERE `is_archived` = 1 ORDER BY `sr_no` DESC LIMIT {$start},{$limit}"
      );

      if (!$limit_query) {
        error_log('Archive list query failed (queries): ' . mysqli_error($con));
        send_json('error', 'Failed to load query archives');
      }

      $i          = $start + 1;
      $table_data = '';

      while ($row = mysqli_fetch_assoc($limit_query)) {
        $archived_on = !empty($row['archived_at'])
          ? date('d-m-Y H:i', strtotime($row['archived_at']))
          : date('d-m-Y H:i', strtotime($row['datentime']));

        $table_data .= "
          <tr>
            <td>{$i}</td>
            <td>" . htmlspecialchars($row['name']) . "</td>
            <td>" . htmlspecialchars($row['email']) . "</td>
            <td>" . htmlspecialchars($row['subject']) . "</td>
            <td>" . htmlspecialchars($row['message']) . "</td>
            <td>{$archived_on}</td>
          </tr>
        ";
        $i++;
      }

      $pagination = '';
      if ($total_rows > $limit) {
        $total_pages = (int)ceil($total_rows / $limit);

        if ($page !== 1) {
          $pagination .= "<li class='page-item'><button onclick='change_page(1)' class='page-link shadow-none'>First</button></li>";
        }

        $disabled = ($page === 1) ? 'disabled' : '';
        $prev     = $page - 1;
        $pagination .= "<li class='page-item {$disabled}'><button onclick='change_page({$prev})' class='page-link shadow-none'>Prev</button></li>";

        $disabled = ($page === $total_pages) ? 'disabled' : '';
        $next     = $page + 1;
        $pagination .= "<li class='page-item {$disabled}'><button onclick='change_page({$next})' class='page-link shadow-none'>Next</button></li>";

        if ($page !== $total_pages) {
          $pagination .= "<li class='page-item'><button onclick='change_page({$total_pages})' class='page-link shadow-none'>Last</button></li>";
        }
      }

      send_json('success', '', [
        'html'       => $table_data,
        'pagination' => $pagination,
      ]);
    }

    // Fallback
    send_json('error', 'Unknown archive type');
  }

  // Permanent delete handlers (archive tables only)
  if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $type = $_POST['type'] ?? '';
    $id   = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($id <= 0) {
      send_json('error', 'Invalid record id');
    }

    // Ensure only full admins can perform destructive archive deletes
    if (($_SESSION['adminRole'] ?? 'admin') !== 'admin') {
      send_json('error', 'You are not allowed to delete archives');
    }

    try {
      if ($type === 'booking') {
        delete("DELETE FROM `archived_booking_details` WHERE `booking_id`=?", [$id], 'i');
        delete("DELETE FROM `archived_booking_order`   WHERE `booking_id`=?", [$id], 'i');

        logAction('archive_delete_booking', "Permanently deleted archived booking_id={$id}");
        send_json('success', 'Archived booking deleted permanently');
      }

      if ($type === 'room') {
        // Delete room-related archive data
        delete("DELETE FROM `archived_room_images`     WHERE `room_id`=?", [$id], 'i');
        delete("DELETE FROM `archived_room_features`   WHERE `room_id`=?", [$id], 'i');
        delete("DELETE FROM `archived_room_facilities` WHERE `room_id`=?", [$id], 'i');
        delete("DELETE FROM `archived_ratings_reviews` WHERE `room_id`=?", [$id], 'i');
        delete("DELETE FROM `archived_rooms`           WHERE `id`=?",      [$id], 'i');

        logAction('archive_delete_room', "Permanently deleted archived room id={$id}");
        send_json('success', 'Archived room deleted permanently');
      }

      if ($type === 'user') {
        delete("DELETE FROM `archived_user_cred` WHERE `id`=?", [$id], 'i');

        logAction('archive_delete_user', "Permanently deleted archived user id={$id}");
        send_json('success', 'Archived user deleted permanently');
      }

      send_json('error', 'Unknown delete type');
    } catch (Throwable $e) {
      error_log('Archive permanent delete failed: ' . $e->getMessage());
      send_json('error', 'Failed to delete archived record');
    }
  }

  if (isset($_POST['archive_record']))
  {
    $frm = filteration($_POST);
    $booking_id = (int)$frm['booking_id'];

    // copy to archive tables
    $bo_res = select("SELECT * FROM booking_order WHERE booking_id=?", [$booking_id], 'i');
    $bd_res = select("SELECT * FROM booking_details WHERE booking_id=?", [$booking_id], 'i');

    if(mysqli_num_rows($bo_res)==0 || mysqli_num_rows($bd_res)==0){
      send_json('error', 'Record not found');
    }

    $bo = mysqli_fetch_assoc($bo_res);
    $bd = mysqli_fetch_assoc($bd_res);

    // insert into archive
    $ins_bo = insert("INSERT INTO archived_booking_order (booking_id,user_id,room_id,check_in,check_out,arrival,refund,booking_status,order_id,trans_id,trans_amt,trans_status,trans_resp_msg,rate_review,datentime) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
      [
        $bo['booking_id'],$bo['user_id'],$bo['room_id'],$bo['check_in'],$bo['check_out'],$bo['arrival'],$bo['refund'],$bo['booking_status'],$bo['order_id'],$bo['trans_id'],$bo['trans_amt'],$bo['trans_status'],$bo['trans_resp_msg'],$bo['rate_review'],$bo['datentime']
      ],
      'iiissiiississis'
    );

    $ins_bd = insert("INSERT INTO archived_booking_details (sr_no,booking_id,room_name,price,total_pay,room_no,user_name,phonenum,address) VALUES (?,?,?,?,?,?,?,?,?)",
      [
        $bd['sr_no'],$bd['booking_id'],$bd['room_name'],$bd['price'],$bd['total_pay'],$bd['room_no'],$bd['user_name'],$bd['phonenum'],$bd['address']
      ],
      'iisiissss'
    );
    // If booking_note exists in live table, store it too (backward compatible)
    if(array_key_exists('booking_note', $bd)){
      // Ensure column exists in archive table
      $col = mysqli_query($con, "SHOW COLUMNS FROM `archived_booking_details` LIKE 'booking_note'");
      if(!$col || mysqli_num_rows($col)==0){
        mysqli_query($con, "ALTER TABLE `archived_booking_details` ADD `booking_note` TEXT NULL");
      }
      update(
        "UPDATE `archived_booking_details` SET `booking_note`=? WHERE `booking_id`=?",
        [$bd['booking_note'], $bd['booking_id']],
        'si'
      );
    }

    if($ins_bo && $ins_bd){
      // delete from live tables
      delete("DELETE FROM booking_details WHERE booking_id=?", [$booking_id], 'i');
      delete("DELETE FROM booking_order WHERE booking_id=?", [$booking_id], 'i');
      send_json('success', 'Booking archived successfully');
    } else {
      send_json('error', 'Archive failed');
    }
  }

  if (isset($_POST['restore_record']))
  {
    $frm = filteration($_POST);
    $booking_id = (int)$frm['booking_id'];

    if ($booking_id <= 0) {
      send_json('error', 'Invalid booking id');
    }

    try {
      mysqli_begin_transaction($con);

      // If booking already exists in live tables, don't attempt restore
      $stmt = mysqli_prepare($con, "SELECT 1 FROM `booking_order` WHERE `booking_id`=? LIMIT 1");
      if (!$stmt) {
        throw new Exception('Failed to prepare booking check');
      }
      mysqli_stmt_bind_param($stmt, 'i', $booking_id);
      mysqli_stmt_execute($stmt);
      $exists = mysqli_stmt_get_result($stmt);
      mysqli_stmt_close($stmt);
      if ($exists && mysqli_num_rows($exists) > 0) {
        mysqli_rollback($con);
        send_json('error', 'Booking is already active (cannot restore)');
      }

      // Load archived rows
      $stmt = mysqli_prepare($con, "SELECT * FROM `archived_booking_order` WHERE `booking_id`=? LIMIT 1");
      if (!$stmt) {
        throw new Exception('Failed to prepare archived booking order');
      }
      mysqli_stmt_bind_param($stmt, 'i', $booking_id);
      mysqli_stmt_execute($stmt);
      $bo_res = mysqli_stmt_get_result($stmt);
      $bo = $bo_res ? mysqli_fetch_assoc($bo_res) : null;
      mysqli_stmt_close($stmt);

      $stmt = mysqli_prepare($con, "SELECT * FROM `archived_booking_details` WHERE `booking_id`=? LIMIT 1");
      if (!$stmt) {
        throw new Exception('Failed to prepare archived booking details');
      }
      mysqli_stmt_bind_param($stmt, 'i', $booking_id);
      mysqli_stmt_execute($stmt);
      $bd_res = mysqli_stmt_get_result($stmt);
      $bd = $bd_res ? mysqli_fetch_assoc($bd_res) : null;
      mysqli_stmt_close($stmt);

      if (!$bo || !$bd) {
        mysqli_rollback($con);
        send_json('error', 'Archived record not found');
      }

      // Restore booking_order (keep original booking_id for referential integrity)
      $q1 = "INSERT INTO `booking_order`
        (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
      $stmt = mysqli_prepare($con, $q1);
      if (!$stmt) {
        throw new Exception('Failed to prepare booking_order insert');
      }
      mysqli_stmt_bind_param(
        $stmt,
        'iiissiiississis',
        $bo['booking_id'],
        $bo['user_id'],
        $bo['room_id'],
        $bo['check_in'],
        $bo['check_out'],
        $bo['arrival'],
        $bo['refund'],
        $bo['booking_status'],
        $bo['order_id'],
        $bo['trans_id'],
        $bo['trans_amt'],
        $bo['trans_status'],
        $bo['trans_resp_msg'],
        $bo['rate_review'],
        $bo['datentime']
      );
      if (!mysqli_stmt_execute($stmt)) {
        $err = mysqli_error($con);
        mysqli_stmt_close($stmt);
        throw new Exception('Failed to restore booking_order: ' . $err);
      }
      mysqli_stmt_close($stmt);

      // Restore booking_details
      // Important: do NOT insert archived sr_no (commonly auto-increment PK)
      // Ensure live table has booking_note column (backward compatible)
      $col = mysqli_query($con, "SHOW COLUMNS FROM `booking_details` LIKE 'booking_note'");
      if(!$col || mysqli_num_rows($col)==0){
        mysqli_query($con, "ALTER TABLE `booking_details` ADD `booking_note` TEXT NULL");
      }

      $bd_note = $bd['booking_note'] ?? null;
      $q2 = "INSERT INTO `booking_details`
        (`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`)
        VALUES (?,?,?,?,?,?,?,?,?)";
      $stmt = mysqli_prepare($con, $q2);
      if (!$stmt) {
        throw new Exception('Failed to prepare booking_details insert');
      }
      mysqli_stmt_bind_param(
        $stmt,
        'isiisssss',
        $bd['booking_id'],
        $bd['room_name'],
        $bd['price'],
        $bd['total_pay'],
        $bd['room_no'],
        $bd['user_name'],
        $bd['phonenum'],
        $bd['address'],
        $bd_note
      );
      if (!mysqli_stmt_execute($stmt)) {
        $err = mysqli_error($con);
        mysqli_stmt_close($stmt);
        throw new Exception('Failed to restore booking_details: ' . $err);
      }
      mysqli_stmt_close($stmt);

      // Remove from archives
      $stmt = mysqli_prepare($con, "DELETE FROM `archived_booking_details` WHERE `booking_id`=?");
      mysqli_stmt_bind_param($stmt, 'i', $booking_id);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_close($stmt);

      $stmt = mysqli_prepare($con, "DELETE FROM `archived_booking_order` WHERE `booking_id`=?");
      mysqli_stmt_bind_param($stmt, 'i', $booking_id);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_close($stmt);

      mysqli_commit($con);
      send_json('success', 'Booking restored successfully');
    } catch (Throwable $e) {
      if (isset($con)) {
        mysqli_rollback($con);
      }
      error_log('Archive restore failed: ' . $e->getMessage());
      send_json('error', 'Restore failed: ' . $e->getMessage());
    }
  }

  // Restore an archived room back to the live rooms table
  if (isset($_POST['restore_room'])) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($id <= 0) {
      send_json('error', 'Invalid room id');
    }

    try {
      mysqli_begin_transaction($con);

      // Load archived room
      $stmt = mysqli_prepare($con, "SELECT * FROM `archived_rooms` WHERE `id`=? LIMIT 1");
      if (!$stmt) throw new Exception('Failed to prepare archived room query');
      mysqli_stmt_bind_param($stmt, 'i', $id);
      mysqli_stmt_execute($stmt);
      $res = mysqli_stmt_get_result($stmt);
      $ar = $res ? mysqli_fetch_assoc($res) : null;
      mysqli_stmt_close($stmt);

      if (!$ar) {
        mysqli_rollback($con);
        send_json('error', 'Archived room not found');
      }

      // Check if the original room still exists (might have been permanently removed)
      $stmt = mysqli_prepare($con, "SELECT `id` FROM `rooms` WHERE `id`=? LIMIT 1");
      mysqli_stmt_bind_param($stmt, 'i', $ar['room_id']);
      mysqli_stmt_execute($stmt);
      $exists = mysqli_stmt_get_result($stmt);
      mysqli_stmt_close($stmt);

      if ($exists && mysqli_num_rows($exists) > 0) {
        // Room still exists — just un-archive it
        $stmt = mysqli_prepare($con, "UPDATE `rooms` SET `is_archived`=0, `removed`=0, `archived_at`=NULL WHERE `id`=?");
        mysqli_stmt_bind_param($stmt, 'i', $ar['room_id']);
        if (!mysqli_stmt_execute($stmt)) throw new Exception('Failed to restore room status');
        mysqli_stmt_close($stmt);
      } else {
        // Room was fully deleted — re-insert it
        $stmt = mysqli_prepare($con,
          "INSERT INTO `rooms` (`id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`is_archived`)
           VALUES (?,?,?,?,?,?,?,?,?,0,0)");
        if (!$stmt) throw new Exception('Failed to prepare room insert');
        mysqli_stmt_bind_param($stmt, 'isiiiiisi',
          $ar['room_id'], $ar['name'], $ar['area'], $ar['price'],
          $ar['quantity'], $ar['adult'], $ar['children'], $ar['description'], $ar['status']
        );
        if (!mysqli_stmt_execute($stmt)) throw new Exception('Failed to re-insert room: ' . mysqli_error($con));
        mysqli_stmt_close($stmt);
      }

      $room_id = $ar['room_id'];

      // Restore room images
      $img_res = mysqli_query($con, "SELECT * FROM `archived_room_images` WHERE `room_id`=" . (int)$id);
      if ($img_res) {
        while ($img = mysqli_fetch_assoc($img_res)) {
          $chk = mysqli_prepare($con, "SELECT 1 FROM `room_images` WHERE `room_id`=? AND `image`=? LIMIT 1");
          mysqli_stmt_bind_param($chk, 'is', $room_id, $img['image']);
          mysqli_stmt_execute($chk);
          $chk_res = mysqli_stmt_get_result($chk);
          mysqli_stmt_close($chk);
          if (!$chk_res || mysqli_num_rows($chk_res) === 0) {
            $ins = mysqli_prepare($con, "INSERT INTO `room_images` (`room_id`,`image`) VALUES (?,?)");
            mysqli_stmt_bind_param($ins, 'is', $room_id, $img['image']);
            mysqli_stmt_execute($ins);
            mysqli_stmt_close($ins);
          }
        }
      }

      // Restore room features
      $feat_res = mysqli_query($con, "SELECT * FROM `archived_room_features` WHERE `room_id`=" . (int)$id);
      if ($feat_res) {
        while ($feat = mysqli_fetch_assoc($feat_res)) {
          $chk = mysqli_prepare($con, "SELECT 1 FROM `room_features` WHERE `room_id`=? AND `features_id`=? LIMIT 1");
          mysqli_stmt_bind_param($chk, 'ii', $room_id, $feat['features_id']);
          mysqli_stmt_execute($chk);
          $chk_res = mysqli_stmt_get_result($chk);
          mysqli_stmt_close($chk);
          if (!$chk_res || mysqli_num_rows($chk_res) === 0) {
            $ins = mysqli_prepare($con, "INSERT INTO `room_features` (`room_id`,`features_id`) VALUES (?,?)");
            mysqli_stmt_bind_param($ins, 'ii', $room_id, $feat['features_id']);
            mysqli_stmt_execute($ins);
            mysqli_stmt_close($ins);
          }
        }
      }

      // Restore room facilities
      $fac_res = mysqli_query($con, "SELECT * FROM `archived_room_facilities` WHERE `room_id`=" . (int)$id);
      if ($fac_res) {
        while ($fac = mysqli_fetch_assoc($fac_res)) {
          $chk = mysqli_prepare($con, "SELECT 1 FROM `room_facilities` WHERE `room_id`=? AND `facilities_id`=? LIMIT 1");
          mysqli_stmt_bind_param($chk, 'ii', $room_id, $fac['facilities_id']);
          mysqli_stmt_execute($chk);
          $chk_res = mysqli_stmt_get_result($chk);
          mysqli_stmt_close($chk);
          if (!$chk_res || mysqli_num_rows($chk_res) === 0) {
            $ins = mysqli_prepare($con, "INSERT INTO `room_facilities` (`room_id`,`facilities_id`) VALUES (?,?)");
            mysqli_stmt_bind_param($ins, 'ii', $room_id, $fac['facilities_id']);
            mysqli_stmt_execute($ins);
            mysqli_stmt_close($ins);
          }
        }
      }

      // Remove from archive tables
      mysqli_query($con, "DELETE FROM `archived_room_images` WHERE `room_id`=" . (int)$id);
      mysqli_query($con, "DELETE FROM `archived_room_features` WHERE `room_id`=" . (int)$id);
      mysqli_query($con, "DELETE FROM `archived_room_facilities` WHERE `room_id`=" . (int)$id);
      mysqli_query($con, "DELETE FROM `archived_rooms` WHERE `id`=" . (int)$id);

      mysqli_commit($con);
      logAction('archive_restore_room', "Restored archived room id={$id} (room_id={$room_id})");
      send_json('success', 'Room restored successfully');
    } catch (Throwable $e) {
      mysqli_rollback($con);
      error_log('Room restore failed: ' . $e->getMessage());
      send_json('error', 'Restore failed: ' . $e->getMessage());
    }
  }

  // Restore an archived user back to the live user_cred table
  if (isset($_POST['restore_user_archive'])) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($id <= 0) {
      send_json('error', 'Invalid user id');
    }

    try {
      mysqli_begin_transaction($con);

      $stmt = mysqli_prepare($con, "SELECT * FROM `archived_user_cred` WHERE `id`=? LIMIT 1");
      if (!$stmt) throw new Exception('Failed to prepare archived user query');
      mysqli_stmt_bind_param($stmt, 'i', $id);
      mysqli_stmt_execute($stmt);
      $res = mysqli_stmt_get_result($stmt);
      $au = $res ? mysqli_fetch_assoc($res) : null;
      mysqli_stmt_close($stmt);

      if (!$au) {
        mysqli_rollback($con);
        send_json('error', 'Archived user not found');
      }

      // Check if user id already exists in live table
      $chk = mysqli_prepare($con, "SELECT 1 FROM `user_cred` WHERE `id`=? LIMIT 1");
      mysqli_stmt_bind_param($chk, 'i', $au['id']);
      mysqli_stmt_execute($chk);
      $chk_res = mysqli_stmt_get_result($chk);
      mysqli_stmt_close($chk);
      if ($chk_res && mysqli_num_rows($chk_res) > 0) {
        mysqli_rollback($con);
        send_json('error', 'User already exists in live table (cannot restore)');
      }

      $stmt = mysqli_prepare($con,
        "INSERT INTO `user_cred` (`id`,`name`,`email`,`address`,`phonenum`,`pincode`,`dob`,`password`,`is_verified`,`token`,`t_expire`,`datentime`,`status`,`profile`)
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
      if (!$stmt) throw new Exception('Failed to prepare user insert');
      mysqli_stmt_bind_param($stmt, 'issssissssssis',
        $au['id'], $au['name'], $au['email'], $au['address'], $au['phonenum'],
        $au['pincode'], $au['dob'], $au['password'], $au['is_verified'],
        $au['token'], $au['t_expire'], $au['datentime'], $au['status'], $au['profile']
      );
      if (!mysqli_stmt_execute($stmt)) throw new Exception('Failed to restore user: ' . mysqli_error($con));
      mysqli_stmt_close($stmt);

      $del = mysqli_prepare($con, "DELETE FROM `archived_user_cred` WHERE `id`=?");
      mysqli_stmt_bind_param($del, 'i', $id);
      mysqli_stmt_execute($del);
      mysqli_stmt_close($del);

      mysqli_commit($con);
      logAction('archive_restore_user', "Restored archived user id={$id}");
      send_json('success', 'User restored successfully');
    } catch (Throwable $e) {
      mysqli_rollback($con);
      error_log('User restore failed: ' . $e->getMessage());
      send_json('error', 'Restore failed: ' . $e->getMessage());
    }
  }

  if(isset($_GET['export']) && ($_GET['export']=='csv' || $_GET['export']=='pdf'))
  {
    $frm_data = filteration($_GET);
    $params = [];
    $types = '';
    $where = build_filters_where($frm_data,$params,$types);
    $res = select("SELECT bo.*, bd.* FROM archived_booking_order bo INNER JOIN archived_booking_details bd ON bo.booking_id = bd.booking_id".$where." ORDER BY bo.booking_id DESC", $params, $types);

    if($_GET['export']=='csv'){
      header('Content-Type: text/csv');
      header('Content-Disposition: attachment; filename="archived_bookings_'.date('Ymd_His').'.csv"');
      $out = fopen('php://output', 'w');
      fputcsv($out, ['Order ID','Guest','Phone','Room','Price','Amount','Status','Date']);
      while($r = mysqli_fetch_assoc($res)){
        fputcsv($out, [$r['order_id'],$r['user_name'],$r['phonenum'],$r['room_name'],$r['price'],$r['trans_amt'],$r['trans_status'],$r['datentime']]);
      }
      fclose($out);
      exit;
    }

    if($_GET['export']=='pdf'){
      require_once('../inc/mpdf/vendor/autoload.php');
      $rows = '';
      while($r = mysqli_fetch_assoc($res)){
        $rows .= "<tr><td>{$r['order_id']}</td><td>{$r['user_name']}</td><td>{$r['phonenum']}</td><td>{$r['room_name']}</td><td>₱{$r['price']}</td><td>₱{$r['trans_amt']}</td><td>{$r['trans_status']}</td><td>{$r['datentime']}</td></tr>";
      }
      $html = "<h3>Archived Bookings</h3><table border='1' cellpadding='6' cellspacing='0' width='100%'><thead><tr><th>Order ID</th><th>Guest</th><th>Phone</th><th>Room</th><th>Price</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead><tbody>".$rows."</tbody></table>";
      $mpdf = new \Mpdf\Mpdf();
      $mpdf->WriteHTML($html);
      $mpdf->Output('archived_bookings_'.date('Ymd_His').'.pdf','D');
      exit;
    }
  }

  send_json('error', 'Invalid request');
