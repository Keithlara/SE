-- Adds optional guest note to bookings
-- Run once in phpMyAdmin / MySQL

ALTER TABLE `booking_details`
  ADD COLUMN `booking_note` TEXT NULL;

ALTER TABLE `archived_booking_details`
  ADD COLUMN `booking_note` TEXT NULL;

