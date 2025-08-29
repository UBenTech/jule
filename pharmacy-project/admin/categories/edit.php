<?php
require_once __DIR__ . '/../header.php'; // Admin header

// Get the category ID from the URL and validate it.
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$category = null;
$errors = [];

if ($id) {
    // Fetch the existing category from the database.
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    $category = $stmt->fetch();
}

// If no category was found for the ID, redirect to the list page.
if (!$category) {
    $_SESSION['message'] = 'Category not found.';
    $_SESSION['message_type'] = 'danger';
    header('Location: list.php');
    exit;
}

// Handle form submission for updating the category.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // Validation
    if (empty($name)) {
        $errors[] = 'Category name is required.';
    }

    // Check if the new name is already taken by another category.
    $stmt = $pdo->prepare("SELECT id FROM categories WHERE name = ? AND id != ?");
    $stmt->execute([$name, $id]);
    if ($stmt->fetch()) {
        $errors[] = 'Another category with this name already exists.';
    }

    // If no errors, update the database.
    if (empty($errors)) {
        $sql = "UPDATE categories SET name = ?, description = ? WHERE id = ?";
        try {
            $pdo->prepare($sql)->execute([$name, $description, $id]);

            // Set success message and redirect.
            $_SESSION['message'] = 'Category updated successfully!';
            $_SESSION['message_type'] = 'success';
            header('Location: list.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Database error: Could not update category.';
            // In a real app, you would log this error.
        }
    }
    // If there were errors, update the category array to show the new (failed) values in the form.
    $category['name'] = $name;
    $category['description'] = $description;
}
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Category</h1>

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
    <form action="edit.php?id=<?php echo $id; ?>" method="POST">
        <div class="mb-4">
            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Category Name</label>
            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($category['name']); ?>" required
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div class="mb-6">
            <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
            <textarea name="description" id="description" rows="4"
                      class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?php echo htmlspecialchars($category['description']); ?></textarea>
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Update Category
            </button>
            <a href="list.php" class="inline-block align-baseline font-bold text-sm text-gray-600 hover:text-gray-800">
                Cancel
            </a>
        </div>
    </form>
</div>

<?php
require_once __DIR__ . '/../footer.php'; // Admin footer
?>
