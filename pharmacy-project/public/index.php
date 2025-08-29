<?php
// This is the main public-facing page.
// It will list all available medicines.

// Set the correct base URL for asset linking
define('BASE_URL', '/pharmacy-project/public');

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php'; // Renders header, nav, and opens main content

// Fetch all medicines with their primary image and category name
$stmt = $pdo->query("
    SELECT
        m.id,
        m.name,
        m.price,
        m.description,
        c.name AS category_name,
        mi.image_url
    FROM medicines m
    LEFT JOIN categories c ON m.category_id = c.id
    LEFT JOIN medicine_images mi ON m.id = mi.medicine_id AND mi.is_primary = 1
    ORDER BY m.name ASC
");
$medicines = $stmt->fetchAll();
?>

<div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
    <h1 class="text-2xl sm:text-3xl font-bold mb-6 text-gray-800 border-b pb-4">Our Products</h1>

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
                        <?php if (!empty($medicine['category_name'])): ?>
                            <a href="category.php?id=<?php echo htmlspecialchars($medicine['category_id'] ?? ''); ?>" class="text-xs text-gray-500 hover:underline">
                                <?php echo htmlspecialchars($medicine['category_name']); ?>
                            </a>
                        <?php endif; ?>
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
            <p class="text-gray-500 text-lg">No medicines are currently available.</p>
            <p class="text-gray-400 mt-2">Please check back later.</p>
        </div>
    <?php endif; ?>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php'; // Renders the footer and closes the HTML
?>
