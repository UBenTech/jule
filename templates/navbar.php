<?php
/**
 * Global Navigation Bar
 *
 * This template contains the main site navigation. It shows different links
 * based on whether a user is logged in as an admin.
 *
 * To edit brand color or DB settings, see config.php.
 */
?>
<header class="navbar">
    <div class="container">
        <a href="/" class="logo">PharmaCare</a>
        <nav>
            <ul class="nav-links">
                <li><a href="/">Home</a></li>
                <li><a href="/view_stock.php">View Stock</a></li>
                <li><a href="/advice.php">Request Advice</a></li>
                <li><a href="/wishlist.php">Wishlist</a></li>
                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) : ?>
                    <li><a href="/admin/">Admin Dashboard</a></li>
                    <li><a href="/admin/login.php?action=logout">Logout</a></li>
                <?php else: ?>
                    <li><a href="/admin/login.php">Admin Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
