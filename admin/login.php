<?php
/**
 * Admin Login Page
 *
 * Handles administrator login and logout.
 *
 * To edit brand color or DB settings, see config.php.
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    redirect('/admin/login.php');
}

// If already logged in, redirect to admin dashboard
if (is_admin()) {
    redirect('/admin/');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Username and password are required.';
    } else {
        $pdo = db_connect();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND is_admin = 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Login success, set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = true;
            redirect('/admin/');
        } else {
            $error = 'Invalid login credentials.';
        }
    }
}

$page_title = 'Admin Login';
// We use a simplified header/footer for the login page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        body {
            background-color: var(--light-gray);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background-color: #fff;
            padding: 2rem 3rem;
            border-radius: 5px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1 class="text-center">Admin Login</h1>
        <p class="text-center">Intelligent Pharmacy Management</p>

        <?php if ($error): ?>
            <div style="padding: 10px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px; margin-bottom: 1rem;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>/admin/login.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
        </form>
        <div class="text-center mt-1">
             <a href="<?php echo BASE_URL; ?>/">← Back to Site</a>
        </div>
    </div>
</body>
</html>
