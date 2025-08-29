<?php
require_once __DIR__ . '/../header.php'; // Admin header

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION['message'] = 'Invalid user ID.';
    $_SESSION['message_type'] = 'danger';
    header('Location: list.php');
    exit;
}

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['message'] = 'User not found.';
    $_SESSION['message_type'] = 'danger';
    header('Location: list.php');
    exit;
}

// Fetch roles for the dropdown
$roles = $pdo->query("SELECT * FROM roles ORDER BY role_name ASC")->fetchAll();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and retrieve form data
    $email = trim($_POST['email'] ?? '');
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $role_id = filter_input(INPUT_POST, 'role_id', FILTER_VALIDATE_INT);
    $password = $_POST['password'] ?? '';

    // --- Validation ---
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
    if (empty($role_id)) $errors[] = 'A role must be selected.';

    // Prevent an admin from changing their own role from admin
    $current_user_id = get_user_id();
    if ($id === $current_user_id && $user['role_id'] == 1 && $role_id != 1) {
        $errors[] = 'You cannot remove your own admin privileges.';
    }

    // Check if email is already in use by another user
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $id]);
    if ($stmt->fetch()) {
        $errors[] = 'This email address is already in use by another account.';
    }

    // If no errors, update the user
    if (empty($errors)) {
        try {
            // Update main user details
            $sql = "UPDATE users SET email = ?, first_name = ?, last_name = ?, role_id = ? WHERE id = ?";
            $pdo->prepare($sql)->execute([$email, $first_name, $last_name, $role_id, $id]);

            // Update password only if a new one is provided
            if (!empty($password)) {
                if (strlen($password) < 8) {
                    $errors[] = 'New password must be at least 8 characters long.';
                } else {
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    $pass_sql = "UPDATE users SET password_hash = ? WHERE id = ?";
                    $pdo->prepare($pass_sql)->execute([$password_hash, $id]);
                }
            }

            // If there were no errors during password update, redirect
            if (empty($errors)) {
                $_SESSION['message'] = 'User updated successfully!';
                $_SESSION['message_type'] = 'success';
                header('Location: list.php');
                exit;
            }

        } catch (PDOException $e) {
            $errors[] = 'Database error: Could not update user.';
        }
    }

    // If there were errors, update the user array to show the new (failed) values in the form.
    $user['email'] = $email;
    $user['first_name'] = $first_name;
    $user['last_name'] = $last_name;
    $user['role_id'] = $role_id;
}
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Edit User: <?php echo htmlspecialchars($user['username']); ?></h1>

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
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="mb-4">
                <label for="username" class="block text-gray-700 font-bold mb-2">Username</label>
                <input type="text" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled class="w-full px-3 py-2 border rounded bg-gray-200 cursor-not-allowed">
                <p class="text-xs text-gray-600 mt-1">Username cannot be changed.</p>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-bold mb-2">Email</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required class="w-full px-3 py-2 border rounded">
            </div>
            <div class="mb-4">
                <label for="first_name" class="block text-gray-700 font-bold mb-2">First Name</label>
                <input type="text" name="first_name" id="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" class="w-full px-3 py-2 border rounded">
            </div>
            <div class="mb-4">
                <label for="last_name" class="block text-gray-700 font-bold mb-2">Last Name</label>
                <input type="text" name="last_name" id="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" class="w-full px-3 py-2 border rounded">
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700 font-bold mb-2">New Password</label>
                <input type="password" name="password" id="password" class="w-full px-3 py-2 border rounded" placeholder="Leave blank to keep current password">
            </div>
            <div class="mb-4">
                <label for="role_id" class="block text-gray-700 font-bold mb-2">Role</label>
                <select name="role_id" id="role_id" required class="w-full px-3 py-2 border rounded">
                    <?php foreach ($roles as $role): ?>
                        <option value="<?php echo $role['id']; ?>" <?php if ($user['role_id'] == $role['id']) echo 'selected'; ?>><?php echo htmlspecialchars(ucfirst($role['role_name'])); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="mt-6 flex items-center justify-end gap-4">
            <a href="list.php" class="text-gray-600 hover:underline">Cancel</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                Update User
            </button>
        </div>
    </form>
</div>

<?php
require_once __DIR__ . '/../footer.php'; // Admin footer
?>
