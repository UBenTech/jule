<?php
// This page lists all categories and provides links to manage them.
require_once __DIR__ . '/../header.php'; // Admin header

// Fetch all categories from the database, ordered by name.
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll();
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Manage Categories</h1>
    <a href="add.php" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
        + Add New Category
    </a>
</div>

<?php if (isset($_SESSION['message'])): ?>
    <div class="bg-<?php echo $_SESSION['message_type'] === 'success' ? 'green' : 'red'; ?>-100 border-l-4 border-<?php echo $_SESSION['message_type'] === 'success' ? 'green' : 'red'; ?>-500 text-<?php echo $_SESSION['message_type'] === 'success' ? 'green' : 'red'; ?>-700 p-4 mb-6" role="alert">
        <p><?php echo htmlspecialchars($_SESSION['message']); ?></p>
    </div>
    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
<?php endif; ?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="w-1/12 text-left py-3 px-4 uppercase font-semibold text-sm">ID</th>
                    <th class="w-4/12 text-left py-3 px-4 uppercase font-semibold text-sm">Name</th>
                    <th class="w-5/12 text-left py-3 px-4 uppercase font-semibold text-sm">Description</th>
                    <th class="w-2/12 text-center py-3 px-4 uppercase font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php if (count($categories) > 0): ?>
                    <?php foreach ($categories as $category): ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-4"><?php echo htmlspecialchars($category['id']); ?></td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($category['name']); ?></td>
                            <td class="py-3 px-4 text-sm"><?php echo htmlspecialchars(substr($category['description'], 0, 100)) . '...'; ?></td>
                            <td class="py-3 px-4 text-center">
                                <a href="edit.php?id=<?php echo $category['id']; ?>" class="text-blue-600 hover:underline mr-4">Edit</a>
                                <a href="delete.php?id=<?php echo $category['id']; ?>"
                                   class="text-red-600 hover:underline"
                                   onclick="return confirm('Are you sure you want to delete this category? This might affect associated medicines.');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center py-6">No categories found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once __DIR__ . '/../footer.php'; // Admin footer
?>
