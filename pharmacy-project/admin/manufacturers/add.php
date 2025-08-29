<?php
require_once __DIR__ . '/../header.php'; // Admin header

$name = '';
$contact_info = '';
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $contact_info = trim($_POST['contact_info'] ?? '');

    // Validation
    if (empty($name)) {
        $errors[] = 'Manufacturer name is required.';
    }

    // Check if manufacturer name already exists
    $stmt = $pdo->prepare("SELECT id FROM manufacturers WHERE name = ?");
    $stmt->execute([$name]);
    if ($stmt->fetch()) {
        $errors[] = 'A manufacturer with this name already exists.';
    }

    // If no errors, insert into the database
    if (empty($errors)) {
        $sql = "INSERT INTO manufacturers (name, contact_info) VALUES (?, ?)";
        try {
            $pdo->prepare($sql)->execute([$name, $contact_info]);

            $_SESSION['message'] = 'Manufacturer added successfully!';
            $_SESSION['message_type'] = 'success';
            header('Location: list.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Database error: Could not add manufacturer.';
        }
    }
}
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Add New Manufacturer</h1>

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
            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Manufacturer Name</label>
            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>" required
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div class="mb-6">
            <label for="contact_info" class="block text-gray-700 text-sm font-bold mb-2">Contact Info</label>
            <textarea name="contact_info" id="contact_info" rows="4"
                      class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?php echo htmlspecialchars($contact_info); ?></textarea>
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Add Manufacturer
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
