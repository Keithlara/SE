<?php 

  require('../inc/db_config.php');
  require('../inc/essentials.php');
  adminLogin();

  // Backward compatible: allow staff/admin reply note on booking
  $col = mysqli_query($con, "SHOW COLUMNS FROM `booking_details` LIKE 'staff_note'");
  if(!$col || mysqli_num_rows($col)==0){
    mysqli_query($con, "ALTER TABLE `booking_details` ADD `staff_note` TEXT NULL");
  }

  function get_extras_html($con, $booking_id) {
    $res = mysqli_query($con, "SELECT * FROM `booking_extras` WHERE `booking_id`=".(int)$booking_id);
    if(!$res || mysqli_num_rows($res) === 0) return '';
    $html = "<div class='mt-2'><b>Add-on Extras:</b><ul class='mb-0 ps-3 small text-muted'>";
    while($ex = mysqli_fetch_assoc($res)){
      $html .= "<li>".htmlspecialchars($ex['name'])." x".$ex['quantity']." &mdash; &#8369;".number_format($ex['unit_price'],2)."/night</li>";
    }
    $html .= "</ul></div>";
    return $html;
  }

  if(isset($_POST['get_bookings']))
  {
    $frm_data = filteration($_POST);
    $search = $frm_data['search'] ?? '';
    $type = isset($frm_data['type']) ? strtolower($frm_data['type']) : 'pending';
    $type = ($type === 'confirmed') ? 'confirmed' : 'pending';

    $statusFilter = ($type === 'confirmed') ? 'booked' : 'pending';

    $query = "SELECT bo.booking_id, bo.order_id, bo.room_id, bo.booking_status, bo.arrival, bo.trans_amt, bo.datentime, bo.check_in, bo.check_out,
                     bo.payment_status, bo.payment_proof, bo.total_amt, bo.downpayment, bo.balance_due,
                     bd.user_name, bd.phonenum, bd.room_name, bd.price, bd.room_no, bd.booking_note, bd.staff_note
              FROM `booking_order` bo
              INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
              WHERE (bo.order_id LIKE ? OR bd.phonenum LIKE ? OR bd.user_name LIKE ?)
                AND bo.booking_status = ?
                AND bo.is_archived = 0";

    $values = ["%$search%","%$search%","%$search%",$statusFilter];
    $datatypes = "ssss";

    if($type === 'confirmed'){
      $query .= " AND bo.arrival = ?";
      $values[] = 0;
      $datatypes .= "i";
    }

    $query .= " ORDER BY bo.booking_id ASC";

    $res = select($query,$values,$datatypes);
    
    $i=1;
    $table_data = "";

    if(mysqli_num_rows($res)==0){
      echo"<tr><td colspan='".($type === 'confirmed' ? 6 : 5)."' class='text-center py-4 text-muted'>No Data Found!</td></tr>";
      exit;
    }

    while($data = mysqli_fetch_assoc($res))
    {
      $date = date("d-m-Y",strtotime($data['datentime']));
      $checkin = date("d-m-Y",strtotime($data['check_in']));
      $checkout = date("d-m-Y",strtotime($data['check_out']));

      $orderId = htmlspecialchars($data['order_id']);
      $userName = htmlspecialchars($data['user_name']);
      $phone = htmlspecialchars($data['phonenum']);
      $roomName = htmlspecialchars($data['room_name']);
      $roomNo = htmlspecialchars($data['room_no'] ?? '');
      $price = htmlspecialchars($data['price']);
      $paid = htmlspecialchars($data['trans_amt'] ?? '0');
      $total_amt   = isset($data['total_amt'])   && $data['total_amt']   > 0 ? number_format((float)$data['total_amt'],   2) : number_format((float)$data['trans_amt']*2, 2);
      $downpayment = isset($data['downpayment']) && $data['downpayment'] > 0 ? number_format((float)$data['downpayment'], 2) : number_format((float)$data['trans_amt'],   2);
      $balance_due = isset($data['balance_due']) && $data['balance_due'] > 0 ? number_format((float)$data['balance_due'], 2) : number_format(max(0,(float)$data['total_amt']-(float)$data['downpayment']), 2);
      $noteRaw = $data['booking_note'] ?? '';
      $noteEsc = '';
      if($noteRaw !== null && trim((string)$noteRaw) !== ''){
        $noteEsc = nl2br(htmlspecialchars((string)$noteRaw, ENT_QUOTES, 'UTF-8'));
      }
      $noteAttr = htmlspecialchars(trim((string)$noteRaw), ENT_QUOTES, 'UTF-8');
      $staffNoteRaw = $data['staff_note'] ?? '';
      $staffNoteAttr = htmlspecialchars(trim((string)$staffNoteRaw), ENT_QUOTES, 'UTF-8');

      $proofFile = $data['payment_proof'] ?? '';
      $proofUrl = '';
      if($proofFile){
        if(filter_var($proofFile, FILTER_VALIDATE_URL)){
          $proofUrl = $proofFile;
        } elseif(strpos($proofFile, 'uploads/') === 0){
          $proofUrl = SITE_URL . ltrim($proofFile, '/');
        } elseif(strpos($proofFile, '/') === 0){
          $proofUrl = SITE_URL . ltrim($proofFile, '/');
        } else {
          $proofUrl = SITE_URL.'uploads/billing_proofs/'.$proofFile;
        }
      }

      $extrasHtml = get_extras_html($con, $data['booking_id']);

      if($type === 'pending'){
        $proofBadge = '';
        $proofButton = '';
        if(!empty($data['payment_proof'])){
          $proofBadge = "<span class='badge bg-info text-dark'>Proof received</span>";
          $safeProofUrl = htmlspecialchars($proofUrl, ENT_QUOTES);
          $proofButton = "<button type='button' class='btn btn-outline-primary btn-sm fw-bold shadow-none' onclick=\"viewProof('$safeProofUrl')\"><i class='bi bi-receipt-cutoff me-1'></i> View Proof</button>";
        } else {
          $proofBadge = "<span class='badge bg-secondary'>Awaiting proof</span>";
        }

        $table_data .="
          <tr>
            <td>$i</td>
            <td>
              <span class='badge bg-primary'>
                Order ID: $orderId
              </span>
              <br>
              <b>Name:</b> $userName
              <br>
              <b>Phone No:</b> $phone
            </td>
            <td>
              <b>Room:</b> $roomName
              <br>
              <b>Price:</b> ₱$price
            </td>
            <td>
              <b>Check-in:</b> $checkin
              <br>
              <b>Check-out:</b> $checkout
              <br>
              <b>Date:</b> $date
              <br>
              <div class='mt-1 p-1 rounded' style='background:#fffbf0;border:1px solid #f0c040;font-size:0.82rem;'>
                <div><b>Total:</b> ₱$total_amt</div>
                <div style='color:#b8860b;'><b>Downpayment (50%):</b> ₱$downpayment</div>
                <div class='text-muted'><b>Balance at check-in:</b> ₱$balance_due</div>
              </div>
              ".($noteEsc !== '' ? "<div class='mt-2'><b>Note:</b><div class='small text-muted'>$noteEsc</div></div>" : "")."
              $extrasHtml
              $proofBadge
            </td>
            <td class='align-middle'>
              <div class='action-buttons d-flex flex-column gap-2'>
                $proofButton
                <button type='button' 
                        onclick='confirm_booking($data[booking_id], this)' 
                        class='btn btn-success btn-sm fw-bold shadow-none confirm-booking-btn'
                        data-booking-id='$data[booking_id]'
                        data-guest-note='$noteAttr'
                        data-staff-note='$staffNoteAttr'>
                  <i class='bi bi-check-circle me-1'></i> Confirm Booking
                </button>
                <button type='button' 
                        onclick='cancel_booking($data[booking_id])' 
                        class='btn btn-outline-danger btn-sm fw-bold shadow-none'>
                  <i class='bi bi-x-circle me-1'></i> Cancel Booking
                </button>
              </div>
            </td>
          </tr>
        ";
      }
      else{
        $statusBadge = "<span class='badge bg-success' id='booking-$data[booking_id]-status'>Approved</span>";
        $preselect = ($data['room_no'] !== null && $data['room_no'] !== '') ? json_encode($data['room_no']) : "null";

        $table_data .="
          <tr>
            <td>$i</td>
            <td>
              <span class='badge bg-primary'>
                Order ID: $orderId
              </span>
              <br>
              <b>Name:</b> $userName
              <br>
              <b>Phone No:</b> $phone
            </td>
            <td>
              <b>Room:</b> $roomName
              <br>
              <b>Price:</b> ₱$price
              <br>
              <b>Preferred Room No:</b> ".($roomNo !== '' ? $roomNo : 'Not selected')."
            </td>
            <td>
              <b>Check-in:</b> $checkin
              <br>
              <b>Check-out:</b> $checkout
              <br>
              <b>Amount Due:</b> ₱$paid
              <br>
              <b>Date:</b> $date
              ".($noteEsc !== '' ? "<div class='mt-2'><b>Note:</b><div class='small text-muted'>$noteEsc</div></div>" : "")."
              $extrasHtml
            </td>
            <td class='align-middle'>$statusBadge</td>
            <td class='align-middle'>
              <div class='action-buttons d-flex flex-column gap-2'>
                <button type='button' 
                        onclick='assign_room($data[booking_id], $data[room_id], $preselect)' 
                        class='btn btn-primary btn-sm fw-bold shadow-none'
                        data-bs-toggle='modal' data-bs-target='#assign-room'>
                  <i class='bi bi-door-open me-1'></i> Assign Room
                </button>
                <button type='button' 
                        onclick='cancel_booking($data[booking_id])' 
                        class='btn btn-outline-danger btn-sm fw-bold shadow-none'>
                  <i class='bi bi-x-circle me-1'></i> Cancel Booking
                </button>
              </div>
            </td>
          </tr>
        ";
      }

      $i++;
    }

    echo $table_data;
  }

  if(isset($_POST['assign_room']))
  {
    $frm_data = filteration($_POST);

    $query = "UPDATE `booking_order` bo INNER JOIN `booking_details` bd
      ON bo.booking_id = bd.booking_id
      SET bo.arrival = ?, bo.rate_review = ?, bd.room_no = ? 
      WHERE bo.booking_id = ? AND bo.booking_status = 'booked'";

    $values = [1,0,$frm_data['room_no'],$frm_data['booking_id']];

    $res = update($query,$values,'iisi');

    // Some MySQL configurations return 1 when one row didn't change.
    // Treat any positive affected-rows as success.
    echo ($res && $res >= 1) ? 1 : 0;
  }

  if(isset($_POST['cancel_booking']))
  {
    $frm_data = filteration($_POST);
    
    // Start transaction
    mysqli_begin_transaction($con);
    
    try {
      // 1. Get the booking details
      $query = "SELECT * FROM `booking_order` bo 
                JOIN `booking_details` bd ON bo.booking_id = bd.booking_id 
                WHERE bo.booking_id = ?";
      $values = [$frm_data['booking_id']];
      $booking_data = select($query, $values, 'i');
      
      if(mysqli_num_rows($booking_data) > 0) {
        $booking = mysqli_fetch_assoc($booking_data);
        
        // 2. Insert into archive tables
        $archive_order_query = "INSERT INTO `archived_booking_order` 
                              SELECT * FROM `booking_order` WHERE `booking_id` = ?";
        $archive_details_query = "INSERT INTO `archived_booking_details` 
                                SELECT * FROM `booking_details` WHERE `booking_id` = ?";
        
        // Execute archive queries
        $stmt1 = mysqli_prepare($con, $archive_order_query);
        mysqli_stmt_bind_param($stmt1, 'i', $frm_data['booking_id']);
        $archive1 = mysqli_stmt_execute($stmt1);
        
        $stmt2 = mysqli_prepare($con, $archive_details_query);
        mysqli_stmt_bind_param($stmt2, 'i', $frm_data['booking_id']);
        $archive2 = mysqli_stmt_execute($stmt2);
        
        // 3. Update the original records as archived
        $update_query = "UPDATE `booking_order` SET `is_archived` = 1, `booking_status` = 'cancelled', `refund` = 0 WHERE `booking_id` = ?";
        $stmt3 = mysqli_prepare($con, $update_query);
        mysqli_stmt_bind_param($stmt3, 'i', $frm_data['booking_id']);
        $update = mysqli_stmt_execute($stmt3);
        
        if($archive1 && $archive2 && $update) {
          // Commit transaction if all queries are successful
          mysqli_commit($con);
          echo 1;
        } else {
          throw new Exception("Failed to archive booking");
        }
      } else {
        throw new Exception("Booking not found");
      }
    } catch (Exception $e) {
      // Rollback transaction on error
      mysqli_rollback($con);
      echo 0;
    }
  }

?>