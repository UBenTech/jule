<?php
/**
 * Category Page
 *
 * Lists all medicines belonging to a specific category.
 * The category is identified by its slug in the URL.
 *
 * URL: /category/{slug}
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
$medicines = get_medicines($pdo, ['category_slug' => $slug]);

// To get the category name for the title, we can either join in get_medicines or do a separate query.
// For simplicity, we'll just derive it from the first result if it exists.
$category_name = !empty($medicines) ? $medicines[0]['category_name'] : ucfirst(str_replace('-', ' ', $slug));

$page_title = 'Category: ' . esc($category_name);
include 'templates/header.php';
?>

<h1>Category: <?php echo esc($category_name); ?></h1>

<?php if (empty($medicines)): ?>
    <p>There are no medicines available in this category.</p>
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
