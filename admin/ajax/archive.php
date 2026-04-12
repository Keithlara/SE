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
    'success' => ($status === 'success'),
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
      } elseif ($type === 'query') {
        $_POST['restore_query_archive'] = 1;
      } elseif ($type === 'ticket') {
        $_POST['restore_support_ticket_archive'] = 1;
      } elseif ($type === 'transaction') {
        $_POST['restore_transaction_archive'] = 1;
      } elseif ($type === 'notification') {
        $_POST['restore_notification_archive'] = 1;
      } elseif ($type === 'review') {
        $_POST['restore_review_archive'] = 1;
      }
      break;

    case 'archive':
      $type = $_POST['type'] ?? '';
      if ($type === 'ticket') {
        $_POST['archive_support_ticket'] = 1;
      } elseif ($type === 'transaction') {
        $_POST['archive_transaction'] = 1;
      } elseif ($type === 'notification') {
        $_POST['archive_notification'] = 1;
      } elseif ($type === 'review') {
        $_POST['archive_review'] = 1;
      }
      break;

    case 'archive_booking':
      $_POST['archive_record'] = 1;
      break;

    case 'delete':
      // Handled later by explicit type checks
      break;
  }
}

// Ensure archive tables exist (idempotent)
function ensure_archive_tables()
{
  if (!function_exists('ensureAppSchema') || !ensureAppSchema()) {
    send_json('error', 'Failed to initialize archive tables');
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

  function build_search_where(array $columns, string $search, array &$params, string &$types): string
  {
    $search = trim($search);
    if ($search === '' || empty($columns)) {
      return '';
    }

    $parts = [];
    foreach ($columns as $column) {
      $parts[] = "{$column} LIKE ?";
      $params[] = '%' . $search . '%';
      $types .= 's';
    }

    return ' WHERE (' . implode(' OR ', $parts) . ')';
  }

  function render_archive_pagination(int $page, int $limit, int $total_rows): string
  {
    if ($total_rows <= $limit) {
      return '';
    }

    $pagination = '';
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

    return $pagination;
  }

  function archive_snapshot_support_ticket(int $ticketId): void
  {
    global $con;

    archiveHelperExec($con, "DELETE FROM `archived_support_ticket_messages` WHERE `ticket_id` = {$ticketId}", 'Failed to clear archived support ticket messages');
    archiveHelperExec($con, "DELETE FROM `archived_support_tickets` WHERE `id` = {$ticketId}", 'Failed to clear archived support ticket');

    archiveHelperExec(
      $con,
      "INSERT INTO `archived_support_tickets`
        (`id`,`ticket_code`,`user_id`,`booking_id`,`order_id`,`subject`,`category`,`priority`,`status`,`assigned_to`,`escalated`,`last_reply_at`,`last_reply_by`,`created_at`,`updated_at`,`archived_at`)
       SELECT
        `id`,`ticket_code`,`user_id`,`booking_id`,`order_id`,`subject`,`category`,`priority`,`status`,`assigned_to`,`escalated`,`last_reply_at`,`last_reply_by`,`created_at`,`updated_at`, NOW()
       FROM `support_tickets`
       WHERE `id` = {$ticketId}
       LIMIT 1",
      'Failed to archive support ticket'
    );

    archiveHelperExec(
      $con,
      "INSERT INTO `archived_support_ticket_messages`
        (`id`,`ticket_id`,`user_id`,`sender_type`,`sender_id`,`sender_name`,`message`,`attachment_path`,`is_internal`,`seen_by_user`,`seen_by_staff`,`created_at`,`archived_at`)
       SELECT
        stm.`id`, stm.`ticket_id`, st.`user_id`, stm.`sender_type`, stm.`sender_id`, stm.`sender_name`, stm.`message`, stm.`attachment_path`, stm.`is_internal`, stm.`seen_by_user`, stm.`seen_by_staff`, stm.`created_at`, NOW()
       FROM `support_ticket_messages` stm
       INNER JOIN `support_tickets` st ON st.`id` = stm.`ticket_id`
       WHERE stm.`ticket_id` = {$ticketId}",
      'Failed to archive support ticket messages'
    );
  }

  function archive_snapshot_transaction(int $transactionId): void
  {
    global $con;

    archiveHelperExec($con, "DELETE FROM `archived_transactions` WHERE `id` = {$transactionId}", 'Failed to clear archived transaction');
    archiveHelperExec(
      $con,
      "INSERT INTO `archived_transactions`
        (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`,`archived_at`)
       SELECT
        `id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`, NOW()
       FROM `transactions`
       WHERE `id` = {$transactionId}
       LIMIT 1",
      'Failed to archive transaction'
    );
  }

  function archive_snapshot_notification(int $notificationId): void
  {
    global $con;

    archiveHelperExec($con, "DELETE FROM `archived_notifications` WHERE `id` = {$notificationId}", 'Failed to clear archived notification');
    archiveHelperExec(
      $con,
      "INSERT INTO `archived_notifications`
        (`id`,`user_id`,`booking_id`,`message`,`type`,`is_read`,`created_at`,`archived_at`)
       SELECT
        `id`,`user_id`,`booking_id`,`message`,`type`,`is_read`,`created_at`, NOW()
       FROM `notifications`
       WHERE `id` = {$notificationId}
       LIMIT 1",
      'Failed to archive notification'
    );
  }

  function archive_snapshot_review(int $reviewId): void
  {
    global $con;

    archiveHelperExec($con, "DELETE FROM `archived_reviews` WHERE `id` = {$reviewId}", 'Failed to clear archived review');
    archiveHelperExec(
      $con,
      "INSERT INTO `archived_reviews`
        (`id`,`booking_id`,`room_id`,`user_id`,`rating`,`review`,`seen`,`datentime`,`archived_at`)
       SELECT
        `sr_no`,`booking_id`,`room_id`,`user_id`,`rating`,`review`,`seen`,`datentime`, NOW()
       FROM `rating_review`
       WHERE `sr_no` = {$reviewId}
       LIMIT 1",
      'Failed to archive review'
    );
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
                     ON bo.booking_id = bd.booking_id
                     WHERE bo.`archive_source` = 'records'";

      if ($where !== '') {
        $base_query .= " AND " . preg_replace('/^\s*WHERE\s*/i', '', $where);
      }

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

      $booking_select = "SELECT
          bo.*,
          bd.*,
          (SELECT COUNT(*) FROM `archived_booking_extras` abe WHERE abe.`booking_id` = bo.`booking_id`) AS extras_count,
          (SELECT COUNT(*) FROM `archived_booking_history` abh WHERE abh.`booking_id` = bo.`booking_id`) AS history_count,
          (SELECT COUNT(*) FROM `archived_booking_transactions` abt WHERE abt.`booking_id` = bo.`booking_id`) AS transaction_count,
          (SELECT COUNT(*) FROM `archived_booking_notifications` abn WHERE abn.`booking_id` = bo.`booking_id`) AS notification_count,
          (SELECT COUNT(*) FROM `archived_booking_support_tickets` abst WHERE abst.`booking_id` = bo.`booking_id`) AS support_ticket_count,
          (SELECT COUNT(*) FROM `archived_booking_support_messages` absm WHERE absm.`booking_id` = bo.`booking_id`) AS support_message_count,
          (SELECT COUNT(*) FROM `archived_booking_guest_notes` abgn WHERE abgn.`booking_id` = bo.`booking_id`) AS guest_note_count";

      if ($types === '') {
        $limit_query = mysqli_query($con, $booking_select . ' ' . $base_query . " ORDER BY bo.booking_id DESC LIMIT $start,$limit");
      } else {
        $limit_query = select($booking_select . ' ' . $base_query . " ORDER BY bo.booking_id DESC LIMIT $start,$limit", $params, $types);
      }

      if (!$limit_query) {
        error_log('Archive list query failed (bookings): ' . mysqli_error($con));
        send_json('error', 'Failed to load archives');
      }

      $i          = $start + 1;
      $table_data = '';

      while ($data = mysqli_fetch_assoc($limit_query)) {
        $date       = date("d-m-Y", strtotime($data['datentime']));
        $archivedOn = !empty($data['archived_at']) ? date("d-m-Y H:i", strtotime($data['archived_at'])) : 'N/A';
        $booking_id = (int)$data['booking_id'];
        $snapshotBits = [
          ((int)($data['transaction_count'] ?? 0)) . ' financial',
          ((int)($data['extras_count'] ?? 0)) . ' extras',
          ((int)($data['history_count'] ?? 0)) . ' history',
          ((int)($data['notification_count'] ?? 0)) . ' notices',
          ((int)($data['support_ticket_count'] ?? 0)) . ' tickets',
          ((int)($data['support_message_count'] ?? 0)) . ' replies',
          ((int)($data['guest_note_count'] ?? 0)) . ' notes',
        ];

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
              <b>Date:</b> {$date}<br>
              <b>Archived:</b> {$archivedOn}<br>
              <small class='text-muted'><b>Snapshot:</b> " . htmlspecialchars(implode(' | ', $snapshotBits)) . "</small>
            </td>
            <td>
              <button type='button' onclick='restore({$booking_id}, \"booking\")' class='btn btn-success btn-sm fw-bold shadow-none me-1' title='Restore booking'>
                <i class='bi bi-arrow-counterclockwise'></i>
              </button>
              <button type='button' onclick='permanentDelete({$booking_id}, \"booking\")' class='btn btn-danger btn-sm fw-bold shadow-none' title='Delete permanently'>
                <i class='bi bi-trash'></i>
              </button>
            </td>
          </tr>
        ";
        $i++;
      }

      $pagination = render_archive_pagination($page, $limit, $total_rows);

      send_json('success', '', [
        'html'       => $table_data,
        'pagination' => $pagination,
      ]);
    }

    // Rooms archive listing
    if ($type === 'rooms') {
      $params = [];
      $types  = '';
      $where  = build_search_where(['`name`'], $frm_data['search'] ?? '', $params, $types);

      $count_res = ($types === '')
        ? mysqli_query($con, "SELECT COUNT(*) as total FROM `archived_rooms`" . $where)
        : select("SELECT COUNT(*) as total FROM `archived_rooms`" . $where, $params, $types);
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

      $room_select = "SELECT
          ar.*,
          (SELECT COUNT(*) FROM `archived_room_images` ari WHERE ari.`room_id` = ar.`id`) AS image_count,
          (SELECT COUNT(*) FROM `archived_room_features` arf WHERE arf.`room_id` = ar.`id`) AS feature_count,
          (SELECT COUNT(*) FROM `archived_room_facilities` arf2 WHERE arf2.`room_id` = ar.`id`) AS facility_count,
          (SELECT COUNT(*) FROM `archived_ratings_reviews` arr WHERE arr.`room_id` = ar.`id`) AS review_count,
          (SELECT COUNT(*) FROM `archived_room_block_dates` arbd WHERE arbd.`room_id` = ar.`id`) AS block_count
        FROM `archived_rooms` ar";

      $limit_query = ($types === '')
        ? mysqli_query($con, $room_select . $where . " ORDER BY `archived_at` DESC LIMIT {$start},{$limit}")
        : select($room_select . $where . " ORDER BY `archived_at` DESC LIMIT {$start},{$limit}", $params, $types);

      if (!$limit_query) {
        error_log('Archive list query failed (rooms): ' . mysqli_error($con));
        send_json('error', 'Failed to load room archives');
      }

      $i          = $start + 1;
      $table_data = '';

      while ($row = mysqli_fetch_assoc($limit_query)) {
        $archived_on = date('d-m-Y H:i', strtotime($row['archived_at']));
        $snapshot = (int)($row['image_count'] ?? 0) . " images | "
          . (int)($row['feature_count'] ?? 0) . " features | "
          . (int)($row['facility_count'] ?? 0) . " facilities | "
          . (int)($row['review_count'] ?? 0) . " reviews | "
          . (int)($row['block_count'] ?? 0) . " blocks";

        $table_data .= "
          <tr>
            <td>{$i}</td>
            <td><b>" . htmlspecialchars($row['name'] ?? $row['room_name'] ?? '') . "</b><br><small class='text-muted'>" . htmlspecialchars($snapshot) . "</small></td>
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

      $pagination = render_archive_pagination($page, $limit, $total_rows);

      send_json('success', '', [
        'html'       => $table_data,
        'pagination' => $pagination,
      ]);
    }

    // Users archive listing
    if ($type === 'users') {
      $params = [];
      $types  = '';
      $where  = build_search_where(['`name`', '`email`', '`username`', '`phonenum`'], $frm_data['search'] ?? '', $params, $types);

      $count_res = ($types === '')
        ? mysqli_query($con, "SELECT COUNT(*) as total FROM `archived_user_cred`" . $where)
        : select("SELECT COUNT(*) as total FROM `archived_user_cred`" . $where, $params, $types);
      if (!$count_res) {
        error_log('Archive count query failed (users): ' . mysqli_error($con));
        send_json('error', 'Failed to load user archives');
      }

      $total_rows = (int)mysqli_fetch_assoc($count_res)['total'];

      if ($total_rows === 0) {
        send_json('success', '', [
          'html'       => "<tr><td colspan='6' class='text-center text-muted'>No archived users found</td></tr>",
          'pagination' => '',
        ]);
      }

      $user_metrics = [
        'notification_count' => [
          'table' => 'archived_user_notifications',
          'alias' => 'aun',
        ],
        'note_count' => [
          'table' => 'archived_user_guest_notes',
          'alias' => 'augn',
        ],
        'support_ticket_count' => [
          'table' => 'archived_user_support_tickets',
          'alias' => 'aust',
        ],
        'support_message_count' => [
          'table' => 'archived_user_support_messages',
          'alias' => 'ausm',
        ],
        'review_count' => [
          'table' => 'archived_user_reviews',
          'alias' => 'aur',
        ],
      ];

      $user_metric_selects = [];
      foreach ($user_metrics as $metric_name => $metric_config) {
        $table_name = $metric_config['table'];
        $table_alias = $metric_config['alias'];

        if (function_exists('archiveHelperTableReadable') && archiveHelperTableReadable($con, $table_name)) {
          $user_metric_selects[] = "(SELECT COUNT(*) FROM `{$table_name}` {$table_alias} WHERE {$table_alias}.`user_id` = auc.`id`) AS {$metric_name}";
        } else {
          $user_metric_selects[] = "0 AS {$metric_name}";
        }
      }

      $user_select = "SELECT
          auc.*,
          " . implode(",\n          ", $user_metric_selects) . "
        FROM `archived_user_cred` auc";

      $limit_query = ($types === '')
        ? mysqli_query($con, $user_select . $where . " ORDER BY `archived_at` DESC LIMIT {$start},{$limit}")
        : select($user_select . $where . " ORDER BY `archived_at` DESC LIMIT {$start},{$limit}", $params, $types);

      if (!$limit_query) {
        error_log('Archive list query failed (users): ' . mysqli_error($con));
        send_json('error', 'Failed to load user archives');
      }

      $i          = $start + 1;
      $table_data = '';

      while ($row = mysqli_fetch_assoc($limit_query)) {
        $archived_on = date('d-m-Y H:i', strtotime($row['archived_at']));
        $username = trim((string)($row['username'] ?? ''));
        $verified = !empty($row['is_verified'])
          ? "<span class='badge bg-success'>Verified</span>"
          : "<span class='badge bg-warning text-dark'>Unverified</span>";
        $snapshot = (int)($row['notification_count'] ?? 0) . " notices | "
          . (int)($row['note_count'] ?? 0) . " notes | "
          . (int)($row['support_ticket_count'] ?? 0) . " tickets | "
          . (int)($row['support_message_count'] ?? 0) . " replies | "
          . (int)($row['review_count'] ?? 0) . " reviews";

        $table_data .= "
          <tr>
            <td>{$i}</td>
            <td>
              <b>" . htmlspecialchars($row['name']) . "</b><br>
              " . ($username !== '' ? "<small class='text-muted'>@" . htmlspecialchars($username) . "</small>" : "<small class='text-muted'>No username</small>") . "<br>
              <small class='text-muted'>" . htmlspecialchars($snapshot) . "</small>
            </td>
            <td>" . htmlspecialchars($row['email']) . "<br>{$verified}</td>
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

      $pagination = render_archive_pagination($page, $limit, $total_rows);

      send_json('success', '', [
        'html'       => $table_data,
        'pagination' => $pagination,
      ]);
    }

    // Simple listing for archived user queries
    if ($type === 'queries') {
      $params = [];
      $types  = '';
      $where  = build_search_where(['`name`', '`email`', '`subject`', '`message`'], $frm_data['search'] ?? '', $params, $types);
      $base_query = " FROM `user_queries` WHERE `is_archived` = 1";
      if ($where !== '') {
        $base_query .= ' AND ' . preg_replace('/^\s*WHERE\s*/i', '', $where);
      }

      $count_res = ($types === '')
        ? mysqli_query($con, "SELECT COUNT(*) as total" . $base_query)
        : select("SELECT COUNT(*) as total" . $base_query, $params, $types);
      if (!$count_res) {
        error_log('Archive count query failed (queries): ' . mysqli_error($con));
        send_json('error', 'Failed to load query archives');
      }

      $total_rows = (int)mysqli_fetch_assoc($count_res)['total'];

      if ($total_rows === 0) {
        send_json('success', '', [
          'html'       => "<tr><td colspan='7' class='text-center text-muted'>No archived queries found</td></tr>",
          'pagination' => '',
        ]);
      }

      $limit_query = ($types === '')
        ? mysqli_query($con, "SELECT *" . $base_query . " ORDER BY COALESCE(`archived_at`,`datentime`) DESC, `sr_no` DESC LIMIT {$start},{$limit}")
        : select("SELECT *" . $base_query . " ORDER BY COALESCE(`archived_at`,`datentime`) DESC, `sr_no` DESC LIMIT {$start},{$limit}", $params, $types);

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
        $seenBadge = !empty($row['seen'])
          ? "<span class='badge bg-success'>Read</span>"
          : "<span class='badge bg-warning text-dark'>Unread</span>";
        $queryId = (int)$row['sr_no'];

        $table_data .= "
          <tr>
            <td>{$i}</td>
            <td>" . htmlspecialchars($row['name']) . "</td>
            <td>" . htmlspecialchars($row['email']) . "</td>
            <td>" . htmlspecialchars($row['subject']) . "</td>
            <td>" . nl2br(htmlspecialchars($row['message'])) . "</td>
            <td>{$archived_on}<br>{$seenBadge}</td>
            <td>
              <button type='button' onclick='restore({$queryId}, \"query\")' class='btn btn-success btn-sm fw-bold shadow-none me-1' title='Restore query'>
                <i class='bi bi-arrow-counterclockwise'></i>
              </button>
              <button type='button' onclick='permanentDelete({$queryId}, \"query\")' class='btn btn-danger btn-sm fw-bold shadow-none' title='Delete permanently'>
                <i class='bi bi-trash'></i>
              </button>
            </td>
          </tr>
        ";
        $i++;
      }

      $pagination = render_archive_pagination($page, $limit, $total_rows);

      send_json('success', '', [
        'html'       => $table_data,
        'pagination' => $pagination,
      ]);
    }

    if ($type === 'tickets') {
      $params = [];
      $types = '';
      $where = build_search_where(['`ticket_code`', '`subject`', '`category`', '`status`'], $frm_data['search'] ?? '', $params, $types);

      $count_res = ($types === '')
        ? mysqli_query($con, "SELECT COUNT(*) as total FROM `archived_support_tickets`" . $where)
        : select("SELECT COUNT(*) as total FROM `archived_support_tickets`" . $where, $params, $types);
      if (!$count_res) {
        send_json('error', 'Failed to load support ticket archives');
      }

      $total_rows = (int)mysqli_fetch_assoc($count_res)['total'];
      if ($total_rows === 0) {
        send_json('success', '', [
          'html' => "<tr><td colspan='7' class='text-center text-muted'>No archived support tickets found</td></tr>",
          'pagination' => '',
        ]);
      }

      $query = "SELECT ast.*,
          (SELECT COUNT(*) FROM `archived_support_ticket_messages` astm WHERE astm.`ticket_id` = ast.`id`) AS message_count
        FROM `archived_support_tickets` ast";
      $limit_query = ($types === '')
        ? mysqli_query($con, $query . $where . " ORDER BY `archived_at` DESC LIMIT {$start},{$limit}")
        : select($query . $where . " ORDER BY `archived_at` DESC LIMIT {$start},{$limit}", $params, $types);

      $i = $start + 1;
      $table_data = '';
      while ($row = mysqli_fetch_assoc($limit_query)) {
        $table_data .= "
          <tr>
            <td>{$i}</td>
            <td><b>" . htmlspecialchars($row['ticket_code']) . "</b><br><small class='text-muted'>" . htmlspecialchars($row['subject']) . "</small></td>
            <td>" . htmlspecialchars((string)($row['category'] ?? 'general')) . "</td>
            <td>" . htmlspecialchars((string)($row['status'] ?? 'open')) . "</td>
            <td>" . (int)($row['message_count'] ?? 0) . " replies</td>
            <td>" . date('d-m-Y H:i', strtotime($row['archived_at'])) . "</td>
            <td>
              <button type='button' onclick='restore(" . (int)$row['id'] . ", \"ticket\")' class='btn btn-success btn-sm fw-bold shadow-none me-1' title='Restore ticket'><i class='bi bi-arrow-counterclockwise'></i></button>
              <button type='button' onclick='permanentDelete(" . (int)$row['id'] . ", \"ticket\")' class='btn btn-danger btn-sm fw-bold shadow-none' title='Delete permanently'><i class='bi bi-trash'></i></button>
            </td>
          </tr>";
        $i++;
      }

      send_json('success', '', [
        'html' => $table_data,
        'pagination' => render_archive_pagination($page, $limit, $total_rows),
      ]);
    }

    if ($type === 'transactions') {
      $params = [];
      $types = '';
      $where = build_search_where(['`guest_name`', '`room_no`', '`method`', '`status`', '`type`'], $frm_data['search'] ?? '', $params, $types);

      $count_res = ($types === '')
        ? mysqli_query($con, "SELECT COUNT(*) as total FROM `archived_transactions`" . $where)
        : select("SELECT COUNT(*) as total FROM `archived_transactions`" . $where, $params, $types);
      if (!$count_res) {
        send_json('error', 'Failed to load transaction archives');
      }
      $total_rows = (int)mysqli_fetch_assoc($count_res)['total'];
      if ($total_rows === 0) {
        send_json('success', '', [
          'html' => "<tr><td colspan='7' class='text-center text-muted'>No archived transactions found</td></tr>",
          'pagination' => '',
        ]);
      }

      $limit_query = ($types === '')
        ? mysqli_query($con, "SELECT * FROM `archived_transactions`" . $where . " ORDER BY `archived_at` DESC LIMIT {$start},{$limit}")
        : select("SELECT * FROM `archived_transactions`" . $where . " ORDER BY `archived_at` DESC LIMIT {$start},{$limit}", $params, $types);

      $i = $start + 1;
      $table_data = '';
      while ($row = mysqli_fetch_assoc($limit_query)) {
        $table_data .= "
          <tr>
            <td>{$i}</td>
            <td><b>" . htmlspecialchars($row['guest_name']) . "</b><br><small class='text-muted'>Room: " . htmlspecialchars((string)$row['room_no']) . "</small></td>
            <td>PHP " . number_format((float)$row['amount'], 2) . "</td>
            <td>" . htmlspecialchars($row['method']) . " / " . htmlspecialchars($row['type']) . "</td>
            <td>" . htmlspecialchars($row['status']) . "</td>
            <td>" . date('d-m-Y H:i', strtotime($row['archived_at'])) . "</td>
            <td>
              <button type='button' onclick='restore(" . (int)$row['id'] . ", \"transaction\")' class='btn btn-success btn-sm fw-bold shadow-none me-1' title='Restore transaction'><i class='bi bi-arrow-counterclockwise'></i></button>
              <button type='button' onclick='permanentDelete(" . (int)$row['id'] . ", \"transaction\")' class='btn btn-danger btn-sm fw-bold shadow-none' title='Delete permanently'><i class='bi bi-trash'></i></button>
            </td>
          </tr>";
        $i++;
      }

      send_json('success', '', [
        'html' => $table_data,
        'pagination' => render_archive_pagination($page, $limit, $total_rows),
      ]);
    }

    if ($type === 'notifications') {
      $params = [];
      $types = '';
      $where = build_search_where(['`message`', '`type`'], $frm_data['search'] ?? '', $params, $types);

      $count_res = ($types === '')
        ? mysqli_query($con, "SELECT COUNT(*) as total FROM `archived_notifications`" . $where)
        : select("SELECT COUNT(*) as total FROM `archived_notifications`" . $where, $params, $types);
      if (!$count_res) {
        send_json('error', 'Failed to load notification archives');
      }
      $total_rows = (int)mysqli_fetch_assoc($count_res)['total'];
      if ($total_rows === 0) {
        send_json('success', '', [
          'html' => "<tr><td colspan='7' class='text-center text-muted'>No archived notifications found</td></tr>",
          'pagination' => '',
        ]);
      }

      $limit_query = ($types === '')
        ? mysqli_query($con, "SELECT * FROM `archived_notifications`" . $where . " ORDER BY `archived_at` DESC LIMIT {$start},{$limit}")
        : select("SELECT * FROM `archived_notifications`" . $where . " ORDER BY `archived_at` DESC LIMIT {$start},{$limit}", $params, $types);

      $i = $start + 1;
      $table_data = '';
      while ($row = mysqli_fetch_assoc($limit_query)) {
        $table_data .= "
          <tr>
            <td>{$i}</td>
            <td>" . htmlspecialchars((string)$row['user_id']) . "</td>
            <td>" . htmlspecialchars((string)$row['booking_id']) . "</td>
            <td>" . nl2br(htmlspecialchars($row['message'])) . "</td>
            <td>" . htmlspecialchars($row['type']) . "</td>
            <td>" . date('d-m-Y H:i', strtotime($row['archived_at'])) . "</td>
            <td>
              <button type='button' onclick='restore(" . (int)$row['id'] . ", \"notification\")' class='btn btn-success btn-sm fw-bold shadow-none me-1' title='Restore notification'><i class='bi bi-arrow-counterclockwise'></i></button>
              <button type='button' onclick='permanentDelete(" . (int)$row['id'] . ", \"notification\")' class='btn btn-danger btn-sm fw-bold shadow-none' title='Delete permanently'><i class='bi bi-trash'></i></button>
            </td>
          </tr>";
        $i++;
      }

      send_json('success', '', [
        'html' => $table_data,
        'pagination' => render_archive_pagination($page, $limit, $total_rows),
      ]);
    }

    if ($type === 'reviews') {
      $params = [];
      $types = '';
      $where = build_search_where(['`review`'], $frm_data['search'] ?? '', $params, $types);

      $count_res = ($types === '')
        ? mysqli_query($con, "SELECT COUNT(*) as total FROM `archived_reviews`" . $where)
        : select("SELECT COUNT(*) as total FROM `archived_reviews`" . $where, $params, $types);
      if (!$count_res) {
        send_json('error', 'Failed to load review archives');
      }
      $total_rows = (int)mysqli_fetch_assoc($count_res)['total'];
      if ($total_rows === 0) {
        send_json('success', '', [
          'html' => "<tr><td colspan='7' class='text-center text-muted'>No archived reviews found</td></tr>",
          'pagination' => '',
        ]);
      }

      $limit_query = ($types === '')
        ? mysqli_query($con, "SELECT * FROM `archived_reviews`" . $where . " ORDER BY `archived_at` DESC LIMIT {$start},{$limit}")
        : select("SELECT * FROM `archived_reviews`" . $where . " ORDER BY `archived_at` DESC LIMIT {$start},{$limit}", $params, $types);

      $i = $start + 1;
      $table_data = '';
      while ($row = mysqli_fetch_assoc($limit_query)) {
        $table_data .= "
          <tr>
            <td>{$i}</td>
            <td>" . htmlspecialchars((string)$row['room_id']) . "</td>
            <td>" . htmlspecialchars((string)$row['user_id']) . "</td>
            <td>" . htmlspecialchars((string)$row['rating']) . " / 5</td>
            <td>" . nl2br(htmlspecialchars($row['review'])) . "</td>
            <td>" . date('d-m-Y H:i', strtotime($row['archived_at'])) . "</td>
            <td>
              <button type='button' onclick='restore(" . (int)$row['id'] . ", \"review\")' class='btn btn-success btn-sm fw-bold shadow-none me-1' title='Restore review'><i class='bi bi-arrow-counterclockwise'></i></button>
              <button type='button' onclick='permanentDelete(" . (int)$row['id'] . ", \"review\")' class='btn btn-danger btn-sm fw-bold shadow-none' title='Delete permanently'><i class='bi bi-trash'></i></button>
            </td>
          </tr>";
        $i++;
      }

      send_json('success', '', [
        'html' => $table_data,
        'pagination' => render_archive_pagination($page, $limit, $total_rows),
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
        archiveDeleteBookingChildren($id);
        delete("DELETE FROM `archived_booking_details` WHERE `booking_id`=?", [$id], 'i');
        delete("DELETE FROM `archived_booking_order`   WHERE `booking_id`=? AND `archive_source`='records'", [$id], 'i');

        logAction('archive_delete_booking', "Permanently deleted archived booking_id={$id}");
        send_json('success', 'Archived booking deleted permanently');
      }

      if ($type === 'room') {
        // Delete room-related archive data
        archiveDeleteRoomRelations($id);
        delete("DELETE FROM `archived_rooms`           WHERE `id`=?",      [$id], 'i');

        logAction('archive_delete_room', "Permanently deleted archived room id={$id}");
        send_json('success', 'Archived room deleted permanently');
      }

      if ($type === 'user') {
        archiveDeleteUserChildren($id);
        delete("DELETE FROM `archived_user_cred` WHERE `id`=?", [$id], 'i');

        $liveDeleteStmt = mysqli_prepare($con, "DELETE FROM `user_cred` WHERE `id`=? AND `is_archived`=1");
        if (!$liveDeleteStmt) {
          throw new Exception('Failed to prepare live user deletion');
        }

        mysqli_stmt_bind_param($liveDeleteStmt, 'i', $id);
        $liveDeleteOk = mysqli_stmt_execute($liveDeleteStmt);
        $liveDeleteErrno = mysqli_stmt_errno($liveDeleteStmt);
        mysqli_stmt_close($liveDeleteStmt);

        if (!$liveDeleteOk) {
          if ((int)$liveDeleteErrno !== 1451) {
            throw new Exception('Failed to delete live archived user');
          }

          $deletedStamp = 'deleted_user_' . $id . '_' . time();
          $deletedEmail = $deletedStamp . '@deleted.local';
          $deletedPhone = 'deleted-' . $id . '-' . time();

          $anonymized = update(
            "UPDATE `user_cred`
             SET `email`=?, `username`=?, `phonenum`=?, `token`=NULL, `verification_code`=NULL, `t_expire`=NULL, `status`=0, `is_archived`=1
             WHERE `id`=? AND `is_archived`=1",
            [$deletedEmail, $deletedStamp, $deletedPhone, $id],
            'sssi'
          );

          if ($anonymized < 0) {
            throw new Exception('Failed to anonymize live archived user');
          }

          logAction('archive_anonymize_user', "Anonymized archived live user id={$id} after permanent archive delete due to linked records");
        }

        logAction('archive_delete_user', "Permanently deleted archived user id={$id}");
        send_json('success', 'Archived user deleted permanently');
      }

      if ($type === 'query') {
        delete("DELETE FROM `user_queries` WHERE `sr_no`=? AND `is_archived`=1", [$id], 'i');

        logAction('archive_delete_query', "Permanently deleted archived query sr_no={$id}");
        send_json('success', 'Archived query deleted permanently');
      }

      if ($type === 'ticket') {
        delete("DELETE FROM `archived_support_ticket_messages` WHERE `ticket_id`=?", [$id], 'i');
        delete("DELETE FROM `archived_support_tickets` WHERE `id`=?", [$id], 'i');
        delete("DELETE FROM `support_ticket_messages` WHERE `ticket_id`=?", [$id], 'i');
        delete("DELETE FROM `support_tickets` WHERE `id`=? AND `is_archived`=1", [$id], 'i');

        logAction('archive_delete_ticket', "Permanently deleted archived ticket id={$id}");
        send_json('success', 'Archived ticket deleted permanently');
      }

      if ($type === 'transaction') {
        delete("DELETE FROM `archived_transactions` WHERE `id`=?", [$id], 'i');
        delete("DELETE FROM `transactions` WHERE `id`=? AND `is_archived`=1", [$id], 'i');

        logAction('archive_delete_transaction', "Permanently deleted archived transaction id={$id}");
        send_json('success', 'Archived transaction deleted permanently');
      }

      if ($type === 'notification') {
        delete("DELETE FROM `archived_notifications` WHERE `id`=?", [$id], 'i');
        delete("DELETE FROM `notifications` WHERE `id`=? AND `is_archived`=1", [$id], 'i');

        logAction('archive_delete_notification', "Permanently deleted archived notification id={$id}");
        send_json('success', 'Archived notification deleted permanently');
      }

      if ($type === 'review') {
        delete("DELETE FROM `archived_reviews` WHERE `id`=?", [$id], 'i');
        delete("DELETE FROM `rating_review` WHERE `sr_no`=? AND `is_archived`=1", [$id], 'i');

        logAction('archive_delete_review', "Permanently deleted archived review id={$id}");
        send_json('success', 'Archived review deleted permanently');
      }

      send_json('error', 'Unknown delete type');
    } catch (Throwable $e) {
      error_log('Archive permanent delete failed: ' . $e->getMessage());
      send_json('error', 'Failed to delete archived record');
    }
  }

  if (isset($_POST['archive_support_ticket']) || isset($_POST['archive_transaction']) || isset($_POST['archive_notification']) || isset($_POST['archive_review'])) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
      send_json('error', 'Invalid record id');
    }

    try {
      mysqli_begin_transaction($con);

      if (isset($_POST['archive_support_ticket'])) {
        $res = select("SELECT `id`,`is_archived` FROM `support_tickets` WHERE `id`=? LIMIT 1", [$id], 'i');
        $row = $res ? mysqli_fetch_assoc($res) : null;
        if (!$row) {
          mysqli_rollback($con);
          send_json('error', 'Support ticket not found');
        }
        if ((int)($row['is_archived'] ?? 0) === 1) {
          mysqli_rollback($con);
          send_json('error', 'Support ticket is already archived');
        }
        archive_snapshot_support_ticket($id);
        update("UPDATE `support_tickets` SET `is_archived`=1, `archived_at`=NOW() WHERE `id`=?", [$id], 'i');
        mysqli_commit($con);
        send_json('success', 'Support ticket archived successfully');
      }

      if (isset($_POST['archive_transaction'])) {
        $res = select("SELECT `id`,`is_archived` FROM `transactions` WHERE `id`=? LIMIT 1", [$id], 'i');
        $row = $res ? mysqli_fetch_assoc($res) : null;
        if (!$row) {
          mysqli_rollback($con);
          send_json('error', 'Transaction not found');
        }
        if ((int)($row['is_archived'] ?? 0) === 1) {
          mysqli_rollback($con);
          send_json('error', 'Transaction is already archived');
        }
        archive_snapshot_transaction($id);
        update("UPDATE `transactions` SET `is_archived`=1, `archived_at`=NOW() WHERE `id`=?", [$id], 'i');
        mysqli_commit($con);
        send_json('success', 'Transaction archived successfully');
      }

      if (isset($_POST['archive_notification'])) {
        $res = select("SELECT `id`,`is_archived` FROM `notifications` WHERE `id`=? LIMIT 1", [$id], 'i');
        $row = $res ? mysqli_fetch_assoc($res) : null;
        if (!$row) {
          mysqli_rollback($con);
          send_json('error', 'Notification not found');
        }
        if ((int)($row['is_archived'] ?? 0) === 1) {
          mysqli_rollback($con);
          send_json('error', 'Notification is already archived');
        }
        archive_snapshot_notification($id);
        update("UPDATE `notifications` SET `is_archived`=1, `archived_at`=NOW() WHERE `id`=?", [$id], 'i');
        mysqli_commit($con);
        send_json('success', 'Notification archived successfully');
      }

      if (isset($_POST['archive_review'])) {
        $res = select("SELECT `sr_no`,`is_archived` FROM `rating_review` WHERE `sr_no`=? LIMIT 1", [$id], 'i');
        $row = $res ? mysqli_fetch_assoc($res) : null;
        if (!$row) {
          mysqli_rollback($con);
          send_json('error', 'Review not found');
        }
        if ((int)($row['is_archived'] ?? 0) === 1) {
          mysqli_rollback($con);
          send_json('error', 'Review is already archived');
        }
        archive_snapshot_review($id);
        update("UPDATE `rating_review` SET `is_archived`=1, `archived_at`=NOW() WHERE `sr_no`=?", [$id], 'i');
        mysqli_commit($con);
        send_json('success', 'Review archived successfully');
      }
    } catch (Throwable $e) {
      mysqli_rollback($con);
      error_log('Archive action failed: ' . $e->getMessage());
      send_json('error', 'Archive failed: ' . $e->getMessage());
    }
  }

  if (isset($_POST['archive_record']))
  {
    $frm = filteration($_POST);
    $booking_id = (int)$frm['booking_id'];

    if ($booking_id <= 0) {
      send_json('error', 'Invalid booking id');
    }

    try {
      mysqli_begin_transaction($con);

      $bo_res = select("SELECT `booking_id`,`is_archived` FROM `booking_order` WHERE `booking_id`=? LIMIT 1", [$booking_id], 'i');
      $bd_res = select("SELECT `booking_id` FROM `booking_details` WHERE `booking_id`=? LIMIT 1", [$booking_id], 'i');

      if (!$bo_res || !$bd_res || mysqli_num_rows($bo_res) === 0 || mysqli_num_rows($bd_res) === 0) {
        mysqli_rollback($con);
        send_json('error', 'Record not found');
      }

      $bo = mysqli_fetch_assoc($bo_res);
      if ((int)($bo['is_archived'] ?? 0) === 1) {
        mysqli_rollback($con);
        send_json('error', 'Booking record is already archived');
      }

      delete("DELETE FROM `archived_booking_details` WHERE `booking_id`=?", [$booking_id], 'i');
      archiveDeleteBookingChildren($booking_id);
      delete("DELETE FROM `archived_booking_order` WHERE `booking_id`=? AND `archive_source`='records'", [$booking_id], 'i');

      $stmt = mysqli_prepare(
        $con,
        "INSERT INTO `archived_booking_order`
          (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`,`archive_source`)
         SELECT
          `booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`,'records'
         FROM `booking_order`
         WHERE `booking_id` = ?"
      );
      mysqli_stmt_bind_param($stmt, 'i', $booking_id);
      $ins_bo = mysqli_stmt_execute($stmt);
      mysqli_stmt_close($stmt);

      $stmt = mysqli_prepare(
        $con,
        "INSERT INTO `archived_booking_details`
          (`sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`)
         SELECT
          `sr_no`,`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`
         FROM `booking_details`
         WHERE `booking_id` = ?"
      );
      mysqli_stmt_bind_param($stmt, 'i', $booking_id);
      $ins_bd = mysqli_stmt_execute($stmt);
      mysqli_stmt_close($stmt);

      if (!$ins_bo || !$ins_bd) {
        throw new Exception('Failed to archive booking snapshot');
      }

      archiveRefreshBookingChildren($booking_id);
      archiveDeleteLiveBookingChildren($booking_id);
      delete("DELETE FROM `booking_details` WHERE `booking_id`=?", [$booking_id], 'i');
      delete("DELETE FROM `booking_order` WHERE `booking_id`=?", [$booking_id], 'i');

      mysqli_commit($con);
      send_json('success', 'Booking archived successfully');
    } catch (Throwable $e) {
      mysqli_rollback($con);
      error_log('Booking record archive failed: ' . $e->getMessage());
      send_json('error', 'Archive failed: ' . $e->getMessage());
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

      // If booking already exists in live tables, allow restore only when it is soft-archived
      $stmt = mysqli_prepare($con, "SELECT `booking_id`,`is_archived` FROM `booking_order` WHERE `booking_id`=? LIMIT 1");
      if (!$stmt) {
        throw new Exception('Failed to prepare booking check');
      }
      mysqli_stmt_bind_param($stmt, 'i', $booking_id);
      mysqli_stmt_execute($stmt);
      $exists = mysqli_stmt_get_result($stmt);
      mysqli_stmt_close($stmt);
      $liveOrder = ($exists && mysqli_num_rows($exists) > 0) ? mysqli_fetch_assoc($exists) : null;
      if ($liveOrder && (int)($liveOrder['is_archived'] ?? 0) === 0) {
        mysqli_rollback($con);
        send_json('error', 'Booking is already active (cannot restore)');
      }

      // Load archived rows
      $stmt = mysqli_prepare($con, "SELECT * FROM `archived_booking_order` WHERE `booking_id`=? AND `archive_source`='records' LIMIT 1");
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

      if ($liveOrder) {
        $q1 = "UPDATE `booking_order`
          SET `user_id`=?,`room_id`=?,`check_in`=?,`check_out`=?,`arrival`=?,`refund`=?,`booking_status`=?,`order_id`=?,`trans_id`=?,`trans_amt`=?,`trans_status`=?,`trans_resp_msg`=?,`rate_review`=?,`datentime`=?,`payment_status`=?,`payment_proof`=?,`refund_proof`=?,`refund_amount`=?,`amount_paid`=?,`confirmed_at`=?,`total_amt`=?,`downpayment`=?,`balance_due`=?,`promo_code`=?,`discount_amount`=?,`is_archived`=0
          WHERE `booking_id`=?";
        $stmt = mysqli_prepare($con, $q1);
        if (!$stmt) {
          throw new Exception('Failed to prepare booking_order update');
        }
        mysqli_stmt_bind_param(
          $stmt,
          'iissiisssississssddsdddsdi',
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
          $bo['datentime'],
          $bo['payment_status'],
          $bo['payment_proof'],
          $bo['refund_proof'],
          $bo['refund_amount'],
          $bo['amount_paid'],
          $bo['confirmed_at'],
          $bo['total_amt'],
          $bo['downpayment'],
          $bo['balance_due'],
          $bo['promo_code'],
          $bo['discount_amount'],
          $bo['booking_id']
        );
      } else {
        $q1 = "INSERT INTO `booking_order`
          (`booking_id`,`user_id`,`room_id`,`check_in`,`check_out`,`arrival`,`refund`,`booking_status`,`order_id`,`trans_id`,`trans_amt`,`trans_status`,`trans_resp_msg`,`rate_review`,`datentime`,`payment_status`,`payment_proof`,`refund_proof`,`refund_amount`,`amount_paid`,`confirmed_at`,`total_amt`,`downpayment`,`balance_due`,`promo_code`,`discount_amount`)
          VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = mysqli_prepare($con, $q1);
        if (!$stmt) {
          throw new Exception('Failed to prepare booking_order insert');
        }
        mysqli_stmt_bind_param(
          $stmt,
          'iiissiisssississssddsdddsd',
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
          $bo['datentime'],
          $bo['payment_status'],
          $bo['payment_proof'],
          $bo['refund_proof'],
          $bo['refund_amount'],
          $bo['amount_paid'],
          $bo['confirmed_at'],
          $bo['total_amt'],
          $bo['downpayment'],
          $bo['balance_due'],
          $bo['promo_code'],
          $bo['discount_amount']
        );
      }
      if (!mysqli_stmt_execute($stmt)) {
        $err = mysqli_error($con);
        mysqli_stmt_close($stmt);
        throw new Exception('Failed to restore booking_order: ' . $err);
      }
      mysqli_stmt_close($stmt);

      // Restore booking_details
      $detailCheck = mysqli_prepare($con, "SELECT `booking_id` FROM `booking_details` WHERE `booking_id`=? LIMIT 1");
      mysqli_stmt_bind_param($detailCheck, 'i', $booking_id);
      mysqli_stmt_execute($detailCheck);
      $detailResult = mysqli_stmt_get_result($detailCheck);
      mysqli_stmt_close($detailCheck);
      $liveDetails = ($detailResult && mysqli_num_rows($detailResult) > 0);

      $bd_note = $bd['booking_note'] ?? null;
      $bd_staff_note = $bd['staff_note'] ?? null;
      if ($liveDetails) {
        $q2 = "UPDATE `booking_details`
          SET `room_name`=?,`price`=?,`total_pay`=?,`room_no`=?,`user_name`=?,`phonenum`=?,`address`=?,`booking_note`=?,`staff_note`=?,`extras_total`=?,`downpayment`=?,`remaining_balance`=?
          WHERE `booking_id`=?";
        $stmt = mysqli_prepare($con, $q2);
        if (!$stmt) {
          throw new Exception('Failed to prepare booking_details update');
        }
        mysqli_stmt_bind_param(
          $stmt,
          'siissssssdddi',
          $bd['room_name'],
          $bd['price'],
          $bd['total_pay'],
          $bd['room_no'],
          $bd['user_name'],
          $bd['phonenum'],
          $bd['address'],
          $bd_note,
          $bd_staff_note,
          $bd['extras_total'],
          $bd['downpayment'],
          $bd['remaining_balance'],
          $bd['booking_id']
        );
      } else {
        $q2 = "INSERT INTO `booking_details`
          (`booking_id`,`room_name`,`price`,`total_pay`,`room_no`,`user_name`,`phonenum`,`address`,`booking_note`,`staff_note`,`extras_total`,`downpayment`,`remaining_balance`)
          VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = mysqli_prepare($con, $q2);
        if (!$stmt) {
          throw new Exception('Failed to prepare booking_details insert');
        }
        mysqli_stmt_bind_param(
          $stmt,
          'isiissssssddd',
          $bd['booking_id'],
          $bd['room_name'],
          $bd['price'],
          $bd['total_pay'],
          $bd['room_no'],
          $bd['user_name'],
          $bd['phonenum'],
          $bd['address'],
          $bd_note,
          $bd_staff_note,
          $bd['extras_total'],
          $bd['downpayment'],
          $bd['remaining_balance']
        );
      }
      if (!mysqli_stmt_execute($stmt)) {
        $err = mysqli_error($con);
        mysqli_stmt_close($stmt);
        throw new Exception('Failed to restore booking_details: ' . $err);
      }
      mysqli_stmt_close($stmt);

      archiveRestoreBookingChildren($booking_id);
      archiveDeleteBookingChildren($booking_id);

      // Remove from archives
      $stmt = mysqli_prepare($con, "DELETE FROM `archived_booking_details` WHERE `booking_id`=?");
      mysqli_stmt_bind_param($stmt, 'i', $booking_id);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_close($stmt);

      $stmt = mysqli_prepare($con, "DELETE FROM `archived_booking_order` WHERE `booking_id`=? AND `archive_source`='records'");
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
        $stmt = mysqli_prepare(
          $con,
          "UPDATE `rooms`
           SET `name`=?, `area`=?, `price`=?, `quantity`=?, `adult`=?, `children`=?, `description`=?, `status`=?, `removed`=0, `is_archived`=0, `archived_at`=NULL
           WHERE `id`=?"
        );
        mysqli_stmt_bind_param(
          $stmt,
          'siiiiisii',
          $ar['name'],
          $ar['area'],
          $ar['price'],
          $ar['quantity'],
          $ar['adult'],
          $ar['children'],
          $ar['description'],
          $ar['status'],
          $ar['room_id']
        );
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
            $ins = mysqli_prepare($con, "INSERT INTO `room_images` (`room_id`,`image`,`thumb`) VALUES (?,?,?)");
            $thumb = (int)($img['thumb'] ?? 0);
            mysqli_stmt_bind_param($ins, 'isi', $room_id, $img['image'], $thumb);
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

      archiveRestoreRoomRelations($id, $room_id);

      // Remove from archive tables
      archiveDeleteRoomRelations($id);
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
      $chk = mysqli_prepare($con, "SELECT `id`,`is_archived` FROM `user_cred` WHERE `id`=? LIMIT 1");
      mysqli_stmt_bind_param($chk, 'i', $au['id']);
      mysqli_stmt_execute($chk);
      $chk_res = mysqli_stmt_get_result($chk);
      mysqli_stmt_close($chk);
      $liveUser = ($chk_res && mysqli_num_rows($chk_res) > 0) ? mysqli_fetch_assoc($chk_res) : null;

      if ($liveUser && (int)($liveUser['is_archived'] ?? 0) === 0) {
        mysqli_rollback($con);
        send_json('error', 'User already exists in live table (cannot restore)');
      }

      if ($liveUser) {
        $stmt = mysqli_prepare($con,
          "UPDATE `user_cred`
           SET `name`=?,`email`=?,`username`=?,`address`=?,`phonenum`=?,`pincode`=?,`dob`=?,`password`=?,`is_verified`=?,`verification_code`=?,`token`=?,`t_expire`=?,`datentime`=?,`status`=?,`profile`=?,`is_archived`=0
           WHERE `id`=?");
        if (!$stmt) throw new Exception('Failed to prepare user restore update');
        mysqli_stmt_bind_param($stmt, 'sssssississssisi',
          $au['name'], $au['email'], $au['username'], $au['address'], $au['phonenum'],
          $au['pincode'], $au['dob'], $au['password'], $au['is_verified'], $au['verification_code'],
          $au['token'], $au['t_expire'], $au['datentime'], $au['status'], $au['profile'], $au['id']
        );
      } else {
        $stmt = mysqli_prepare($con,
          "INSERT INTO `user_cred` (`id`,`name`,`email`,`username`,`address`,`phonenum`,`pincode`,`dob`,`password`,`is_verified`,`verification_code`,`token`,`t_expire`,`datentime`,`status`,`profile`,`is_archived`)
           VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,0)");
        if (!$stmt) throw new Exception('Failed to prepare user insert');
        mysqli_stmt_bind_param($stmt, 'isssssississsssis',
          $au['id'], $au['name'], $au['email'], $au['username'], $au['address'], $au['phonenum'],
          $au['pincode'], $au['dob'], $au['password'], $au['is_verified'], $au['verification_code'],
          $au['token'], $au['t_expire'], $au['datentime'], $au['status'], $au['profile']
        );
      }
      if (!mysqli_stmt_execute($stmt)) throw new Exception('Failed to restore user: ' . mysqli_error($con));
      mysqli_stmt_close($stmt);

      archiveRestoreUserChildren($id);
      archiveDeleteUserChildren($id);

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

  if (isset($_POST['restore_query_archive'])) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($id <= 0) {
      send_json('error', 'Invalid query id');
    }

    try {
      $restored = update(
        "UPDATE `user_queries` SET `is_archived`=0, `archived_at`=NULL WHERE `sr_no`=? AND `is_archived`=1",
        [$id],
        'i'
      );

      if ($restored < 1) {
        send_json('error', 'Archived query not found');
      }

      logAction('archive_restore_query', "Restored archived query sr_no={$id}");
      send_json('success', 'Query restored successfully');
    } catch (Throwable $e) {
      error_log('Query restore failed: ' . $e->getMessage());
      send_json('error', 'Restore failed: ' . $e->getMessage());
    }
  }

  if (isset($_POST['restore_support_ticket_archive'])) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
      send_json('error', 'Invalid support ticket id');
    }

    try {
      mysqli_begin_transaction($con);
      $res = select("SELECT * FROM `archived_support_tickets` WHERE `id`=? LIMIT 1", [$id], 'i');
      $ticket = $res ? mysqli_fetch_assoc($res) : null;
      if (!$ticket) {
        mysqli_rollback($con);
        send_json('error', 'Archived support ticket not found');
      }

      $live = select("SELECT `id`,`is_archived` FROM `support_tickets` WHERE `id`=? LIMIT 1", [$id], 'i');
      $liveRow = $live ? mysqli_fetch_assoc($live) : null;
      if ($liveRow && (int)($liveRow['is_archived'] ?? 0) === 0) {
        mysqli_rollback($con);
        send_json('error', 'Support ticket is already active');
      }

      if ($liveRow) {
        update(
          "UPDATE `support_tickets`
           SET `ticket_code`=?,`user_id`=?,`booking_id`=?,`order_id`=?,`subject`=?,`category`=?,`priority`=?,`status`=?,`assigned_to`=?,`escalated`=?,`last_reply_at`=?,`last_reply_by`=?,`created_at`=?,`updated_at`=?,`is_archived`=0,`archived_at`=NULL
           WHERE `id`=?",
          [
            $ticket['ticket_code'], $ticket['user_id'], $ticket['booking_id'], $ticket['order_id'], $ticket['subject'], $ticket['category'],
            $ticket['priority'], $ticket['status'], $ticket['assigned_to'], $ticket['escalated'], $ticket['last_reply_at'], $ticket['last_reply_by'],
            $ticket['created_at'], $ticket['updated_at'], $id
          ],
          'siisssssiissssi'
        );
      } else {
        insert(
          "INSERT INTO `support_tickets`
            (`id`,`ticket_code`,`user_id`,`booking_id`,`order_id`,`subject`,`category`,`priority`,`status`,`assigned_to`,`escalated`,`last_reply_at`,`last_reply_by`,`created_at`,`updated_at`,`is_archived`)
           VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,0)",
          [
            $id, $ticket['ticket_code'], $ticket['user_id'], $ticket['booking_id'], $ticket['order_id'], $ticket['subject'], $ticket['category'],
            $ticket['priority'], $ticket['status'], $ticket['assigned_to'], $ticket['escalated'], $ticket['last_reply_at'], $ticket['last_reply_by'],
            $ticket['created_at'], $ticket['updated_at']
          ],
          'isiisssssiissss'
        );
      }

      archiveHelperExec(
        $con,
        "REPLACE INTO `support_ticket_messages`
          (`id`,`ticket_id`,`sender_type`,`sender_id`,`sender_name`,`message`,`attachment_path`,`is_internal`,`seen_by_user`,`seen_by_staff`,`created_at`)
         SELECT
          `id`,`ticket_id`,`sender_type`,`sender_id`,`sender_name`,`message`,`attachment_path`,`is_internal`,`seen_by_user`,`seen_by_staff`,`created_at`
         FROM `archived_support_ticket_messages`
         WHERE `ticket_id` = {$id}",
        'Failed to restore support ticket messages'
      );

      delete("DELETE FROM `archived_support_ticket_messages` WHERE `ticket_id`=?", [$id], 'i');
      delete("DELETE FROM `archived_support_tickets` WHERE `id`=?", [$id], 'i');
      mysqli_commit($con);
      send_json('success', 'Support ticket restored successfully');
    } catch (Throwable $e) {
      mysqli_rollback($con);
      send_json('error', 'Restore failed: ' . $e->getMessage());
    }
  }

  if (isset($_POST['restore_transaction_archive'])) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
      send_json('error', 'Invalid transaction id');
    }
    try {
      mysqli_begin_transaction($con);
      $res = select("SELECT * FROM `archived_transactions` WHERE `id`=? LIMIT 1", [$id], 'i');
      $row = $res ? mysqli_fetch_assoc($res) : null;
      if (!$row) {
        mysqli_rollback($con);
        send_json('error', 'Archived transaction not found');
      }

      $live = select("SELECT `id`,`is_archived` FROM `transactions` WHERE `id`=? LIMIT 1", [$id], 'i');
      $liveRow = $live ? mysqli_fetch_assoc($live) : null;
      if ($liveRow && (int)($liveRow['is_archived'] ?? 0) === 0) {
        mysqli_rollback($con);
        send_json('error', 'Transaction is already active');
      }

      if ($liveRow) {
        update(
          "UPDATE `transactions`
           SET `booking_id`=?,`guest_name`=?,`room_no`=?,`amount`=?,`method`=?,`status`=?,`type`=?,`admin_id`=?,`datentime`=?,`is_archived`=0,`archived_at`=NULL
           WHERE `id`=?",
          [$row['booking_id'], $row['guest_name'], $row['room_no'], $row['amount'], $row['method'], $row['status'], $row['type'], $row['admin_id'], $row['datentime'], $id],
          'ississsisi'
        );
      } else {
        insert(
          "INSERT INTO `transactions` (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`,`is_archived`)
           VALUES (?,?,?,?,?,?,?,?,?,?,0)",
          [$id, $row['booking_id'], $row['guest_name'], $row['room_no'], $row['amount'], $row['method'], $row['status'], $row['type'], $row['admin_id'], $row['datentime']],
          'iississsis'
        );
      }

      delete("DELETE FROM `archived_transactions` WHERE `id`=?", [$id], 'i');
      mysqli_commit($con);
      send_json('success', 'Transaction restored successfully');
    } catch (Throwable $e) {
      mysqli_rollback($con);
      send_json('error', 'Restore failed: ' . $e->getMessage());
    }
  }

  if (isset($_POST['restore_notification_archive'])) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
      send_json('error', 'Invalid notification id');
    }
    try {
      mysqli_begin_transaction($con);
      $res = select("SELECT * FROM `archived_notifications` WHERE `id`=? LIMIT 1", [$id], 'i');
      $row = $res ? mysqli_fetch_assoc($res) : null;
      if (!$row) {
        mysqli_rollback($con);
        send_json('error', 'Archived notification not found');
      }

      $live = select("SELECT `id`,`is_archived` FROM `notifications` WHERE `id`=? LIMIT 1", [$id], 'i');
      $liveRow = $live ? mysqli_fetch_assoc($live) : null;
      if ($liveRow && (int)($liveRow['is_archived'] ?? 0) === 0) {
        mysqli_rollback($con);
        send_json('error', 'Notification is already active');
      }

      if ($liveRow) {
        update(
          "UPDATE `notifications`
           SET `user_id`=?,`booking_id`=?,`message`=?,`type`=?,`is_read`=?,`created_at`=?,`is_archived`=0,`archived_at`=NULL
           WHERE `id`=?",
          [$row['user_id'], $row['booking_id'], $row['message'], $row['type'], $row['is_read'], $row['created_at'], $id],
          'iissisi'
        );
      } else {
        insert(
          "INSERT INTO `notifications` (`id`,`user_id`,`booking_id`,`message`,`is_read`,`created_at`,`type`,`is_archived`)
           VALUES (?,?,?,?,?,?,?,0)",
          [$id, $row['user_id'], $row['booking_id'], $row['message'], $row['is_read'], $row['created_at'], $row['type']],
          'iiisiss'
        );
      }

      delete("DELETE FROM `archived_notifications` WHERE `id`=?", [$id], 'i');
      mysqli_commit($con);
      send_json('success', 'Notification restored successfully');
    } catch (Throwable $e) {
      mysqli_rollback($con);
      send_json('error', 'Restore failed: ' . $e->getMessage());
    }
  }

  if (isset($_POST['restore_review_archive'])) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
      send_json('error', 'Invalid review id');
    }
    try {
      mysqli_begin_transaction($con);
      $res = select("SELECT * FROM `archived_reviews` WHERE `id`=? LIMIT 1", [$id], 'i');
      $row = $res ? mysqli_fetch_assoc($res) : null;
      if (!$row) {
        mysqli_rollback($con);
        send_json('error', 'Archived review not found');
      }

      $live = select("SELECT `sr_no`,`is_archived` FROM `rating_review` WHERE `sr_no`=? LIMIT 1", [$id], 'i');
      $liveRow = $live ? mysqli_fetch_assoc($live) : null;
      if ($liveRow && (int)($liveRow['is_archived'] ?? 0) === 0) {
        mysqli_rollback($con);
        send_json('error', 'Review is already active');
      }

      if ($liveRow) {
        update(
          "UPDATE `rating_review`
           SET `booking_id`=?,`room_id`=?,`user_id`=?,`rating`=?,`review`=?,`seen`=?,`datentime`=?,`is_archived`=0,`archived_at`=NULL
           WHERE `sr_no`=?",
          [$row['booking_id'], $row['room_id'], $row['user_id'], $row['rating'], $row['review'], $row['seen'], $row['datentime'], $id],
          'iiiisisi'
        );
      } else {
        insert(
          "INSERT INTO `rating_review` (`sr_no`,`booking_id`,`room_id`,`user_id`,`rating`,`review`,`seen`,`datentime`,`is_archived`)
           VALUES (?,?,?,?,?,?,?,?,0)",
          [$id, $row['booking_id'], $row['room_id'], $row['user_id'], $row['rating'], $row['review'], $row['seen'], $row['datentime']],
          'iiiiisis'
        );
      }

      delete("DELETE FROM `archived_reviews` WHERE `id`=?", [$id], 'i');
      mysqli_commit($con);
      send_json('success', 'Review restored successfully');
    } catch (Throwable $e) {
      mysqli_rollback($con);
      send_json('error', 'Restore failed: ' . $e->getMessage());
    }
  }

  if(isset($_GET['export']) && ($_GET['export']=='csv' || $_GET['export']=='pdf'))
  {
    $frm_data = filteration($_GET);
    $params = [];
    $types = '';
    $where = build_filters_where($frm_data,$params,$types);
    $base = "SELECT bo.*, bd.* FROM archived_booking_order bo INNER JOIN archived_booking_details bd ON bo.booking_id = bd.booking_id WHERE bo.archive_source = 'records'";
    if ($where !== '') {
      $base .= " AND " . preg_replace('/^\s*WHERE\s*/i', '', $where);
    }
    $res = select($base." ORDER BY bo.booking_id DESC", $params, $types);

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
