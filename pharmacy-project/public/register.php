<?php
define('BASE_URL', '/pharmacy-project/public');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

// If user is already logged in, redirect them from this page
if (is_logged_in()) {
    header('Location: profile.php');
    exit;
}

$errors = [];
$username = '';
$email = '';
$first_name = '';
$last_name = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and retrieve form data
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');

    // --- Validation ---
    if (empty($username)) $errors[] = 'Username is required.';
    if (empty($email)) $errors[] = 'Email is required.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email address is required.';
    if (empty($password)) $errors[] = 'Password is required.';
    elseif (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters long.';
    if ($password !== $password_confirm) $errors[] = 'Passwords do not match.';

    // --- Check for existing user ---
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = 'An account with that username or email already exists.';
        }
    }

    // --- Process registration ---
    if (empty($errors)) {
        // Hash the password for secure storage
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // New users are assigned the 'client' role (ID 2 from demo_data.sql)
        $role_id = 2;

        $sql = "INSERT INTO users (username, email, password_hash, first_name, last_name, role_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        try {
            $stmt->execute([$username, $email, $password_hash, $first_name, $last_name, $role_id]);
            $user_id = $pdo->lastInsertId();

            // Fetch the newly created user record
            $user_stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $user_stmt->execute([$user_id]);
            $new_user = $user_stmt->fetch();

            // Automatically log the new user in
            log_in_user($new_user);

            // Redirect to the user's profile page
            header('Location: profile.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'A database error occurred. Please try again later.';
            // For debugging: error_log('Registration failed: ' . $e->getMessage());
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="max-w-md mx-auto bg-white p-6 sm:p-8 rounded-lg shadow-md mt-8">
    <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">Create an Account</h1>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p class="font-bold">Errors Found:</p>
            <ul class="list-disc list-inside">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="register.php" method="POST" novalidate>
        <div class="mb-4">
            <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username</label>
            <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($username); ?>" required
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <div class="mb-4">
            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" required
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="first_name" class="block text-gray-700 text-sm font-bold mb-2">First Name</label>
                <input type="text" name="first_name" id="first_name" value="<?php echo htmlspecialchars($first_name); ?>"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div>
                <label for="last_name" class="block text-gray-700 text-sm font-bold mb-2">Last Name</label>
                <input type="text" name="last_name" id="last_name" value="<?php echo htmlspecialchars($last_name); ?>"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
        </div>
        <div class="mb-4">
            <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
            <input type="password" name="password" id="password" required
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <div class="mb-6">
            <label for="password_confirm" class="block text-gray-700 text-sm font-bold mb-2">Confirm Password</label>
            <input type="password" name="password_confirm" id="password_confirm" required
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <div class="text-center">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg focus:outline-none focus:shadow-outline">
                Register
            </button>
        </div>
    </form>
    <p class="text-center text-gray-600 text-sm mt-6">
        Already have an account?
        <a href="login.php" class="font-bold text-blue-600 hover:text-blue-800">
            Login here
        </a>
    </p>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
