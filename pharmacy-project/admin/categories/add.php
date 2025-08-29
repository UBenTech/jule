<?php
require_once __DIR__ . '/../header.php'; // Admin header

$name = '';
$description = '';
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // Validation
    if (empty($name)) {
        $errors[] = 'Category name is required.';
    }

    // Check if category name already exists
    $stmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
    $stmt->execute([$name]);
    if ($stmt->fetch()) {
        $errors[] = 'A category with this name already exists.';
    }

    // If no errors, insert into the database
    if (empty($errors)) {
        $sql = "INSERT INTO categories (name, description) VALUES (?, ?)";
        try {
            $pdo->prepare($sql)->execute([$name, $description]);

            // Set success message and redirect
            $_SESSION['message'] = 'Category added successfully!';
            $_SESSION['message_type'] = 'success';
            header('Location: list.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Database error: Could not add category.';
            // In a real app, you would log this error.
        }
    }
}
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Add New Category</h1>

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
    <form action="add.php" method="POST">
        <div class="mb-4">
            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Category Name</label>
            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>" required
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div class="mb-6">
            <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
            <textarea name="description" id="description" rows="4"
                      class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?php echo htmlspecialchars($description); ?></textarea>
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Add Category
            </button>
            <a href="list.php" class="inline-block align-baseline font-bold text-sm text-blue-600 hover:text-blue-800">
                Cancel
            </a>
        </div>
    </form>
</div>

<?php
require_once __DIR__ . '/../footer.php'; // Admin footer
?>
