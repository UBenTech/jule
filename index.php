<?php
/**
 * Homepage
 *
 * This is the main landing page of the site. It displays a grid of
 * available medicines.
 *
 * To edit brand color or DB settings, see config.php.
 */

require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$pdo = db_connect();
$medicines = get_medicines($pdo);

$page_title = 'Welcome to the Pharmacy';
include 'templates/header.php';
?>

<div class="mb-2">
    <h1>Our Medicines</h1>
    <p>Browse our collection of available medicines. Click on any item for more details or to book a pickup.</p>
</div>

<?php if (empty($medicines)): ?>
    <p>No medicines are currently available. Please check back later.</p>
<?php else: ?>
    <div class="medicine-grid">
        <?php foreach ($medicines as $medicine): ?>
            <?php include 'templates/med_card.php'; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php
include 'templates/footer.php';
?>
