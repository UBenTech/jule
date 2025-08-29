<?php
// This page displays detailed information for a single medicine.

define('BASE_URL', '/pharmacy-project/public');

require_once __DIR__ . '/../includes/db.php';

// Validate the medicine ID from the URL.
$medicine_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$medicine = null;

if ($medicine_id) {
    // Fetch detailed information about the medicine.
    // This query joins multiple tables to get all necessary data at once.
    $stmt = $pdo->prepare("
        SELECT
            m.*,
            c.name AS category_name,
            mf.name AS manufacturer_name,
            mi.image_url,
            s.quantity AS stock_quantity
        FROM medicines m
        LEFT JOIN categories c ON m.category_id = c.id
        LEFT JOIN manufacturers mf ON m.manufacturer_id = mf.id
        LEFT JOIN medicine_images mi ON m.id = mi.medicine_id AND mi.is_primary = 1
        LEFT JOIN stock s ON m.id = s.medicine_id
        WHERE m.id = ?
    ");
    $stmt->execute([$medicine_id]);
    $medicine = $stmt->fetch();
}

// If no medicine is found for the given ID, redirect to the homepage.
if (!$medicine) {
    header('Location: index.php');
    exit;
}

// Set stock quantity, defaulting to 0 if null.
$stock_quantity = $medicine['stock_quantity'] ?? 0;

// Include header after data fetching and validation.
require_once __DIR__ . '/../includes/header.php';
?>

<div class="bg-white rounded-lg shadow-xl p-6 md:p-8 max-w-4xl mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Medicine Image Section -->
        <div class="flex-shrink-0">
            <img src="<?php echo htmlspecialchars(BASE_URL . '/' . ($medicine['image_url'] ?? 'assets/images/default_med_image.png')); ?>"
                 alt="<?php echo htmlspecialchars($medicine['name']); ?>"
                 class="w-full h-auto rounded-lg shadow-md object-cover"
                 onerror="this.onerror=null;this.src='<?php echo BASE_URL; ?>/assets/images/default_med_image.png';">
        </div>

        <!-- Medicine Details Section -->
        <div class="flex flex-col">
            <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-2"><?php echo htmlspecialchars($medicine['name']); ?></h1>

            <div class="flex flex-wrap items-center text-sm text-gray-600 mb-4">
                <?php if (!empty($medicine['category_id']) && !empty($medicine['category_name'])): ?>
                    <a href="category.php?id=<?php echo htmlspecialchars($medicine['category_id']); ?>" class="text-blue-600 hover:underline mr-4">
                        <?php echo htmlspecialchars($medicine['category_name']); ?>
                    </a>
                <?php endif; ?>
                <?php if (!empty($medicine['manufacturer_name'])): ?>
                    <span class="text-gray-500">By: <?php echo htmlspecialchars($medicine['manufacturer_name']); ?></span>
                <?php endif; ?>
            </div>

            <div class="text-gray-700 mb-6 prose max-w-none">
                <?php echo nl2br(htmlspecialchars($medicine['description'])); ?>
            </div>

            <div class="mt-auto">
                <div class="mb-4">
                    <span class="text-3xl font-extrabold text-gray-900">$<?php echo htmlspecialchars(number_format($medicine['price'], 2)); ?></span>
                </div>

                <div class="mb-6 text-sm">
                    <p>
                        Availability:
                        <?php if ($stock_quantity > 0): ?>
                            <span class="font-semibold text-green-600">In Stock (<?php echo $stock_quantity; ?> available)</span>
                        <?php else: ?>
                            <span class="font-semibold text-red-600">Out of Stock</span>
                        <?php endif; ?>
                    </p>
                    <p class="text-gray-500 mt-1">Expiry Date: <?php echo htmlspecialchars(date('F Y', strtotime($medicine['expiry_date']))); ?></p>
                </div>

                <!-- Add to Cart Form -->
                <form action="cart.php" method="POST" class="flex items-stretch">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($medicine['id']); ?>">
                    <div class="flex items-center border border-gray-300 rounded-l-md">
                        <input type="number" name="quantity" value="1" min="1" max="<?php echo $stock_quantity; ?>"
                               class="w-16 text-center focus:outline-none"
                               <?php if ($stock_quantity <= 0) echo 'disabled'; ?>>
                    </div>
                    <button type="submit"
                            class="bg-blue-600 text-white font-bold py-3 px-6 rounded-r-md hover:bg-blue-700 transition-colors duration-300 flex-grow <?php if ($stock_quantity <= 0) echo 'opacity-50 cursor-not-allowed'; ?>"
                            <?php if ($stock_quantity <= 0) echo 'disabled'; ?>>
                        Add to Cart
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
