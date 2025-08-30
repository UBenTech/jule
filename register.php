<?php
/**
 * User Registration Page
 *
 * Allows public users to create a new account.
 */

require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// If user is already logged in, redirect them to the account page
if (isset($_SESSION['user_id']) && !is_admin()) {
    redirect('/account.php');
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = db_connect();

    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Validation
    if (empty($username) || empty($email) || empty($password)) {
        $errors[] = 'All fields are required.';
    }
    if ($password !== $password_confirm) {
        $errors[] = 'Passwords do not match.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long.';
    }

    // Check if username or email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->fetch()) {
        $errors[] = 'Username or email already in use.';
    }

    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // is_admin defaults to 0 for regular users
        $sql = "INSERT INTO users (username, email, password_hash, is_admin) VALUES (?, ?, ?, 0)";
        $stmt = $pdo->prepare($sql);

        try {
            $stmt->execute([$username, $email, $password_hash]);
            // Automatically log the user in after registration
            $user_id = $pdo->lastInsertId();
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['is_admin'] = false; // Explicitly set for regular user
            redirect('/account.php');
        } catch (PDOException $e) {
            $errors[] = 'An error occurred during registration. Please try again.';
            error_log('User Registration Error: ' . $e->getMessage());
        }
    }
}

$page_title = 'Register New Account';
include 'templates/header.php';
?>

<h1>Register an Account</h1>
<p>Create an account to manage your bookings and wishlist.</p>

<div style="max-width: 500px;">
    <?php if (!empty($errors)): ?>
        <div style="padding: 15px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px; margin-bottom: 20px;">
            <strong>Please fix the following errors:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>/register.php" method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required value="<?php echo esc($_POST['username'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required value="<?php echo esc($_POST['email'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="password">Password (min. 8 characters)</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="password_confirm">Confirm Password</label>
            <input type="password" id="password_confirm" name="password_confirm" required>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
    <p class="mt-2">
        Already have an account? <a href="<?php echo BASE_URL; ?>/login.php">Login here</a>.
    </p>
</div>

<?php include 'templates/footer.php'; ?>
