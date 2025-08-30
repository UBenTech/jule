<?php
/**
 * Admin Authentication Check
 *
 * This script checks if a user is logged in as an administrator.
 * If not, it redirects them to the login page.
 *
 * This file should be required at the top of every protected admin page.
 */

// Ensure session is started. config.php should do this, but this is a safeguard.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Use the is_admin() function from functions.php
if (!is_admin()) {
    // Redirect to login page
    header('Location: /admin/login.php');
    exit();
}
?>
