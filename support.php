<?php
  if (session_status() === PHP_SESSION_NONE) { session_start(); }
  require('admin/inc/essentials.php');
  require('admin/inc/db_config.php');

  if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
    redirect('index.php');
  }

  $message = '';
  $message_type = 'success';

  if (isset($_POST['create_ticket'])) {
    $subject = trim((string)($_POST['subject'] ?? ''));
    $category = trim((string)($_POST['category'] ?? 'general'));
    $priority = trim((string)($_POST['priority'] ?? 'normal'));
    $booking_id = !empty($_POST['booking_id']) ? (int)$_POST['booking_id'] : null;
    $order_id = trim((string)($_POST['order_id'] ?? ''));
    $body = trim((string)($_POST['message'] ?? ''));
    $attachment_path = '';

    if (isset($_FILES['ticket_attachment'])) {
      $attachment_result = storeSupportAttachment($_FILES['ticket_attachment']);
      if (in_array($attachment_result, ['invalid_type', 'too_large', 'upload_error'], true)) {
        $message = 'Attachment upload failed. Allowed: JPG, PNG, WEBP, PDF up to 5MB.';
        $message_type = 'error';
      } else {
        $attachment_path = $attachment_result;
      }
    }

    if ($message === '') {
      $ticket_id = createSupportTicket(
        (int)$_SESSION['uId'],
        $subject,
        $body,
        [
          'booking_id' => $booking_id,
          'order_id' => $order_id,
          'category' => $category,
          'priority' => $priority,
          'attachment_path' => $attachment_path,
          'sender_name' => (string)($_SESSION['uName'] ?? 'Guest'),
        ]
      );

      if ($ticket_id) {
        if ($booking_id) {
          createBookingHistoryEntry(
            $booking_id,
            'support_opened',
            'Guest opened a support request',
            $subject
          );
        }
        $message = 'Your support ticket has been created.';
      } else {
        $message = 'Unable to create the support ticket.';
        $message_type = 'error';
      }
    }
  }

  if (isset($_POST['reply_ticket'])) {
    $ticket_id = (int)($_POST['ticket_id'] ?? 0);
    $reply_message = trim((string)($_POST['reply_message'] ?? ''));
    $attachment_path = '';

    if (isset($_FILES['reply_attachment'])) {
      $attachment_result = storeSupportAttachment($_FILES['reply_attachment']);
      if (in_array($attachment_result, ['invalid_type', 'too_large', 'upload_error'], true)) {
        $message = 'Attachment upload failed. Allowed: JPG, PNG, WEBP, PDF up to 5MB.';
        $message_type = 'error';
      } else {
        $attachment_path = $attachment_result;
      }
    }

    if ($message === '') {
      $ticket_check = select(
        "SELECT `id`,`booking_id` FROM `support_tickets` WHERE `id`=? AND `user_id`=? LIMIT 1",
        [$ticket_id, (int)$_SESSION['uId']],
        'ii'
      );
      if ($ticket_check && mysqli_num_rows($ticket_check) === 1) {
        $ticket_meta = mysqli_fetch_assoc($ticket_check);
        if (addSupportTicketMessage($ticket_id, 'guest', (int)$_SESSION['uId'], (string)$_SESSION['uName'], $reply_message, ['attachment_path' => $attachment_path, 'next_status' => 'open'])) {
          if (!empty($ticket_meta['booking_id'])) {
            createBookingHistoryEntry((int)$ticket_meta['booking_id'], 'support_reply', 'Guest replied to support', sanitizeMultilineText($reply_message, 180));
          }
          $message = 'Reply sent successfully.';
        } else {
          $message = 'Unable to send the reply.';
          $message_type = 'error';
        }
      }
    }
  }

  $prefill_booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
  $prefill_category = trim((string)($_GET['category'] ?? 'general'));
  if (!isset(supportTicketCategories()[$prefill_category])) {
    $prefill_category = 'general';
  }

  $booking_options = [];
  $booking_res = select(
    "SELECT bo.booking_id, bo.order_id, bo.booking_status, bd.room_name
     FROM `booking_order` bo
     INNER JOIN `booking_details` bd ON bd.booking_id = bo.booking_id
     WHERE bo.user_id=?
     ORDER BY bo.booking_id DESC",
    [(int)$_SESSION['uId']],
    'i'
  );
  while ($row = mysqli_fetch_assoc($booking_res)) {
    $booking_options[] = $row;
  }

  $prefill_order_id = '';
  foreach ($booking_options as $booking_option) {
    if ($prefill_booking_id === (int)$booking_option['booking_id']) {
      $prefill_order_id = (string)($booking_option['order_id'] ?? '');
      break;
    }
  }

  $ticket_rows = [];
  $ticket_res = select(
    "SELECT * FROM `support_tickets` WHERE `user_id`=? ORDER BY FIELD(`status`,'open','pending','escalated','resolved'), `updated_at` DESC, `id` DESC",
    [(int)$_SESSION['uId']],
    'i'
  );
  while ($row = mysqli_fetch_assoc($ticket_res)) {
    $ticket_rows[] = $row;
  }

  $selected_ticket = null;
  $selected_ticket_messages = [];
  if (!empty($_GET['ticket'])) {
    $ticket_id = (int)$_GET['ticket'];
    $ticket_detail_res = select("SELECT * FROM `support_tickets` WHERE `id`=? AND `user_id`=? LIMIT 1", [$ticket_id, (int)$_SESSION['uId']], 'ii');
    if ($ticket_detail_res && mysqli_num_rows($ticket_detail_res) === 1) {
      $selected_ticket = mysqli_fetch_assoc($ticket_detail_res);
      markSupportTicketSeenByUser($ticket_id);
      $msg_res = select("SELECT * FROM `support_ticket_messages` WHERE `ticket_id`=? AND `is_internal`=0 ORDER BY `created_at` ASC, `id` ASC", [$ticket_id], 'i');
      while ($row = mysqli_fetch_assoc($msg_res)) {
        $selected_ticket_messages[] = $row;
      }
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('inc/links.php'); ?>
  <title><?php echo $settings_r['site_title']; ?> - Support</title>
  <style>
    .support-ticket-card, .support-compose-card, .support-thread-card {
      background: #fff; border-radius: 18px; border: 1px solid #e5e7eb; box-shadow: 0 12px 30px rgba(15,23,42,0.05);
    }
    .support-ticket-link {
      display: block; border-radius: 16px; border: 1px solid #e5e7eb; padding: 14px 16px; text-decoration: none; color: inherit;
      transition: all 0.18s ease;
    }
    .support-ticket-link:hover, .support-ticket-link.active { border-color: rgba(46,193,172,0.45); background: rgba(46,193,172,0.08); transform: translateY(-1px); }
    .support-bubble { border-radius: 16px; padding: 14px 16px; max-width: 92%; }
    .support-bubble.guest { margin-left: auto; background: rgba(46,193,172,0.12); }
    .support-bubble.staff { margin-right: auto; background: rgba(15,23,42,0.06); }
  </style>
</head>
<body class="bg-light">
  <?php require('inc/header.php'); ?>

  <div class="container py-5">
    <div class="row">
      <div class="col-12 my-4 px-4">
        <h2 class="fw-bold">SUPPORT</h2>
        <div style="font-size: 14px;">
          <a href="index.php" class="text-secondary text-decoration-none">HOME</a>
          <span class="text-secondary"> > </span>
          <a href="#" class="text-secondary text-decoration-none">SUPPORT</a>
        </div>
      </div>

      <?php if ($message !== ''): ?>
        <div class="col-12 px-4"><?php alert($message_type, $message); ?></div>
      <?php endif; ?>

      <div class="col-lg-4 px-4 mb-4">
        <div class="support-compose-card p-4">
          <h5 class="fw-bold mb-3">Create Ticket</h5>
          <form method="POST" enctype="multipart/form-data">
            <div class="mb-2">
              <label class="form-label">Subject</label>
              <input type="text" class="form-control shadow-none" name="subject" required>
            </div>
            <div class="row g-2">
              <div class="col-md-6">
                <label class="form-label">Category</label>
                <select class="form-select shadow-none" name="category">
                  <?php foreach (supportTicketCategories() as $value => $label): ?>
                    <option value="<?php echo $value; ?>" <?php echo $prefill_category === $value ? 'selected' : ''; ?>><?php echo $label; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Priority</label>
                <select class="form-select shadow-none" name="priority">
                  <?php foreach (supportTicketPriorities() as $value => $label): ?>
                    <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="mb-2 mt-2">
              <label class="form-label">Related Booking</label>
              <select class="form-select shadow-none" name="booking_id" onchange="this.form.order_id.value = this.options[this.selectedIndex].dataset.order || '';">
                <option value="">No booking linked</option>
                <?php foreach ($booking_options as $booking): ?>
                  <option value="<?php echo (int)$booking['booking_id']; ?>" data-order="<?php echo htmlspecialchars($booking['order_id']); ?>" <?php echo $prefill_booking_id === (int)$booking['booking_id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($booking['order_id'] . ' • ' . $booking['room_name'] . ' • ' . ucfirst($booking['booking_status'])); ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($prefill_order_id); ?>">
            </div>
            <div class="mb-2">
              <label class="form-label">Message</label>
              <textarea class="form-control shadow-none" name="message" rows="4" required placeholder="Tell us how we can help."></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Attachment</label>
              <input type="file" class="form-control shadow-none" name="ticket_attachment" accept=".jpg,.jpeg,.png,.webp,.pdf">
            </div>
            <button type="submit" class="btn text-white custom-bg shadow-none" name="create_ticket">Submit Ticket</button>
          </form>
        </div>
      </div>

      <div class="col-lg-8 px-4">
        <div class="support-thread-card p-4 mb-4">
          <div class="row g-4">
            <div class="col-lg-5">
              <h5 class="fw-bold mb-3">My Tickets</h5>
              <div class="d-flex flex-column gap-2">
                <?php foreach ($ticket_rows as $ticket): ?>
                  <a href="?ticket=<?php echo (int)$ticket['id']; ?>" class="support-ticket-link <?php echo !empty($selected_ticket['id']) && (int)$selected_ticket['id'] === (int)$ticket['id'] ? 'active' : ''; ?>">
                    <div class="fw-semibold"><?php echo htmlspecialchars($ticket['subject']); ?></div>
                    <div class="small text-muted mt-1"><?php echo htmlspecialchars($ticket['ticket_code']); ?></div>
                    <div class="d-flex justify-content-between mt-2">
                      <span class="badge <?php echo $ticket['status'] === 'resolved' ? 'bg-success' : ($ticket['status'] === 'escalated' ? 'bg-danger' : ($ticket['status'] === 'pending' ? 'bg-warning text-dark' : 'bg-primary')); ?>"><?php echo ucfirst($ticket['status']); ?></span>
                      <span class="small text-muted"><?php echo date('M d', strtotime($ticket['updated_at'])); ?></span>
                    </div>
                  </a>
                <?php endforeach; ?>
                <?php if (empty($ticket_rows)): ?>
                  <div class="text-muted small">No support tickets yet.</div>
                <?php endif; ?>
              </div>
            </div>
            <div class="col-lg-7">
              <?php if ($selected_ticket): ?>
                <div class="mb-3">
                  <div class="text-muted small"><?php echo htmlspecialchars($selected_ticket['ticket_code']); ?></div>
                  <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($selected_ticket['subject']); ?></h5>
                  <div class="small text-muted">Status: <?php echo ucfirst($selected_ticket['status']); ?></div>
                </div>
                <div class="d-flex flex-column gap-3 mb-4">
                  <?php foreach ($selected_ticket_messages as $row): ?>
                    <div class="support-bubble <?php echo $row['sender_type'] === 'guest' ? 'guest' : 'staff'; ?>">
                      <div class="small text-muted mb-1"><?php echo htmlspecialchars($row['sender_name'] ?: ucfirst($row['sender_type'])); ?> • <?php echo date('M d, Y h:i A', strtotime($row['created_at'])); ?></div>
                      <div style="white-space:pre-wrap;"><?php echo htmlspecialchars($row['message']); ?></div>
                      <?php if (!empty($row['attachment_path'])): ?>
                        <div class="mt-2">
                          <a href="<?php echo SITE_URL . ltrim($row['attachment_path'], '/'); ?>" target="_blank" class="btn btn-sm btn-outline-secondary shadow-none">Open attachment</a>
                        </div>
                      <?php endif; ?>
                    </div>
                  <?php endforeach; ?>
                </div>
                <form method="POST" enctype="multipart/form-data">
                  <input type="hidden" name="ticket_id" value="<?php echo (int)$selected_ticket['id']; ?>">
                  <div class="mb-2">
                    <label class="form-label">Reply</label>
                    <textarea class="form-control shadow-none" name="reply_message" rows="3" required></textarea>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Attachment</label>
                    <input type="file" class="form-control shadow-none" name="reply_attachment" accept=".jpg,.jpeg,.png,.webp,.pdf">
                  </div>
                  <button type="submit" class="btn btn-dark shadow-none" name="reply_ticket">Send Reply</button>
                </form>
              <?php else: ?>
                <div class="text-center text-muted py-5">
                  <i class="bi bi-chat-square-text fs-1 d-block mb-2"></i>
                  Select a ticket to view the conversation.
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php require('inc/footer.php'); ?>
</body>
</html>
