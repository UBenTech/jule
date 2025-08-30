<?php
/**
 * Public User Login Page
 */

require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// If user is already logged in, redirect them.
if (isset($_SESSION['user_id'])) {
    if (is_admin()) {
        redirect('/admin/');
    } else {
        redirect('/account.php');
    }
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Email and password are required.';
    } else {
        $pdo = db_connect();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Login success, set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = (bool)$user['is_admin'];

            // Handle redirect after login
            $redirect_url = '/account.php';
            if (isset($_SESSION['redirect_to'])) {
                $redirect_url = $_SESSION['redirect_to'];
                unset($_SESSION['redirect_to']);
            }

            if ($_SESSION['is_admin']) {
                redirect('/admin/'); // Admins always go to dashboard
            } else {
                redirect($redirect_url);
            }
        } else {
            $error = 'Invalid login credentials.';
        }
    }
}

$page_title = 'Login';
include 'templates/header.php';
?>

<h1>User Login</h1>
<p>Log in to your account to manage your bookings and wishlist.</p>

<div style="max-width: 500px;">
    <?php if ($error): ?>
        <div style="padding: 10px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px; margin-bottom: 1rem;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>/login.php" method="POST">
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
    <p class="mt-2">
        Don't have an account? <a href="<?php echo BASE_URL; ?>/register.php">Register here</a>.
    </p>
    <p class="mt-1">
        Are you an administrator? <a href="<?php echo BASE_URL; ?>/admin/login.php">Admin Login</a>.
    </p>
</div>

<?php include 'templates/footer.php'; ?>
