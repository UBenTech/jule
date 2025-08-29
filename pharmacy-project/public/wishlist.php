<?php
define('BASE_URL', '/pharmacy-project/public');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

require_login(); // Wishlist is a feature for logged-in users only.

$user_id = get_user_id();
$action = $_GET['action'] ?? 'show'; // Default action is to show the wishlist.
$medicine_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$redirect_url = $_SERVER['HTTP_REFERER'] ?? 'index.php'; // Redirect back to the previous page

// --- Handle Add/Remove/Toggle Actions ---
if ($medicine_id && in_array($action, ['add', 'remove', 'toggle'])) {
    // Check if the item exists in the user's wishlist
    $stmt = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = ? AND medicine_id = ?");
    $stmt->execute([$user_id, $medicine_id]);
    $exists = $stmt->fetch();

    if ($action === 'add' && !$exists) {
        $insert_stmt = $pdo->prepare("INSERT INTO wishlist (user_id, medicine_id) VALUES (?, ?)");
        $insert_stmt->execute([$user_id, $medicine_id]);
        $_SESSION['message'] = 'Item added to your wishlist.';
    } elseif ($action === 'remove' && $exists) {
        $delete_stmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND medicine_id = ?");
        $delete_stmt->execute([$user_id, $medicine_id]);
        $_SESSION['message'] = 'Item removed from your wishlist.';
    } elseif ($action === 'toggle') {
        if ($exists) {
            $delete_stmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND medicine_id = ?");
            $delete_stmt->execute([$user_id, $medicine_id]);
            $_SESSION['message'] = 'Item removed from your wishlist.';
        } else {
            $insert_stmt = $pdo->prepare("INSERT INTO wishlist (user_id, medicine_id) VALUES (?, ?)");
            $insert_stmt->execute([$user_id, $medicine_id]);
            $_SESSION['message'] = 'Item added to your wishlist.';
        }
    }

    $_SESSION['message_type'] = 'success';
    // Prevent redirect loops if the action is initiated from the wishlist page itself
    if (strpos($redirect_url, 'wishlist.php') !== false) {
        $redirect_url = 'wishlist.php';
    }
    header("Location: {$redirect_url}");
    exit;
}

// --- Display Wishlist Page ---
$stmt = $pdo->prepare("
    SELECT m.id, m.name, m.price, m.description, mi.image_url
    FROM wishlist w
    JOIN medicines m ON w.medicine_id = m.id
    LEFT JOIN medicine_images mi ON m.id = mi.medicine_id AND mi.is_primary = 1
    WHERE w.user_id = ?
    ORDER BY w.created_at DESC
");
$stmt->execute([$user_id]);
$wishlist_items = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="bg-white rounded-lg shadow-md p-6 max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-6 text-gray-800 border-b pb-4">Your Wishlist</h1>

    <?php if (isset($_SESSION['message'])): ?>
    <div class="bg-<?php echo $_SESSION['message_type'] === 'success' ? 'green' : 'red'; ?>-100 border-l-4 border-<?php echo $_SESSION['message_type'] === 'success' ? 'green' : 'red'; ?>-500 text-<?php echo $_SESSION['message_type'] === 'success' ? 'green' : 'red'; ?>-700 p-4 mb-6" role="alert">
        <p><?php echo htmlspecialchars($_SESSION['message']); ?></p>
    </div>
    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
    <?php endif; ?>

    <?php if (count($wishlist_items) > 0): ?>
        <div class="space-y-6">
            <?php foreach ($wishlist_items as $item): ?>
                <div class="flex flex-col md:flex-row items-center border rounded-lg p-4 hover:shadow-lg transition-shadow">
                    <img src="<?php echo htmlspecialchars(BASE_URL . '/' . ($item['image_url'] ?? 'assets/images/default_med_image.png')); ?>"
                         alt="<?php echo htmlspecialchars($item['name']); ?>"
                         class="w-32 h-32 rounded object-cover mb-4 md:mb-0 md:mr-6"
                         onerror="this.onerror=null;this.src='<?php echo BASE_URL; ?>/assets/images/default_med_image.png';">
                    <div class="flex-grow text-center md:text-left">
                        <h2 class="text-xl font-semibold">
                            <a href="medicine.php?id=<?php echo $item['id']; ?>" class="hover:text-blue-600">
                                <?php echo htmlspecialchars($item['name']); ?>
                            </a>
                        </h2>
                        <p class="text-gray-600 mt-1 text-sm"><?php echo htmlspecialchars(substr($item['description'], 0, 150)) . '...'; ?></p>
                    </div>
                    <div class="flex flex-col items-center md:items-end mt-4 md:mt-0 md:ml-6 space-y-3 flex-shrink-0 w-48">
                        <span class="text-2xl font-bold text-gray-800">$<?php echo htmlspecialchars(number_format($item['price'], 2)); ?></span>
                        <a href="cart.php?action=add&id=<?php echo htmlspecialchars($item['id']); ?>" class="bg-blue-500 text-white px-4 py-2 rounded-md text-sm font-semibold hover:bg-blue-600 w-full text-center">Add to Cart</a>
                        <a href="wishlist.php?action=remove&id=<?php echo $item['id']; ?>" class="text-red-500 hover:underline text-sm font-semibold">Remove from Wishlist</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-16">
            <p class="text-gray-500 text-lg">Your wishlist is currently empty.</p>
            <p class="text-gray-400 mt-2">Browse our products and add items by clicking the heart icon or "Add to Wishlist" button.</p>
        </div>
    <?php endif; ?>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
