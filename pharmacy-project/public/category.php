<?php
// This page lists medicines for a specific category.

define('BASE_URL', '/pharmacy-project/public');

require_once __DIR__ . '/../includes/db.php';

// Get category ID from URL and validate it.
$category_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$category = null;

if ($category_id) {
    // Fetch the category details to display its name.
    $cat_stmt = $pdo->prepare("SELECT id, name, description FROM categories WHERE id = ?");
    $cat_stmt->execute([$category_id]);
    $category = $cat_stmt->fetch();
}

// If no valid category is found, redirect to the homepage.
if (!$category) {
    header('Location: index.php');
    exit;
}

// Fetch all medicines belonging to this category.
$med_stmt = $pdo->prepare("
    SELECT
        m.id,
        m.name,
        m.price,
        m.description,
        mi.image_url
    FROM medicines m
    LEFT JOIN medicine_images mi ON m.id = mi.medicine_id AND mi.is_primary = 1
    WHERE m.category_id = ?
    ORDER BY m.name ASC
");
$med_stmt->execute([$category_id]);
$medicines = $med_stmt->fetchAll();

// Now, include the header. This is done after fetching data and validation
// to avoid sending HTML before a potential redirect.
require_once __DIR__ . '/../includes/header.php';
?>

<div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
    <div class="border-b pb-4 mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">
            Category: <span class="text-blue-600"><?php echo htmlspecialchars($category['name']); ?></span>
        </h1>
        <?php if (!empty($category['description'])): ?>
            <p class="mt-2 text-gray-600"><?php echo htmlspecialchars($category['description']); ?></p>
        <?php endif; ?>
    </div>

    <?php if (count($medicines) > 0): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php foreach ($medicines as $medicine): ?>
                <div class="border rounded-lg bg-white overflow-hidden shadow-lg hover:shadow-xl transition-shadow duration-300 ease-in-out flex flex-col">
                    <a href="medicine.php?id=<?php echo htmlspecialchars($medicine['id']); ?>" class="block">
                        <img src="<?php echo htmlspecialchars(BASE_URL . '/' . ($medicine['image_url'] ?? 'assets/images/default_med_image.png')); ?>"
                             alt="<?php echo htmlspecialchars($medicine['name']); ?>"
                             class="w-full h-48 object-cover"
                             onerror="this.onerror=null;this.src='<?php echo BASE_URL; ?>/assets/images/default_med_image.png';">
                    </a>
                    <div class="p-4 flex flex-col flex-grow">
                        <h2 class="text-lg font-semibold text-gray-900 leading-tight">
                            <a href="medicine.php?id=<?php echo htmlspecialchars($medicine['id']); ?>" class="hover:text-blue-600">
                                <?php echo htmlspecialchars($medicine['name']); ?>
                            </a>
                        </h2>
                        <p class="text-gray-600 mt-2 text-sm flex-grow"><?php echo htmlspecialchars(substr($medicine['description'], 0, 50)) . '...'; ?></p>
                        <div class="flex justify-between items-center mt-4">
                            <span class="text-xl font-bold text-gray-800">$<?php echo htmlspecialchars(number_format($medicine['price'], 2)); ?></span>
                            <a href="cart.php?action=add&id=<?php echo htmlspecialchars($medicine['id']); ?>" class="bg-blue-500 text-white px-3 py-1.5 rounded-md text-xs font-semibold hover:bg-blue-600 transition-colors duration-300">
                                Add to Cart
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-16">
            <p class="text-gray-500 text-lg">There are no medicines available in this category at the moment.</p>
            <p class="text-gray-400 mt-2">Please try another category or check back later.</p>
        </div>
    <?php endif; ?>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
