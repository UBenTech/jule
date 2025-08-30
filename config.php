<?php
/**
 * Global Configuration File
 *
 * Contains database connection settings and site-wide constants.
 *
 * HOW TO EDIT:
 * 1. Update the DB_HOST, DB_NAME, DB_USER, and DB_PASS with your MySQL database credentials.
 * 2. Change BRAND_GREEN to your desired hex color for the site's branding.
 */

// --- Database Configuration ---
// Replace with your database server host (e.g., 'localhost' or '127.0.0.1')
define('DB_HOST', 'localhost');

// Replace with your database name
define('DB_NAME', 'pharmacy_db');

// Replace with your database username
define('DB_USER', 'root');

// Replace with your database password
define('DB_PASS', '');


// --- Site Configuration ---
// Brand color used throughout the site's CSS.
// The user-provided hex code {BRAND_GREEN_HEX} is replaced here.
// Fallback is #0f9d58 if not provided.
define('BRAND_GREEN', '#0f9d58');

// --- Session & Error Handling ---
// Start the session for all pages
session_start();

// Basic error reporting for development.
// In a production environment, you might want to set this to 0 and log errors to a file.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
