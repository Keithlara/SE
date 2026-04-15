<?php

function systemPermissionCatalog(): array
{
  return [
    'dashboard.view'        => ['label' => 'Dashboard',           'description' => 'View the main admin dashboard'],
    'bookings.new'          => ['label' => 'New Bookings',        'description' => 'Review and confirm new online bookings'],
    'bookings.walkin'       => ['label' => 'Walk-In Booking',     'description' => 'Create and manage front desk walk-in bookings'],
    'bookings.refunds'      => ['label' => 'Refund Bookings',     'description' => 'Handle cancelled bookings and refund requests'],
    'bookings.records'      => ['label' => 'Booking Records',     'description' => 'Review processed booking history and records'],
    'bookings.calendar'     => ['label' => 'Booking Calendar',    'description' => 'View occupancy calendar and block room dates'],
    'service.center'        => ['label' => 'Service Center',      'description' => 'Manage support tickets, guest notes, and service actions'],
    'service.queries'       => ['label' => 'User Queries',        'description' => 'Review unread contact and user query messages'],
    'reports.all_time'      => ['label' => 'All Time Reports',    'description' => 'View operational reports and analytics'],
    'reports.transactions'  => ['label' => 'Transactions',        'description' => 'Review payment and transaction history'],
    'email_logs.view'       => ['label' => 'Email Logs',          'description' => 'Review outgoing email delivery history'],
    'users.manage'          => ['label' => 'User Accounts',       'description' => 'Manage guest user accounts and account status'],
    'permissions.manage'    => ['label' => 'Permissions',         'description' => 'Update staff access permissions'],
    'content.manage'        => ['label' => 'Content',             'description' => 'Manage rooms, carousel, extras, and reviews'],
    'promos.manage'         => ['label' => 'Promo Codes',         'description' => 'Create and control booking promo codes'],
    'utilities.archives'    => ['label' => 'Archives',            'description' => 'Access archive and restore utilities'],
    'utilities.backup'      => ['label' => 'Backup & Restore',    'description' => 'Use backup and restore tools'],
    'utilities.logs'        => ['label' => 'Activity Logs',       'description' => 'Review admin-side activity history'],
    'utilities.settings'    => ['label' => 'Settings',            'description' => 'Manage system-wide settings'],
    'utilities.manual'      => ['label' => 'Admin Manual',        'description' => 'View and maintain the admin manual'],

    'bookings.manage'       => ['label' => 'Bookings (Legacy)',   'description' => 'Legacy full booking access', 'hidden' => true],
    'calendar.manage'       => ['label' => 'Calendar (Legacy)',   'description' => 'Legacy booking calendar access', 'hidden' => true],
    'support.manage'        => ['label' => 'Support (Legacy)',    'description' => 'Legacy customer service access', 'hidden' => true],
    'reports.view'          => ['label' => 'Reports (Legacy)',    'description' => 'Legacy reporting access', 'hidden' => true],
    'utilities.manage'      => ['label' => 'Utilities (Legacy)',  'description' => 'Legacy utilities access', 'hidden' => true],
  ];
}

function systemPermissionGroups(): array
{
  return [
    'dashboard' => [
      'label' => 'Dashboard',
      'description' => 'Main overview access',
      'codes' => ['dashboard.view'],
    ],
    'bookings' => [
      'label' => 'Bookings',
      'description' => 'Choose exactly which booking pages this staff member can use',
      'codes' => ['bookings.new', 'bookings.walkin', 'bookings.refunds', 'bookings.records', 'bookings.calendar'],
    ],
    'service' => [
      'label' => 'Service',
      'description' => 'Guest support and user communication tools',
      'codes' => ['service.center', 'service.queries'],
    ],
    'reports' => [
      'label' => 'Reports',
      'description' => 'Operational reports and transaction history',
      'codes' => ['reports.all_time', 'reports.transactions', 'email_logs.view'],
    ],
    'users' => [
      'label' => 'Users and Access',
      'description' => 'Guest accounts and permission management',
      'codes' => ['users.manage', 'permissions.manage'],
    ],
    'content' => [
      'label' => 'Content',
      'description' => 'Rooms, amenities, extras, reviews, and promos',
      'codes' => ['content.manage', 'promos.manage'],
    ],
    'utilities' => [
      'label' => 'Utilities',
      'description' => 'Archive, backups, logs, settings, and manual',
      'codes' => ['utilities.archives', 'utilities.backup', 'utilities.logs', 'utilities.settings', 'utilities.manual'],
    ],
  ];
}

function systemPermissionImplicationMap(): array
{
  return [
    'bookings.manage' => ['bookings.new', 'bookings.walkin', 'bookings.refunds', 'bookings.records'],
    'calendar.manage' => ['bookings.calendar'],
    'support.manage' => ['service.center', 'service.queries'],
    'reports.view' => ['reports.all_time', 'reports.transactions'],
    'utilities.manage' => ['utilities.archives', 'utilities.backup', 'utilities.logs', 'utilities.settings', 'utilities.manual'],
  ];
}

function defaultStaffPermissions(): array
{
  return [
    'dashboard.view',
    'bookings.new',
    'bookings.walkin',
    'bookings.refunds',
    'bookings.records',
    'bookings.calendar',
    'service.center',
    'service.queries',
    'reports.all_time',
    'reports.transactions',
    'email_logs.view',
  ];
}

function adminPagePermissionMap(): array
{
  return [
    'dashboard.php'        => 'dashboard.view',
    'new_bookings.php'     => 'bookings.new',
    'walkin_booking.php'   => 'bookings.walkin',
    'refund_bookings.php'  => 'bookings.refunds',
    'booking_records.php'  => 'bookings.records',
    'booking_calendar.php' => 'bookings.calendar',
    'support_center.php'   => 'service.center',
    'all_time_reports.php' => 'reports.all_time',
    'transaction.php'      => 'reports.transactions',
    'users.php'            => 'users.manage',
    'user_queries.php'     => 'service.queries',
    'manage_users.php'     => 'users.manage',
    'create_user.php'      => 'users.manage',
    'staff_permissions.php'=> 'permissions.manage',
    'rooms.php'            => 'content.manage',
    'features_facilities.php' => 'content.manage',
    'extras.php'           => 'content.manage',
    'carousel.php'         => 'content.manage',
    'rate_review.php'      => 'content.manage',
    'promo_codes.php'      => 'promos.manage',
    'archives.php'         => 'utilities.archives',
    'Archives.php'         => 'utilities.archives',
    'backup_restore.php'   => 'utilities.backup',
    'activity_logs.php'    => 'utilities.logs',
    'settings.php'         => 'utilities.settings',
    'manual.php'           => 'utilities.manual',
    'email_logs.php'       => 'email_logs.view',
    'change_password.php'  => 'dashboard.view',
    'logout.php'           => 'dashboard.view',
  ];
}

function adminAjaxPermissionMap(): array
{
  return [
    'dashboard.php'        => 'dashboard.view',
    'confirm_booking.php'  => 'bookings.new',
    'new_bookings.php'     => 'bookings.new',
    'walkin_booking.php'   => 'bookings.walkin',
    'refund_bookings.php'  => 'bookings.refunds',
    'booking_records.php'  => 'bookings.records',
    'reports.php'          => 'reports.all_time',
    'transactions.php'     => 'reports.transactions',
    'service_center.php'   => 'service.center',
    'promo_codes.php'      => 'promos.manage',
    'booking_calendar.php' => 'bookings.calendar',
    'users.php'            => 'users.manage',
    'rooms.php'            => 'content.manage',
    'rooms_fixed.php'      => 'content.manage',
    'features_facilities.php' => 'content.manage',
    'extras.php'           => 'content.manage',
    'carousel_crud.php'    => 'content.manage',
    'archive.php'          => 'utilities.archives',
    'archived_bookings.php'=> 'utilities.archives',
    'archived_rooms.php'   => 'utilities.archives',
    'archived_users.php'   => 'utilities.archives',
    'backup_restore.php'   => 'utilities.backup',
    'settings_crud.php'    => 'utilities.settings',
  ];
}

function normalizeSystemPermissionCodes(array $codes): array
{
  $catalog = systemPermissionCatalog();
  $implications = systemPermissionImplicationMap();
  $normalized = [];

  foreach ($codes as $code) {
    $code = trim((string)$code);
    if ($code !== '' && (isset($catalog[$code]) || isset($implications[$code]))) {
      $normalized[$code] = true;
    }
  }

  return array_keys($normalized);
}

function expandSystemPermissionCodes(array $codes): array
{
  $implications = systemPermissionImplicationMap();
  $expanded = [];
  $queue = normalizeSystemPermissionCodes($codes);

  while (!empty($queue)) {
    $code = array_shift($queue);
    if (isset($expanded[$code])) {
      continue;
    }

    $expanded[$code] = true;
    foreach ($implications[$code] ?? [] as $childCode) {
      if (!isset($expanded[$childCode])) {
        $queue[] = $childCode;
      }
    }
  }

  return array_keys($expanded);
}

function getAdminAssignedPermissions(int $adminId): array
{
  $con = $GLOBALS['con'] ?? null;
  if (!$con instanceof mysqli || $adminId <= 0 || !function_exists('appSchemaTableExists') || !appSchemaTableExists($con, 'admin_user_permissions')) {
    return [];
  }

  $res = select(
    "SELECT `permission_code` FROM `admin_user_permissions` WHERE `admin_user_id`=?",
    [$adminId],
    'i'
  );

  $codes = [];
  while ($row = mysqli_fetch_assoc($res)) {
    $codes[] = $row['permission_code'];
  }

  return normalizeSystemPermissionCodes($codes);
}

function currentAdminPermissions(bool $forceRefresh = false): array
{
  if (session_status() === PHP_SESSION_NONE) {
    @session_start();
  }

  $role = $_SESSION['adminRole'] ?? 'admin';
  if ($role !== 'staff') {
    return array_keys(systemPermissionCatalog());
  }

  $adminId = (int)($_SESSION['adminId'] ?? 0);
  $assigned = getAdminAssignedPermissions($adminId);
  if (empty($assigned)) {
    $assigned = defaultStaffPermissions();
  }

  $expanded = expandSystemPermissionCodes($assigned);
  $_SESSION['_admin_permissions_cache'] = $expanded;
  return $expanded;
}

function currentAdminCan(string $permissionCode): bool
{
  if (session_status() === PHP_SESSION_NONE) {
    @session_start();
  }

  if (!(isset($_SESSION['adminLogin']) && $_SESSION['adminLogin'] == true)) {
    return false;
  }

  $role = $_SESSION['adminRole'] ?? 'admin';
  if ($role !== 'staff') {
    return true;
  }

  return in_array($permissionCode, currentAdminPermissions(), true);
}

function currentAdminCanAny(array $permissionCodes): bool
{
  foreach ($permissionCodes as $permissionCode) {
    if (currentAdminCan((string)$permissionCode)) {
      return true;
    }
  }

  return false;
}

function saveAdminPermissionAssignments(int $adminId, array $codes): bool
{
  $con = $GLOBALS['con'] ?? null;
  if (!$con instanceof mysqli || $adminId <= 0) {
    return false;
  }

  $codes = normalizeSystemPermissionCodes($codes);

  mysqli_begin_transaction($con);
  try {
    delete("DELETE FROM `admin_user_permissions` WHERE `admin_user_id`=?", [$adminId], 'i');

    foreach ($codes as $code) {
      insert(
        "INSERT INTO `admin_user_permissions` (`admin_user_id`,`permission_code`) VALUES (?,?)",
        [$adminId, $code],
        'is'
      );
    }

    mysqli_commit($con);

    if (session_status() === PHP_SESSION_NONE) {
      @session_start();
    }
    if ((int)($_SESSION['adminId'] ?? 0) === $adminId) {
      $_SESSION['_admin_permissions_cache'] = $codes;
    }

    return true;
  } catch (Throwable $e) {
    mysqli_rollback($con);
    error_log('saveAdminPermissionAssignments failed: ' . $e->getMessage());
    return false;
  }
}

function requireAdminPermission(string $permissionCode, string $fallback = 'dashboard.php'): void
{
  if (!currentAdminCan($permissionCode)) {
    if (strpos($_SERVER['PHP_SELF'] ?? '', '/admin/ajax/') !== false) {
      http_response_code(403);
      header('Content-Type: application/json');
      echo json_encode(['status' => 0, 'message' => 'Forbidden']);
      exit;
    }
    redirect($fallback);
  }
}

function supportTicketStatuses(): array
{
  return [
    'open' => 'Open',
    'pending' => 'Pending',
    'resolved' => 'Resolved',
    'escalated' => 'Escalated',
  ];
}

function supportTicketCategories(): array
{
  return [
    'booking' => 'Booking help',
    'payment' => 'Payment concern',
    'refund' => 'Refund follow-up',
    'reschedule' => 'Reschedule request',
    'general' => 'General question',
  ];
}

function supportTicketPriorities(): array
{
  return [
    'low' => 'Low',
    'normal' => 'Normal',
    'high' => 'High',
    'urgent' => 'Urgent',
  ];
}

function sanitizeMultilineText(string $text, int $limit = 1500): string
{
  $text = trim(str_replace("\r\n", "\n", str_replace("\r", "\n", $text)));
  if (function_exists('mb_substr')) {
    return mb_substr($text, 0, $limit);
  }
  return substr($text, 0, $limit);
}

function generateSupportTicketCode(): string
{
  return 'TIC-' . date('Ymd') . '-' . random_int(1000, 9999);
}

function storeSupportAttachment(array $file)
{
  if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
    return '';
  }

  if ($file['error'] !== UPLOAD_ERR_OK) {
    return 'upload_error';
  }

  if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
    return 'too_large';
  }

  $allowed = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
    'application/pdf' => 'pdf',
  ];

  $mime = mime_content_type($file['tmp_name']);
  if (!isset($allowed[$mime])) {
    return 'invalid_type';
  }

  $dir = rtrim(UPLOADS_PATH, '/\\') . '/ticket_attachments/';
  if (!is_dir($dir) && !@mkdir($dir, 0777, true)) {
    return 'upload_error';
  }

  $filename = 'ticket_' . time() . '_' . random_int(1000, 9999) . '.' . $allowed[$mime];
  $target = $dir . $filename;

  if (!move_uploaded_file($file['tmp_name'], $target)) {
    return 'upload_error';
  }

  return 'uploads/ticket_attachments/' . $filename;
}

function createSupportTicket(
  int $userId,
  string $subject,
  string $message,
  array $options = [],
  &$wasDuplicate = null
)
{
  $con = $GLOBALS['con'] ?? null;
  if (!$con instanceof mysqli || $userId <= 0) {
    return false;
  }

  $wasDuplicate = false;

  $subject = sanitizeMultilineText($subject, 180);
  $message = sanitizeMultilineText($message, 3000);
  if ($subject === '' || $message === '') {
    return false;
  }

  $bookingId = !empty($options['booking_id']) ? (int)$options['booking_id'] : null;
  $orderId = trim((string)($options['order_id'] ?? ''));
  $category = trim((string)($options['category'] ?? 'general'));
  $priority = trim((string)($options['priority'] ?? 'normal'));
  $attachmentPath = trim((string)($options['attachment_path'] ?? ''));
  $senderName = trim((string)($options['sender_name'] ?? 'Guest'));
  $ticketCode = generateSupportTicketCode();

  if (!isset(supportTicketCategories()[$category])) {
    $category = 'general';
  }
  if (!isset(supportTicketPriorities()[$priority])) {
    $priority = 'normal';
  }

  $duplicateTicketRes = select(
    "SELECT st.`id`
     FROM `support_tickets` st
     INNER JOIN `support_ticket_messages` stm ON stm.`ticket_id` = st.`id`
     WHERE st.`user_id`=?
       AND st.`is_archived`=0
       AND COALESCE(st.`booking_id`, 0)=?
       AND st.`subject`=?
       AND st.`category`=?
       AND st.`priority`=?
       AND st.`created_at` >= DATE_SUB(NOW(), INTERVAL 1 DAY)
       AND stm.`id` = (
         SELECT MIN(stm2.`id`)
         FROM `support_ticket_messages` stm2
         WHERE stm2.`ticket_id` = st.`id`
       )
       AND stm.`sender_type`='guest'
       AND stm.`is_internal`=0
       AND stm.`message`=?
     ORDER BY st.`id` DESC
     LIMIT 1",
    [$userId, $bookingId ?? 0, $subject, $category, $priority, $message],
    'iissss'
  );

  if ($duplicateTicketRes && mysqli_num_rows($duplicateTicketRes) === 1) {
    $duplicateTicket = mysqli_fetch_assoc($duplicateTicketRes);
    $wasDuplicate = true;
    return (int)($duplicateTicket['id'] ?? 0);
  }

  mysqli_begin_transaction($con);
  try {
    insert(
      "INSERT INTO `support_tickets`
        (`ticket_code`,`user_id`,`booking_id`,`order_id`,`subject`,`category`,`priority`,`status`,`assigned_to`,`escalated`,`last_reply_at`,`last_reply_by`)
       VALUES (?,?,?,?,?,?,?,'open',NULL,0,NOW(),'guest')",
      [$ticketCode, $userId, $bookingId, $orderId !== '' ? $orderId : null, $subject, $category, $priority],
      'siissss'
    );

    $ticketId = mysqli_insert_id($con);

    insert(
      "INSERT INTO `support_ticket_messages`
        (`ticket_id`,`sender_type`,`sender_id`,`sender_name`,`message`,`attachment_path`,`is_internal`,`seen_by_user`,`seen_by_staff`)
       VALUES (?,'guest',?,?,?, ?,0,1,0)",
      [$ticketId, $userId, $senderName, $message, $attachmentPath !== '' ? $attachmentPath : null],
      'iisss'
    );

    mysqli_commit($con);
    return $ticketId;
  } catch (Throwable $e) {
    mysqli_rollback($con);
    error_log('createSupportTicket failed: ' . $e->getMessage());
    return false;
  }
}

function addSupportTicketMessage(
  int $ticketId,
  string $senderType,
  int $senderId,
  string $senderName,
  string $message,
  array $options = [],
  &$wasDuplicate = null
): bool
{
  $con = $GLOBALS['con'] ?? null;
  if (!$con instanceof mysqli || $ticketId <= 0) {
    return false;
  }

  $wasDuplicate = false;

  $senderType = in_array($senderType, ['guest', 'staff', 'admin', 'system'], true) ? $senderType : 'system';
  $message = sanitizeMultilineText($message, 3000);
  if ($message === '') {
    return false;
  }

  $attachmentPath = trim((string)($options['attachment_path'] ?? ''));
  $isInternal = !empty($options['is_internal']) ? 1 : 0;
  $seenByUser = in_array($senderType, ['staff', 'admin', 'system'], true) ? 0 : 1;
  $seenByStaff = in_array($senderType, ['staff', 'admin', 'system'], true) ? 1 : 0;
  $lastReplyBy = $senderType === 'guest' ? 'guest' : 'staff';
  $nextStatus = trim((string)($options['next_status'] ?? ''));
  if ($nextStatus !== '' && !isset(supportTicketStatuses()[$nextStatus])) {
    $nextStatus = '';
  }

  $duplicateMessageRes = select(
    "SELECT `id`
     FROM `support_ticket_messages`
     WHERE `ticket_id`=?
       AND `sender_type`=?
       AND COALESCE(`sender_id`, 0)=?
       AND COALESCE(`sender_name`, '')=?
       AND `is_internal`=?
       AND COALESCE(`attachment_path`, '')=?
       AND `message`=?
       AND `created_at` >= DATE_SUB(NOW(), INTERVAL 30 MINUTE)
     ORDER BY `id` DESC
     LIMIT 1",
    [$ticketId, $senderType, max(0, $senderId), $senderName, $isInternal, $attachmentPath, $message],
    'isissss'
  );

  if ($duplicateMessageRes && mysqli_num_rows($duplicateMessageRes) === 1) {
    $wasDuplicate = true;
    return true;
  }

  mysqli_begin_transaction($con);
  try {
    insert(
      "INSERT INTO `support_ticket_messages`
        (`ticket_id`,`sender_type`,`sender_id`,`sender_name`,`message`,`attachment_path`,`is_internal`,`seen_by_user`,`seen_by_staff`)
       VALUES (?,?,?,?,?,?,?,?,?)",
      [$ticketId, $senderType, $senderId > 0 ? $senderId : null, $senderName, $message, $attachmentPath !== '' ? $attachmentPath : null, $isInternal, $seenByUser, $seenByStaff],
      'isisssiii'
    );

    if ($nextStatus === '') {
      $nextStatus = $senderType === 'guest' ? 'open' : 'pending';
    }

    update(
      "UPDATE `support_tickets`
       SET `status`=?, `last_reply_at`=NOW(), `last_reply_by`=?, `updated_at`=NOW()
       WHERE `id`=?",
      [$nextStatus, $lastReplyBy, $ticketId],
      'ssi'
    );

    mysqli_commit($con);
    return true;
  } catch (Throwable $e) {
    mysqli_rollback($con);
    error_log('addSupportTicketMessage failed: ' . $e->getMessage());
    return false;
  }
}

function markSupportTicketSeenByUser(int $ticketId): void
{
  $con = $GLOBALS['con'] ?? null;
  if ($con instanceof mysqli && $ticketId > 0) {
    @update("UPDATE `support_ticket_messages` SET `seen_by_user`=1 WHERE `ticket_id`=?", [$ticketId], 'i');
  }
}

function markSupportTicketSeenByStaff(int $ticketId): void
{
  $con = $GLOBALS['con'] ?? null;
  if ($con instanceof mysqli && $ticketId > 0) {
    @update("UPDATE `support_ticket_messages` SET `seen_by_staff`=1 WHERE `ticket_id`=?", [$ticketId], 'i');
  }
}

function getSupportTicketUnreadCountForAdmin(): int
{
  $con = $GLOBALS['con'] ?? null;
  if (!$con instanceof mysqli) {
    return 0;
  }

  $res = mysqli_query($con, "
    SELECT COUNT(DISTINCT stm.ticket_id) AS unread_count
    FROM support_ticket_messages stm
    INNER JOIN support_tickets st ON st.id = stm.ticket_id
    WHERE stm.seen_by_staff = 0
      AND st.status IN ('open','pending','escalated')
  ");

  $row = $res ? mysqli_fetch_assoc($res) : null;
  return (int)($row['unread_count'] ?? 0);
}

function getSupportTicketUnreadCountForUser(int $userId): int
{
  $con = $GLOBALS['con'] ?? null;
  if (!$con instanceof mysqli || $userId <= 0) {
    return 0;
  }

  $res = select(
    "SELECT COUNT(DISTINCT stm.ticket_id) AS unread_count
     FROM `support_ticket_messages` stm
     INNER JOIN `support_tickets` st ON st.id = stm.ticket_id
     WHERE st.user_id=? AND stm.seen_by_user=0",
    [$userId],
    'i'
  );
  $row = mysqli_fetch_assoc($res);
  return (int)($row['unread_count'] ?? 0);
}

function createBookingHistoryEntry(
  int $bookingId,
  string $eventType,
  string $title,
  string $details = '',
  array $meta = [],
  ?string $actorType = null,
  ?int $actorId = null,
  ?string $actorName = null
): bool
{
  $con = $GLOBALS['con'] ?? null;
  if (!$con instanceof mysqli || $bookingId <= 0) {
    return false;
  }

  if ($actorType === null) {
    if (session_status() === PHP_SESSION_NONE) {
      @session_start();
    }
    if (isset($_SESSION['adminLogin']) && $_SESSION['adminLogin'] == true) {
      $actorType = $_SESSION['adminRole'] ?? 'admin';
      $actorId = $actorId ?? (int)($_SESSION['adminId'] ?? 0);
      $actorName = $actorName ?? (string)($_SESSION['adminName'] ?? 'Admin');
    } elseif (isset($_SESSION['login']) && $_SESSION['login'] == true) {
      $actorType = 'guest';
      $actorId = $actorId ?? (int)($_SESSION['uId'] ?? 0);
      $actorName = $actorName ?? (string)($_SESSION['uName'] ?? 'Guest');
    } else {
      $actorType = 'system';
      $actorName = $actorName ?? 'System';
    }
  }

  $title = sanitizeMultilineText($title, 180);
  $details = sanitizeMultilineText($details, 1200);
  $metaJson = !empty($meta) ? json_encode($meta) : null;

  return (bool)insert(
    "INSERT INTO `booking_history`
      (`booking_id`,`actor_type`,`actor_id`,`actor_name`,`event_type`,`title`,`details`,`meta_json`)
     VALUES (?,?,?,?,?,?,?,?)",
    [$bookingId, $actorType, $actorId, $actorName, $eventType, $title, $details !== '' ? $details : null, $metaJson],
    'isisssss'
  );
}

function getBookingHistoryEntries(int $bookingId): array
{
  $entries = [];
  if ($bookingId <= 0) {
    return $entries;
  }

  $res = select(
    "SELECT * FROM `booking_history` WHERE `booking_id`=? ORDER BY `created_at` DESC, `id` DESC",
    [$bookingId],
    'i'
  );

  while ($row = mysqli_fetch_assoc($res)) {
    $entries[] = $row;
  }

  return $entries;
}

function inferEmailTemplateKey(string $subject): string
{
  $subject = strtolower($subject);
  if (strpos($subject, 'verify') !== false) return 'verification';
  if (strpos($subject, 'reset') !== false) return 'password_reset';
  if (strpos($subject, 'confirmed') !== false) return 'booking_confirmed';
  if (strpos($subject, 'received') !== false) return 'booking_received';
  if (strpos($subject, 'refund') !== false) return 'refund';
  return 'general';
}

function logEmailDispatch(array $data): bool
{
  $con = $GLOBALS['con'] ?? null;
  if (!$con instanceof mysqli || !function_exists('appSchemaTableExists') || !appSchemaTableExists($con, 'email_logs')) {
    return false;
  }

  $recipientEmail = trim((string)($data['recipient_email'] ?? ''));
  if ($recipientEmail === '') {
    return false;
  }

  $subject = trim((string)($data['subject'] ?? ''));
  $templateKey = trim((string)($data['template_key'] ?? ''));
  if ($templateKey === '') {
    $templateKey = inferEmailTemplateKey($subject);
  }

  $status = trim((string)($data['status'] ?? 'sent'));
  if (!in_array($status, ['queued', 'sent', 'failed'], true)) {
    $status = 'sent';
  }

  return (bool)insert(
    "INSERT INTO `email_logs`
      (`related_booking_id`,`related_user_id`,`recipient_email`,`recipient_name`,`subject`,`template_key`,`status`,`error_message`,`triggered_by`)
     VALUES (?,?,?,?,?,?,?,?,?)",
    [
      !empty($data['related_booking_id']) ? (int)$data['related_booking_id'] : null,
      !empty($data['related_user_id']) ? (int)$data['related_user_id'] : null,
      $recipientEmail,
      trim((string)($data['recipient_name'] ?? '')),
      $subject,
      $templateKey,
      $status,
      trim((string)($data['error_message'] ?? '')),
      trim((string)($data['triggered_by'] ?? 'system'))
    ],
    'iisssssss'
  );
}

function getPromoByCode(string $code)
{
  $code = strtoupper(trim($code));
  if ($code === '') {
    return null;
  }

  $res = select(
    "SELECT * FROM `promo_codes` WHERE `code`=? AND `is_active`=1 LIMIT 1",
    [$code],
    's'
  );

  return ($res && mysqli_num_rows($res) === 1) ? mysqli_fetch_assoc($res) : null;
}

function validatePromoForAmount(string $code, float $subtotal, int $userId = 0): array
{
  $promo = getPromoByCode($code);
  if (!$promo) {
    return ['ok' => false, 'message' => 'Promo code not found or inactive.'];
  }

  $today = date('Y-m-d');
  if (!empty($promo['start_date']) && $promo['start_date'] > $today) {
    return [
      'ok' => false,
      'message' => 'Promo code will be active on ' . date('F j, Y', strtotime((string)$promo['start_date'])) . '.',
    ];
  }
  if (!empty($promo['end_date']) && $promo['end_date'] < $today) {
    return [
      'ok' => false,
      'message' => 'Promo code expired on ' . date('F j, Y', strtotime((string)$promo['end_date'])) . '.',
    ];
  }
  if ((float)$promo['min_amount'] > 0 && $subtotal < (float)$promo['min_amount']) {
    return ['ok' => false, 'message' => 'Booking total does not meet the promo minimum amount.'];
  }
  if ((int)$promo['usage_limit'] > 0 && (int)$promo['used_count'] >= (int)$promo['usage_limit']) {
    return ['ok' => false, 'message' => 'Promo code usage limit has been reached.'];
  }

  if ($userId > 0 && function_exists('appSchemaTableExists') && appSchemaTableExists($GLOBALS['con'], 'promo_redemptions')) {
    $usedRes = select(
      "SELECT COUNT(*) AS c FROM `promo_redemptions` WHERE `promo_id`=? AND `user_id`=?",
      [(int)$promo['id'], $userId],
      'ii'
    );
    $usedRow = mysqli_fetch_assoc($usedRes);
    if ((int)($usedRow['c'] ?? 0) > 0) {
      return ['ok' => false, 'message' => 'You have already used this promo code.'];
    }
  }

  $discount = 0.0;
  if (($promo['discount_type'] ?? '') === 'percent') {
    $discount = round($subtotal * ((float)$promo['discount_value'] / 100), 2);
  } else {
    $discount = round((float)$promo['discount_value'], 2);
  }

  if ((float)$promo['max_discount'] > 0 && $discount > (float)$promo['max_discount']) {
    $discount = (float)$promo['max_discount'];
  }

  $discount = min($discount, $subtotal);

  return [
    'ok' => true,
    'message' => 'Promo code applied successfully.',
    'promo' => $promo,
    'discount' => $discount,
    'new_total' => max(0, $subtotal - $discount),
  ];
}

function recordPromoRedemption(int $promoId, int $bookingId, int $userId, float $discount): bool
{
  $con = $GLOBALS['con'] ?? null;
  if (!$con instanceof mysqli || $promoId <= 0 || $bookingId <= 0 || $userId <= 0) {
    return false;
  }

  mysqli_begin_transaction($con);
  try {
    insert(
      "INSERT INTO `promo_redemptions` (`promo_id`,`booking_id`,`user_id`,`discount_amount`) VALUES (?,?,?,?)",
      [$promoId, $bookingId, $userId, $discount],
      'iiid'
    );
    update("UPDATE `promo_codes` SET `used_count`=`used_count`+1 WHERE `id`=?", [$promoId], 'i');
    mysqli_commit($con);
    return true;
  } catch (Throwable $e) {
    mysqli_rollback($con);
    error_log('recordPromoRedemption failed: ' . $e->getMessage());
    return false;
  }
}
