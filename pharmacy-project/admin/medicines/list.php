<?php
require_once __DIR__ . '/../header.php'; // Admin header

// Fetch all medicines with their category and manufacturer names for display.
$stmt = $pdo->query("
    SELECT
        m.id,
        m.name,
        m.price,
        m.expiry_date,
        c.name AS category_name,
        mf.name AS manufacturer_name,
        s.quantity AS stock_quantity
    FROM medicines m
    LEFT JOIN categories c ON m.category_id = c.id
    LEFT JOIN manufacturers mf ON m.manufacturer_id = mf.id
    LEFT JOIN stock s ON m.id = s.medicine_id
    ORDER BY m.name ASC
");
$medicines = $stmt->fetchAll();
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Manage Medicines</h1>
    <a href="add.php" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
        + Add New Medicine
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
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Name</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Category</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Price</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Stock</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Expires</th>
                    <th class="text-center py-3 px-4 uppercase font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php if (count($medicines) > 0): ?>
                    <?php foreach ($medicines as $medicine):
                        $is_expired = strtotime($medicine['expiry_date']) < time();
                        $is_low_stock = $medicine['stock_quantity'] < 20; // Assuming 20 is low stock threshold
                    ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-100 <?php if ($is_expired) echo 'bg-red-50'; ?>">
                            <td class="py-3 px-4 font-semibold"><?php echo htmlspecialchars($medicine['name']); ?></td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($medicine['category_name'] ?? 'N/A'); ?></td>
                            <td class="py-3 px-4">$<?php echo htmlspecialchars(number_format($medicine['price'], 2)); ?></td>
                            <td class="py-3 px-4 <?php if ($is_low_stock) echo 'text-red-600 font-bold'; ?>">
                                <?php echo htmlspecialchars($medicine['stock_quantity'] ?? '0'); ?>
                            </td>
                            <td class="py-3 px-4 <?php if ($is_expired) echo 'text-red-600 font-bold'; ?>">
                                <?php echo htmlspecialchars(date('M Y', strtotime($medicine['expiry_date']))); ?>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <a href="edit.php?id=<?php echo $medicine['id']; ?>" class="text-blue-600 hover:underline mr-4">Edit</a>
                                <a href="delete.php?id=<?php echo $medicine['id']; ?>"
                                   class="text-red-600 hover:underline"
                                   onclick="return confirm('Are you sure you want to delete this medicine? This action cannot be undone.');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-6">No medicines found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once __DIR__ . '/../footer.php'; // Admin footer
?>
