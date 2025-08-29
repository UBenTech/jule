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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Basic validation
    if (empty($username) || empty($password)) {
        $errors[] = 'Both username and password are required.';
    } else {
        // Find user by username or email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        // Verify user exists and password is correct
        if ($user && password_verify($password, $user['password_hash'])) {
            // Successful login: log user in
            log_in_user($user);

            // Redirect to the intended destination or the profile page
            $redirect_url = $_SESSION['redirect_url'] ?? 'profile.php';
            unset($_SESSION['redirect_url']); // Clear the stored URL
            header("Location: {$redirect_url}");
            exit;
        } else {
            // Invalid credentials
            $errors[] = 'Invalid username or password.';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="max-w-md mx-auto bg-white p-6 sm:p-8 rounded-lg shadow-md mt-8">
    <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">Member Login</h1>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p><?php echo htmlspecialchars($errors[0]); ?></p>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6" role="alert">
            <p><?php echo htmlspecialchars($_SESSION['message']); ?></p>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <form action="login.php" method="POST" novalidate>
        <div class="mb-4">
            <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username or Email</label>
            <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($username); ?>" required
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <div class="mb-6">
            <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
            <input type="password" name="password" id="password" required
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <div class="text-center">
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline">
                Sign In
            </button>
        </div>
    </form>
    <p class="text-center text-gray-600 text-sm mt-6">
        Don't have an account?
        <a href="register.php" class="font-bold text-blue-600 hover:text-blue-800">
            Register here
        </a>
    </p>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
