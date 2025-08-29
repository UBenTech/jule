<?php
require_once __DIR__ . '/../header.php'; // Admin header

// Fetch all users with their role names.
$stmt = $pdo->query("
    SELECT u.id, u.username, u.email, u.first_name, u.last_name, r.role_name
    FROM users u
    JOIN roles r ON u.role_id = r.id
    ORDER BY u.id ASC
");
$users = $stmt->fetchAll();

$current_user_id = get_user_id(); // Get the ID of the currently logged-in admin
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Manage Users</h1>
    <a href="add.php" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
        + Add New User
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
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Username</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Email</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Name</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Role</th>
                    <th class="text-center py-3 px-4 uppercase font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php if (count($users) > 0): ?>
                    <?php foreach ($users as $user): ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-4 font-semibold"><?php echo htmlspecialchars($user['username']); ?></td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($user['email']); ?></td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 font-semibold leading-tight rounded-full text-xs
                                <?php echo $user['role_name'] === 'admin' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700'; ?>">
                                    <?php echo htmlspecialchars(ucfirst($user['role_name'])); ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <a href="edit.php?id=<?php echo $user['id']; ?>" class="text-blue-600 hover:underline mr-4">Edit</a>
                                <?php if ($user['id'] !== $current_user_id): // Prevent admin from deleting themselves ?>
                                    <a href="delete.php?id=<?php echo $user['id']; ?>"
                                       class="text-red-600 hover:underline"
                                       onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">Delete</a>
                                <?php else: ?>
                                    <span class="text-gray-400 cursor-not-allowed">Delete</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-6">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once __DIR__ . '/../footer.php'; // Admin footer
?>
