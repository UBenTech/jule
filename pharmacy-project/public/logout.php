<?php
// This script handles the user logout process.

require_once __DIR__ . '/../includes/auth.php';

// Call the centralized logout function to destroy the session
log_out_user();

// Redirect the user to the login page after logging out.
// This is a good practice to provide clear feedback that they are no longer signed in.
header('Location: login.php');
exit;
