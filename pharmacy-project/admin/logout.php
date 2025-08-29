<?php
// This script handles logout specifically for the admin panel.
// It ensures that after an admin logs out, they are redirected
// to the public login page, providing a clear exit from the admin area.

// We are in the /admin/ directory, so includes are one level up.
require_once __DIR__ . '/../includes/auth.php';

// Call the centralized logout function.
log_out_user();

// Define the public base URL to construct the redirect path.
if (!defined('BASE_URL')) {
    define('BASE_URL', '/pharmacy-project/public');
}

// Redirect to the main public login page.
header('Location: ' . BASE_URL . '/login.php');
exit;
