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
        <a href="<?php echo BASE_URL; ?>/" class="logo">PharmaCare</a>
        <nav>
            <ul class="nav-links">
                <li><a href="<?php echo BASE_URL; ?>/">Home</a></li>
                <li><a href="<?php echo BASE_URL; ?>/view_stock.php">View Stock</a></li>
                <li><a href="<?php echo BASE_URL; ?>/advice.php">Request Advice</a></li>
                <li><a href="<?php echo BASE_URL; ?>/wishlist.php">Wishlist</a></li>

                <?php if (isset($_SESSION['user_id'])) : ?>
                    <?php if ($_SESSION['is_admin']) : ?>
                        <li><a href="<?php echo BASE_URL; ?>/admin/">Admin Dashboard</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo BASE_URL; ?>/account.php">My Account</a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo BASE_URL; ?>/logout.php">Logout</a></li>
                <?php else : ?>
                    <li><a href="<?php echo BASE_URL; ?>/register.php">Register</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
