<?php
require_once __DIR__ . '/../header.php'; // Admin header

// Fetch categories and manufacturers for dropdowns
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll();
$manufacturers = $pdo->query("SELECT id, name FROM manufacturers ORDER BY name ASC")->fetchAll();

$errors = [];
$name = '';
$description = '';
$price = '';
$category_id = '';
$manufacturer_id = '';
$manufacturing_date = '';
$expiry_date = '';
$quantity = 0;

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
    if (!$image || $image['error'] !== UPLOAD_ERR_OK) $errors[] = 'A primary image is required.';
    // More robust image validation (type, size) would go here in a real app.

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // 1. Insert into `medicines` table
            $sql = "INSERT INTO medicines (name, description, price, category_id, manufacturer_id, manufacturing_date, expiry_date) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $description, $price, $category_id, $manufacturer_id, $manufacturing_date ?: null, $expiry_date]);
            $medicine_id = $pdo->lastInsertId();

            // 2. Insert into `stock` table
            $stock_sql = "INSERT INTO stock (medicine_id, quantity) VALUES (?, ?)";
            $pdo->prepare($stock_sql)->execute([$medicine_id, $quantity]);

            // 3. Handle image upload
            $upload_dir = __DIR__ . '/../../public/uploads/medicine_images/';
            $image_name = 'med_' . $medicine_id . '_' . basename($image['name']);
            $upload_file = $upload_dir . $image_name;

            if (move_uploaded_file($image['tmp_name'], $upload_file)) {
                $image_path_for_db = 'uploads/medicine_images/' . $image_name;
                $image_sql = "INSERT INTO medicine_images (medicine_id, image_url, is_primary) VALUES (?, ?, ?)";
                $pdo->prepare($image_sql)->execute([$medicine_id, $image_path_for_db, 1]);
            } else {
                throw new Exception('Failed to move uploaded file.');
            }

            $pdo->commit();

            $_SESSION['message'] = 'Medicine added successfully!';
            $_SESSION['message_type'] = 'success';
            header('Location: list.php');
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = 'Database error: Could not add medicine. ' . $e->getMessage();
        }
    }
}
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Add New Medicine</h1>

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
    <form action="add.php" method="POST" enctype="multipart/form-data">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div>
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 font-bold mb-2">Medicine Name</label>
                    <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>" required class="w-full px-3 py-2 border rounded">
                </div>
                <div class="mb-4">
                    <label for="price" class="block text-gray-700 font-bold mb-2">Price ($)</label>
                    <input type="number" step="0.01" name="price" id="price" value="<?php echo htmlspecialchars($price); ?>" required class="w-full px-3 py-2 border rounded">
                </div>
                <div class="mb-4">
                    <label for="category_id" class="block text-gray-700 font-bold mb-2">Category</label>
                    <select name="category_id" id="category_id" required class="w-full px-3 py-2 border rounded">
                        <option value="">Select a Category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php if ($category_id == $cat['id']) echo 'selected'; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="manufacturer_id" class="block text-gray-700 font-bold mb-2">Manufacturer</label>
                    <select name="manufacturer_id" id="manufacturer_id" class="w-full px-3 py-2 border rounded">
                        <option value="">Select a Manufacturer</option>
                        <?php foreach ($manufacturers as $man): ?>
                            <option value="<?php echo $man['id']; ?>" <?php if ($manufacturer_id == $man['id']) echo 'selected'; ?>><?php echo htmlspecialchars($man['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="quantity" class="block text-gray-700 font-bold mb-2">Initial Stock Quantity</label>
                    <input type="number" name="quantity" id="quantity" value="<?php echo htmlspecialchars($quantity); ?>" required class="w-full px-3 py-2 border rounded">
                </div>
            </div>
            <!-- Right Column -->
            <div>
                <div class="mb-4">
                    <label for="manufacturing_date" class="block text-gray-700 font-bold mb-2">Manufacturing Date</label>
                    <input type="date" name="manufacturing_date" id="manufacturing_date" value="<?php echo htmlspecialchars($manufacturing_date); ?>" class="w-full px-3 py-2 border rounded">
                </div>
                <div class="mb-4">
                    <label for="expiry_date" class="block text-gray-700 font-bold mb-2">Expiry Date</label>
                    <input type="date" name="expiry_date" id="expiry_date" value="<?php echo htmlspecialchars($expiry_date); ?>" required class="w-full px-3 py-2 border rounded">
                </div>
                <div class="mb-4">
                    <label for="description" class="block text-gray-700 font-bold mb-2">Description</label>
                    <textarea name="description" id="description" rows="5" class="w-full px-3 py-2 border rounded"><?php echo htmlspecialchars($description); ?></textarea>
                </div>
                <div class="mb-4">
                    <label for="image" class="block text-gray-700 font-bold mb-2">Primary Image</label>
                    <input type="file" name="image" id="image" required class="w-full px-3 py-2 border rounded">
                </div>
            </div>
        </div>
        <div class="mt-6 flex items-center justify-end gap-4">
            <a href="list.php" class="text-gray-600 hover:underline">Cancel</a>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded">
                Add Medicine
            </button>
        </div>
    </form>
</div>

<?php
require_once __DIR__ . '/../footer.php'; // Admin footer
?>
