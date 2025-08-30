<?php
/**
 * Admin Area Sidebar Navigation
 */

// A simple way to highlight the active link
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="admin-sidebar">
    <h3>Menu</h3>
    <ul>
        <li>
            <a href="<?php echo BASE_URL; ?>/admin/index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                Dashboard
            </a>
        </li>
        <li>
            <a href="<?php echo BASE_URL; ?>/admin/medicines.php" class="<?php echo ($current_page == 'medicines.php') ? 'active' : ''; ?>">
                Manage Medicines
            </a>
        </li>
        <li>
            <a href="<?php echo BASE_URL; ?>/admin/categories.php" class="<?php echo ($current_page == 'categories.php') ? 'active' : ''; ?>">
                Manage Categories
            </a>
        </li>
        <li>
            <a href="<?php echo BASE_URL; ?>/admin/stock.php" class="<?php echo ($current_page == 'stock.php') ? 'active' : ''; ?>">
                Manage Stock
            </a>
        </li>
        <li>
            <a href="<?php echo BASE_URL; ?>/admin/bookings.php" class="<?php echo ($current_page == 'bookings.php') ? 'active' : ''; ?>">
                View Bookings
            </a>
        </li>
        <li>
            <a href="<?php echo BASE_URL; ?>/admin/advice_requests.php" class="<?php echo ($current_page == 'advice_requests.php') ? 'active' : ''; ?>">
                Advice Requests
            </a>
        </li>
    </ul>
    <hr>
    <a href="<?php echo BASE_URL; ?>/" target="_blank">View Public Site</a>
</aside>
