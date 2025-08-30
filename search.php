<?php
/**
 * Search Page
 *
 * Provides a search form and displays results for a given query.
 *
 * To edit brand color or DB settings, see config.php.
 */

require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$search_query = $_GET['q'] ?? '';
$medicines = [];

if (!empty($search_query)) {
    $pdo = db_connect();
    $medicines = get_medicines($pdo, ['search' => $search_query]);
}

$page_title = 'Search Results';
include 'templates/header.php';
?>

<h1>Search for Medicines</h1>

<form action="/search.php" method="GET" class="mb-2" style="max-width: 600px;">
    <div class="form-group">
        <label for="q">Search by name, description, or group:</label>
        <input type="search" id="q" name="q" value="<?php echo esc($search_query); ?>" placeholder="e.g., Paracetamol, fever, group one" required>
    </div>
    <button type="submit" class="btn btn-primary">Search</button>
</form>

<hr class="mt-2 mb-2">

<?php if (!empty($search_query)): ?>
    <h2>Results for "<?php echo esc($search_query); ?>"</h2>

    <?php if (empty($medicines)): ?>
        <p>No medicines found matching your search term. Please try again with different keywords.</p>
    <?php else: ?>
        <p>Found <?php echo count($medicines); ?> result(s).</p>
        <div class="medicine-grid">
            <?php foreach ($medicines as $medicine): ?>
                <?php include 'templates/med_card.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>


<?php
include 'templates/footer.php';
?>
