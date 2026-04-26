<?php

require('../inc/db_config.php');
require('../inc/essentials.php');
date_default_timezone_set("Asia/Manila");
adminLogin();

function get_extras_html($con, $booking_id)
{
  $res = mysqli_query($con, "SELECT * FROM `booking_extras` WHERE `booking_id`=" . (int)$booking_id);
  if (!$res || mysqli_num_rows($res) === 0) {
    return '';
  }

  $html = "<div class='record-extras'><div class='record-extras-title'>Extras</div><ul>";
  while ($ex = mysqli_fetch_assoc($res)) {
    $html .= "<li>" . htmlspecialchars($ex['name']) . " x" . (int)$ex['quantity'] . " &mdash; &#8369;" . number_format((float)$ex['unit_price'], 2) . "/night</li>";
  }
  $html .= "</ul></div>";
  return $html;
}

if (isset($_POST['get_bookings'])) {
  $frm_data = filteration($_POST);

  $limit = 5;
  $page = isset($frm_data['page']) ? (int)$frm_data['page'] : 1;
  if ($page < 1) {
    $page = 1;
  }
  $start = ($page - 1) * $limit;

  $search = $frm_data['search'] ?? '';
  $status = $frm_data['status'] ?? 'all';
  $month = isset($frm_data['month']) ? (int)$frm_data['month'] : 0;
  $year = isset($frm_data['year']) ? (int)$frm_data['year'] : 0;

  $statusCondition = "((bo.booking_status='booked' AND (bo.arrival=1 OR (bo.booking_source='walk_in' AND bo.payment_status='paid')))
    OR (bo.booking_status='cancelled' AND bo.refund=1)
    OR (bo.booking_status='payment failed'))";

  if ($status === 'booked') {
    $statusCondition = "(bo.booking_status='booked' AND (bo.arrival=1 OR (bo.booking_source='walk_in' AND bo.payment_status='paid')))";
  } elseif ($status === 'cancelled') {
    $statusCondition = "(bo.booking_status='cancelled' AND bo.refund=1)";
  } elseif ($status === 'payment_failed') {
    $statusCondition = "(bo.booking_status='payment failed')";
  }

  $query = "SELECT bo.*, bd.*, COALESCE(bhr.`has_rescheduled`, 0) AS `has_rescheduled` FROM `booking_order` bo
    INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
    LEFT JOIN (
      SELECT `booking_id`, 1 AS `has_rescheduled`
      FROM `booking_history`
      WHERE `event_type`='reschedule'
      GROUP BY `booking_id`
    ) bhr ON bhr.booking_id = bo.booking_id
    WHERE $statusCondition
    AND bo.is_archived = 0
    AND (bo.order_id LIKE ? OR bd.phonenum LIKE ? OR bd.user_name LIKE ?)";

  $values = ["%$search%", "%$search%", "%$search%"];
  $datatypes = 'sss';

  if ($month >= 1 && $month <= 12 && $year >= 2000) {
    $date_start = sprintf('%04d-%02d-01', $year, $month);
    $date_end = date('Y-m-d', strtotime($date_start . ' +1 month'));
    $query .= " AND bo.datentime >= ? AND bo.datentime < ?";
    $values[] = $date_start;
    $values[] = $date_end;
    $datatypes .= 'ss';
  }

  $query .= " ORDER BY bo.booking_id DESC";

  $res = select($query, $values, $datatypes);
  $limit_query = $query . " LIMIT $start,$limit";
  $limit_res = select($limit_query, $values, $datatypes);
  $total_rows = mysqli_num_rows($res);

  if ($total_rows == 0) {
    echo json_encode([
      'table_data' => "
        <tr>
          <td colspan='6'>
            <div class='records-empty'>
              <i class='bi bi-journal-x'></i>
              <div class='fw-semibold text-dark mb-1'>No booking records found</div>
              <div>Try changing the filters or search term.</div>
            </div>
          </td>
        </tr>",
      'pagination' => '',
      'summary' => 'No records match the current filters.'
    ]);
    exit;
  }

  $i = $start + 1;
  $table_data = '';

  while ($data = mysqli_fetch_assoc($limit_res)) {
    $date = date('d-m-Y', strtotime($data['datentime']));
    $checkin_label = date('M d, Y', strtotime($data['check_in']));
    $checkout_label = date('M d, Y', strtotime($data['check_out']));

    if ($data['booking_status'] == 'booked') {
      $status_class = 'booked';
      $status_label = 'Booked';
    } elseif ($data['booking_status'] == 'cancelled') {
      $status_class = 'cancelled';
      $status_label = 'Cancelled';
    } else {
      $status_class = 'payment-failed';
      $status_label = 'Payment Failed';
    }

    $proofFile = $data['payment_proof'] ?? '';
    $proofUrl = '';
    if ($proofFile) {
      if (filter_var($proofFile, FILTER_VALIDATE_URL)) {
        $proofUrl = $proofFile;
      } elseif (strpos($proofFile, 'uploads/') === 0) {
        $proofUrl = SITE_URL . ltrim($proofFile, '/');
      } elseif (strpos($proofFile, '/') === 0) {
        $proofUrl = SITE_URL . ltrim($proofFile, '/');
      } else {
        $proofUrl = SITE_URL . 'uploads/billing_proofs/' . $proofFile;
      }
    }

    if ($proofFile && $proofUrl) {
      $safeProofUrl = htmlspecialchars($proofUrl, ENT_QUOTES);
      $proofHtml = "
        <div class='record-proof-stack'>
          <span class='record-proof-chip'><i class='bi bi-patch-check-fill'></i>Proof on file</span>
          <a href='{$safeProofUrl}' target='_blank' class='btn btn-outline-primary btn-sm shadow-none record-proof-btn'>
            <i class='bi bi-receipt-cutoff me-1'></i>View Proof
          </a>
        </div>";
    } else {
      $proofHtml = "<div class='record-proof-stack'><span class='record-proof-chip muted'><i class='bi bi-info-circle'></i>No billing proof</span></div>";
    }

    $extrasHtml = get_extras_html($con, $data['booking_id']);
    $order_id = htmlspecialchars($data['order_id']);
    $user_name = htmlspecialchars($data['user_name']);
    $phone = htmlspecialchars($data['phonenum']);
    $room_name = htmlspecialchars($data['room_name']);
    $price = number_format((float)$data['price'], 2);
    $trans_amt = number_format((float)$data['trans_amt'], 2);
    $booking_id = (int)$data['booking_id'];
    $sourceBadge = (($data['booking_source'] ?? 'online') === 'walk_in')
      ? "<span class='record-proof-chip' style='background:#e0f2fe;color:#075985;'><i class='bi bi-person-walking'></i>Walk-In</span>"
      : '';

    $rescheduleBadge = ((int)($data['has_rescheduled'] ?? 0) === 1)
      ? "<span class='record-proof-chip' style='background:#ede9fe;color:#5b21b6;'><i class='bi bi-arrow-repeat'></i>Rescheduled</span>"
      : '';

    $table_data .= "
      <tr>
        <td class='record-index'>{$i}</td>
        <td>
          <span class='record-order-pill'>
            <i class='bi bi-hash'></i>Order ID: {$order_id}
          </span>
          {$sourceBadge}
          <p class='record-line'><span class='label'>Guest</span>{$user_name}</p>
          <p class='record-line'><span class='label'>Phone</span>{$phone}</p>
        </td>
        <td>
          <div class='record-room-title'>{$room_name}</div>
          <div class='record-meta-stack'>
            <p class='record-line'><span class='label'>Rate</span>&#8369;{$price} per night</p>
            <p class='record-line'><span class='label'>Check-in</span>{$checkin_label}</p>
            <p class='record-line'><span class='label'>Check-out</span>{$checkout_label}</p>
          </div>
          {$extrasHtml}
        </td>
        <td>
          <div class='record-amount'>&#8369;{$trans_amt}</div>
          <div class='record-date-box'>
            <div><strong>Recorded:</strong> {$date}</div>
            <div><strong>Reference:</strong> {$order_id}</div>
          </div>
          {$proofHtml}
        </td>
        <td>
          <span class='record-status {$status_class}'>{$status_label}</span>
          {$rescheduleBadge}
        </td>
        <td>
          <div class='d-flex gap-2 flex-wrap'>
            <button type='button' onclick='download({$booking_id})' class='btn btn-outline-success shadow-none record-action-btn' title='Download PDF'>
              <i class='bi bi-file-earmark-arrow-down-fill'></i>
            </button>
            <button type='button' data-booking-id='{$booking_id}' data-id='{$booking_id}' class='btn btn-outline-warning shadow-none record-action-btn js-archive-booking-record archive-btn' title='Archive booking'>
              <i class='bi bi-archive-fill'></i>
            </button>
          </div>
        </td>
      </tr>";

    $i++;
  }

  $pagination = '';

  if ($total_rows > $limit) {
    $total_pages = ceil($total_rows / $limit);

    if ($page != 1) {
      $pagination .= "<li class='page-item'>
        <button onclick='change_page(1)' class='page-link shadow-none'>First</button>
      </li>";
    }

    $disabled = ($page == 1) ? 'disabled' : '';
    $prev = $page - 1;
    $pagination .= "<li class='page-item $disabled'>
      <button onclick='change_page($prev)' class='page-link shadow-none'>Prev</button>
    </li>";

    $disabled = ($page == $total_pages) ? 'disabled' : '';
    $next = $page + 1;
    $pagination .= "<li class='page-item $disabled'>
      <button onclick='change_page($next)' class='page-link shadow-none'>Next</button>
    </li>";

    if ($page != $total_pages) {
      $pagination .= "<li class='page-item'>
        <button onclick='change_page($total_pages)' class='page-link shadow-none'>Last</button>
      </li>";
    }
  }

  $summary = $total_rows . ' booking record' . ($total_rows === 1 ? '' : 's') . ' found';
  if ($month >= 1 && $month <= 12 && $year >= 2000) {
    $summary .= ' for ' . date('F Y', strtotime(sprintf('%04d-%02d-01', $year, $month)));
  }

  echo json_encode([
    'table_data' => $table_data,
    'pagination' => $pagination,
    'summary' => $summary
  ]);
}

?>
