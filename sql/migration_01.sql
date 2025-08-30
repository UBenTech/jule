-- Migration 01: Adjust users table for public registration
--
-- This script changes the default value for the `is_admin` column in the `users` table.
-- The original schema defaulted this to 1 (true), which is not suitable for public registration.
-- This sets the default to 0 (false).
--
-- Run this script on your database if you have already imported the original schema.sql.

ALTER TABLE `users` MODIFY COLUMN `is_admin` tinyint(1) NOT NULL DEFAULT 0;
