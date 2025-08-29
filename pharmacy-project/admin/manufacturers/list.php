<?php
require_once __DIR__ . '/../header.php'; // Admin header

// Fetch all manufacturers from the database, ordered by name.
$stmt = $pdo->query("SELECT * FROM manufacturers ORDER BY name ASC");
$manufacturers = $stmt->fetchAll();
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Manage Manufacturers</h1>
    <a href="add.php" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
        + Add New Manufacturer
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
                    <th class="w-5/12 text-left py-3 px-4 uppercase font-semibold text-sm">Contact Info</th>
                    <th class="w-2/12 text-center py-3 px-4 uppercase font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php if (count($manufacturers) > 0): ?>
                    <?php foreach ($manufacturers as $manufacturer): ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-4"><?php echo htmlspecialchars($manufacturer['id']); ?></td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($manufacturer['name']); ?></td>
                            <td class="py-3 px-4 text-sm"><?php echo htmlspecialchars($manufacturer['contact_info']); ?></td>
                            <td class="py-3 px-4 text-center">
                                <a href="edit.php?id=<?php echo $manufacturer['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                                <!-- Deletion is omitted as per file structure -->
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center py-6">No manufacturers found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once __DIR__ . '/../footer.php'; // Admin footer
?>
