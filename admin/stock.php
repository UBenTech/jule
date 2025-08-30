<?php
/**
 * Admin: Manage Stock
 *
 * Allows administrators to manually adjust stock levels for medicines.
 * All adjustments are logged in the inventory_transactions table.
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/auth.php';

$pdo = db_connect();
$medicines = get_medicines($pdo);
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $medicine_id = $_POST['medicine_id'] ?? null;
    $transaction_type = $_POST['transaction_type'] ?? null;
    $quantity = (int)($_POST['quantity'] ?? 0);
    $reason = trim($_POST['reason'] ?? '');
    $admin_user_id = $_SESSION['user_id'];

    if (!$medicine_id || !$transaction_type || $quantity <= 0) {
        $error = 'Please fill all required fields and provide a valid quantity.';
    } else {
        $pdo->beginTransaction();
        try {
            // Get current stock
            $stmt = $pdo->prepare("SELECT quantity_in_stock FROM medicines WHERE id = ? FOR UPDATE");
            $stmt->execute([$medicine_id]);
            $current_stock = $stmt->fetchColumn();

            $quantity_change = 0;
            if ($transaction_type === 'stock_in') {
                $quantity_change = abs($quantity); // Always positive
            } elseif ($transaction_type === 'stock_out') {
                $quantity_change = -abs($quantity); // Always negative
            } elseif ($transaction_type === 'adjustment') {
                // For adjustment, the quantity entered IS the change (can be positive or negative)
                $quantity_change = $quantity;
            }

            $new_stock = $current_stock + $quantity_change;

            if ($new_stock < 0) {
                throw new Exception('Stock level cannot go below zero.');
            }

            // 1. Update medicine stock
            $update_stmt = $pdo->prepare("UPDATE medicines SET quantity_in_stock = ? WHERE id = ?");
            $update_stmt->execute([$new_stock, $medicine_id]);

            // 2. Log the transaction
            $log_stmt = $pdo->prepare("INSERT INTO inventory_transactions (medicine_id, transaction_type, quantity_change, reason, user_id) VALUES (?, ?, ?, ?, ?)");
            $log_stmt->execute([$medicine_id, $transaction_type, $quantity_change, $reason, $admin_user_id]);

            $pdo->commit();
            $message = 'Stock level updated and transaction logged successfully.';
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'An error occurred: ' . $e->getMessage();
        }
    }
}


$page_title = 'Manage Stock';
include __DIR__ . '/../templates/admin_header.php';
?>

<h1>Manage Stock Levels</h1>
<p>Use this form to record stock changes, such as receiving a new shipment (Stock In) or removing expired items (Stock Out).</p>

<?php if ($message): ?>
    <div style="padding: 15px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px; margin-bottom: 20px;">
        <?php echo $message; ?>
    </div>
<?php endif; ?>
<?php if ($error): ?>
    <div style="padding: 15px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px; margin-bottom: 20px;">
        <?php echo $error; ?>
    </div>
<?php endif; ?>


<form action="<?php echo BASE_URL; ?>/admin/stock.php" method="POST" style="max-width: 600px;">
    <div class="form-group">
        <label for="medicine_id">Select Medicine *</label>
        <select name="medicine_id" id="medicine_id" required>
            <option value="">-- Choose a medicine --</option>
            <?php foreach ($medicines as $med): ?>
                <option value="<?php echo $med['id']; ?>"><?php echo esc($med['name']); ?> (Current Stock: <?php echo $med['quantity_in_stock']; ?>)</option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="transaction_type">Transaction Type *</label>
        <select name="transaction_type" id="transaction_type" required>
            <option value="stock_in">Stock In (+)</option>
            <option value="stock_out">Stock Out (-)</option>
            <option value="adjustment">Manual Adjustment</option>
        </select>
        <small>For adjustments, a positive number adds stock, a negative number removes it.</small>
    </div>

    <div class="form-group">
        <label for="quantity">Quantity *</label>
        <input type="number" name="quantity" id="quantity" required placeholder="e.g., 50 or -10 for adjustment">
    </div>

    <div class="form-group">
        <label for="reason">Reason for transaction (e.g., "New shipment from HealthWell", "Expired items removed")</label>
        <input type="text" name="reason" id="reason">
    </div>

    <button type="submit" class="btn btn-primary">Update Stock</button>
</form>


<h2 class="mt-2">Recent Inventory Transactions</h2>
<table class="admin-table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Medicine</th>
            <th>Type</th>
            <th>Quantity Change</th>
            <th>Reason</th>
            <th>Admin</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $log_stmt = $pdo->query("
            SELECT it.*, m.name as medicine_name, u.username as admin_username
            FROM inventory_transactions it
            JOIN medicines m ON it.medicine_id = m.id
            JOIN users u ON it.user_id = u.id
            ORDER BY it.transaction_date DESC
            LIMIT 20
        ");
        while ($log = $log_stmt->fetch()):
        ?>
            <tr>
                <td><?php echo format_date($log['transaction_date']); ?></td>
                <td><?php echo esc($log['medicine_name']); ?></td>
                <td><?php echo esc($log['transaction_type']); ?></td>
                <td><?php echo esc($log['quantity_change']); ?></td>
                <td><?php echo esc($log['reason']); ?></td>
                <td><?php echo esc($log['admin_username']); ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>


<?php
include __DIR__ . '/../templates/admin_footer.php';
?>
