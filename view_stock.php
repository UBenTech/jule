<?php
/**
 * View Stock Page
 *
 * Displays a public list of all medicines and their current stock levels.
 * It highlights medicines that are out of stock or low on stock.
 *
 * To edit brand color or DB settings, see config.php.
 */

require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$pdo = db_connect();
// We can reuse get_medicines to fetch all medicines.
$medicines = get_medicines($pdo);

$page_title = 'Current Stock Levels';
include 'templates/header.php';
?>

<h1>Current Stock Levels</h1>
<p>This page shows the availability of our medicines. Low stock items are highlighted.</p>

<table class="admin-table">
    <thead>
        <tr>
            <th>Medicine Name</th>
            <th>Category</th>
            <th>Stock Level</th>
            <th>Status</th>
            <th>Expiry Date</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($medicines)): ?>
            <tr>
                <td colspan="5" class="text-center">No medicines found.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($medicines as $medicine):
                $is_expired = check_expired($medicine['expiry_date']);
                $in_stock = stock_available($medicine['quantity_in_stock']);
                $is_low_stock = $medicine['quantity_in_stock'] > 0 && $medicine['quantity_in_stock'] < $medicine['min_stock'];
            ?>
                <tr>
                    <td>
                        <a href="/medicine/<?php echo esc($medicine['slug']); ?>">
                            <?php echo esc($medicine['name']); ?>
                        </a>
                    </td>
                    <td><?php echo esc($medicine['category_name']); ?></td>
                    <td><?php echo esc($medicine['quantity_in_stock']); ?></td>
                    <td>
                        <?php if ($is_expired): ?>
                            <span class="badge badge-expired">Expired</span>
                        <?php elseif (!$in_stock): ?>
                            <span class="badge badge-expired">Out of Stock</span>
                        <?php elseif ($is_low_stock): ?>
                            <span class="badge badge-low-stock">Low Stock</span>
                        <?php else: ?>
                            <span class="badge badge-in-stock">In Stock</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo format_date($medicine['expiry_date']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php
include 'templates/footer.php';
?>
