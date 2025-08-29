<?php
require_once __DIR__ . '/../header.php'; // Admin header

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$manufacturer = null;
$errors = [];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM manufacturers WHERE id = ?");
    $stmt->execute([$id]);
    $manufacturer = $stmt->fetch();
}

if (!$manufacturer) {
    $_SESSION['message'] = 'Manufacturer not found.';
    $_SESSION['message_type'] = 'danger';
    header('Location: list.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $contact_info = trim($_POST['contact_info'] ?? '');

    if (empty($name)) {
        $errors[] = 'Manufacturer name is required.';
    }

    $stmt = $pdo->prepare("SELECT id FROM manufacturers WHERE name = ? AND id != ?");
    $stmt->execute([$name, $id]);
    if ($stmt->fetch()) {
        $errors[] = 'Another manufacturer with this name already exists.';
    }

    if (empty($errors)) {
        $sql = "UPDATE manufacturers SET name = ?, contact_info = ? WHERE id = ?";
        try {
            $pdo->prepare($sql)->execute([$name, $contact_info, $id]);

            $_SESSION['message'] = 'Manufacturer updated successfully!';
            $_SESSION['message_type'] = 'success';
            header('Location: list.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Database error: Could not update manufacturer.';
        }
    }
    // If there were errors, update the array to show the new (failed) values in the form.
    $manufacturer['name'] = $name;
    $manufacturer['contact_info'] = $contact_info;
}
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Manufacturer</h1>

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
            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Manufacturer Name</label>
            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($manufacturer['name']); ?>" required
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div class="mb-6">
            <label for="contact_info" class="block text-gray-700 text-sm font-bold mb-2">Contact Info</label>
            <textarea name="contact_info" id="contact_info" rows="4"
                      class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?php echo htmlspecialchars($manufacturer['contact_info']); ?></textarea>
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Update Manufacturer
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
