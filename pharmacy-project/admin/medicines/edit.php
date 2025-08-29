<?php
require_once __DIR__ . '/../header.php'; // Admin header

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION['message'] = 'Invalid medicine ID.';
    $_SESSION['message_type'] = 'danger';
    header('Location: list.php');
    exit;
}

// Fetch existing medicine data
$stmt = $pdo->prepare("
    SELECT m.*, s.quantity, mi.image_url
    FROM medicines m
    LEFT JOIN stock s ON m.id = s.medicine_id
    LEFT JOIN medicine_images mi ON m.id = mi.medicine_id AND mi.is_primary = 1
    WHERE m.id = ?
");
$stmt->execute([$id]);
$medicine = $stmt->fetch();

if (!$medicine) {
    $_SESSION['message'] = 'Medicine not found.';
    $_SESSION['message_type'] = 'danger';
    header('Location: list.php');
    exit;
}

// Fetch categories and manufacturers for dropdowns
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll();
$manufacturers = $pdo->query("SELECT id, name FROM manufacturers ORDER BY name ASC")->fetchAll();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and retrieve form data
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
    $manufacturer_id = filter_input(INPUT_POST, 'manufacturer_id', FILTER_VALIDATE_INT);
    $manufacturing_date = trim($_POST['manufacturing_date'] ?? '');
    $expiry_date = trim($_POST['expiry_date'] ?? '');
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
    $image = $_FILES['image'] ?? null;

    // --- Validation ---
    if (empty($name)) $errors[] = 'Medicine name is required.';
    if (empty($price) || !is_numeric($price) || $price < 0) $errors[] = 'A valid price is required.';
    if (empty($category_id)) $errors[] = 'Category is required.';
    if (empty($expiry_date)) $errors[] = 'Expiry date is required.';
    if ($quantity === false || $quantity < 0) $errors[] = 'A valid stock quantity is required.';
    if ($image && $image['error'] !== UPLOAD_ERR_OK && $image['error'] !== UPLOAD_ERR_NO_FILE) $errors[] = 'There was an error with the image upload.';

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // 1. Update `medicines` table
            $sql = "UPDATE medicines SET name = ?, description = ?, price = ?, category_id = ?, manufacturer_id = ?, manufacturing_date = ?, expiry_date = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $description, $price, $category_id, $manufacturer_id, $manufacturing_date ?: null, $expiry_date, $id]);

            // 2. Update `stock` table (using INSERT...ON DUPLICATE KEY UPDATE for simplicity)
            $stock_sql = "INSERT INTO stock (medicine_id, quantity) VALUES (?, ?) ON DUPLICATE KEY UPDATE quantity = VALUES(quantity)";
            $pdo->prepare($stock_sql)->execute([$id, $quantity]);

            // 3. Handle image upload if a new one is provided
            if ($image && $image['error'] === UPLOAD_ERR_OK) {
                // (Optional) Delete old image file
                if (!empty($medicine['image_url']) && file_exists(__DIR__ . '/../../public/' . $medicine['image_url'])) {
                    unlink(__DIR__ . '/../../public/' . $medicine['image_url']);
                }

                $upload_dir = __DIR__ . '/../../public/uploads/medicine_images/';
                $image_name = 'med_' . $id . '_' . basename($image['name']);
                $upload_file = $upload_dir . $image_name;

                if (move_uploaded_file($image['tmp_name'], $upload_file)) {
                    $image_path_for_db = 'uploads/medicine_images/' . $image_name;
                    // Update or insert image record
                    $img_sql = "INSERT INTO medicine_images (medicine_id, image_url, is_primary) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE image_url = VALUES(image_url)";
                    $pdo->prepare($img_sql)->execute([$id, $image_path_for_db]);
                } else {
                    throw new Exception('Failed to move new uploaded file.');
                }
            }

            $pdo->commit();

            $_SESSION['message'] = 'Medicine updated successfully!';
            $_SESSION['message_type'] = 'success';
            header('Location: list.php');
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = 'Database error: Could not update medicine. ' . $e->getMessage();
        }
    }
     // If there are errors, update the medicine array to show the new (failed) values in the form.
    $medicine['name'] = $name;
    $medicine['description'] = $description;
    $medicine['price'] = $price;
    $medicine['category_id'] = $category_id;
    $medicine['manufacturer_id'] = $manufacturer_id;
    $medicine['manufacturing_date'] = $manufacturing_date;
    $medicine['expiry_date'] = $expiry_date;
    $medicine['quantity'] = $quantity;
}
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Medicine</h1>

<?php if (!empty($errors)): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
        <p class="font-bold">Please fix the following errors:</p>
        <ul class="list-disc list-inside">
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <form action="edit.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div>
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 font-bold mb-2">Medicine Name</label>
                    <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($medicine['name']); ?>" required class="w-full px-3 py-2 border rounded">
                </div>
                <div class="mb-4">
                    <label for="price" class="block text-gray-700 font-bold mb-2">Price ($)</label>
                    <input type="number" step="0.01" name="price" id="price" value="<?php echo htmlspecialchars($medicine['price']); ?>" required class="w-full px-3 py-2 border rounded">
                </div>
                <div class="mb-4">
                    <label for="category_id" class="block text-gray-700 font-bold mb-2">Category</label>
                    <select name="category_id" id="category_id" required class="w-full px-3 py-2 border rounded">
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php if ($medicine['category_id'] == $cat['id']) echo 'selected'; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="manufacturer_id" class="block text-gray-700 font-bold mb-2">Manufacturer</label>
                    <select name="manufacturer_id" id="manufacturer_id" class="w-full px-3 py-2 border rounded">
                        <option value="">Select a Manufacturer</option>
                        <?php foreach ($manufacturers as $man): ?>
                            <option value="<?php echo $man['id']; ?>" <?php if ($medicine['manufacturer_id'] == $man['id']) echo 'selected'; ?>><?php echo htmlspecialchars($man['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                 <div class="mb-4">
                    <label for="quantity" class="block text-gray-700 font-bold mb-2">Stock Quantity</label>
                    <input type="number" name="quantity" id="quantity" value="<?php echo htmlspecialchars($medicine['quantity'] ?? 0); ?>" required class="w-full px-3 py-2 border rounded">
                </div>
            </div>
            <!-- Right Column -->
            <div>
                <div class="mb-4">
                    <label for="manufacturing_date" class="block text-gray-700 font-bold mb-2">Manufacturing Date</label>
                    <input type="date" name="manufacturing_date" id="manufacturing_date" value="<?php echo htmlspecialchars($medicine['manufacturing_date']); ?>" class="w-full px-3 py-2 border rounded">
                </div>
                <div class="mb-4">
                    <label for="expiry_date" class="block text-gray-700 font-bold mb-2">Expiry Date</label>
                    <input type="date" name="expiry_date" id="expiry_date" value="<?php echo htmlspecialchars($medicine['expiry_date']); ?>" required class="w-full px-3 py-2 border rounded">
                </div>
                <div class="mb-4">
                    <label for="description" class="block text-gray-700 font-bold mb-2">Description</label>
                    <textarea name="description" id="description" rows="5" class="w-full px-3 py-2 border rounded"><?php echo htmlspecialchars($medicine['description']); ?></textarea>
                </div>
                <div class="mb-4">
                    <label for="image" class="block text-gray-700 font-bold mb-2">Update Primary Image</label>
                    <input type="file" name="image" id="image" class="w-full px-3 py-2 border rounded">
                    <?php if (!empty($medicine['image_url'])): ?>
                        <p class="text-sm text-gray-600 mt-2">Current: <img src="<?php echo BASE_URL . '/' . htmlspecialchars($medicine['image_url']); ?>" alt="Current Image" class="w-20 h-20 object-cover inline-block ml-2 rounded"></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="mt-6 flex items-center justify-end gap-4">
            <a href="list.php" class="text-gray-600 hover:underline">Cancel</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                Update Medicine
            </button>
        </div>
    </form>
</div>

<?php
require_once __DIR__ . '/../footer.php'; // Admin footer
?>
