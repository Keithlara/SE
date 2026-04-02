<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('inc/links.php'); ?>
  <title><?php echo $settings_r['site_title'] ?> - BOOKINGS</title>
  <style>
    .booking-page {
      padding-bottom: 4rem;
    }

    .booking-hero {
      position: relative;
      overflow: hidden;
      border-radius: 28px;
      padding: 1.75rem 1.8rem;
      background:
        radial-gradient(circle at top right, rgba(46, 193, 172, 0.2), transparent 28%),
        linear-gradient(135deg, #ffffff 0%, #f8fbff 55%, #eef6f4 100%);
      border: 1px solid rgba(15, 23, 42, 0.08);
      box-shadow: 0 20px 40px rgba(15, 23, 42, 0.06);
    }

    .booking-hero__eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 0.45rem;
      padding: 0.4rem 0.75rem;
      border-radius: 999px;
      background: rgba(46, 193, 172, 0.12);
      color: #11796a;
      font-size: 0.74rem;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
    }

    .booking-page__title {
      font-size: clamp(2rem, 3vw, 2.7rem);
      letter-spacing: -0.03em;
    }

    .booking-page__crumbs {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 0.55rem;
      font-size: 0.82rem;
      font-weight: 600;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: #64748b;
    }

    .booking-page__crumbs a {
      color: inherit;
      text-decoration: none;
    }

    .booking-page__crumbs span {
      opacity: 0.55;
    }

    .booking-hero__hint {
      max-width: 22rem;
      margin: 0;
      color: #526073;
      font-size: 0.95rem;
      line-height: 1.7;
    }

    .booking-grid {
      row-gap: 0.25rem;
    }

    .booking-card-col {
      display: flex;
    }

    .booking-card {
      width: 100%;
      border: 1px solid rgba(15, 23, 42, 0.08);
      border-radius: 24px;
      background: #ffffff;
      padding: 1.05rem;
      box-shadow: 0 18px 35px rgba(15, 23, 42, 0.06);
      display: flex;
      flex-direction: column;
      gap: 0.85rem;
      transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
    }

    .booking-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 22px 40px rgba(15, 23, 42, 0.1);
    }

    .booking-card--booked {
      border-top: 4px solid #15803d;
    }

    .booking-card--pending {
      border-top: 4px solid #d97706;
    }

    .booking-card--cancelled {
      border-top: 4px solid #dc2626;
    }

    .booking-card--payment-failed {
      border-top: 4px solid #475569;
    }

    .booking-card__top {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 0.8rem;
    }

    .booking-card__eyebrow {
      margin: 0;
      color: #64748b;
      font-size: 0.74rem;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
    }

    .booking-card__room {
      margin: 0;
      font-size: 1.2rem;
      line-height: 1.2;
      color: #0f172a;
    }

    .booking-card__price {
      display: inline-flex;
      align-items: baseline;
      gap: 0.45rem;
      margin-top: 0.45rem;
      padding: 0.38rem 0.72rem;
      border-radius: 999px;
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      color: #0f172a;
      font-weight: 700;
      font-size: 0.92rem;
    }

    .booking-card__price small {
      color: #64748b;
      font-size: 0.8rem;
      font-weight: 500;
    }

    .booking-card__meta-top {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      gap: 0.4rem;
      min-width: 9rem;
    }

    .booking-card__status {
      border-radius: 999px;
      padding: 0.45rem 0.72rem;
      font-size: 0.68rem;
      font-weight: 700;
      letter-spacing: 0.04em;
    }

    .booking-card__order {
      color: #64748b;
      font-size: 0.72rem;
      text-align: right;
      word-break: break-word;
    }

    .booking-card__quick {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 0.6rem;
    }

    .booking-card__quick-item {
      padding: 0.7rem 0.8rem;
      border-radius: 16px;
      background: #f8fafc;
      border: 1px solid #e2e8f0;
    }

    .booking-card__quick-label {
      display: block;
      margin-bottom: 0.28rem;
      color: #64748b;
      font-size: 0.67rem;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
    }

    .booking-card__quick-value {
      display: block;
      color: #0f172a;
      font-size: 0.9rem;
      font-weight: 700;
      line-height: 1.45;
    }

    .booking-card__menu {
      display: grid;
      gap: 0.55rem;
    }

    .booking-card__menu-btn {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 0.75rem;
      width: 100%;
      padding: 0.72rem 0.85rem;
      border-radius: 16px;
      border: 1px solid #dbe5ef;
      background: #fcfdff;
      color: #0f172a;
      font-weight: 700;
      text-align: left;
      box-shadow: none !important;
    }

    .booking-card__menu-btn:hover {
      background: #f8fbff;
      border-color: #c7d7ea;
      color: #0f172a;
    }

    .booking-card__menu-btn span {
      display: inline-flex;
      align-items: center;
      gap: 0.55rem;
    }

    .booking-card__menu-chevron {
      transition: transform 0.18s ease;
    }

    .booking-card__menu-btn:not(.collapsed) .booking-card__menu-chevron {
      transform: rotate(180deg);
    }

    .booking-collapse {
      margin-top: -0.05rem;
      padding: 0.2rem 0.1rem 0.15rem;
      display: grid;
      gap: 0.7rem;
    }

    .booking-collapse--actions {
      padding-top: 0.1rem;
    }

    .booking-detail-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 0.75rem;
    }

    .booking-detail {
      min-height: 100%;
      padding: 0.85rem 0.95rem;
      border-radius: 18px;
      border: 1px solid #e2e8f0;
      background: #f8fafc;
    }

    .booking-detail__label {
      display: block;
      margin-bottom: 0.35rem;
      color: #64748b;
      font-size: 0.69rem;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
    }

    .booking-detail__value {
      display: block;
      color: #0f172a;
      font-weight: 600;
      line-height: 1.5;
    }

    .booking-section,
    .booking-note-card,
    .booking-message {
      border-radius: 18px;
    }

    .booking-section {
      border: 1px solid #e2e8f0;
      background: #ffffff;
      padding: 1rem 1rem 0.9rem;
    }

    .booking-section__title {
      color: #0f172a;
      font-weight: 700;
    }

    .booking-billing {
      background: linear-gradient(180deg, #fffdf6 0%, #fffaf0 100%);
      border-color: rgba(240, 192, 64, 0.5);
    }

    .booking-billing__header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 1rem;
      margin-bottom: 0.85rem;
    }

    .booking-pay-badge {
      border-radius: 999px;
      padding: 0.45rem 0.7rem;
      font-size: 0.72rem;
      font-weight: 700;
    }

    .booking-billing__body {
      display: grid;
      gap: 0.45rem;
    }

    .booking-billing__row {
      display: flex;
      justify-content: space-between;
      gap: 1rem;
      color: #334155;
      font-size: 0.94rem;
    }

    .booking-billing__row strong {
      color: #0f172a;
      font-weight: 700;
    }

    .booking-billing__row--discount {
      color: #047857;
    }

    .booking-billing__row--discount strong {
      color: #047857;
    }

    .booking-billing__row--accent {
      color: #b45309;
    }

    .booking-billing__row--accent strong {
      color: #b45309;
    }

    .booking-billing__row--due {
      color: #dc2626;
    }

    .booking-billing__row--due strong {
      color: #dc2626;
    }

    .booking-billing__note {
      margin-top: 0.75rem;
      padding-top: 0.75rem;
      border-top: 1px dashed rgba(180, 83, 9, 0.25);
      color: #78613b;
      font-size: 0.78rem;
    }

    .booking-note-stack {
      display: grid;
      gap: 0.75rem;
    }

    .booking-note-card {
      padding: 0.95rem 1rem;
      border: 1px solid #e2e8f0;
      background: #ffffff;
    }

    .booking-note-card--guest {
      background: #f8fafc;
      border-left: 4px solid #64748b;
    }

    .booking-note-card--staff {
      background: #f0fdf4;
      border-left: 4px solid #15803d;
    }

    .booking-note-card__title {
      margin-bottom: 0.45rem;
      font-size: 0.82rem;
      font-weight: 700;
      letter-spacing: 0.06em;
      text-transform: uppercase;
    }

    .booking-note-card--guest .booking-note-card__title {
      color: #475569;
    }

    .booking-note-card--staff .booking-note-card__title {
      color: #166534;
    }

    .booking-note-card__text {
      color: #0f172a;
      font-size: 0.92rem;
      line-height: 1.6;
      white-space: pre-wrap;
    }

    .booking-message {
      display: flex;
      align-items: flex-start;
      gap: 0.75rem;
      padding: 0.95rem 1rem;
      border: 1px solid transparent;
      color: #334155;
      font-size: 0.92rem;
      line-height: 1.6;
    }

    .booking-message i {
      margin-top: 0.12rem;
      font-size: 1rem;
    }

    .booking-message--info {
      background: #f8fafc;
      border-color: #cbd5e1;
    }

    .booking-message--warning {
      background: #fff8eb;
      border-color: #fed7aa;
      color: #9a3412;
    }

    .booking-message--success {
      background: #effaf4;
      border-color: #bbf7d0;
      color: #166534;
    }

    .booking-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 0.65rem;
      margin-top: 0;
      padding-top: 0;
    }

    .booking-actions .btn {
      border-radius: 14px;
      padding: 0.6rem 0.9rem;
      font-weight: 600;
      letter-spacing: 0.01em;
    }

    .booking-actions .btn-sm {
      font-size: 0.88rem;
    }

    .booking-empty {
      border: 1px solid rgba(15, 23, 42, 0.08);
      border-radius: 28px;
      background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
      box-shadow: 0 18px 35px rgba(15, 23, 42, 0.06);
    }

    @media (max-width: 991.98px) {
      .booking-card__top {
        flex-direction: column;
      }

      .booking-card__meta-top {
        align-items: flex-start;
        min-width: 0;
      }

      .booking-card__order {
        text-align: left;
      }
    }

    @media (max-width: 767.98px) {
      .booking-page {
        padding-bottom: 3rem;
      }

      .booking-hero {
        padding: 1.4rem;
      }

      .booking-detail-grid {
        grid-template-columns: 1fr;
      }

      .booking-card {
        padding: 0.95rem;
        border-radius: 20px;
      }

      .booking-card__quick {
        grid-template-columns: 1fr;
      }

      .booking-actions .btn {
        width: 100%;
      }
    }
  </style>
</head>
<body class="bg-light">

<?php 
    require('inc/header.php'); 

    if(!(isset($_SESSION['login']) && $_SESSION['login']==true)){
      redirect('index.php');
    }

  ?>

  <div class="container booking-page">
    <div class="row booking-grid">

      <div class="col-12 my-5 px-4">
        <div class="booking-hero">
          <div class="booking-hero__eyebrow mb-3">
            <i class="bi bi-journal-check"></i>
            <span>Guest Center</span>
          </div>
          <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-end">
            <div>
              <h2 class="fw-bold booking-page__title mb-2">Bookings</h2>
              <div class="booking-page__crumbs">
                <a href="index.php">Home</a>
                <span>/</span>
                <span>Bookings</span>
              </div>
            </div>
            <p class="booking-hero__hint">Track confirmations, balances, refunds, and support updates in one cleaner place.</p>
          </div>
        </div>
      </div>

      <?php 
        // Include notifications functions
        require_once('inc/notifications_functions.php');
        
        $query = "SELECT bo.*, bd.*, bo.confirmed_at, 
                 (SELECT COUNT(*) FROM notifications n 
                  WHERE n.booking_id = bo.booking_id AND n.type = 'refund' AND n.is_read = 0) as has_unread_refund
                 FROM `booking_order` bo
                 INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
                 WHERE ((bo.booking_status='booked') 
                 OR (bo.booking_status='pending')
                 OR (bo.booking_status='cancelled')
                 OR (bo.booking_status='payment failed')) 
                 AND (bo.user_id=?)
                 ORDER BY bo.booking_id DESC";

        $result = select($query,[$_SESSION['uId']],'i');
        $has_bookings = false;

        while($data = mysqli_fetch_assoc($result))
        {
            $has_bookings = true;
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
            $status_note = '';
            $support_category = 'booking';
            $action_buttons = [];
            
            if($data['booking_status']=='booked') {
                $status_bg = "bg-success";
                if($data['arrival']==1) {
                    $action_buttons[] = "<a href='generate_pdf.php?gen_pdf&id=$data[booking_id]' class='btn btn-dark btn-sm shadow-none'>Download PDF</a>";
                    if($data['rate_review']==0) {
                        $action_buttons[] = "<button type='button' onclick='review_room($data[booking_id],$data[room_id])' data-bs-toggle='modal' data-bs-target='#reviewModal' class='btn btn-dark btn-sm shadow-none'>Rate & Review</button>";
                    }
                } else {
                    $status_note = "<div class='alert alert-light border small py-2 px-3 mt-3 mb-0'><i class='bi bi-hourglass-split me-1'></i>Your stay is confirmed and waiting for check-in.</div>";
                    $action_buttons[] = "<button onclick='cancel_booking($data[booking_id])' type='button' class='btn btn-danger btn-sm shadow-none'>Cancel</button>";
                    $action_buttons[] = "<a href='support.php?booking_id=$data[booking_id]&category=reschedule' class='btn btn-outline-secondary btn-sm shadow-none'>Request Reschedule</a>";
                }
            } 
            else if($data['booking_status']=='pending') {
                $status_bg = "bg-warning text-dark";
                $status_text = "Pending";
                $status_note = "<div class='alert alert-warning small py-2 px-3 mt-3 mb-0'><i class='bi bi-info-circle me-1'></i>Your booking is waiting for admin confirmation.</div>";
                $action_buttons[] = "<button onclick='cancel_booking($data[booking_id])' type='button' class='btn btn-outline-danger btn-sm shadow-none'>Cancel Request</button>";
                $action_buttons[] = "<a href='support.php?booking_id=$data[booking_id]&category=booking' class='btn btn-outline-secondary btn-sm shadow-none'>Need Help?</a>";
            }
            else if($data['booking_status']=='cancelled') {
                $status_bg = "bg-danger";
                $support_category = 'refund';
                
                if($data['refund'] == 0) {
                    $status_note = "<div class='alert alert-warning small py-2 px-3 mt-3 mb-0'><i class='bi bi-arrow-counterclockwise me-1'></i>Your cancellation is recorded and the refund is still being processed.</div>";
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

          $status_note = '';
          $support_category = 'booking';
          $action_buttons = [];

          if($data['booking_status']=='booked') {
            $status_bg = "bg-success";
            if($data['arrival']==1) {
              $action_buttons[] = "<a href='generate_pdf.php?gen_pdf&id=$data[booking_id]' class='btn btn-dark btn-sm shadow-none'>Download PDF</a>";
              if($data['rate_review']==0) {
                $action_buttons[] = "<button type='button' onclick='review_room($data[booking_id],$data[room_id])' data-bs-toggle='modal' data-bs-target='#reviewModal' class='btn btn-dark btn-sm shadow-none'>Rate & Review</button>";
              }
            } else {
              $status_note = "<div class='booking-message booking-message--info'><i class='bi bi-hourglass-split'></i><span>Your stay is confirmed and waiting for check-in.</span></div>";
              $action_buttons[] = "<button onclick='cancel_booking($data[booking_id])' type='button' class='btn btn-danger btn-sm shadow-none'>Cancel</button>";
              $action_buttons[] = "<a href='support.php?booking_id=$data[booking_id]&category=reschedule' class='btn btn-outline-secondary btn-sm shadow-none'>Request Reschedule</a>";
            }
          } elseif($data['booking_status']=='pending') {
            $status_bg = "bg-warning text-dark";
            $status_text = "Pending";
            $status_note = "<div class='booking-message booking-message--warning'><i class='bi bi-info-circle'></i><span>Your booking is waiting for admin confirmation.</span></div>";
            $action_buttons[] = "<button onclick='cancel_booking($data[booking_id])' type='button' class='btn btn-outline-danger btn-sm shadow-none'>Cancel Request</button>";
          } elseif($data['booking_status']=='cancelled') {
            $status_bg = "bg-danger";
            $support_category = 'refund';
            if($data['refund'] == 0) {
              $status_note = "<div class='booking-message booking-message--warning'><i class='bi bi-arrow-counterclockwise'></i><span>Your cancellation is recorded and the refund is still being processed.</span></div>";
            } else {
              $status_note = "<div class='alert alert-success small py-2 px-3 mt-3 mb-0'><i class='bi bi-check-circle me-1'></i>Refunded: â‚±$refund_amount</div>";
              $action_buttons[] = "<a href='generate_pdf.php?gen_pdf&id=$data[booking_id]' class='btn btn-dark btn-sm shadow-none'>Download Invoice</a>";
              $status_note = "<div class='booking-message booking-message--success'><i class='bi bi-check-circle'></i><span>Refunded: &#8369;{$refund_amount}</span></div>";
              if ($has_unread_refund) {
                $action_buttons[] = "<button type='button' onclick='viewRefundDetails($data[booking_id])' class='btn btn-info btn-sm shadow-none'><i class='bi bi-cash-stack me-1'></i>View Refund Details <span class='badge bg-white text-danger'>New</span></button>";
              } else {
                $action_buttons[] = "<button type='button' onclick='viewRefundDetails($data[booking_id])' class='btn btn-outline-info btn-sm shadow-none'><i class='bi bi-cash-stack me-1'></i>Refund Details</button>";
              }
            }
          } else {
            $status_bg = "bg-warning text-dark";
            $status_text = "Payment Failed";
            $status_note = "<div class='booking-message booking-message--warning'><i class='bi bi-upload'></i><span>Your payment proof still needs attention. Re-upload a clearer copy to continue processing.</span></div>";
            $action_buttons[] = "<button type='button' onclick='openUploadProofModal($data[booking_id])' class='btn btn-primary btn-sm shadow-none'>Re-upload Proof</button>";
            $action_buttons[] = "<a href='generate_pdf.php?gen_pdf&id=$data[booking_id]' class='btn btn-outline-dark btn-sm shadow-none'>Download Invoice</a>";
          }

          $status_slug = trim(preg_replace('/[^a-z0-9]+/', '-', strtolower((string)$data['booking_status'])), '-');
          $action_buttons[] = "<button type='button' onclick='viewBookingTimeline($data[booking_id])' class='btn btn-outline-primary btn-sm shadow-none'>View Timeline</button>";
          $action_buttons[] = "<a href='support.php?booking_id=$data[booking_id]&category=$support_category' class='btn btn-outline-secondary btn-sm shadow-none'>Open Support</a>";
          $btn = "<div class='booking-actions'>" . implode('', $action_buttons) . "</div>";

          // Build special request / admin reply blocks
          $special_request_block = '';
          if (!empty($data['booking_note'])) {
              $note_escaped = htmlspecialchars($data['booking_note'], ENT_QUOTES);
              $special_request_block .= "
              <div class='booking-note-card booking-note-card--guest'>
                <div class='booking-note-card__title'><i class='bi bi-chat-left-text me-1'></i>Your Special Request</div>
                <div class='booking-note-card__text'>$note_escaped</div>
              </div>";
          }
          if (!empty($data['staff_note'])) {
              $staff_note_escaped = htmlspecialchars($data['staff_note'], ENT_QUOTES);
              $special_request_block .= "
              <div class='booking-note-card booking-note-card--staff'>
                <div class='booking-note-card__title'><i class='bi bi-reply-fill me-1'></i>Admin Reply</div>
                <div class='booking-note-card__text'>$staff_note_escaped</div>
              </div>";
          }
          if ($special_request_block !== '') {
            $special_request_block = "<div class='booking-note-stack'>{$special_request_block}</div>";
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
          $pay_status_badge = "<span class='badge booking-pay-badge {$pay_badge_info[0]}'><i class='bi {$pay_badge_info[1]} me-1'></i>{$pay_badge_info[2]}</span>";

          $billing_block = '';
          $discount_line = '';
          if($b_total > 0){
            if(!empty($data['discount_amount']) && (float)$data['discount_amount'] > 0){
              $discount_label = !empty($data['promo_code']) ? "Promo Discount ({$data['promo_code']})" : "Promo Discount";
              $discount_line = "<div class='d-flex justify-content-between' style='color:#047857;'><span>{$discount_label}</span><span class='fw-semibold'>-PHP ".number_format((float)$data['discount_amount'],2)."</span></div>";
            }
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

          if($b_total > 0){
            $discount_row = '';
            if(!empty($data['discount_amount']) && (float)$data['discount_amount'] > 0){
              $discount_label = !empty($data['promo_code']) ? "Promo Discount ({$data['promo_code']})" : "Promo Discount";
              $discount_row = "<div class='booking-billing__row booking-billing__row--discount'><span>{$discount_label}</span><strong>-&#8369;".number_format((float)$data['discount_amount'],2)."</strong></div>";
            }

            $billing_block = "
              <section class='booking-section booking-billing'>
                <div class='booking-billing__header'>
                  <span class='booking-section__title'><i class='bi bi-receipt me-2'></i>Billing Summary</span>
                  {$pay_status_badge}
                </div>
                <div class='booking-billing__body'>
                  <div class='booking-billing__row'><span>Total Amount</span><strong>&#8369;".number_format($b_total,2)."</strong></div>
                  {$discount_row}
                  <div class='booking-billing__row booking-billing__row--accent'><span>Downpayment Paid (50%)</span><strong>&#8369;".number_format($b_downpay,2)."</strong></div>
                  <div class='booking-billing__row booking-billing__row--due'><span>Balance Due at Hotel</span><strong>&#8369;".number_format($b_balance,2)."</strong></div>
                </div>
                <div class='booking-billing__note'><i class='bi bi-info-circle me-1'></i>Remaining balance is to be paid upon check-in.</div>
              </section>";
          }

          $booking_details_block = "
            <div class='booking-detail-grid'>
              <div class='booking-detail'>
                <span class='booking-detail__label'>Check in</span>
                <span class='booking-detail__value'>$checkin</span>
              </div>
              <div class='booking-detail'>
                <span class='booking-detail__label'>Check out</span>
                <span class='booking-detail__value'>$checkout</span>
              </div>
              <div class='booking-detail'>
                <span class='booking-detail__label'>Room Preference</span>
                <span class='booking-detail__value'>$room_preference</span>
              </div>
              <div class='booking-detail'>
                <span class='booking-detail__label'>Payment Status</span>
                <span class='booking-detail__value'>$payment_status</span>
              </div>
              <div class='booking-detail'>
                <span class='booking-detail__label'>Booked on</span>
                <span class='booking-detail__value'>$date</span>
              </div>
              <div class='booking-detail'>
                <span class='booking-detail__label'>Confirmed at</span>
                <span class='booking-detail__value'>$confirmed_at</span>
              </div>
            </div>";

          $booking_quick_block = "
            <div class='booking-card__quick'>
              <div class='booking-card__quick-item'>
                <span class='booking-card__quick-label'>Check in</span>
                <span class='booking-card__quick-value'>$checkin</span>
              </div>
              <div class='booking-card__quick-item'>
                <span class='booking-card__quick-label'>Check out</span>
                <span class='booking-card__quick-value'>$checkout</span>
              </div>
            </div>";

          $status_badge_html = "<span class='badge booking-card__status {$status_bg} text-capitalize'>{$status_text}</span>";
          $details_id = 'booking-details-' . (int)$data['booking_id'];
          $summary_id = 'booking-summary-' . (int)$data['booking_id'];
          $actions_id = 'booking-actions-' . (int)$data['booking_id'];

          $details_panel = "
            <button class='btn booking-card__menu-btn collapsed' type='button' data-bs-toggle='collapse' data-bs-target='#{$details_id}' aria-expanded='false' aria-controls='{$details_id}'>
              <span><i class='bi bi-list-ul'></i>Details</span>
              <i class='bi bi-chevron-down booking-card__menu-chevron'></i>
            </button>
            <div class='collapse' id='{$details_id}'>
              <div class='booking-collapse'>
                {$booking_details_block}
                {$special_request_block}
              </div>
            </div>";

          $summary_panel = '';
          if($billing_block !== ''){
            $summary_panel = "
              <button class='btn booking-card__menu-btn collapsed' type='button' data-bs-toggle='collapse' data-bs-target='#{$summary_id}' aria-expanded='false' aria-controls='{$summary_id}'>
                <span><i class='bi bi-receipt'></i>Total Summary</span>
                <i class='bi bi-chevron-down booking-card__menu-chevron'></i>
              </button>
              <div class='collapse' id='{$summary_id}'>
                <div class='booking-collapse'>
                  {$billing_block}
                </div>
              </div>";
          }

          $actions_panel = "
            <button class='btn booking-card__menu-btn collapsed' type='button' data-bs-toggle='collapse' data-bs-target='#{$actions_id}' aria-expanded='false' aria-controls='{$actions_id}'>
              <span><i class='bi bi-gear'></i>Actions</span>
              <i class='bi bi-chevron-down booking-card__menu-chevron'></i>
            </button>
            <div class='collapse' id='{$actions_id}'>
              <div class='booking-collapse booking-collapse--actions'>
                {$btn}
              </div>
            </div>";

          echo<<<booking_cards_clean
            <div class='col-xl-4 col-md-6 px-3 mb-4 booking-card-col'>
              <article class='booking-card booking-card--{$status_slug} h-100'>
                <div class='booking-card__top'>
                  <div>
                    <p class='booking-card__eyebrow'>Reservation</p>
                    <h5 class='fw-bold booking-card__room'>$data[room_name]</h5>
                    <div class='booking-card__price'>
                      <span>&#8369;$data[price]</span>
                      <small>per night</small>
                    </div>
                  </div>
                  <div class='booking-card__meta-top'>
                    {$status_badge_html}
                    <div class='booking-card__order'>Order ID: $data[order_id]</div>
                  </div>
                </div>
                {$booking_quick_block}
                $status_note
                <div class='booking-card__menu'>
                  {$details_panel}
                  {$summary_panel}
                  {$actions_panel}
                </div>
              </article>
            </div>
          booking_cards_clean;

          if(false){
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
                $discount_line
                $special_request_block
                $status_note
                <p class='mt-3 mb-0'>
                  <span class='badge $status_bg text-capitalize'>$status_text</span>
                </p>
                $btn
              </div>
            </div>
          bookings;
          }

        }

        if(!$has_bookings){
          echo "
            <div class='col-12 px-4'>
              <div class='booking-empty p-5 text-center text-muted'>
                <i class='bi bi-journal-x fs-1 d-block mb-3'></i>
                <h5 class='fw-semibold mb-2'>No bookings yet</h5>
                <p class='mb-3'>Your confirmed, pending, or cancelled bookings will appear here once you start reserving rooms.</p>
                <a href='rooms.php' class='btn btn-dark shadow-none'>Browse Rooms</a>
              </div>
            </div>";
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

    function viewBookingTimeline(bookingId) {
      const modalEl = document.getElementById('bookingTimelineModal');
      const content = document.getElementById('bookingTimelineContent');
      let modal = bootstrap.Modal.getInstance(modalEl);
      if (!modal) {
        modal = new bootstrap.Modal(modalEl);
      }

      content.innerHTML = `
        <div class="text-center py-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="mt-2 mb-0">Loading booking timeline...</p>
        </div>
      `;

      modal.show();

      fetch(`ajax/get_booking_timeline.php?booking_id=${bookingId}`)
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            content.innerHTML = data.html;
          } else {
            content.innerHTML = `
              <div class="alert alert-warning mb-0">
                <i class="bi bi-exclamation-triangle me-2"></i>
                ${data.message || 'Unable to load the booking timeline right now.'}
              </div>
            `;
          }
        })
        .catch(() => {
          content.innerHTML = `
            <div class="alert alert-danger mb-0">
              <i class="bi bi-exclamation-octagon me-2"></i>
              An error occurred while loading the booking timeline.
            </div>
          `;
        });
    }

    function openUploadProofModal(bookingId) {
      const form = document.getElementById('paymentProofForm');
      const bookingIdInput = document.getElementById('paymentProofBookingId');
      const feedback = document.getElementById('paymentProofFeedback');
      if (!form || !bookingIdInput) return;

      form.reset();
      bookingIdInput.value = bookingId;
      feedback.className = 'small text-muted';
      feedback.textContent = 'Upload a clear JPG, PNG, or PDF file up to 10MB.';

      const modalEl = document.getElementById('paymentProofModal');
      let modal = bootstrap.Modal.getInstance(modalEl);
      if (!modal) {
        modal = new bootstrap.Modal(modalEl);
      }
      modal.show();
    }

    // Function to view refund details
    function viewRefundDetails(bookingId) {
      const modal = new bootstrap.Modal(document.getElementById('refundDetailsModal'));
      const contentDiv = document.getElementById('refundDetailsContent');
      const downloadBtn = document.getElementById('downloadRefundReceipt');
      const defaultDownloadLabel = '<i class="bi bi-download me-1"></i> Download Proof';
      
      // Show loading state
      contentDiv.innerHTML = `
        <div class="text-center py-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="mt-2">Loading refund details...</p>
        </div>
      `;
      downloadBtn.style.display = 'none';
      downloadBtn.removeAttribute('href');
      downloadBtn.removeAttribute('download');
      downloadBtn.setAttribute('target', '_blank');
      downloadBtn.innerHTML = defaultDownloadLabel;
      
      // Fetch refund details
      fetch(`ajax/get_refund_details.php?booking_id=${bookingId}`)
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            const refundDate = data.refund.processed_at_label || 'N/A';
            
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
            
            if (data.refund.proof_url) {
              downloadBtn.href = data.refund.proof_url;
              downloadBtn.setAttribute('download', '');
              downloadBtn.style.display = 'inline-block';
              downloadBtn.innerHTML = defaultDownloadLabel;
            } else {
              downloadBtn.style.display = 'none';
            }
            
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

    const paymentProofForm = document.getElementById('paymentProofForm');
    if (paymentProofForm) {
      paymentProofForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const submitBtn = document.getElementById('paymentProofSubmit');
        const feedback = document.getElementById('paymentProofFeedback');
        const formData = new FormData(paymentProofForm);

        submitBtn.disabled = true;
        feedback.className = 'small text-muted';
        feedback.textContent = 'Uploading payment proof...';

        fetch('ajax/upload_payment_proof.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            feedback.className = 'small text-success';
            feedback.textContent = data.message || 'Payment proof uploaded successfully.';
            setTimeout(() => {
              window.location.reload();
            }, 800);
          } else {
            feedback.className = 'small text-danger';
            feedback.textContent = data.message || 'Unable to upload the payment proof.';
          }
        })
        .catch(() => {
          feedback.className = 'small text-danger';
          feedback.textContent = 'A network error occurred while uploading the payment proof.';
        })
        .finally(() => {
          submitBtn.disabled = false;
        });
      });
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
            <i class="bi bi-download me-1"></i> Download Proof
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="bookingTimelineModal" tabindex="-1" aria-labelledby="bookingTimelineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="bookingTimelineModalLabel">
            <i class="bi bi-clock-history me-2"></i> Booking Timeline
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="bookingTimelineContent">
          <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 mb-0">Loading booking timeline...</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="paymentProofModal" tabindex="-1" aria-labelledby="paymentProofModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="paymentProofForm" enctype="multipart/form-data">
          <div class="modal-header">
            <h5 class="modal-title" id="paymentProofModalLabel">
              <i class="bi bi-upload me-2"></i> Re-upload Payment Proof
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="booking_id" id="paymentProofBookingId" value="">
            <div class="mb-3">
              <label class="form-label">Payment Proof</label>
              <input type="file" name="payment_proof" class="form-control shadow-none" accept=".jpg,.jpeg,.png,.pdf" required>
            </div>
            <div id="paymentProofFeedback" class="small text-muted">Upload a clear JPG, PNG, or PDF file up to 10MB.</div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary shadow-none" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary shadow-none" id="paymentProofSubmit">Upload Proof</button>
          </div>
        </form>
      </div>
    </div>
  </div>

</body>
</html>
