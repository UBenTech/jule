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

// --- Base URL Configuration ---
// The absolute URL to the project root.
// IMPORTANT: Update this to match your server environment.
// Example: http://localhost/pharmacy-project
// Example: https://yourdomain.com/demo/aropharmacy
define('BASE_URL', '/demo/aropharmacy'); // Using a relative path for flexibility

// --- Database Configuration ---
define('DB_HOST', 'localhost');
define('DB_NAME', 'u662439561_pharmacy');
define('DB_USER', 'u662439561_pharmacy1');
define('DB_PASS', '1a!Wh]4Tx^');


// --- Site Configuration ---
// Brand color used throughout the site's CSS.
define('BRAND_GREEN', 'green');

// --- Session & Error Handling ---
// Start the session for all pages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Basic error reporting for development.
// In a production environment, you might want to set this to 0 and log errors to a file.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
