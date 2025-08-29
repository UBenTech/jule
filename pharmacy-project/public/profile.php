<?php
define('BASE_URL', '/pharmacy-project/public');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

// Use the helper function to protect this page.
// It will redirect to login.php if the user is not authenticated.
require_login();

// Get the current user's ID from the session.
$user_id = get_user_id();

// Fetch the user's full profile data from the database.
$stmt = $pdo->prepare("SELECT id, username, email, first_name, last_name, created_at FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// If for some reason the user isn't in the DB, log them out.
if (!$user) {
    log_out_user();
    header('Location: login.php');
    exit;
}

// Fetch the user's recent bookings.
$bookings_stmt = $pdo->prepare("SELECT id, status, created_at FROM bookings WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$bookings_stmt->execute([$user_id]);
$bookings = $bookings_stmt->fetchAll();

// Fetch the user's wishlist items.
$wishlist_stmt = $pdo->prepare("
    SELECT m.id, m.name, m.price, mi.image_url
    FROM wishlist w
    JOIN medicines m ON w.medicine_id = m.id
    LEFT JOIN medicine_images mi ON m.id = mi.medicine_id AND mi.is_primary = 1
    WHERE w.user_id = ?
    ORDER BY w.created_at DESC
");
$wishlist_stmt->execute([$user_id]);
$wishlist_items = $wishlist_stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-8 border-b-4 border-blue-600">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">
            Welcome, <?php echo htmlspecialchars($user['first_name'] ?: $user['username']); ?>!
        </h1>
        <p class="text-gray-600">This is your personal dashboard.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content Area (Bookings & Wishlist) -->
        <div class="lg:col-span-2 space-y-8">
            <!-- My Bookings Section -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-bold mb-4 text-gray-700 border-b pb-2">My Recent Bookings</h2>
                <?php if ($bookings): ?>
                    <ul class="space-y-4">
                        <?php foreach ($bookings as $booking): ?>
                            <li class="border p-3 rounded-lg flex justify-between items-center bg-gray-50">
                                <div>
                                    <p class="font-semibold">Booking #<?php echo $booking['id']; ?></p>
                                    <p class="text-sm text-gray-500">Date: <?php echo date('F j, Y', strtotime($booking['created_at'])); ?></p>
                                </div>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full
                                    <?php
                                        switch($booking['status']) {
                                            case 'pending': echo 'bg-yellow-100 text-yellow-800'; break;
                                            case 'confirmed': echo 'bg-green-100 text-green-800'; break;
                                            case 'rejected': echo 'bg-red-100 text-red-800'; break;
                                            case 'completed': echo 'bg-blue-100 text-blue-800'; break;
                                            default: echo 'bg-gray-100 text-gray-800';
                                        }
                                    ?>">
                                    <?php echo htmlspecialchars(ucfirst($booking['status'])); ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-gray-500">You have not made any bookings yet.</p>
                <?php endif; ?>
            </div>

            <!-- My Wishlist Section -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-bold mb-4 text-gray-700 border-b pb-2">My Wishlist</h2>
                <?php if ($wishlist_items): ?>
                     <ul class="space-y-4">
                        <?php foreach ($wishlist_items as $item): ?>
                            <li class="flex items-center justify-between border-b pb-3 last:border-b-0">
                                <div class="flex items-center">
                                    <img src="<?php echo htmlspecialchars(BASE_URL . '/' . ($item['image_url'] ?? 'assets/images/default_med_image.png')); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-12 h-12 rounded object-cover mr-4">
                                    <div>
                                        <a href="medicine.php?id=<?php echo $item['id']; ?>" class="font-semibold hover:text-blue-600"><?php echo htmlspecialchars($item['name']); ?></a>
                                        <p class="text-gray-600">$<?php echo htmlspecialchars(number_format($item['price'], 2)); ?></p>
                                    </div>
                                </div>
                                <a href="wishlist.php?action=remove&id=<?php echo $item['id']; ?>" class="text-red-500 hover:text-red-700 text-sm font-semibold">Remove</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-gray-500">Your wishlist is empty. Browse products to add items.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sidebar: Profile Info -->
        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-bold mb-4 text-gray-700 border-b pb-2">Your Details</h2>
                <div class="space-y-3 text-gray-600">
                    <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
                    <p><strong>Member Since:</strong> <?php echo date('F Y', strtotime($user['created_at'])); ?></p>
                </div>
                <a href="#" class="text-blue-600 hover:underline mt-6 inline-block font-semibold">Edit Profile &rarr;</a>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
