<?php 

  require('../inc/db_config.php');
  require('../inc/essentials.php');
  // notifications helper lives in project-level inc/, not admin/inc/
  require('../../inc/notifications_functions.php');
  adminLogin();

  // Refund processing is admin-only. Staff may view refund requests but cannot process them.
  $is_admin_role = (($_SESSION['adminRole'] ?? 'admin') === 'admin');

  if(isset($_POST['get_bookings']))
  {
      $frm_data = filteration($_POST);

      $query = "SELECT bo.*, bd.*, uc.email 
                FROM `booking_order` bo
                INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
                INNER JOIN `user_cred` uc ON bo.user_id = uc.id
                WHERE (bo.order_id LIKE ? OR bd.phonenum LIKE ? OR bd.user_name LIKE ?) 
                AND (bo.booking_status='cancelled' AND bo.refund=0) 
                ORDER BY bo.booking_id ASC";

      $res = select($query,["%$frm_data[search]%","%$frm_data[search]%","%$frm_data[search]%"],'sss');
      
      $i=1;
      $table_data = "";

      if(mysqli_num_rows($res)==0){
          echo"<tr><td colspan='5' class='text-center py-4'><b>No refund requests found.</b></td></tr>";
          exit;
      }

      while($data = mysqli_fetch_assoc($res))
      {
          $date = date("M d, Y h:i A",strtotime($data['datentime']));
          $checkin = date("M d, Y",strtotime($data['check_in']));
          $checkout = date("M d, Y",strtotime($data['check_out']));
          $refund_amount = $data['trans_amt'] * 0.5; // 50% refund policy

          $table_data .="
          <tr>
              <td>$i</td>
              <td>
                  <span class='badge bg-primary'>
                      Booking #$data[booking_id]
                  </span>
                  <br>
                  <b>Name:</b> $data[user_name]
                  <br>
                  <b>Email:</b> $data[email]
                  <br>
                  <b>Phone:</b> $data[phonenum]
              </td>
              <td>
                  <b>Room:</b> $data[room_name]
                  <br>
                  <b>Check-in:</b> $checkin
                  <br>
                  <b>Check-out:</b> $checkout
                  <br>
                  <b>Booked on:</b> $date
              </td>
              <td class='text-end'>
                  <b>Total Amount:</b> ₱" . number_format($data['trans_amt'], 2) . "
                  <br>
                  <b class='text-success'>Refund Amount (50%):</b> 
                  <span class='fw-bold text-success'>₱" . number_format($refund_amount, 2) . "</span>
              </td>
              <td class='text-center'>";

          if($is_admin_role){
              $proof_indicator = $data['refund_proof']
                  ? "<span class='badge bg-info text-dark d-block mb-1'><i class='bi bi-image me-1'></i>Proof uploaded</span>"
                  : "";
              $table_data .= "
                  <div class='d-flex flex-column gap-1 align-items-center'>
                      $proof_indicator
                      <button type='button' onclick='refund_booking($data[booking_id], $refund_amount, this)' 
                              class='btn btn-success btn-sm fw-bold shadow-none w-100'>
                          <i class='bi bi-cash-stack me-1'></i> Process Refund
                      </button>
                      <button type='button' onclick='upload_proof_only($data[booking_id], this)' 
                              class='btn btn-outline-primary btn-sm shadow-none w-100'>
                          <i class='bi bi-upload me-1'></i> Upload Proof
                      </button>
                  </div>";
          } else {
              $table_data .= "
                  <span class='badge bg-secondary'>Awaiting admin approval</span>";
          }

          $table_data .= "
              </td>
          </tr>
          ";

          $i++;
      }

      echo $table_data;
      exit;
  }

  if(isset($_POST['refund_booking']))
  {
      if(!$is_admin_role){ echo "0"; exit; }

      $booking_id   = (int)($_POST['booking_id'] ?? 0);
      $refund_amount = (float)($_POST['refund_amount'] ?? 0);

      // Handle proof image upload
      $proof_path = null;
      if(isset($_FILES['refund_proof']) && $_FILES['refund_proof']['error'] === UPLOAD_ERR_OK){
          $allowed_types = ['image/jpeg','image/png','image/gif','image/webp','application/pdf'];
          $ftype = mime_content_type($_FILES['refund_proof']['tmp_name']);
          if(in_array($ftype, $allowed_types) && $_FILES['refund_proof']['size'] <= 5 * 1024 * 1024){
              $ext   = pathinfo($_FILES['refund_proof']['name'], PATHINFO_EXTENSION);
              $fname = 'refund_' . $booking_id . '_' . time() . '.' . $ext;
              $upload_dir = dirname(__DIR__, 2) . '/uploads/refund_proofs/';
              if(!is_dir($upload_dir)){ mkdir($upload_dir, 0777, true); }
              $dest  = $upload_dir . $fname;
              if(move_uploaded_file($_FILES['refund_proof']['tmp_name'], $dest)){
                  $proof_path = 'uploads/refund_proofs/' . $fname;
              }
          }
      }

      mysqli_begin_transaction($con);
      try {
          // 1. Update booking to refunded (with optional proof path)
          if($proof_path){
              $query  = "UPDATE `booking_order` SET `refund`=1, `refund_amount`=?, `refund_proof`=? WHERE `booking_id`=?";
              $res = update($query, [$refund_amount, $proof_path, $booking_id], 'dsi');
          } else {
              $query  = "UPDATE `booking_order` SET `refund`=1, `refund_amount`=? WHERE `booking_id`=?";
              $res = update($query, [$refund_amount, $booking_id], 'di');
          }
          if(!$res) throw new Exception("Failed to update booking");

          // 2. Get user for notification
          $user_data = select(
              "SELECT u.id FROM `booking_order` bo JOIN `user_cred` u ON bo.user_id=u.id WHERE bo.booking_id=?",
              [$booking_id], 'i'
          );
          if(mysqli_num_rows($user_data) === 0) throw new Exception("User not found");
          $user = mysqli_fetch_assoc($user_data);

          // 3. Build notification message
          $message = "Your refund of ₱" . number_format($refund_amount, 2) . " for booking #$booking_id has been processed.";
          if($proof_path){
              $message .= " Proof of refund has been uploaded — you can view it in your notifications.";
          }

          $notif_query = "INSERT INTO notifications (user_id, booking_id, message, type, is_read) VALUES (?,?,?,?,0)";
          insert($notif_query, [$user['id'], $booking_id, $message, 'refund'], 'iiss');

          mysqli_commit($con);
          echo "1";
      } catch(Exception $e){
          mysqli_rollback($con);
          error_log("Refund Error: " . $e->getMessage());
          echo "0";
      }
      exit;
  }

  // Upload proof only (without re-processing the refund)
  if(isset($_POST['upload_proof_only']))
  {
      if(!$is_admin_role){ echo "0"; exit; }

      $booking_id = (int)($_POST['booking_id'] ?? 0);
      if(!$booking_id){ echo "0"; exit; }

      $allowed_types = ['image/jpeg','image/png','image/gif','image/webp','application/pdf'];
      if(!isset($_FILES['refund_proof']) || $_FILES['refund_proof']['error'] !== UPLOAD_ERR_OK){
          echo "no_file"; exit;
      }
      $ftype = mime_content_type($_FILES['refund_proof']['tmp_name']);
      if(!in_array($ftype, $allowed_types)){
          echo "bad_type"; exit;
      }
      if($_FILES['refund_proof']['size'] > 5 * 1024 * 1024){
          echo "too_large"; exit;
      }
      $ext   = pathinfo($_FILES['refund_proof']['name'], PATHINFO_EXTENSION);
      $fname = 'refund_' . $booking_id . '_' . time() . '.' . $ext;
      $upload_dir = dirname(__DIR__, 2) . '/uploads/refund_proofs/';
      if(!is_dir($upload_dir)){ mkdir($upload_dir, 0777, true); }
      $dest  = $upload_dir . $fname;
      if(!move_uploaded_file($_FILES['refund_proof']['tmp_name'], $dest)){
          echo "0"; exit;
      }
      $proof_path = 'uploads/refund_proofs/' . $fname;
      $res = update("UPDATE `booking_order` SET `refund_proof`=? WHERE `booking_id`=?", [$proof_path, $booking_id], 'si');
      echo $res ? "1" : "0";
      exit;
  }

  // Fetch already-processed refunds (refund=1)
  if(isset($_POST['get_processed']))
  {
      $frm_data = filteration($_POST);

      $query = "SELECT bo.*, bd.*, uc.email 
                FROM `booking_order` bo
                INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
                INNER JOIN `user_cred` uc ON bo.user_id = uc.id
                WHERE (bo.order_id LIKE ? OR bd.phonenum LIKE ? OR bd.user_name LIKE ?)
                AND (bo.booking_status='cancelled' AND bo.refund=1)
                ORDER BY bo.booking_id DESC";

      $res = select($query,["%$frm_data[search]%","%$frm_data[search]%","%$frm_data[search]%"],'sss');

      if(mysqli_num_rows($res)==0){
          echo "<tr><td colspan='5' class='text-center py-4'><b>No processed refunds found.</b></td></tr>";
          exit;
      }

      $i = 1;
      $table_data = "";
      while($data = mysqli_fetch_assoc($res))
      {
          $date     = date("M d, Y h:i A", strtotime($data['datentime']));
          $checkin  = date("M d, Y", strtotime($data['check_in']));
          $checkout = date("M d, Y", strtotime($data['check_out']));
          $refund_amount = $data['refund_amount'] ?? ($data['trans_amt'] * 0.5);

          if($data['refund_proof']){
              $proof_url = SITE_URL . ltrim($data['refund_proof'], '/');
              $safe_proof_url = htmlspecialchars($proof_url, ENT_QUOTES, 'UTF-8');
              $proof_cell = "
                  <div class='d-flex flex-column gap-1 align-items-center'>
                      <button type='button' onclick=\"viewRefundProof('$safe_proof_url')\"
                              class='btn btn-info btn-sm shadow-none w-100 text-dark'>
                          <i class='bi bi-eye me-1'></i> View Proof
                      </button>";
              if($is_admin_role){
                  $proof_cell .= "
                      <button type='button' onclick='upload_proof_only($data[booking_id], this)'
                              class='btn btn-outline-primary btn-sm shadow-none w-100'>
                          <i class='bi bi-arrow-repeat me-1'></i> Replace
                      </button>";
              }
              $proof_cell .= "</div>";
          } else {
              $proof_cell = $is_admin_role
                  ? "<button type='button' onclick='upload_proof_only($data[booking_id], this)'
                             class='btn btn-outline-primary btn-sm shadow-none'>
                         <i class='bi bi-upload me-1'></i> Upload Proof
                     </button>"
                  : "<span class='text-muted small'>No proof yet</span>";
          }

          $table_data .= "
          <tr>
              <td>$i</td>
              <td>
                  <span class='badge bg-success'>Booking #$data[booking_id]</span><br>
                  <b>Name:</b> $data[user_name]<br>
                  <b>Email:</b> $data[email]<br>
                  <b>Phone:</b> $data[phonenum]
              </td>
              <td>
                  <b>Room:</b> $data[room_name]<br>
                  <b>Check-in:</b> $checkin<br>
                  <b>Check-out:</b> $checkout<br>
                  <b>Booked on:</b> $date
              </td>
              <td class='text-end'>
                  <b>Refunded:</b>
                  <span class='fw-bold text-success'>₱" . number_format($refund_amount, 2) . "</span>
              </td>
              <td class='text-center'>$proof_cell</td>
          </tr>";
          $i++;
      }
      echo $table_data;
      exit;
  }

  // Get refund amount for a booking
  if(isset($_POST['get_refund_amount'])) {
      // Only admin role should see the exact computed refund amount used for processing
      if(!$is_admin_role){
          echo "0.00";
          exit;
      }
      $frm_data = filteration($_POST);
      $booking_id = $frm_data['booking_id'];
      
      $query = "SELECT trans_amt FROM `booking_order` WHERE booking_id = ?";
      $res = select($query, [$booking_id], 'i');
      
      if(mysqli_num_rows($res) > 0) {
          $data = mysqli_fetch_assoc($res);
          $refund_amount = $data['trans_amt'] * 0.5; // 50% refund policy
          echo number_format($refund_amount, 2);
      } else {
          echo "0.00";
      }
      
      exit;
  }

?>
