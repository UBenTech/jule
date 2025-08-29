<?php
// This is the main dashboard page for the admin panel.
require_once __DIR__ . '/header.php'; // The header includes the database connection and authentication check.

// --- Fetch Statistics for Dashboard Widgets ---

// Get total number of registered users.
$user_count = $pdo->query("SELECT count(*) FROM users")->fetchColumn();

// Get total number of unique medicines in the catalog.
$medicine_count = $pdo->query("SELECT count(*) FROM medicines")->fetchColumn();

// Get the number of bookings that are currently pending review.
$pending_booking_count = $pdo->query("SELECT count(*) FROM bookings WHERE status = 'pending'")->fetchColumn();

// Get the number of items with low stock.
// The threshold is hardcoded here but could be moved to a setting in the 'settings' table.
$low_stock_threshold = 20;
$low_stock_count = $pdo->query("SELECT count(*) FROM stock WHERE quantity < {$low_stock_threshold}")->fetchColumn();

?>

<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">Admin Dashboard</h1>
    <p class="text-gray-600">A quick overview of your pharmacy's status.</p>
</div>

<!-- Statistics Widgets -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
        <div>
            <h2 class="text-sm font-medium text-gray-500 uppercase">Total Users</h2>
            <p class="text-3xl font-bold text-gray-900 mt-1"><?php echo $user_count; ?></p>
        </div>
        <!-- You can add an icon here -->
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
        <div>
            <h2 class="text-sm font-medium text-gray-500 uppercase">Listed Medicines</h2>
            <p class="text-3xl font-bold text-gray-900 mt-1"><?php echo $medicine_count; ?></p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
        <div>
            <h2 class="text-sm font-medium text-gray-500 uppercase">Pending Bookings</h2>
            <p class="text-3xl font-bold text-yellow-600 mt-1"><?php echo $pending_booking_count; ?></p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
        <div>
            <h2 class="text-sm font-medium text-gray-500 uppercase">Low Stock Items</h2>
            <p class="text-3xl font-bold text-red-600 mt-1"><?php echo $low_stock_count; ?></p>
        </div>
    </div>
</div>

<!-- Quick Actions Section -->
<div class="mt-10 bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-xl font-bold mb-4 text-gray-800">Quick Actions</h2>
    <div class="flex flex-wrap gap-4">
        <a href="<?php echo ADMIN_BASE_URL; ?>/medicines/add.php" class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition-colors">
            Add New Medicine
        </a>
        <a href="<?php echo ADMIN_BASE_URL; ?>/categories/add.php" class="bg-green-600 text-white px-5 py-2 rounded-lg hover:bg-green-700 transition-colors">
            Add New Category
        </a>
        <a href="<?php echo ADMIN_BASE_URL; ?>/bookings/list.php" class="bg-yellow-500 text-white px-5 py-2 rounded-lg hover:bg-yellow-600 transition-colors">
            View Pending Bookings
        </a>
        <a href="<?php echo ADMIN_BASE_URL; ?>/stock/list.php" class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
            Manage Stock
        </a>
    </div>
</div>

<?php
// Include the admin footer to close the HTML structure.
require_once __DIR__ . '/footer.php';
?>
