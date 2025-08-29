<?php
require_once __DIR__ . '/../header.php'; // Admin header

// Fetch roles for the dropdown
$roles = $pdo->query("SELECT * FROM roles ORDER BY role_name ASC")->fetchAll();

$errors = [];
$username = '';
$email = '';
$first_name = '';
$last_name = '';
$role_id = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $role_id = filter_input(INPUT_POST, 'role_id', FILTER_VALIDATE_INT);

    // --- Validation ---
    if (empty($username)) $errors[] = 'Username is required.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
    if (empty($password) || strlen($password) < 8) $errors[] = 'A password of at least 8 characters is required.';
    if (empty($role_id)) $errors[] = 'A role must be selected.';

    // Check if username or email already exist
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = 'Username or email is already in use.';
        }
    }

    // If no errors, create user
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, email, password_hash, first_name, last_name, role_id) VALUES (?, ?, ?, ?, ?, ?)";
        try {
            $pdo->prepare($sql)->execute([$username, $email, $password_hash, $first_name, $last_name, $role_id]);

            $_SESSION['message'] = 'User created successfully!';
            $_SESSION['message_type'] = 'success';
            header('Location: list.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Database error: Could not create user.';
        }
    }
}
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Add New User</h1>

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
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="mb-4">
                <label for="username" class="block text-gray-700 font-bold mb-2">Username</label>
                <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($username); ?>" required class="w-full px-3 py-2 border rounded">
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-bold mb-2">Email</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" required class="w-full px-3 py-2 border rounded">
            </div>
            <div class="mb-4">
                <label for="first_name" class="block text-gray-700 font-bold mb-2">First Name</label>
                <input type="text" name="first_name" id="first_name" value="<?php echo htmlspecialchars($first_name); ?>" class="w-full px-3 py-2 border rounded">
            </div>
            <div class="mb-4">
                <label for="last_name" class="block text-gray-700 font-bold mb-2">Last Name</label>
                <input type="text" name="last_name" id="last_name" value="<?php echo htmlspecialchars($last_name); ?>" class="w-full px-3 py-2 border rounded">
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700 font-bold mb-2">Password</label>
                <input type="password" name="password" id="password" required class="w-full px-3 py-2 border rounded" placeholder="Min. 8 characters">
            </div>
            <div class="mb-4">
                <label for="role_id" class="block text-gray-700 font-bold mb-2">Role</label>
                <select name="role_id" id="role_id" required class="w-full px-3 py-2 border rounded">
                    <option value="">Select a Role</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?php echo $role['id']; ?>" <?php if ($role_id == $role['id']) echo 'selected'; ?>><?php echo htmlspecialchars(ucfirst($role['role_name'])); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="mt-6 flex items-center justify-end gap-4">
            <a href="list.php" class="text-gray-600 hover:underline">Cancel</a>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded">
                Create User
            </button>
        </div>
    </form>
</div>

<?php
require_once __DIR__ . '/../footer.php'; // Admin footer
?>
