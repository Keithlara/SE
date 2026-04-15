<?php
  require('inc/essentials.php');
  require('inc/db_config.php');
  require_once('inc/email_config.php');
  require_once('../inc/smtp_mailer.php');
  adminLogin();
  requireAdminPermission('service.center');

  function adminSupportFlashSet(string $type, string $message): void
  {
    $_SESSION['admin_support_flash'] = [
      'type' => $type,
      'message' => $message,
    ];
  }

  function adminSupportRedirect(string $query = ''): void
  {
    $url = 'support_center.php';
    if ($query !== '') {
      $url .= '?' . ltrim($query, '?');
    }
    redirect($url);
  }

  $tab = isset($_GET['tab']) ? trim($_GET['tab']) : 'tickets';
  if (!in_array($tab, ['tickets', 'notes', 'email'], true)) {
    $tab = 'tickets';
  }

  $message = '';
  $message_type = 'success';

  if (!empty($_SESSION['admin_support_flash']) && is_array($_SESSION['admin_support_flash'])) {
    $message = (string)($_SESSION['admin_support_flash']['message'] ?? '');
    $message_type = (string)($_SESSION['admin_support_flash']['type'] ?? 'success');
    unset($_SESSION['admin_support_flash']);
  }

  function send_reschedule_approved_email(mysqli $con, int $ticketId): bool
  {
    $res = select(
      "SELECT st.`id`, st.`booking_id`, st.`user_id`, st.`category`, st.`subject`,
              bo.`order_id`, bo.`check_in`, bo.`check_out`,
              bd.`room_name`, bd.`room_no`, bd.`user_name`,
              uc.`email`, uc.`name`
       FROM `support_tickets` st
       INNER JOIN `user_cred` uc ON uc.`id` = st.`user_id`
       LEFT JOIN `booking_order` bo ON bo.`booking_id` = st.`booking_id`
       LEFT JOIN `booking_details` bd ON bd.`booking_id` = st.`booking_id`
       WHERE st.`id`=? AND st.`category`='reschedule' LIMIT 1",
      [$ticketId],
      'i'
    );

    if (!$res || mysqli_num_rows($res) === 0) {
      return false;
    }

    $ticket = mysqli_fetch_assoc($res);
    $guestEmail = trim((string)($ticket['email'] ?? ''));
    $guestName = trim((string)($ticket['name'] ?? $ticket['user_name'] ?? 'Guest'));

    if (!filter_var($guestEmail, FILTER_VALIDATE_EMAIL)) {
      return false;
    }

    $siteName = defined('SITE_NAME') ? SITE_NAME : 'Travelers Place';
    $orderId = (string)($ticket['order_id'] ?? ('#' . (int)($ticket['booking_id'] ?? 0)));
    $checkIn = !empty($ticket['check_in']) ? date('F j, Y', strtotime((string)$ticket['check_in'])) : 'To be confirmed';
    $checkOut = !empty($ticket['check_out']) ? date('F j, Y', strtotime((string)$ticket['check_out'])) : 'To be confirmed';
    $roomLabel = trim((string)($ticket['room_name'] ?? 'Room'));
    $roomNo = trim((string)($ticket['room_no'] ?? ''));
    if ($roomNo !== '') {
      $roomLabel .= ' - Room ' . $roomNo;
    }

    $subject = 'Reschedule Approved - ' . $orderId;
    $safeGuestName = htmlspecialchars($guestName, ENT_QUOTES, 'UTF-8');
    $safeOrderId = htmlspecialchars($orderId, ENT_QUOTES, 'UTF-8');
    $safeRoom = htmlspecialchars($roomLabel, ENT_QUOTES, 'UTF-8');
    $safeTicketSubject = htmlspecialchars((string)($ticket['subject'] ?? 'Reschedule request'), ENT_QUOTES, 'UTF-8');

    $html = "
      <div style='font-family:Arial,sans-serif;max-width:640px;margin:0 auto;background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden'>
        <div style='background:#0f766e;padding:26px 30px'>
          <h1 style='margin:0;color:#fff;font-size:24px'>{$siteName}</h1>
          <p style='margin:6px 0 0;color:#ccfbf1;font-size:13px'>Booking schedule update</p>
        </div>
        <div style='padding:28px 30px'>
          <h2 style='margin:0 0 12px;color:#0f172a;font-size:22px'>Reschedule approved</h2>
          <p style='margin:0 0 18px;color:#475569;line-height:1.7'>Hello <strong>{$safeGuestName}</strong>, your reschedule request has been approved.</p>
          <table style='width:100%;border-collapse:collapse;margin-bottom:18px'>
            <tr><td style='padding:10px 12px;border-bottom:1px solid #e5e7eb;color:#64748b;font-size:13px'>Booking Reference</td><td style='padding:10px 12px;border-bottom:1px solid #e5e7eb;color:#111827;font-weight:700'>{$safeOrderId}</td></tr>
            <tr><td style='padding:10px 12px;border-bottom:1px solid #e5e7eb;color:#64748b;font-size:13px'>Room</td><td style='padding:10px 12px;border-bottom:1px solid #e5e7eb;color:#111827'>{$safeRoom}</td></tr>
            <tr><td style='padding:10px 12px;border-bottom:1px solid #e5e7eb;color:#64748b;font-size:13px'>Updated Schedule</td><td style='padding:10px 12px;border-bottom:1px solid #e5e7eb;color:#111827'>{$checkIn} to {$checkOut}</td></tr>
            <tr><td style='padding:10px 12px;color:#64748b;font-size:13px'>Request</td><td style='padding:10px 12px;color:#111827'>{$safeTicketSubject}</td></tr>
          </table>
          <p style='margin:0;color:#64748b;line-height:1.7'>If you have additional changes, please reply through the support center.</p>
        </div>
      </div>";

    return send_email_smtp_basic($guestEmail, $guestName, $subject, $html);
  }

  function send_reschedule_approved_email_with_changes(
    string $guestEmail,
    string $guestName,
    string $orderId,
    string $oldCheckIn,
    string $oldCheckOut,
    string $newCheckIn,
    string $newCheckOut,
    string $newRoomLabel
  ): bool {
    if (!filter_var($guestEmail, FILTER_VALIDATE_EMAIL)) {
      return false;
    }
    $siteName = defined('SITE_NAME') ? SITE_NAME : 'Travelers Place';
    $safeGuest = htmlspecialchars($guestName, ENT_QUOTES, 'UTF-8');
    $safeOrder = htmlspecialchars($orderId, ENT_QUOTES, 'UTF-8');
    $oldCheckInLabel = date('l, F j, Y', strtotime($oldCheckIn));
    $oldCheckOutLabel = date('l, F j, Y', strtotime($oldCheckOut));
    $newCheckInLabel = date('l, F j, Y', strtotime($newCheckIn));
    $newCheckOutLabel = date('l, F j, Y', strtotime($newCheckOut));
    $safeOldRange = htmlspecialchars($oldCheckInLabel . ' to ' . $oldCheckOutLabel, ENT_QUOTES, 'UTF-8');
    $safeNewRange = htmlspecialchars($newCheckInLabel . ' to ' . $newCheckOutLabel, ENT_QUOTES, 'UTF-8');
    $safeRoom = htmlspecialchars($newRoomLabel, ENT_QUOTES, 'UTF-8');
    $subject = 'Booking Rescheduled - ' . $safeOrder;
    $html = "
      <div style='font-family:Arial,sans-serif;max-width:640px;margin:0 auto;background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden'>
        <div style='background:#1d4ed8;padding:24px 28px;color:#fff'>
          <h2 style='margin:0;font-size:22px'>{$siteName}</h2>
          <p style='margin:6px 0 0;color:#dbeafe;font-size:13px'>Booking schedule update</p>
        </div>
        <div style='padding:24px 28px;color:#0f172a;line-height:1.7'>
          <p style='margin:0 0 12px'>Hello <strong>{$safeGuest}</strong>,</p>
          <p style='margin:0 0 18px'>Your booking has been successfully rescheduled.</p>
          <p style='margin:0 0 18px;color:#475569'>Your updated stay is now scheduled for <strong>{$safeNewRange}</strong>.</p>
          <table style='width:100%;border-collapse:collapse'>
            <tr><td style='padding:8px 0;color:#64748b'>Booking ID</td><td style='padding:8px 0;text-align:right'><strong>{$safeOrder}</strong></td></tr>
            <tr><td style='padding:8px 0;color:#64748b'>Old Schedule</td><td style='padding:8px 0;text-align:right'><strong>{$safeOldRange}</strong></td></tr>
            <tr><td style='padding:8px 0;color:#64748b'>New Schedule</td><td style='padding:8px 0;text-align:right'><strong>{$safeNewRange}</strong></td></tr>
            <tr><td style='padding:8px 0;color:#64748b'>New Room</td><td style='padding:8px 0;text-align:right'><strong>{$safeRoom}</strong></td></tr>
          </table>
        </div>
      </div>";

    return send_email_smtp_basic($guestEmail, $guestName, $subject, $html);
  }

  function process_reschedule_resolution(mysqli $con, int $ticketId, int $adminId, array $input, ?array &$emailData = null): array
  {
    $bookingId = (int)($input['booking_id'] ?? 0);
    if ($bookingId <= 0) {
      return [false, 'Reschedule requires a valid booking reference.'];
    }

    $newCheckIn = trim((string)($input['reschedule_check_in'] ?? ''));
    $newCheckOut = trim((string)($input['reschedule_check_out'] ?? ''));
    $newRoomId = (int)($input['reschedule_room_id'] ?? 0);
    $newRoomNo = trim((string)($input['reschedule_room_no'] ?? ''));

    if ($newCheckIn === '' || $newCheckOut === '' || $newRoomId <= 0) {
      return [false, 'Please complete new check-in, new check-out, and room selection.'];
    }
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $newCheckIn) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $newCheckOut)) {
      return [false, 'Please provide valid reschedule dates.'];
    }
    if (strtotime($newCheckOut) <= strtotime($newCheckIn)) {
      return [false, 'Check-out must be later than check-in for reschedule.'];
    }

    $bookingRes = select(
      "SELECT bo.`booking_id`, bo.`order_id`, bo.`room_id`, bo.`check_in`, bo.`check_out`, bo.`booking_status`, bo.`is_archived`,
              bd.`room_name`, bd.`room_no`, bd.`user_name`,
              uc.`email`, uc.`name`
       FROM `booking_order` bo
       LEFT JOIN `booking_details` bd ON bd.`booking_id` = bo.`booking_id`
       LEFT JOIN `user_cred` uc ON uc.`id` = bo.`user_id`
       WHERE bo.`booking_id`=? LIMIT 1",
      [$bookingId],
      'i'
    );
    if (!$bookingRes || mysqli_num_rows($bookingRes) === 0) {
      return [false, 'Booking record not found.'];
    }
    $booking = mysqli_fetch_assoc($bookingRes);
    if ((int)($booking['is_archived'] ?? 0) === 1) {
      return [false, 'Cannot reschedule an archived booking.'];
    }
    $status = strtolower(trim((string)($booking['booking_status'] ?? '')));
    if (!in_array($status, ['pending', 'booked'], true)) {
      return [false, 'Only pending or booked reservations can be rescheduled.'];
    }

    $roomRes = select(
      "SELECT `id`,`name`,`quantity` FROM `rooms` WHERE `id`=? AND `removed`=0 AND `is_archived`=0 LIMIT 1",
      [$newRoomId],
      'i'
    );
    if (!$roomRes || mysqli_num_rows($roomRes) === 0) {
      return [false, 'Selected room does not exist or is unavailable.'];
    }
    $room = mysqli_fetch_assoc($roomRes);

    $roomQuantity = max(0, (int)($room['quantity'] ?? 0));
    if ($roomQuantity <= 0) {
      return [false, 'The selected room has no available inventory configured.'];
    }

    $blockedNumbers = [];
    if (function_exists('appSchemaTableExists') && appSchemaTableExists($con, 'room_block_dates')) {
      $blockedRes = select(
        "SELECT `room_no`,`block_type`
         FROM `room_block_dates`
         WHERE `room_id`=? AND `status`='active'
           AND `end_date` > ? AND `start_date` < ?",
        [$newRoomId, $newCheckIn, $newCheckOut],
        'iss'
      );
      while ($blockedRes && $blockedRow = mysqli_fetch_assoc($blockedRes)) {
        $blockedRoomNo = trim((string)($blockedRow['room_no'] ?? ''));
        if ($blockedRoomNo === '') {
          return [false, 'The selected room is blocked for the chosen reschedule dates.'];
        }
        $blockedNumbers[$blockedRoomNo] = true;
      }
    }

    $overlapRes = select(
      "SELECT COUNT(*) AS total_overlap FROM `booking_order`
       WHERE `room_id`=? AND `is_archived`=0
         AND `booking_status` IN ('pending','booked')
         AND `booking_id`<>?
         AND `check_out` > ? AND `check_in` < ?",
      [$newRoomId, $bookingId, $newCheckIn, $newCheckOut],
      'iiss'
    );
    $overlapRow = ($overlapRes && mysqli_num_rows($overlapRes) > 0) ? mysqli_fetch_assoc($overlapRes) : ['total_overlap' => 0];
    $totalOverlap = (int)($overlapRow['total_overlap'] ?? 0);
    if ($totalOverlap >= $roomQuantity) {
      return [false, 'The selected room is unavailable for the chosen reschedule dates.'];
    }

    if ($newRoomNo !== '') {
      if (isset($blockedNumbers[$newRoomNo])) {
        return [false, 'The selected room number is blocked for the new schedule.'];
      }
      $roomNoRes = select(
        "SELECT bd.`booking_id`
         FROM `booking_details` bd
         INNER JOIN `booking_order` bo ON bo.`booking_id` = bd.`booking_id`
         WHERE bo.`room_id`=? AND bo.`is_archived`=0
           AND bo.`booking_status` IN ('pending','booked')
           AND bo.`booking_id`<>?
           AND bo.`check_out` > ? AND bo.`check_in` < ?
           AND TRIM(COALESCE(bd.`room_no`, '')) = ?
         LIMIT 1",
        [$newRoomId, $bookingId, $newCheckIn, $newCheckOut, $newRoomNo],
        'iisss'
      );
      if ($roomNoRes && mysqli_num_rows($roomNoRes) > 0) {
        return [false, 'The selected room number is not available for the new schedule.'];
      }
    }

    mysqli_begin_transaction($con);
    try {
      $updatedOrder = update(
        "UPDATE `booking_order` SET `check_in`=?, `check_out`=?, `room_id`=? WHERE `booking_id`=? LIMIT 1",
        [$newCheckIn, $newCheckOut, $newRoomId, $bookingId],
        'ssii'
      );
      if (!$updatedOrder) {
        throw new Exception('Failed to update booking schedule.');
      }

      $updatedDetails = update(
        "UPDATE `booking_details` SET `room_name`=?, `room_no`=? WHERE `booking_id`=? LIMIT 1",
        [$room['name'], $newRoomNo, $bookingId],
        'ssi'
      );
      if ($updatedDetails === false) {
        throw new Exception('Failed to update booking room details.');
      }

      $oldRange = date('F j, Y', strtotime((string)$booking['check_in'])) . ' to ' . date('F j, Y', strtotime((string)$booking['check_out']));
      $newRange = date('F j, Y', strtotime($newCheckIn)) . ' to ' . date('F j, Y', strtotime($newCheckOut));
      $oldRoom = trim((string)($booking['room_name'] ?? 'Room'));
      $oldRoomNo = trim((string)($booking['room_no'] ?? ''));
      if ($oldRoomNo !== '') {
        $oldRoom .= ' (Room ' . $oldRoomNo . ')';
      }
      $newRoomLabel = trim((string)$room['name']);
      if ($newRoomNo !== '') {
        $newRoomLabel .= ' (Room ' . $newRoomNo . ')';
      }

      createBookingHistoryEntry(
        $bookingId,
        'reschedule',
        'Booking rescheduled by admin',
        "Schedule updated from {$oldRange} to {$newRange}; room updated from {$oldRoom} to {$newRoomLabel}."
      );

      if (function_exists('addSupportTicketMessage')) {
        @addSupportTicketMessage(
          $ticketId,
          'system',
          $adminId,
          'System',
          "Booking rescheduled by admin. Old: {$oldRange} ({$oldRoom}) | New: {$newRange} ({$newRoomLabel})"
        );
      }

      mysqli_commit($con);

      $guestName = trim((string)($booking['name'] ?? $booking['user_name'] ?? 'Guest'));
      if ($guestName === '') {
        $guestName = 'Guest';
      }
      $emailData = [
        'email' => (string)($booking['email'] ?? ''),
        'name' => $guestName,
        'order_id' => (string)($booking['order_id'] ?? ('#' . $bookingId)),
        'old_check_in' => date('F j, Y', strtotime((string)$booking['check_in'])),
        'old_check_out' => date('F j, Y', strtotime((string)$booking['check_out'])),
        'new_check_in' => date('F j, Y', strtotime($newCheckIn)),
        'new_check_out' => date('F j, Y', strtotime($newCheckOut)),
        'new_room' => $newRoomLabel,
      ];

      return [true, 'Booking rescheduled successfully.'];
    } catch (Throwable $e) {
      mysqli_rollback($con);
      return [false, $e->getMessage()];
    }
  }

  if (isset($_POST['update_ticket_status'])) {
    $ticket_id = (int)($_POST['ticket_id'] ?? 0);
    $status = trim((string)($_POST['status'] ?? 'open'));
    $escalated = !empty($_POST['escalated']) ? 1 : 0;

    if ($ticket_id > 0 && isset(supportTicketStatuses()[$status])) {
      $ticketMetaRes = select("SELECT `booking_id`,`category`,`status` FROM `support_tickets` WHERE `id`=? LIMIT 1", [$ticket_id], 'i');
      $ticketMeta = ($ticketMetaRes && mysqli_num_rows($ticketMetaRes) === 1) ? mysqli_fetch_assoc($ticketMetaRes) : null;
      $previousStatus = (string)($ticketMeta['status'] ?? '');
      $rescheduleEmail = null;

      if ($ticketMeta && ($ticketMeta['category'] ?? '') === 'reschedule' && $status === 'resolved' && strtolower($previousStatus) !== 'resolved') {
        [$okReschedule, $resMessage] = process_reschedule_resolution(
          $con,
          $ticket_id,
          (int)($_SESSION['adminId'] ?? 0),
          $_POST,
          $rescheduleEmail
        );
        if (!$okReschedule) {
          $message = $resMessage !== '' ? $resMessage : 'Unable to complete reschedule.';
          $message_type = 'error';
          $tab = 'tickets';
          goto support_ticket_status_done;
        }
      }

      update(
        "UPDATE `support_tickets` SET `status`=?, `escalated`=?, `updated_at`=NOW() WHERE `id`=?",
        [$status, $escalated, $ticket_id],
        'sii'
      );
      createBookingHistoryEntry(
        (int)($_POST['booking_id'] ?? 0),
        'support_status',
        'Support ticket status updated',
        'Ticket #' . $ticket_id . ' was moved to ' . ucfirst($status) . '.'
      );
      if ($ticketMeta && ($ticketMeta['category'] ?? '') === 'reschedule' && $status === 'resolved' && strtolower($previousStatus) !== 'resolved') {
        if (is_array($rescheduleEmail) && !empty($rescheduleEmail['email'])) {
          send_reschedule_approved_email_with_changes(
            (string)$rescheduleEmail['email'],
            (string)$rescheduleEmail['name'],
            (string)$rescheduleEmail['order_id'],
            (string)$rescheduleEmail['old_check_in'],
            (string)$rescheduleEmail['old_check_out'],
            (string)$rescheduleEmail['new_check_in'],
            (string)$rescheduleEmail['new_check_out'],
            (string)$rescheduleEmail['new_room']
          );
        } else {
          send_reschedule_approved_email($con, $ticket_id);
        }
        $message = 'Reschedule approved successfully. The booking was updated and the guest was notified by email.';
      } else {
        $message = 'Ticket status updated.';
      }
    } else {
      $message = 'Invalid ticket update.';
      $message_type = 'error';
    }
support_ticket_status_done:
    if ($message_type !== 'error') {
      adminSupportFlashSet($message_type, $message);
      adminSupportRedirect('tab=tickets&ticket=' . $ticket_id);
    }
    $tab = 'tickets';
  }

  if (isset($_POST['reply_ticket'])) {
    $ticket_id = (int)($_POST['ticket_id'] ?? 0);
    $reply_message = trim((string)($_POST['reply_message'] ?? ''));
    $next_status = trim((string)($_POST['next_status'] ?? 'pending'));
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
      $sender_type = ($_SESSION['adminRole'] ?? 'admin') === 'staff' ? 'staff' : 'admin';
      $was_duplicate_reply = false;
      $saved = addSupportTicketMessage(
        $ticket_id,
        $sender_type,
        (int)($_SESSION['adminId'] ?? 0),
        (string)($_SESSION['adminName'] ?? 'Admin'),
        $reply_message,
        [
          'attachment_path' => $attachment_path,
          'next_status' => $next_status,
        ]
        ,
        $was_duplicate_reply
      );

      if ($saved) {
        if (!$was_duplicate_reply) {
          $ticketMetaRes = select("SELECT `booking_id`,`user_id`,`subject` FROM `support_tickets` WHERE `id`=? LIMIT 1", [$ticket_id], 'i');
          $ticketMeta = mysqli_fetch_assoc($ticketMetaRes);
          if ($ticketMeta) {
            createNotification(
              $con,
              (int)$ticketMeta['user_id'],
              (int)($ticketMeta['booking_id'] ?? 0),
              'Customer service updated your ticket: ' . ($ticketMeta['subject'] ?? 'Support request')
            );
            if (!empty($ticketMeta['booking_id'])) {
              createBookingHistoryEntry(
                (int)$ticketMeta['booking_id'],
                'support_reply',
                'Support team replied',
                sanitizeMultilineText($reply_message, 180)
              );
            }
          }
        }
        $message = $was_duplicate_reply ? 'That reply is already saved on this ticket.' : 'Reply sent successfully.';
      } else {
        $message = 'Unable to save the reply.';
        $message_type = 'error';
      }
    }
    if ($message_type !== 'error') {
      adminSupportFlashSet($message_type, $message);
      adminSupportRedirect('tab=tickets&ticket=' . $ticket_id);
    }
    $tab = 'tickets';
  }

  if (isset($_POST['add_guest_note'])) {
    $user_id = (int)($_POST['user_id'] ?? 0);
    $booking_id = !empty($_POST['booking_id']) ? (int)$_POST['booking_id'] : null;
    $note_type = trim((string)($_POST['note_type'] ?? 'internal'));
    $title = trim((string)($_POST['title'] ?? ''));
    $note = trim((string)($_POST['note'] ?? ''));

    if ($user_id > 0 && $title !== '' && $note !== '') {
      insert(
        "INSERT INTO `guest_notes` (`user_id`,`booking_id`,`note_type`,`title`,`note`,`created_by`) VALUES (?,?,?,?,?,?)",
        [$user_id, $booking_id, $note_type, sanitizeMultilineText($title, 150), sanitizeMultilineText($note, 2000), (int)($_SESSION['adminId'] ?? 0)],
        'iisssi'
      );
      $message = 'Guest note saved.';
    } else {
      $message = 'Please complete the guest note form.';
      $message_type = 'error';
    }
    if ($message_type !== 'error') {
      adminSupportFlashSet($message_type, $message);
      adminSupportRedirect('tab=notes');
    }
    $tab = 'notes';
  }

  if (isset($_POST['add_canned_reply']) && ($_SESSION['adminRole'] ?? 'admin') === 'admin') {
    $title = trim((string)($_POST['title'] ?? ''));
    $category = trim((string)($_POST['category'] ?? 'general'));
    $reply_text = trim((string)($_POST['reply_text'] ?? ''));
    if ($title !== '' && $reply_text !== '') {
      insert(
        "INSERT INTO `support_canned_replies` (`title`,`category`,`reply_text`,`is_active`,`created_by`) VALUES (?,?,?,1,?)",
        [sanitizeMultilineText($title, 120), $category, sanitizeMultilineText($reply_text, 2000), (int)($_SESSION['adminId'] ?? 0)],
        'sssi'
      );
      $message = 'Canned reply added.';
    } else {
      $message = 'Please complete the canned reply form.';
      $message_type = 'error';
    }
    if ($message_type !== 'error') {
      adminSupportFlashSet($message_type, $message);
      adminSupportRedirect('tab=tickets');
    }
    $tab = 'tickets';
  }

  $ticket_search = trim((string)($_GET['ticket_search'] ?? ''));
  $note_search = trim((string)($_GET['note_search'] ?? ''));
  $email_search = trim((string)($_GET['email_search'] ?? ''));
  $ticket_id = isset($_GET['ticket']) ? (int)$_GET['ticket'] : 0;

  $ticket_counts = ['open' => 0, 'pending' => 0, 'resolved' => 0, 'escalated' => 0];
  $ticketStatsRes = mysqli_query($con, "SELECT `status`, COUNT(*) AS c FROM `support_tickets` WHERE `is_archived` = 0 GROUP BY `status`");
  if ($ticketStatsRes) {
    while ($row = mysqli_fetch_assoc($ticketStatsRes)) {
      if (isset($ticket_counts[$row['status']])) {
        $ticket_counts[$row['status']] = (int)$row['c'];
      }
    }
  }

  $ticket_sql = "
    SELECT st.*, uc.name AS guest_name, uc.email AS guest_email, bo.order_id,
      (SELECT COUNT(*) FROM support_ticket_messages stm WHERE stm.ticket_id = st.id AND stm.seen_by_staff = 0) AS unread_staff
    FROM `support_tickets` st
    INNER JOIN `user_cred` uc ON uc.id = st.user_id
    LEFT JOIN `booking_order` bo ON bo.booking_id = st.booking_id
    WHERE st.is_archived = 0 AND (
      st.ticket_code LIKE ?
      OR st.subject LIKE ?
      OR uc.name LIKE ?
      OR uc.email LIKE ?
    )
    ORDER BY FIELD(st.status,'open','pending','escalated','resolved'), st.last_reply_at DESC, st.created_at DESC
  ";
  $ticket_like = '%' . $ticket_search . '%';
  $ticket_res = select($ticket_sql, [$ticket_like, $ticket_like, $ticket_like, $ticket_like], 'ssss');

  $selected_ticket = null;
  $ticket_messages = [];

  if ($ticket_id > 0) {
    $ticket_detail_res = select(
      "SELECT st.*, uc.name AS guest_name, uc.email AS guest_email, bo.order_id, bo.booking_status, bo.check_in, bo.check_out,
              bd.room_name, bd.room_no, bo.room_id
       FROM `support_tickets` st
       INNER JOIN `user_cred` uc ON uc.id = st.user_id
       LEFT JOIN `booking_order` bo ON bo.booking_id = st.booking_id
       LEFT JOIN `booking_details` bd ON bd.booking_id = st.booking_id
       WHERE st.id=? AND st.is_archived = 0 LIMIT 1",
      [$ticket_id],
      'i'
    );
    if ($ticket_detail_res && mysqli_num_rows($ticket_detail_res) === 1) {
      $selected_ticket = mysqli_fetch_assoc($ticket_detail_res);
      markSupportTicketSeenByStaff($ticket_id);
      $message_res = select(
        "SELECT * FROM `support_ticket_messages` WHERE `ticket_id`=? ORDER BY `created_at` ASC, `id` ASC",
        [$ticket_id],
        'i'
      );
      while ($row = mysqli_fetch_assoc($message_res)) {
        $ticket_messages[] = $row;
      }
    }
  }

  $canned_replies = [];
  $canned_res = mysqli_query($con, "SELECT * FROM `support_canned_replies` WHERE `is_active`=1 ORDER BY `category`,`title`");
  if ($canned_res) {
    while ($row = mysqli_fetch_assoc($canned_res)) {
      $canned_replies[] = $row;
    }
  }

  $notes = [];
  $note_like = '%' . $note_search . '%';
  $notes_res = select(
    "SELECT gn.*, uc.name AS guest_name, bo.order_id, au.username AS created_by_name
     FROM `guest_notes` gn
     INNER JOIN `user_cred` uc ON uc.id = gn.user_id
     LEFT JOIN `booking_order` bo ON bo.booking_id = gn.booking_id
     LEFT JOIN `admin_users` au ON au.id = gn.created_by
     WHERE uc.name LIKE ? OR gn.title LIKE ? OR gn.note LIKE ?
     ORDER BY gn.updated_at DESC, gn.id DESC",
    [$note_like, $note_like, $note_like],
    'sss'
  );
  while ($row = mysqli_fetch_assoc($notes_res)) {
    $notes[] = $row;
  }

  $guest_options = [];
  $guest_option_res = mysqli_query($con, "SELECT `id`,`name`,`email` FROM `user_cred` ORDER BY `name` ASC");
  if ($guest_option_res) {
    while ($row = mysqli_fetch_assoc($guest_option_res)) {
      $guest_options[] = $row;
    }
  }

  $active_rooms = [];
  $active_rooms_res = mysqli_query($con, "SELECT `id`,`name` FROM `rooms` WHERE `removed`=0 AND `is_archived`=0 ORDER BY `name` ASC");
  if ($active_rooms_res) {
    while ($row = mysqli_fetch_assoc($active_rooms_res)) {
      $active_rooms[] = $row;
    }
  }

  $email_logs = [];
  $email_like = '%' . $email_search . '%';
  $email_res = select(
    "SELECT el.*, uc.name AS guest_name, bo.order_id
     FROM `email_logs` el
     LEFT JOIN `user_cred` uc ON uc.id = el.related_user_id
     LEFT JOIN `booking_order` bo ON bo.booking_id = el.related_booking_id
     WHERE el.recipient_email LIKE ? OR el.subject LIKE ? OR el.template_key LIKE ?
     ORDER BY el.created_at DESC, el.id DESC
     LIMIT 150",
    [$email_like, $email_like, $email_like],
    'sss'
  );
  while ($row = mysqli_fetch_assoc($email_res)) {
    $email_logs[] = $row;
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Service Center</title>
  <?php require('inc/links.php'); ?>
  <style>
    .service-shell .nav-link { border-radius: 999px !important; }
    .service-stat {
      border: 1px solid rgba(148,163,184,0.18);
      border-radius: 18px;
      padding: 14px 16px;
      background: linear-gradient(180deg, rgba(255,255,255,0.96) 0%, rgba(248,250,252,0.92) 100%);
      box-shadow: 0 14px 32px rgba(15,23,42,0.05);
    }
    .service-stat .count { font-size: 1.4rem; font-weight: 700; color: var(--admin-text); }
    .service-ticket-link {
      display: block; padding: 14px 16px; border-radius: 16px; text-decoration: none;
      border: 1px solid rgba(148,163,184,0.16); background: rgba(255,255,255,0.9); color: inherit;
      transition: all 0.18s ease;
    }
    .service-ticket-link:hover, .service-ticket-link.active { transform: translateY(-1px); border-color: rgba(var(--admin-accent-rgb),0.35); background: rgba(var(--admin-accent-rgb),0.08); }
    .ticket-thread { display: flex; flex-direction: column; gap: 14px; }
    .ticket-bubble { border-radius: 16px; padding: 14px 16px; max-width: 92%; }
    .ticket-bubble.staff { align-self: flex-end; background: rgba(var(--admin-accent-rgb),0.14); }
    .ticket-bubble.guest { align-self: flex-start; background: rgba(15,23,42,0.06); }
    .ticket-bubble.system { align-self: center; background: rgba(249,115,22,0.12); }
    .ticket-meta { font-size: 0.76rem; color: var(--admin-text-muted); margin-bottom: 6px; }
    .service-note-card, .email-log-row {
      border: 1px solid rgba(148,163,184,0.16); border-radius: 16px; padding: 14px 16px; background: rgba(255,255,255,0.92);
    }
    .email-status-pill, .ticket-status-pill {
      display: inline-flex; align-items: center; padding: 5px 10px; border-radius: 999px; font-size: 0.75rem; font-weight: 700;
    }
    .status-open, .status-queued { background: rgba(37,99,235,0.14); color: #1d4ed8; }
    .status-pending { background: rgba(245,158,11,0.16); color: #b45309; }
    .status-escalated, .status-failed { background: rgba(239,68,68,0.14); color: #b91c1c; }
    .status-resolved, .status-sent { background: rgba(16,185,129,0.16); color: #047857; }
  </style>
</head>
<body class="bg-light">
  <?php require('inc/header.php'); ?>

  <div class="container-fluid service-shell" id="main-content">
    <div class="p-4">
      <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
          <h3 class="mb-1">Service Center</h3>
          <p class="text-muted mb-0">Manage support tickets, guest notes, and outgoing email history in one cleaner workspace.</p>
        </div>
        <div class="d-flex gap-3 flex-wrap">
          <div class="service-stat">
            <div class="text-muted small text-uppercase fw-semibold">Open Tickets</div>
            <div class="count"><?php echo $ticket_counts['open']; ?></div>
          </div>
          <div class="service-stat">
            <div class="text-muted small text-uppercase fw-semibold">Pending</div>
            <div class="count"><?php echo $ticket_counts['pending']; ?></div>
          </div>
          <div class="service-stat">
            <div class="text-muted small text-uppercase fw-semibold">Escalated</div>
            <div class="count"><?php echo $ticket_counts['escalated']; ?></div>
          </div>
        </div>
      </div>

      <?php if ($message !== '') { alert($message_type, $message); } ?>

      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <ul class="nav nav-tabs mb-4">
            <li class="nav-item"><a class="nav-link <?php echo $tab === 'tickets' ? 'active' : ''; ?>" href="?tab=tickets">Tickets</a></li>
            <li class="nav-item"><a class="nav-link <?php echo $tab === 'notes' ? 'active' : ''; ?>" href="?tab=notes">Guest Notes</a></li>
            <li class="nav-item"><a class="nav-link <?php echo $tab === 'email' ? 'active' : ''; ?>" href="?tab=email">Email Logs</a></li>
          </ul>

          <?php if ($tab === 'tickets'): ?>
            <div class="row g-4">
              <div class="col-xl-4">
                <form class="mb-3" method="GET">
                  <input type="hidden" name="tab" value="tickets">
                  <input type="text" class="form-control shadow-none" name="ticket_search" value="<?php echo htmlspecialchars($ticket_search); ?>" placeholder="Search ticket, guest, or email...">
                </form>
                <div class="d-flex flex-column gap-2">
                  <?php while ($ticket = mysqli_fetch_assoc($ticket_res)): ?>
                    <a href="?tab=tickets&ticket=<?php echo (int)$ticket['id']; ?>&ticket_search=<?php echo urlencode($ticket_search); ?>" class="service-ticket-link <?php echo $ticket_id === (int)$ticket['id'] ? 'active' : ''; ?>">
                      <div class="d-flex justify-content-between gap-2">
                        <div class="fw-semibold text-dark"><?php echo htmlspecialchars($ticket['subject']); ?></div>
                        <?php if ((int)$ticket['unread_staff'] > 0): ?>
                          <span class="badge bg-danger rounded-pill"><?php echo (int)$ticket['unread_staff']; ?></span>
                        <?php endif; ?>
                      </div>
                      <div class="small text-muted mt-1"><?php echo htmlspecialchars($ticket['ticket_code']); ?> • <?php echo htmlspecialchars($ticket['guest_name']); ?></div>
                      <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="ticket-status-pill status-<?php echo htmlspecialchars($ticket['status']); ?>"><?php echo ucfirst($ticket['status']); ?></span>
                        <span class="small text-muted"><?php echo date('M d, h:i A', strtotime($ticket['last_reply_at'] ?: $ticket['created_at'])); ?></span>
                      </div>
                    </a>
                  <?php endwhile; ?>
                </div>
              </div>
              <div class="col-xl-8">
                <?php if ($selected_ticket): ?>
                  <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
                    <div>
                      <div class="text-muted small"><?php echo htmlspecialchars($selected_ticket['ticket_code']); ?></div>
                      <h4 class="mb-1"><?php echo htmlspecialchars($selected_ticket['subject']); ?></h4>
                      <div class="text-muted small"><?php echo htmlspecialchars($selected_ticket['guest_name']); ?> • <?php echo htmlspecialchars($selected_ticket['guest_email']); ?></div>
                      <?php if (!empty($selected_ticket['order_id'])): ?>
                        <div class="text-muted small mt-1">Booking Ref: <?php echo htmlspecialchars($selected_ticket['order_id']); ?></div>
                      <?php endif; ?>
                    </div>
                    <form method="POST" class="row g-2 align-items-end">
                      <input type="hidden" name="ticket_id" value="<?php echo (int)$selected_ticket['id']; ?>">
                      <input type="hidden" name="booking_id" value="<?php echo (int)($selected_ticket['booking_id'] ?? 0); ?>">
                      <?php if (($selected_ticket['category'] ?? '') === 'reschedule'): ?>
                        <div class="col-12">
                          <div class="border rounded-3 p-3 bg-light-subtle">
                            <div class="fw-semibold mb-2">Reschedule Details</div>
                            <div class="small text-muted mb-2">
                              Current Room: <strong><?php echo htmlspecialchars((string)($selected_ticket['room_name'] ?? 'N/A')); ?></strong>
                              <?php if (!empty($selected_ticket['room_no'])): ?>
                                • Room No: <strong><?php echo htmlspecialchars((string)$selected_ticket['room_no']); ?></strong>
                              <?php else: ?>
                                • Room No: <strong>Not assigned</strong>
                              <?php endif; ?>
                              <br>
                              Current Stay:
                              <strong>
                                <?php
                                  echo !empty($selected_ticket['check_in']) ? date('M d, Y', strtotime((string)$selected_ticket['check_in'])) : 'N/A';
                                  echo ' - ';
                                  echo !empty($selected_ticket['check_out']) ? date('M d, Y', strtotime((string)$selected_ticket['check_out'])) : 'N/A';
                                ?>
                              </strong>
                            </div>
                            <div class="row g-2">
                              <div class="col-md-3">
                                <label class="form-label small text-muted mb-1">New Check-in</label>
                                <input type="date" class="form-control shadow-none" name="reschedule_check_in" value="<?php echo !empty($selected_ticket['check_in']) ? htmlspecialchars((string)$selected_ticket['check_in']) : ''; ?>">
                              </div>
                              <div class="col-md-3">
                                <label class="form-label small text-muted mb-1">New Check-out</label>
                                <input type="date" class="form-control shadow-none" name="reschedule_check_out" value="<?php echo !empty($selected_ticket['check_out']) ? htmlspecialchars((string)$selected_ticket['check_out']) : ''; ?>">
                              </div>
                              <div class="col-md-4">
                                <label class="form-label small text-muted mb-1">Room Selection</label>
                                <select class="form-select shadow-none" name="reschedule_room_id">
                                  <option value="">Select room...</option>
                                  <?php foreach ($active_rooms as $room): ?>
                                    <option value="<?php echo (int)$room['id']; ?>" <?php echo ((int)$room['id'] === (int)($selected_ticket['room_id'] ?? 0)) ? 'selected' : ''; ?>>
                                      <?php echo htmlspecialchars((string)$room['name']); ?>
                                    </option>
                                  <?php endforeach; ?>
                                </select>
                              </div>
                              <div class="col-md-2">
                                <label class="form-label small text-muted mb-1">Room No. (opt)</label>
                                <input type="text" class="form-control shadow-none" name="reschedule_room_no" placeholder="e.g. 3" value="<?php echo htmlspecialchars((string)($selected_ticket['room_no'] ?? '')); ?>">
                              </div>
                            </div>
                          </div>
                        </div>
                      <?php endif; ?>
                      <div class="col-auto">
                        <label class="form-label small text-muted mb-1">Status</label>
                        <select class="form-select shadow-none" name="status">
                          <?php foreach (supportTicketStatuses() as $value => $label): ?>
                            <option value="<?php echo $value; ?>" <?php echo $selected_ticket['status'] === $value ? 'selected' : ''; ?>><?php echo $label; ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                      <div class="col-auto">
                        <label class="form-label small text-muted mb-1">Escalate</label>
                        <div class="form-check pt-2">
                          <input class="form-check-input" type="checkbox" name="escalated" value="1" <?php echo !empty($selected_ticket['escalated']) ? 'checked' : ''; ?>>
                        </div>
                      </div>
                      <div class="col-auto">
                        <button class="btn btn-primary shadow-none" type="submit" name="update_ticket_status">Update</button>
                      </div>
                      <div class="col-auto">
                        <button class="btn btn-warning shadow-none" type="button" onclick="archiveSupportTicket(<?php echo (int)$selected_ticket['id']; ?>)">Archive</button>
                      </div>
                    </form>
                  </div>

                  <div class="ticket-thread mb-4">
                    <?php foreach ($ticket_messages as $messageRow): ?>
                      <?php $bubble_class = $messageRow['sender_type'] === 'guest' ? 'guest' : (($messageRow['sender_type'] === 'system') ? 'system' : 'staff'); ?>
                      <div class="ticket-bubble <?php echo $bubble_class; ?>">
                        <div class="ticket-meta"><?php echo htmlspecialchars($messageRow['sender_name'] ?: ucfirst($messageRow['sender_type'])); ?> • <?php echo date('M d, Y h:i A', strtotime($messageRow['created_at'])); ?></div>
                        <div style="white-space:pre-wrap;"><?php echo htmlspecialchars($messageRow['message']); ?></div>
                        <?php if (!empty($messageRow['attachment_path'])): ?>
                          <div class="mt-2">
                            <a href="<?php echo SITE_URL . ltrim($messageRow['attachment_path'], '/'); ?>" target="_blank" class="btn btn-sm btn-outline-primary shadow-none">Open attachment</a>
                          </div>
                        <?php endif; ?>
                      </div>
                    <?php endforeach; ?>
                  </div>

                  <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="ticket_id" value="<?php echo (int)$selected_ticket['id']; ?>">
                    <div class="row g-3">
                      <div class="col-md-8">
                        <label class="form-label">Reply</label>
                        <textarea class="form-control shadow-none" name="reply_message" rows="4" placeholder="Reply to the guest..." required></textarea>
                      </div>
                      <div class="col-md-4">
                        <label class="form-label">Quick Reply</label>
                        <select class="form-select shadow-none mb-2" onchange="if(this.value){ this.form.reply_message.value = this.value; }">
                          <option value="">Select canned reply...</option>
                          <?php foreach ($canned_replies as $reply): ?>
                            <option value="<?php echo htmlspecialchars($reply['reply_text'], ENT_QUOTES); ?>"><?php echo htmlspecialchars($reply['title']); ?></option>
                          <?php endforeach; ?>
                        </select>
                        <label class="form-label">Next Status</label>
                        <select class="form-select shadow-none mb-2" name="next_status">
                          <?php foreach (supportTicketStatuses() as $value => $label): ?>
                            <option value="<?php echo $value; ?>" <?php echo $value === 'pending' ? 'selected' : ''; ?>><?php echo $label; ?></option>
                          <?php endforeach; ?>
                        </select>
                        <label class="form-label">Attachment</label>
                        <input type="file" class="form-control shadow-none mb-2" name="reply_attachment" accept=".jpg,.jpeg,.png,.webp,.pdf">
                        <button type="submit" class="btn btn-primary shadow-none w-100" name="reply_ticket">Send Reply</button>
                      </div>
                    </div>
                  </form>

                  <?php if (($_SESSION['adminRole'] ?? 'admin') === 'admin'): ?>
                    <hr class="my-4">
                    <div class="row g-3 align-items-start">
                      <div class="col-md-12">
                        <h5 class="mb-3">Canned Replies</h5>
                      </div>
                      <div class="col-lg-5">
                        <form method="POST">
                          <div class="mb-2">
                            <input type="text" class="form-control shadow-none" name="title" placeholder="Reply title" required>
                          </div>
                          <div class="mb-2">
                            <select class="form-select shadow-none" name="category">
                              <?php foreach (supportTicketCategories() as $value => $label): ?>
                                <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                              <?php endforeach; ?>
                            </select>
                          </div>
                          <div class="mb-2">
                            <textarea class="form-control shadow-none" name="reply_text" rows="4" placeholder="Saved reply text..." required></textarea>
                          </div>
                          <button type="submit" class="btn btn-outline-primary shadow-none" name="add_canned_reply">Save Canned Reply</button>
                        </form>
                      </div>
                      <div class="col-lg-7">
                        <div class="row g-2">
                          <?php foreach ($canned_replies as $reply): ?>
                            <div class="col-md-6">
                              <div class="service-note-card h-100">
                                <div class="fw-semibold"><?php echo htmlspecialchars($reply['title']); ?></div>
                                <div class="small text-muted mb-2"><?php echo htmlspecialchars($reply['category']); ?></div>
                                <div class="small text-secondary"><?php echo nl2br(htmlspecialchars($reply['reply_text'])); ?></div>
                              </div>
                            </div>
                          <?php endforeach; ?>
                        </div>
                      </div>
                    </div>
                  <?php endif; ?>
                <?php else: ?>
                  <div class="text-center py-5 text-muted">
                    <i class="bi bi-chat-square-text fs-1 d-block mb-2"></i>
                    Select a ticket from the left to review the conversation.
                  </div>
                <?php endif; ?>
              </div>
            </div>
          <?php elseif ($tab === 'notes'): ?>
            <div class="row g-4">
              <div class="col-lg-4">
                <div class="service-note-card">
                  <h5 class="mb-3">Add Guest Note</h5>
                  <form method="POST">
                    <div class="mb-2">
                      <label class="form-label">Guest</label>
                      <select class="form-select shadow-none" name="user_id" required>
                        <option value="">Select guest...</option>
                        <?php foreach ($guest_options as $guest): ?>
                          <option value="<?php echo (int)$guest['id']; ?>"><?php echo htmlspecialchars($guest['name'] . ' • ' . $guest['email']); ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="mb-2">
                      <label class="form-label">Booking ID</label>
                      <input type="number" class="form-control shadow-none" name="booking_id" placeholder="Optional">
                    </div>
                    <div class="mb-2">
                      <label class="form-label">Flag Type</label>
                      <select class="form-select shadow-none" name="note_type">
                        <option value="vip">VIP</option>
                        <option value="watch">Frequent canceller / watchlist</option>
                        <option value="request">Special request</option>
                        <option value="info">Helpful info</option>
                        <option value="internal" selected>Internal note</option>
                      </select>
                    </div>
                    <div class="mb-2">
                      <label class="form-label">Title</label>
                      <input type="text" class="form-control shadow-none" name="title" required>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Note</label>
                      <textarea class="form-control shadow-none" name="note" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary shadow-none" name="add_guest_note">Save Note</button>
                  </form>
                </div>
              </div>
              <div class="col-lg-8">
                <form class="mb-3" method="GET">
                  <input type="hidden" name="tab" value="notes">
                  <input type="text" class="form-control shadow-none" name="note_search" value="<?php echo htmlspecialchars($note_search); ?>" placeholder="Search guest notes...">
                </form>
                <div class="row g-3">
                  <?php foreach ($notes as $note): ?>
                    <div class="col-md-6">
                      <div class="service-note-card h-100">
                        <div class="d-flex justify-content-between gap-2 mb-2">
                          <div class="fw-semibold"><?php echo htmlspecialchars($note['title']); ?></div>
                          <span class="ticket-status-pill status-<?php echo $note['note_type'] === 'vip' ? 'resolved' : ($note['note_type'] === 'watch' ? 'escalated' : 'open'); ?>"><?php echo htmlspecialchars(ucfirst($note['note_type'])); ?></span>
                        </div>
                        <div class="small text-muted mb-2"><?php echo htmlspecialchars($note['guest_name']); ?><?php echo !empty($note['order_id']) ? ' • ' . htmlspecialchars($note['order_id']) : ''; ?></div>
                        <div class="small text-secondary" style="white-space:pre-wrap;"><?php echo htmlspecialchars($note['note']); ?></div>
                        <div class="small text-muted mt-3">Saved by <?php echo htmlspecialchars($note['created_by_name'] ?: 'Admin'); ?> • <?php echo date('M d, Y h:i A', strtotime($note['updated_at'])); ?></div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          <?php else: ?>
            <form class="mb-3" method="GET">
              <input type="hidden" name="tab" value="email">
              <input type="text" class="form-control shadow-none" name="email_search" value="<?php echo htmlspecialchars($email_search); ?>" placeholder="Search recipient, subject, or template...">
            </form>
            <div class="d-flex flex-column gap-2">
              <?php foreach ($email_logs as $log): ?>
                <div class="email-log-row">
                  <div class="d-flex justify-content-between flex-wrap gap-3">
                    <div>
                      <div class="fw-semibold"><?php echo htmlspecialchars($log['subject']); ?></div>
                      <div class="small text-muted"><?php echo htmlspecialchars($log['recipient_email']); ?><?php echo !empty($log['guest_name']) ? ' • ' . htmlspecialchars($log['guest_name']) : ''; ?></div>
                    </div>
                    <div class="text-end">
                      <span class="email-status-pill status-<?php echo htmlspecialchars($log['status']); ?>"><?php echo ucfirst($log['status']); ?></span>
                      <div class="small text-muted mt-1"><?php echo date('M d, Y h:i A', strtotime($log['created_at'])); ?></div>
                    </div>
                  </div>
                  <div class="small text-secondary mt-2">
                    Template: <strong><?php echo htmlspecialchars($log['template_key']); ?></strong>
                    <?php if (!empty($log['order_id'])): ?>
                      • Booking: <strong><?php echo htmlspecialchars($log['order_id']); ?></strong>
                    <?php endif; ?>
                    • Triggered by: <strong><?php echo htmlspecialchars($log['triggered_by']); ?></strong>
                  </div>
                  <?php if (!empty($log['error_message'])): ?>
                    <div class="alert alert-danger py-2 px-3 mt-2 mb-0"><?php echo htmlspecialchars($log['error_message']); ?></div>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <?php require('inc/scripts.php'); ?>
  <script>
    function archiveSupportTicket(id){
      Swal.fire({
        title: 'Archive support ticket?',
        text: 'This ticket and its replies will move to Archives and can be restored later.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, archive it',
        cancelButtonText: 'Cancel',
        reverseButtons: true
      }).then((result) => {
        if (!result.isConfirmed) return;

        const formData = new FormData();
        formData.append('action', 'archive');
        formData.append('type', 'ticket');
        formData.append('id', id);

        fetch('ajax/archive.php', { method: 'POST', body: formData })
          .then(r => r.json())
          .then(data => {
            if (data.status === 'success') {
              Swal.fire({ icon: 'success', title: 'Archived', text: data.message, timer: 1800, showConfirmButton: false })
                .then(() => { window.location.href = 'support_center.php?tab=tickets'; });
            } else {
              Swal.fire('Error', data.message || 'Failed to archive ticket', 'error');
            }
          })
          .catch(() => Swal.fire('Error', 'Failed to archive ticket', 'error'));
      });
    }
  </script>
</body>
</html>
