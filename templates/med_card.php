<?php
/**
 * Medicine Card Template
 *
 * This is a reusable template for displaying a single medicine in a grid.
 * It expects a $medicine variable (array) to be available in the scope where it's included.
 *
 * Required variables:
 * - $medicine: An associative array containing medicine details.
 *
 * To edit brand color or DB settings, see config.php.
 */

// Ensure the medicine variable exists to avoid errors
if (!isset($medicine)) {
    echo '<p>Error: Medicine data is missing for this card.</p>';
    return;
}

$is_expired = check_expired($medicine['expiry_date']);
$in_stock = stock_available($medicine['quantity_in_stock']);
$is_low_stock = $medicine['quantity_in_stock'] > 0 && $medicine['quantity_in_stock'] < $medicine['min_stock'];

?>
<div class="med-card">
    <a href="/medicine/<?php echo esc($medicine['slug']); ?>">
        <img src="/<?php echo esc($medicine['thumbnail_url']); ?>" alt="<?php echo esc($medicine['name']); ?>" class="med-card-img">
    </a>
    <div class="med-card-body">
        <h3 class="med-card-title">
            <a href="/medicine/<?php echo esc($medicine['slug']); ?>"><?php echo esc($medicine['name']); ?></a>
        </h3>

        <p class="med-card-text">
            <?php echo esc(substr($medicine['description'], 0, 80)); ?>...
        </p>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
            <span class="med-card-price"><?php echo format_price($medicine['price']); ?></span>

            <?php if ($is_expired): ?>
                <span class="badge badge-expired">Expired</span>
            <?php elseif (!$in_stock): ?>
                <span class="badge badge-expired">Out of Stock</span>
            <?php elseif ($is_low_stock): ?>
                <span class="badge badge-low-stock">Low Stock</span>
            <?php else: ?>
                <span class="badge badge-in-stock">In Stock</span>
            <?php endif; ?>
        </div>
    </div>
    <div class="med-card-footer">
        <a href="/book.php?medicine_id=<?php echo esc($medicine['id']); ?>" class="btn btn-secondary btn-sm">Book Now</a>
        <button class="btn btn-primary btn-sm btn-wishlist" data-medicine-id="<?php echo esc($medicine['id']); ?>">
            Add to Wishlist
        </button>
    </div>
</div>
