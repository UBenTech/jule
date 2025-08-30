<?php
/**
 * Admin Dashboard
 *
 * This is the main page of the administration panel.
 * It shows summary statistics and links to management pages.
 *
 * To edit brand color or DB settings, see config.php.
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Protect this page
require_once __DIR__ . '/auth.php';

$pdo = db_connect();

// Fetch stats
$medicines_count = $pdo->query("SELECT count(*) FROM medicines")->fetchColumn();
$categories_count = $pdo->query("SELECT count(*) FROM categories")->fetchColumn();
$pending_bookings_count = $pdo->query("SELECT count(*) FROM bookings WHERE status = 'pending'")->fetchColumn();
$unread_advice_count = $pdo->query("SELECT count(*) FROM advice_requests WHERE is_read = 0")->fetchColumn();


$page_title = 'Admin Dashboard';
include __DIR__ . '/../templates/header.php';
?>

<h1>Admin Dashboard</h1>
<p>Welcome, <?php echo esc($_SESSION['username']); ?>! Here's a summary of your pharmacy status.</p>

<div class="admin-dashboard-stats" style="display: flex; gap: 20px; margin-bottom: 2rem;">
    <div class="stat-card" style="flex: 1; padding: 20px; background: #f0f0f0; text-align: center; border-radius: 5px;">
        <h2><?php echo $medicines_count; ?></h2>
        <p>Total Medicines</p>
        <a href="medicines.php" class="btn btn-secondary">Manage Medicines</a>
    </div>
    <div class="stat-card" style="flex: 1; padding: 20px; background: #f0f0f0; text-align: center; border-radius: 5px;">
        <h2><?php echo $categories_count; ?></h2>
        <p>Total Categories</p>
        <a href="categories.php" class="btn btn-secondary">Manage Categories</a>
    </div>
    <div class="stat-card" style="flex: 1; padding: 20px; background: #f0f0f0; text-align: center; border-radius: 5px;">
        <h2><?php echo $pending_bookings_count; ?></h2>
        <p>Pending Bookings</p>
        <a href="bookings.php" class="btn btn-secondary">Manage Bookings</a>
    </div>
     <div class="stat-card" style="flex: 1; padding: 20px; background: #f0f0f0; text-align: center; border-radius: 5px;">
        <h2><?php echo $unread_advice_count; ?></h2>
        <p>Unread Advice Requests</p>
        <a href="advice_requests.php" class="btn btn-secondary">View Requests</a>
    </div>
</div>

<h2>Quick Actions</h2>
<ul>
    <li><a href="medicines.php?action=add">Add New Medicine</a></li>
    <li><a href="categories.php?action=add">Add New Category</a></li>
    <li><a href="stock.php">Manage Stock</a></li>
</ul>


<?php
// Note: A dedicated admin footer could be created, but for simplicity, we reuse the main one.
include __DIR__ . '/../templates/footer.php';
?>
