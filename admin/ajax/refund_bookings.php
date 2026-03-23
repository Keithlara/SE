<?php 

  require('../inc/db_config.php');
  require('../inc/essentials.php');
  // notifications helper lives in project-level inc/, not admin/inc/
  require('../../inc/notifications_functions.php');
  adminLogin();

  // Refund processing is admin-only. Staff may view refund requests but cannot process them.
  $is_admin_role = (($_SESSION['adminRole'] ?? 'admin') === 'admin');

  // Function to send email notification
  function send_refund_email($to_email, $user_name, $booking_id, $amount) {
      $subject = "Refund Processed for Booking #$booking_id";
      $body = "
          <h2>Refund Processed</h2>
          <p>Dear $user_name,</p>
          <p>We have processed your refund for booking #$booking_id.</p>
          <p><strong>Refund Amount:</strong> ₱" . number_format($amount, 2) . "</p>
          <p>The amount will be credited to your original payment method within 3-5 business days.</p>
          <p>If you have any questions, please contact our support team.</p>
          <p>Best regards,<br>Resort Management</p>
      ";
      
      return send_mail($to_email, $subject, $body);
  }

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
              $table_data .= "
                  <button type='button' onclick='refund_booking($data[booking_id], $refund_amount, this)' 
                          class='btn btn-success btn-sm fw-bold shadow-none'>
                      <i class='bi bi-cash-stack me-1'></i> Process Refund
                  </button>";
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
      // Only admin role may actually process a refund
      if(!$is_admin_role){
          echo "0";
          exit;
      }
      $frm_data = filteration($_POST);
      $booking_id = $frm_data['booking_id'];
      $refund_amount = $frm_data['refund_amount'] ?? 0;
      
      // Start transaction
      mysqli_begin_transaction($con);
      
      try {
          // 1. Update booking status to refunded
          $query = "UPDATE `booking_order` SET `refund` = 1, `refund_amount` = ? WHERE `booking_id` = ?";
          $values = [$refund_amount, $booking_id];
          $res = update($query, $values, 'di');
          
          if(!$res) {
              throw new Exception("Failed to update booking status");
          }
          
          // 2. Get user details for notification
          $user_query = "SELECT u.id, u.email, bd.user_name 
                        FROM `booking_order` bo 
                        JOIN `user_cred` u ON bo.user_id = u.id
                        JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
                        WHERE bo.booking_id = ?";
          $user_data = select($user_query, [$booking_id], 'i');
          
          if(mysqli_num_rows($user_data) === 0) {
              throw new Exception("User details not found");
          }
          
          $user = mysqli_fetch_assoc($user_data);
          
          // 3. Add notification
          $message = "Your refund of ₱" . number_format($refund_amount, 2) . " for booking #$booking_id has been processed successfully.";
          
          if(!add_refund_notification($user['id'], $booking_id, $refund_amount)) {
              throw new Exception("Failed to add notification");
          }
          
          // 4. Send email notification
          send_refund_email($user['email'], $user['user_name'], $booking_id, $refund_amount);
          
          // Commit transaction
          mysqli_commit($con);
          
          echo "1"; // Success
          
      } catch (Exception $e) {
          // Rollback transaction on error
          mysqli_rollback($con);
          error_log("Refund Error: " . $e->getMessage());
          echo "0"; // Error
      }
      
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