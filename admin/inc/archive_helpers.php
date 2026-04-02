<?php

if (!function_exists('archiveHelperEnsureSchema')) {
  function archiveHelperEnsureSchema(): void
  {
    if (function_exists('ensureAppSchema')) {
      ensureAppSchema();
    }
  }
}

if (!function_exists('archiveHelperExec')) {
  function archiveHelperExec(mysqli $con, string $sql, string $context): void
  {
    if (!mysqli_query($con, $sql)) {
      throw new Exception($context . ': ' . mysqli_error($con));
    }
  }
}

if (!function_exists('archiveDeleteBookingChildren')) {
  function archiveDeleteBookingChildren(int $bookingId): void
  {
    $con = $GLOBALS['con'] ?? null;
    if (!$con instanceof mysqli || $bookingId <= 0) {
      return;
    }

    archiveHelperEnsureSchema();
    $bookingId = (int)$bookingId;

    foreach ([
      'archived_booking_support_messages',
      'archived_booking_support_tickets',
      'archived_booking_notifications',
      'archived_booking_transactions',
      'archived_booking_history',
      'archived_booking_extras',
      'archived_booking_guest_notes',
    ] as $table) {
      archiveHelperExec($con, "DELETE FROM `{$table}` WHERE `booking_id` = {$bookingId}", "Failed to delete archived booking child rows from {$table}");
    }
  }
}

if (!function_exists('archiveRefreshBookingChildren')) {
  function archiveRefreshBookingChildren(int $bookingId): void
  {
    $con = $GLOBALS['con'] ?? null;
    if (!$con instanceof mysqli || $bookingId <= 0) {
      return;
    }

    archiveHelperEnsureSchema();
    $bookingId = (int)$bookingId;
    archiveDeleteBookingChildren($bookingId);

    archiveHelperExec(
      $con,
      "INSERT INTO `archived_booking_extras`
        (`id`,`booking_id`,`extra_id`,`name`,`quantity`,`unit_price`,`total_price`,`created_at`)
       SELECT
        `id`,`booking_id`,`extra_id`,`name`,`quantity`,`unit_price`,`total_price`,`created_at`
       FROM `booking_extras`
       WHERE `booking_id` = {$bookingId}",
      'Failed to archive booking extras'
    );

    archiveHelperExec(
      $con,
      "INSERT INTO `archived_booking_history`
        (`id`,`booking_id`,`actor_type`,`actor_id`,`actor_name`,`event_type`,`title`,`details`,`meta_json`,`created_at`)
       SELECT
        `id`,`booking_id`,`actor_type`,`actor_id`,`actor_name`,`event_type`,`title`,`details`,`meta_json`,`created_at`
       FROM `booking_history`
       WHERE `booking_id` = {$bookingId}",
      'Failed to archive booking history'
    );

    archiveHelperExec(
      $con,
      "INSERT INTO `archived_booking_transactions`
        (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`)
       SELECT
        `id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`
       FROM `transactions`
       WHERE `booking_id` = {$bookingId}",
      'Failed to archive booking transactions'
    );

    archiveHelperExec(
      $con,
      "INSERT INTO `archived_booking_notifications`
        (`id`,`booking_id`,`user_id`,`message`,`type`,`is_read`,`created_at`)
       SELECT
        `id`,`booking_id`,`user_id`,`message`,`type`,`is_read`,`created_at`
       FROM `notifications`
       WHERE `booking_id` = {$bookingId}",
      'Failed to archive booking notifications'
    );

    archiveHelperExec(
      $con,
      "INSERT INTO `archived_booking_support_tickets`
        (`id`,`booking_id`,`ticket_code`,`user_id`,`order_id`,`subject`,`category`,`priority`,`status`,`assigned_to`,`escalated`,`last_reply_at`,`last_reply_by`,`created_at`,`updated_at`)
       SELECT
        `id`,`booking_id`,`ticket_code`,`user_id`,`order_id`,`subject`,`category`,`priority`,`status`,`assigned_to`,`escalated`,`last_reply_at`,`last_reply_by`,`created_at`,`updated_at`
       FROM `support_tickets`
       WHERE `booking_id` = {$bookingId}",
      'Failed to archive booking support tickets'
    );

    archiveHelperExec(
      $con,
      "INSERT INTO `archived_booking_support_messages`
        (`id`,`booking_id`,`ticket_id`,`sender_type`,`sender_id`,`sender_name`,`message`,`attachment_path`,`is_internal`,`seen_by_user`,`seen_by_staff`,`created_at`)
       SELECT
        stm.`id`, st.`booking_id`, stm.`ticket_id`, stm.`sender_type`, stm.`sender_id`, stm.`sender_name`, stm.`message`, stm.`attachment_path`, stm.`is_internal`, stm.`seen_by_user`, stm.`seen_by_staff`, stm.`created_at`
       FROM `support_ticket_messages` stm
       INNER JOIN `support_tickets` st ON st.`id` = stm.`ticket_id`
       WHERE st.`booking_id` = {$bookingId}",
      'Failed to archive booking support messages'
    );

    archiveHelperExec(
      $con,
      "INSERT INTO `archived_booking_guest_notes`
        (`id`,`booking_id`,`user_id`,`note_type`,`title`,`note`,`created_by`,`created_at`,`updated_at`)
       SELECT
        `id`,`booking_id`,`user_id`,`note_type`,`title`,`note`,`created_by`,`created_at`,`updated_at`
       FROM `guest_notes`
       WHERE `booking_id` = {$bookingId}",
      'Failed to archive booking guest notes'
    );
  }
}

if (!function_exists('archiveRestoreBookingChildren')) {
  function archiveRestoreBookingChildren(int $bookingId): void
  {
    $con = $GLOBALS['con'] ?? null;
    if (!$con instanceof mysqli || $bookingId <= 0) {
      return;
    }

    archiveHelperEnsureSchema();
    $bookingId = (int)$bookingId;

    archiveHelperExec(
      $con,
      "REPLACE INTO `booking_extras`
        (`id`,`booking_id`,`extra_id`,`name`,`quantity`,`unit_price`,`total_price`,`created_at`)
       SELECT
        `id`,`booking_id`,`extra_id`,`name`,`quantity`,`unit_price`,`total_price`,`created_at`
       FROM `archived_booking_extras`
       WHERE `booking_id` = {$bookingId}",
      'Failed to restore booking extras'
    );

    archiveHelperExec(
      $con,
      "REPLACE INTO `booking_history`
        (`id`,`booking_id`,`actor_type`,`actor_id`,`actor_name`,`event_type`,`title`,`details`,`meta_json`,`created_at`)
       SELECT
        `id`,`booking_id`,`actor_type`,`actor_id`,`actor_name`,`event_type`,`title`,`details`,`meta_json`,`created_at`
       FROM `archived_booking_history`
       WHERE `booking_id` = {$bookingId}",
      'Failed to restore booking history'
    );

    archiveHelperExec(
      $con,
      "REPLACE INTO `transactions`
        (`id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`)
       SELECT
        `id`,`booking_id`,`guest_name`,`room_no`,`amount`,`method`,`status`,`type`,`admin_id`,`datentime`
       FROM `archived_booking_transactions`
       WHERE `booking_id` = {$bookingId}",
      'Failed to restore booking transactions'
    );

    archiveHelperExec(
      $con,
      "REPLACE INTO `notifications`
        (`id`,`user_id`,`booking_id`,`message`,`type`,`is_read`,`created_at`)
       SELECT
        `id`,`user_id`,`booking_id`,`message`,`type`,`is_read`,`created_at`
       FROM `archived_booking_notifications`
       WHERE `booking_id` = {$bookingId}",
      'Failed to restore booking notifications'
    );

    archiveHelperExec(
      $con,
      "REPLACE INTO `support_tickets`
        (`id`,`ticket_code`,`user_id`,`booking_id`,`order_id`,`subject`,`category`,`priority`,`status`,`assigned_to`,`escalated`,`last_reply_at`,`last_reply_by`,`created_at`,`updated_at`)
       SELECT
        `id`,`ticket_code`,`user_id`,`booking_id`,`order_id`,`subject`,`category`,`priority`,`status`,`assigned_to`,`escalated`,`last_reply_at`,`last_reply_by`,`created_at`,`updated_at`
       FROM `archived_booking_support_tickets`
       WHERE `booking_id` = {$bookingId}",
      'Failed to restore booking support tickets'
    );

    archiveHelperExec(
      $con,
      "REPLACE INTO `support_ticket_messages`
        (`id`,`ticket_id`,`sender_type`,`sender_id`,`sender_name`,`message`,`attachment_path`,`is_internal`,`seen_by_user`,`seen_by_staff`,`created_at`)
       SELECT
        `id`,`ticket_id`,`sender_type`,`sender_id`,`sender_name`,`message`,`attachment_path`,`is_internal`,`seen_by_user`,`seen_by_staff`,`created_at`
       FROM `archived_booking_support_messages`
       WHERE `booking_id` = {$bookingId}",
      'Failed to restore booking support messages'
    );

    archiveHelperExec(
      $con,
      "REPLACE INTO `guest_notes`
        (`id`,`user_id`,`booking_id`,`note_type`,`title`,`note`,`created_by`,`created_at`,`updated_at`)
       SELECT
        `id`,`user_id`,`booking_id`,`note_type`,`title`,`note`,`created_by`,`created_at`,`updated_at`
       FROM `archived_booking_guest_notes`
       WHERE `booking_id` = {$bookingId}",
      'Failed to restore booking guest notes'
    );
  }
}

if (!function_exists('archiveRefreshRoomRelations')) {
  function archiveRefreshRoomRelations(int $roomId, int $archivedRoomId): void
  {
    $con = $GLOBALS['con'] ?? null;
    if (!$con instanceof mysqli || $roomId <= 0 || $archivedRoomId <= 0) {
      return;
    }

    archiveHelperEnsureSchema();
    $roomId = (int)$roomId;
    $archivedRoomId = (int)$archivedRoomId;

    archiveHelperExec($con, "DELETE FROM `archived_ratings_reviews` WHERE `room_id` = {$archivedRoomId}", 'Failed to clear archived room reviews');
    archiveHelperExec($con, "DELETE FROM `archived_room_block_dates` WHERE `room_id` = {$archivedRoomId}", 'Failed to clear archived room block dates');

    archiveHelperExec(
      $con,
      "INSERT INTO `archived_ratings_reviews`
        (`room_id`,`source_review_id`,`booking_id`,`user_id`,`rating`,`review`,`seen`,`datentime`)
       SELECT
        {$archivedRoomId}, `sr_no`, `booking_id`, `user_id`, `rating`, `review`, `seen`, `datentime`
       FROM `rating_review`
       WHERE `room_id` = {$roomId}",
      'Failed to archive room reviews'
    );

    archiveHelperExec(
      $con,
      "INSERT INTO `archived_room_block_dates`
        (`source_block_id`,`room_id`,`room_no`,`start_date`,`end_date`,`block_type`,`reason`,`status`,`created_by`,`created_at`)
       SELECT
        `id`, {$archivedRoomId}, `room_no`, `start_date`, `end_date`, `block_type`, `reason`, `status`, `created_by`, `created_at`
       FROM `room_block_dates`
       WHERE `room_id` = {$roomId}",
      'Failed to archive room block dates'
    );
  }
}

if (!function_exists('archiveRestoreRoomRelations')) {
  function archiveRestoreRoomRelations(int $archivedRoomId, int $roomId): void
  {
    $con = $GLOBALS['con'] ?? null;
    if (!$con instanceof mysqli || $archivedRoomId <= 0 || $roomId <= 0) {
      return;
    }

    archiveHelperEnsureSchema();
    $archivedRoomId = (int)$archivedRoomId;
    $roomId = (int)$roomId;

    archiveHelperExec(
      $con,
      "REPLACE INTO `rating_review`
        (`sr_no`,`booking_id`,`room_id`,`user_id`,`rating`,`review`,`seen`,`datentime`)
       SELECT
        COALESCE(`source_review_id`, `id`), `booking_id`, {$roomId}, `user_id`, `rating`, `review`, `seen`, `datentime`
       FROM `archived_ratings_reviews`
       WHERE `room_id` = {$archivedRoomId}",
      'Failed to restore room reviews'
    );

    archiveHelperExec(
      $con,
      "REPLACE INTO `room_block_dates`
        (`id`,`room_id`,`room_no`,`start_date`,`end_date`,`block_type`,`reason`,`status`,`created_by`,`created_at`)
       SELECT
        COALESCE(`source_block_id`, `id`), {$roomId}, `room_no`, `start_date`, `end_date`, `block_type`, `reason`, `status`, `created_by`, `created_at`
       FROM `archived_room_block_dates`
       WHERE `room_id` = {$archivedRoomId}",
      'Failed to restore room block dates'
    );
  }
}

if (!function_exists('archiveDeleteRoomRelations')) {
  function archiveDeleteRoomRelations(int $archivedRoomId): void
  {
    $con = $GLOBALS['con'] ?? null;
    if (!$con instanceof mysqli || $archivedRoomId <= 0) {
      return;
    }

    archiveHelperEnsureSchema();
    $archivedRoomId = (int)$archivedRoomId;

    foreach ([
      'archived_room_images',
      'archived_room_features',
      'archived_room_facilities',
      'archived_ratings_reviews',
      'archived_room_block_dates',
    ] as $table) {
      archiveHelperExec($con, "DELETE FROM `{$table}` WHERE `room_id` = {$archivedRoomId}", "Failed to delete archived room child rows from {$table}");
    }
  }
}

if (!function_exists('archiveDeleteUserChildren')) {
  function archiveDeleteUserChildren(int $userId): void
  {
    $con = $GLOBALS['con'] ?? null;
    if (!$con instanceof mysqli || $userId <= 0) {
      return;
    }

    archiveHelperEnsureSchema();
    $userId = (int)$userId;

    foreach ([
      'archived_user_notifications',
      'archived_user_guest_notes',
      'archived_user_support_messages',
      'archived_user_support_tickets',
      'archived_user_reviews',
    ] as $table) {
      archiveHelperExec($con, "DELETE FROM `{$table}` WHERE `user_id` = {$userId}", "Failed to delete archived user child rows from {$table}");
    }
  }
}

if (!function_exists('archiveRefreshUserChildren')) {
  function archiveRefreshUserChildren(int $userId): void
  {
    $con = $GLOBALS['con'] ?? null;
    if (!$con instanceof mysqli || $userId <= 0) {
      return;
    }

    archiveHelperEnsureSchema();
    $userId = (int)$userId;
    archiveDeleteUserChildren($userId);

    archiveHelperExec(
      $con,
      "INSERT INTO `archived_user_notifications`
        (`id`,`user_id`,`booking_id`,`message`,`type`,`is_read`,`created_at`)
       SELECT
        `id`,`user_id`,`booking_id`,`message`,`type`,`is_read`,`created_at`
       FROM `notifications`
       WHERE `user_id` = {$userId}",
      'Failed to archive user notifications'
    );

    archiveHelperExec(
      $con,
      "INSERT INTO `archived_user_guest_notes`
        (`id`,`user_id`,`booking_id`,`note_type`,`title`,`note`,`created_by`,`created_at`,`updated_at`)
       SELECT
        `id`,`user_id`,`booking_id`,`note_type`,`title`,`note`,`created_by`,`created_at`,`updated_at`
       FROM `guest_notes`
       WHERE `user_id` = {$userId}",
      'Failed to archive user guest notes'
    );

    archiveHelperExec(
      $con,
      "INSERT INTO `archived_user_support_tickets`
        (`id`,`user_id`,`booking_id`,`ticket_code`,`order_id`,`subject`,`category`,`priority`,`status`,`assigned_to`,`escalated`,`last_reply_at`,`last_reply_by`,`created_at`,`updated_at`)
       SELECT
        `id`,`user_id`,`booking_id`,`ticket_code`,`order_id`,`subject`,`category`,`priority`,`status`,`assigned_to`,`escalated`,`last_reply_at`,`last_reply_by`,`created_at`,`updated_at`
       FROM `support_tickets`
       WHERE `user_id` = {$userId}",
      'Failed to archive user support tickets'
    );

    archiveHelperExec(
      $con,
      "INSERT INTO `archived_user_support_messages`
        (`id`,`user_id`,`ticket_id`,`sender_type`,`sender_id`,`sender_name`,`message`,`attachment_path`,`is_internal`,`seen_by_user`,`seen_by_staff`,`created_at`)
       SELECT
        stm.`id`, st.`user_id`, stm.`ticket_id`, stm.`sender_type`, stm.`sender_id`, stm.`sender_name`, stm.`message`, stm.`attachment_path`, stm.`is_internal`, stm.`seen_by_user`, stm.`seen_by_staff`, stm.`created_at`
       FROM `support_ticket_messages` stm
       INNER JOIN `support_tickets` st ON st.`id` = stm.`ticket_id`
       WHERE st.`user_id` = {$userId}",
      'Failed to archive user support messages'
    );

    archiveHelperExec(
      $con,
      "INSERT INTO `archived_user_reviews`
        (`id`,`user_id`,`booking_id`,`room_id`,`rating`,`review`,`seen`,`datentime`)
       SELECT
        `sr_no`,`user_id`,`booking_id`,`room_id`,`rating`,`review`,`seen`,`datentime`
       FROM `rating_review`
       WHERE `user_id` = {$userId}",
      'Failed to archive user reviews'
    );
  }
}

if (!function_exists('archiveRestoreUserChildren')) {
  function archiveRestoreUserChildren(int $userId): void
  {
    $con = $GLOBALS['con'] ?? null;
    if (!$con instanceof mysqli || $userId <= 0) {
      return;
    }

    archiveHelperEnsureSchema();
    $userId = (int)$userId;

    archiveHelperExec(
      $con,
      "REPLACE INTO `notifications`
        (`id`,`user_id`,`booking_id`,`message`,`type`,`is_read`,`created_at`)
       SELECT
        `id`,`user_id`,`booking_id`,`message`,`type`,`is_read`,`created_at`
       FROM `archived_user_notifications`
       WHERE `user_id` = {$userId}",
      'Failed to restore user notifications'
    );

    archiveHelperExec(
      $con,
      "REPLACE INTO `guest_notes`
        (`id`,`user_id`,`booking_id`,`note_type`,`title`,`note`,`created_by`,`created_at`,`updated_at`)
       SELECT
        `id`,`user_id`,`booking_id`,`note_type`,`title`,`note`,`created_by`,`created_at`,`updated_at`
       FROM `archived_user_guest_notes`
       WHERE `user_id` = {$userId}",
      'Failed to restore user guest notes'
    );

    archiveHelperExec(
      $con,
      "REPLACE INTO `support_tickets`
        (`id`,`ticket_code`,`user_id`,`booking_id`,`order_id`,`subject`,`category`,`priority`,`status`,`assigned_to`,`escalated`,`last_reply_at`,`last_reply_by`,`created_at`,`updated_at`)
       SELECT
        `id`,`ticket_code`,`user_id`,`booking_id`,`order_id`,`subject`,`category`,`priority`,`status`,`assigned_to`,`escalated`,`last_reply_at`,`last_reply_by`,`created_at`,`updated_at`
       FROM `archived_user_support_tickets`
       WHERE `user_id` = {$userId}",
      'Failed to restore user support tickets'
    );

    archiveHelperExec(
      $con,
      "REPLACE INTO `support_ticket_messages`
        (`id`,`ticket_id`,`sender_type`,`sender_id`,`sender_name`,`message`,`attachment_path`,`is_internal`,`seen_by_user`,`seen_by_staff`,`created_at`)
       SELECT
        `id`,`ticket_id`,`sender_type`,`sender_id`,`sender_name`,`message`,`attachment_path`,`is_internal`,`seen_by_user`,`seen_by_staff`,`created_at`
       FROM `archived_user_support_messages`
       WHERE `user_id` = {$userId}",
      'Failed to restore user support messages'
    );

    archiveHelperExec(
      $con,
      "REPLACE INTO `rating_review`
        (`sr_no`,`booking_id`,`room_id`,`user_id`,`rating`,`review`,`seen`,`datentime`)
       SELECT
        `id`,`booking_id`,`room_id`,`user_id`,`rating`,`review`,`seen`,`datentime`
       FROM `archived_user_reviews`
       WHERE `user_id` = {$userId}",
      'Failed to restore user reviews'
    );
  }
}
