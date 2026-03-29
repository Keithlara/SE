<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('inc/links.php'); ?>
  <title><?php echo $settings_r['site_title'] ?> - BOOKINGS</title>
</head>
<body class="bg-light">

<?php 
    require('inc/header.php'); 

    if(!(isset($_SESSION['login']) && $_SESSION['login']==true)){
      redirect('index.php');
    }

    function ensure_confirmed_at_exists($con){
      $col = mysqli_query($con, "SHOW COLUMNS FROM `booking_order` LIKE 'confirmed_at'");
      if(!$col || mysqli_num_rows($col) === 0){
        mysqli_query($con, "ALTER TABLE `booking_order` ADD `confirmed_at` DATETIME NULL DEFAULT NULL");
      }
    }
    ensure_confirmed_at_exists($con);
  ?>


  <div class="container">
    <div class="row">

      <div class="col-12 my-5 px-4">
        <h2 class="fw-bold">BOOKINGS</h2>
        <div style="font-size: 14px;">
          <a href="index.php" class="text-secondary text-decoration-none">HOME</a>
          <span class="text-secondary"> > </span>
          <a href="#" class="text-secondary text-decoration-none">BOOKINGS</a>
        </div>
      </div>

      <?php 
        // Include notifications functions
        require_once('inc/notifications_functions.php');
        
        // Check if notifications table has the required columns
        $check_columns = "SHOW COLUMNS FROM `notifications` WHERE Field IN ('type', 'is_read')";
        $columns_result = mysqli_query($con, $check_columns);
        $has_columns = (mysqli_num_rows($columns_result) == 2);
        
        // Build the query based on whether the columns exist
        if ($has_columns) {
            $query = "SELECT bo.*, bd.*, bo.confirmed_at, 
                     (SELECT COUNT(*) FROM notifications n 
                      WHERE n.booking_id = bo.booking_id AND n.type = 'refund' AND n.is_read = 0) as has_unread_refund
                     FROM `booking_order` bo
                     INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
                     WHERE ((bo.booking_status='booked') 
                     OR (bo.booking_status='cancelled')
                     OR (bo.booking_status='payment failed')) 
                     AND (bo.user_id=?)
                     ORDER BY bo.booking_id DESC";
        } else {
            // Fallback query if the columns don't exist yet
            $query = "SELECT bo.*, bd.*, bo.confirmed_at, 0 as has_unread_refund
                     FROM `booking_order` bo
                     INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
                     WHERE ((bo.booking_status='booked') 
                     OR (bo.booking_status='cancelled')
                     OR (bo.booking_status='payment failed')) 
                     AND (bo.user_id=?)
                     ORDER BY bo.booking_id DESC";
        }

        $result = select($query,[$_SESSION['uId']],'i');

        while($data = mysqli_fetch_assoc($result))
        {
            $date = date("M d, Y g:i A",strtotime($data['datentime']));
            $checkin = date("M d, Y",strtotime($data['check_in']));
            $checkout = date("M d, Y",strtotime($data['check_out']));
            $confirmed_at = $data['confirmed_at'] ? date("M d, Y g:i A", strtotime($data['confirmed_at'])) : 'Awaiting confirmation';
            $room_preference = (!empty($data['room_no'])) ? 'Room '.$data['room_no'] : 'No preference selected';
            $payment_status = isset($data['payment_status']) ? ucfirst(str_replace('_',' ',$data['payment_status'])) : 'n/a';
            $refund_amount = isset($data['refund_amount']) ? number_format($data['refund_amount'], 2) : '0.00';
            $has_unread_refund = isset($data['has_unread_refund']) && $data['has_unread_refund'] > 0;

            $status_bg = "";
            $status_text = ucfirst($data['booking_status']);
            $btn = "";
            $refund_badge = "";
            
            if($data['booking_status']=='booked') {
                $status_bg = "bg-success";
                if($data['arrival']==1) {
                    $btn = "<a href='generate_pdf.php?gen_pdf&id=$data[booking_id]' class='btn btn-dark btn-sm shadow-none'>Download PDF</a>";
                    if($data['rate_review']==0) {
                        $btn.="<button type='button' onclick='review_room($data[booking_id],$data[room_id])' data-bs-toggle='modal' data-bs-target='#reviewModal' class='btn btn-dark btn-sm shadow-none ms-2'>Rate & Review</button>";
                    }
                } else {
                    $btn = "<button onclick='cancel_booking($data[booking_id])' type='button' class='btn btn-danger btn-sm shadow-none'>Cancel</button>";
                }
            } 
            else if($data['booking_status']=='cancelled') {
                $status_bg = "bg-danger";
                
                if($data['refund'] == 0) {
                    $refund_badge = "<span class='badge bg-warning text-dark ms-2'>Refund Pending</span>";
                    $btn = "<span class='badge bg-primary'>Refund in process</span>";
                } else {
                    $refund_badge = "<span class='badge bg-success ms-2'>Refunded: ₱$refund_amount</span>";
                    $btn = "<a href='generate_pdf.php?gen_pdf&id=$data[booking_id]' class='btn btn-dark btn-sm shadow-none me-2'>Download Invoice</a>";
                    
                    // Add view refund details button if there are unread refund notifications
                    if ($has_unread_refund) {
                        $btn .= "<button type='button' onclick='viewRefundDetails($data[booking_id])' class='btn btn-info btn-sm shadow-none'>
                                    <i class='bi bi-cash-stack me-1'></i> View Refund Details <span class='badge bg-white text-danger'>New</span>
                                </button>";
                    } else {
                        $btn .= "<button type='button' onclick='viewRefundDetails($data[booking_id])' class='btn btn-outline-info btn-sm shadow-none'>
                                    <i class='bi bi-cash-stack me-1'></i> Refund Details
                                </button>";
                    }
                }
            } 
            else {
                $status_bg = "bg-warning";
                $btn = "<a href='generate_pdf.php?gen_pdf&id=$data[booking_id]' class='btn btn-dark btn-sm shadow-none'>Download Invoice</a>";
            }   

          // Build special request / admin reply blocks
          $special_request_block = '';
          if (!empty($data['booking_note'])) {
              $note_escaped = htmlspecialchars($data['booking_note'], ENT_QUOTES);
              $special_request_block .= "
              <div class='mt-3 p-2 rounded' style='background:#f8f9fa;border-left:3px solid #6c757d;'>
                <div class='small fw-semibold text-secondary mb-1'><i class='bi bi-chat-left-text me-1'></i>Your Special Request</div>
                <div class='small text-dark' style='white-space:pre-wrap;'>$note_escaped</div>
              </div>";
          }
          if (!empty($data['staff_note'])) {
              $staff_note_escaped = htmlspecialchars($data['staff_note'], ENT_QUOTES);
              $special_request_block .= "
              <div class='mt-2 p-2 rounded' style='background:#e8f5e9;border-left:3px solid #198754;'>
                <div class='small fw-semibold text-success mb-1'><i class='bi bi-reply-fill me-1'></i>Admin Reply</div>
                <div class='small text-dark' style='white-space:pre-wrap;'>$staff_note_escaped</div>
              </div>";
          }


          // Billing breakdown
          $b_total      = isset($data['total_amt'])   && $data['total_amt']   > 0 ? (float)$data['total_amt']   : (float)($data['trans_amt'] ?? 0) * 2;
          $b_downpay    = isset($data['downpayment']) && $data['downpayment'] > 0 ? (float)$data['downpayment'] : (float)($data['trans_amt'] ?? 0);
          $b_balance    = isset($data['balance_due']) && $data['balance_due'] > 0 ? (float)$data['balance_due'] : max(0, $b_total - $b_downpay);

          // Payment status badge
          $pay_status_raw = isset($data['payment_status']) ? strtolower($data['payment_status']) : 'pending';
          $pay_badge_map = [
            'paid'    => ['bg-success',  'bi-check-circle-fill', 'Paid'],
            'partial' => ['bg-warning text-dark', 'bi-clock-fill', 'Partially Paid'],
            'pending' => ['bg-secondary', 'bi-hourglass-split', 'Payment Pending'],
          ];
          $pay_badge_info = $pay_badge_map[$pay_status_raw] ?? ['bg-secondary','bi-dash-circle','Unknown'];
          $pay_status_badge = "<span class='badge {$pay_badge_info[0]}'><i class='bi {$pay_badge_info[1]} me-1'></i>{$pay_badge_info[2]}</span>";

          $billing_block = '';
          if($b_total > 0){
            $billing_block = "
              <div class='mt-2 p-2 rounded' style='background:#fffbf0;border:1px solid #f0c040;font-size:0.8rem;'>
                <div class='d-flex justify-content-between align-items-center mb-1'>
                  <span class='fw-semibold' style='color:#b8860b;'><i class='bi bi-receipt me-1'></i>Billing Summary</span>
                  {$pay_status_badge}
                </div>
                <div class='d-flex justify-content-between'><span class='text-muted'>Total Amount</span><span class='fw-semibold'>₱".number_format($b_total,2)."</span></div>
                <div class='d-flex justify-content-between' style='color:#b8860b;'><span>Downpayment Paid (50%)</span><span class='fw-semibold'>₱".number_format($b_downpay,2)."</span></div>
                <div class='d-flex justify-content-between' style='color:#dc3545;'><span>Balance Due at Hotel</span><span class='fw-semibold'>₱".number_format($b_balance,2)."</span></div>
                <div class='mt-1 text-muted' style='font-size:0.72rem;'><i class='bi bi-info-circle me-1'></i>Remaining balance is to be paid upon check-in.</div>
              </div>";
          }

          echo<<<bookings
            <div class='col-md-4 px-4 mb-4'>
              <div class='bg-white p-3 rounded shadow-sm'>
                <h5 class='fw-bold'>$data[room_name]</h5>
                <p>₱$data[price] per night</p>
                <ul class='list-unstyled small text-muted mb-3'>
                  <li><b>Check in:</b> $checkin</li>
                  <li><b>Check out:</b> $checkout</li>
                  <li><b>Room Preference:</b> $room_preference</li>
                  <li><b>Booked on:</b> $date</li>
                  <li><b>Confirmed at:</b> $confirmed_at</li>
                  <li><b>Payment Status:</b> $payment_status</li>
                  <li><b>Order ID:</b> $data[order_id]</li>
                </ul>
                $billing_block
                $special_request_block
                <p class='mt-3'>
                  <span class='badge $status_bg text-capitalize'>$data[booking_status]</span>
                </p>
                $btn
              </div>
            </div>
          bookings;

        }

      ?>


    </div>
  </div>


  <div class="modal fade" id="reviewModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="review-form">
          <div class="modal-header">
            <h5 class="modal-title d-flex align-items-center">
              <i class="bi bi-chat-square-heart-fill fs-3 me-2"></i> Rate & Review
            </h5>
            <button type="reset" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Rating</label>
              <div class="d-flex align-items-center gap-1" id="guest-rating" role="radiogroup" aria-label="Guest rating">
                <button type="button" class="btn p-0 shadow-none rating-star" data-value="1" aria-label="1 star">
                  <i class="bi bi-star text-warning fs-4"></i>
                </button>
                <button type="button" class="btn p-0 shadow-none rating-star" data-value="2" aria-label="2 stars">
                  <i class="bi bi-star text-warning fs-4"></i>
                </button>
                <button type="button" class="btn p-0 shadow-none rating-star" data-value="3" aria-label="3 stars">
                  <i class="bi bi-star text-warning fs-4"></i>
                </button>
                <button type="button" class="btn p-0 shadow-none rating-star" data-value="4" aria-label="4 stars">
                  <i class="bi bi-star text-warning fs-4"></i>
                </button>
                <button type="button" class="btn p-0 shadow-none rating-star" data-value="5" aria-label="5 stars">
                  <i class="bi bi-star text-warning fs-4"></i>
                </button>
                <span class="ms-2 small text-muted" id="guest-rating-value">5/5</span>
              </div>
              <input type="hidden" name="rating" value="5">
            </div>
            <div class="mb-4">
              <label class="form-label">Review</label>
              <textarea type="password" name="review" rows="3" required class="form-control shadow-none"></textarea>
            </div>
            
            <input type="hidden" name="booking_id">
            <input type="hidden" name="room_id">

            <div class="text-end">
              <button type="submit" class="btn custom-bg text-white shadow-none">SUBMIT</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>



  <?php 
    if(isset($_GET['cancel_status'])){
      alert('success','Booking Cancelled!');
    }  
    else if(isset($_GET['review_status'])){
      alert('success','Thank you for rating & review!');
    }  
  ?>

  <?php require('inc/footer.php'); ?>

  <script>
    // Star rating widget (submits numeric rating like before)
    (function initGuestRating(){
      const container = document.getElementById('guest-rating');
      const form = document.getElementById('review-form');
      if(!container || !form) return;

      const hidden = form.querySelector('input[name="rating"]');
      const valueLabel = document.getElementById('guest-rating-value');
      const buttons = Array.from(container.querySelectorAll('.rating-star'));

      function render(val){
        const rating = Math.max(1, Math.min(5, parseInt(val,10) || 5));
        if(hidden) hidden.value = String(rating);
        if(valueLabel) valueLabel.textContent = `${rating}/5`;

        buttons.forEach((btn) => {
          const v = parseInt(btn.getAttribute('data-value') || '0', 10);
          const icon = btn.querySelector('i');
          const filled = v <= rating;
          btn.setAttribute('aria-checked', filled ? 'true' : 'false');
          if(icon){
            icon.classList.toggle('bi-star-fill', filled);
            icon.classList.toggle('bi-star', !filled);
          }
        });
      }

      buttons.forEach((btn) => {
        btn.addEventListener('click', () => render(btn.getAttribute('data-value')));
      });

      // default
      render(hidden ? hidden.value : 5);
      window.__setGuestRating = render; // used by review_room() to reset per open
    })();

    function cancel_booking(id)
    {
      if(confirm('Are you sure to cancel booking?'))
      {        
        let xhr = new XMLHttpRequest();
        xhr.open("POST","ajax/cancel_booking.php",true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function(){
          if(this.responseText==1){
            window.location.href="bookings.php?cancel_status=true";
          }
          else{
            alert('error','Cancellation Failed!');
          }
        }

        xhr.send('cancel_booking&id='+id);
      }
    }

    let review_form = document.getElementById('review-form');

    function review_room(bid,rid){
      review_form.elements['booking_id'].value = bid;
      review_form.elements['room_id'].value = rid;
      if(typeof window.__setGuestRating === 'function'){
        window.__setGuestRating(5);
      }
    }

    review_form.addEventListener('submit',function(e){
      e.preventDefault();

      let data = new FormData();

      data.append('review_form','');
      data.append('rating',review_form.elements['rating'].value);
      data.append('review',review_form.elements['review'].value);
      data.append('booking_id',review_form.elements['booking_id'].value);
      data.append('room_id',review_form.elements['room_id'].value);

      let xhr = new XMLHttpRequest();
      xhr.open("POST","ajax/review_room.php",true);

      xhr.onload = function()
      {

        if(this.responseText == 1)
        {
          window.location.href = 'bookings.php?review_status=true';
        }
        else{
          var myModal = document.getElementById('reviewModal');
          var modal = bootstrap.Modal.getInstance(myModal);
          modal.hide();
  
          alert('error',"Rating & Review Failed!");
        }
      }

      xhr.send(data);
    })

    // Function to view refund details
    function viewRefundDetails(bookingId) {
      const modal = new bootstrap.Modal(document.getElementById('refundDetailsModal'));
      const contentDiv = document.getElementById('refundDetailsContent');
      const downloadBtn = document.getElementById('downloadRefundReceipt');
      
      // Show loading state
      contentDiv.innerHTML = `
        <div class="text-center py-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="mt-2">Loading refund details...</p>
        </div>
      `;
      
      // Fetch refund details
      fetch(`ajax/get_refund_details.php?booking_id=${bookingId}`)
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            // Format the refund date
            const refundDate = new Date(data.refund.processed_at).toLocaleString('en-US', {
              year: 'numeric',
              month: 'long',
              day: 'numeric',
              hour: '2-digit',
              minute: '2-digit'
            });
            
            // Update the modal content
            contentDiv.innerHTML = `
              <div class="row">
                <div class="col-md-6">
                  <h6 class="fw-bold">Booking Information</h6>
                  <p class="mb-1"><strong>Booking ID:</strong> ${data.booking.booking_id}</p>
                  <p class="mb-1"><strong>Order ID:</strong> ${data.booking.order_id}</p>
                  <p class="mb-1"><strong>Room:</strong> ${data.booking.room_name}</p>
                  <p class="mb-1"><strong>Check-in:</strong> ${data.booking.check_in}</p>
                  <p class="mb-1"><strong>Check-out:</strong> ${data.booking.check_out}</p>
                </div>
                <div class="col-md-6">
                  <h6 class="fw-bold">Refund Information</h6>
                  <p class="mb-1"><strong>Status:</strong> <span class="badge bg-success">Refunded</span></p>
                  <p class="mb-1"><strong>Amount Refunded:</strong> ₱${parseFloat(data.refund.amount).toFixed(2)}</p>
                  <p class="mb-1"><strong>Refund Method:</strong> ${data.refund.method || 'Original Payment Method'}</p>
                  <p class="mb-1"><strong>Processed On:</strong> ${refundDate}</p>
                  <p class="mb-1"><strong>Reference ID:</strong> ${data.refund.reference_id || 'N/A'}</p>
                </div>
              </div>
              
              ${data.refund.proof_url ? `
              <div class="mt-4">
                <h6 class="fw-bold"><i class="bi bi-image me-1"></i>Refund Proof</h6>
                ${/\.pdf($|\?)/i.test(data.refund.proof_url)
                  ? `<iframe src="${data.refund.proof_url}" class="w-100 rounded border" style="height:320px;" frameborder="0"></iframe>`
                  : `<a href="${data.refund.proof_url}" target="_blank" rel="noopener">
                       <img src="${data.refund.proof_url}" class="img-fluid rounded border shadow-sm" style="max-height:320px;cursor:pointer;" alt="Refund proof">
                     </a>`
                }
                <div class="mt-1">
                  <a href="${data.refund.proof_url}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary mt-1">
                    <i class="bi bi-box-arrow-up-right me-1"></i>Open full size
                  </a>
                </div>
              </div>` : ''}

              <div class="mt-4">
                <h6 class="fw-bold">Notes</h6>
                <p>${data.refund.notes || 'The refund has been processed and the amount will be credited to your original payment method within 3-5 business days.'}</p>
                
                ${data.refund.additional_notes ? `
                  <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    ${data.refund.additional_notes}
                  </div>
                ` : ''}
              </div>
              
              <div class="alert alert-success mt-3">
                <i class="bi bi-check-circle-fill me-2"></i>
                Your refund has been processed successfully. Thank you for choosing our service!
              </div>
            `;
            
            // Update download button
            downloadBtn.href = `generate_refund_receipt.php?booking_id=${bookingId}`;
            downloadBtn.style.display = 'inline-block';
            
            // Mark notification as read
            markRefundNotificationAsRead(bookingId);
            
          } else {
            contentDiv.innerHTML = `
              <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>
                ${data.message || 'Failed to load refund details. Please try again later.'}
              </div>
            `;
            downloadBtn.style.display = 'none';
          }
        })
        .catch(error => {
          console.error('Error:', error);
          contentDiv.innerHTML = `
            <div class="alert alert-danger">
              <i class="bi bi-exclamation-octagon me-2"></i>
              An error occurred while loading refund details. Please try again later.
            </div>
          `;
          downloadBtn.style.display = 'none';
        });
      
      // Show the modal
      modal.show();
    }
    
    // Function to mark refund notification as read
    function markRefundNotificationAsRead(bookingId) {
      // Send a request to mark the notification as read
      fetch('ajax/mark_notification_read.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `booking_id=${bookingId}&type=refund`
      });
      
      // Update the UI to remove the "New" badge
      const badge = document.querySelector(`button[onclick*="viewRefundDetails(${bookingId})"] .badge`);
      if (badge) {
        badge.remove();
      }
    }
  </script>

  <!-- Refund Details Modal -->
  <div class="modal fade" id="refundDetailsModal" tabindex="-1" aria-labelledby="refundDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="refundDetailsModalLabel">
            <i class="bi bi-cash-stack me-2"></i> Refund Details
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="refundDetailsContent">
          <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading refund details...</p>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <a href="#" id="downloadRefundReceipt" class="btn btn-primary">
            <i class="bi bi-download me-1"></i> Download Receipt
          </a>
        </div>
      </div>
    </div>
  </div>

</body>
</html>