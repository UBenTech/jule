<?php
/**
 * Single Medicine Detail Page
 *
 * Displays detailed information about a specific medicine,
 * retrieved via its slug from the URL.
 *
 * URL: /medicine/{slug}
 * To edit brand color or DB settings, see config.php.
 */

require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$slug = $_GET['slug'] ?? null;

if (!$slug) {
    header("Location: /");
    exit();
}

$pdo = db_connect();
$medicine = get_medicine_by_slug($pdo, $slug);

if (!$medicine) {
    http_response_code(404);
    $page_title = 'Medicine Not Found';
    include 'templates/header.php';
    echo "<h1>404 - Medicine Not Found</h1>";
    echo "<p>Sorry, the medicine you are looking for does not exist.</p>";
    include 'templates/footer.php';
    exit();
}

$page_title = esc($medicine['name']);
$is_expired = check_expired($medicine['expiry_date']);
$in_stock = stock_available($medicine['quantity_in_stock']);
$is_low_stock = $medicine['quantity_in_stock'] > 0 && $medicine['quantity_in_stock'] < $medicine['min_stock'];

include 'templates/header.php';
?>

<div class="medicine-detail">
    <h1><?php echo esc($medicine['name']); ?></h1>
    <div class="medicine-detail-grid">
        <div class="medicine-detail-image">
            <img src="/<?php echo esc($medicine['thumbnail_url']); ?>" alt="<?php echo esc($medicine['name']); ?>" class="medicine-detail-img">
        </div>
        <div class="medicine-detail-info">
            <p><?php echo nl2br(esc($medicine['description'])); ?></p>

            <table class="medicine-info-table">
                <tr>
                    <td>Price</td>
                    <td><strong><?php echo format_price($medicine['price']); ?></strong></td>
                </tr>
                <tr>
                    <td>Stock Status</td>
                    <td>
                        <?php if ($is_expired): ?>
                            <span class="badge badge-expired">Expired</span>
                        <?php elseif (!$in_stock): ?>
                            <span class="badge badge-expired">Out of Stock</span>
                        <?php elseif ($is_low_stock): ?>
                            <span class="badge badge-low-stock">Low Stock (<?php echo esc($medicine['quantity_in_stock']); ?> left)</span>
                        <?php else: ?>
                            <span class="badge badge-in-stock">In Stock (<?php echo esc($medicine['quantity_in_stock']); ?> available)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>Category</td>
                    <td><a href="/category/<?php echo esc($medicine['category_slug'] ?? slugify($medicine['category_name'])); ?>"><?php echo esc($medicine['category_name']); ?></a></td>
                </tr>
                <tr>
                    <td>Group</td>
                    <td><a href="/group/<?php echo esc(urlencode($medicine['group_name'])); ?>"><?php echo esc($medicine['group_name']); ?></a></td>
                </tr>
                <tr>
                    <td>Manufacturer</td>
                    <td><?php echo esc($medicine['manufacturer_name'] ?? 'N/A'); ?></td>
                </tr>
                 <tr>
                    <td>Batch Number</td>
                    <td><?php echo esc($medicine['batch_number'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <td>Manufacture Date</td>
                    <td><?php echo format_date($medicine['manufacture_date']); ?></td>
                </tr>
                <tr>
                    <td>Expiry Date</td>
                    <td><?php echo format_date($medicine['expiry_date']); ?></td>
                </tr>
            </table>

            <div class="mt-2">
                <a href="/book.php?medicine_id=<?php echo esc($medicine['id']); ?>" class="btn btn-primary">Book Now</a>
                <button class="btn btn-secondary btn-wishlist" data-medicine-id="<?php echo esc($medicine['id']); ?>">Add to Wishlist</button>
            </div>
        </div>
    </div>
</div>


<?php
include 'templates/footer.php';
?>
