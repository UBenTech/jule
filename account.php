<?php
/**
 * Account Page (Placeholder)
 *
 * This page serves as a placeholder for future user account functionality.
 * Currently, only an admin account system is implemented.
 *
 * To edit brand color or DB settings, see config.php.
 */

require_once 'config.php';
require_once 'includes/functions.php';

$page_title = 'My Account';
include 'templates/header.php';
?>

<h1>My Account</h1>

<div class="card" style="padding: 20px; background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px;">
    <h2>Feature Coming Soon</h2>
    <p>
        A dedicated portal for registered users is currently under development.
        This will allow you to view your booking history, manage your wishlist, and more.
    </p>
    <p>
        For now, please use the guest wishlist and booking features.
    </p>
    <hr>
    <p>
        If you are an administrator, you can log in to the admin panel.
    </p>
    <a href="/admin/login.php" class="btn btn-secondary">Admin Login</a>
</div>

<?php
include 'templates/footer.php';
?>
