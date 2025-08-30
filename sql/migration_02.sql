-- Migration 02: Add user_id to bookings table
--
-- This script links bookings to the users table, allowing registered users
-- to see their booking history.
--
-- Run this script on your database if you have already imported the previous schema files.

ALTER TABLE `bookings`
ADD COLUMN `user_id` INT(11) NULL DEFAULT NULL AFTER `email`,
ADD CONSTRAINT `fk_booking_user`
FOREIGN KEY (`user_id`)
REFERENCES `users`(`id`)
ON DELETE SET NULL;
