<?php
/**
 * User Profile Editing Page
 *
 * Allows a logged-in user to change their password.
 */

require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Protect this page: only logged-in users allowed.
if (!isset($_SESSION['user_id'])) {
    redirect('/login.php');
}

$pdo = db_connect();
$user_id = $_SESSION['user_id'];

$errors = [];
$success_message = '';

// Fetch user details to display
$stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    // Should not happen if user is logged in, but as a safeguard:
    session_destroy();
    redirect('/login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $errors[] = 'Please fill in all password fields.';
    } else {
        // 1. Verify current password
        $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $password_hash = $stmt->fetchColumn();

        if (password_verify($current_password, $password_hash)) {
            // 2. Validate new password
            if (strlen($new_password) < 8) {
                $errors[] = 'New password must be at least 8 characters long.';
            }
            if ($new_password !== $confirm_password) {
                $errors[] = 'New passwords do not match.';
            }

            // 3. If all is well, update the password
            if (empty($errors)) {
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $update_stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                if ($update_stmt->execute([$new_password_hash, $user_id])) {
                    $success_message = 'Your password has been updated successfully.';
                } else {
                    $errors[] = 'An unexpected error occurred. Please try again.';
                }
            }
        } else {
            $errors[] = 'Your current password is not correct.';
        }
    }
}


$page_title = 'Edit Profile';
include 'templates/header.php';
?>

<h1>Edit Profile & Password</h1>

<div style="display: flex; gap: 2rem;">
    <div style="flex: 1;">
        <h3>Your Details</h3>
        <div class="form-group">
            <label>Username</label>
            <input type="text" value="<?php echo esc($user['username']); ?>" disabled>
        </div>
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" value="<?php echo esc($user['email']); ?>" disabled>
        </div>
        <a href="<?php echo BASE_URL; ?>/account.php">← Back to Account Dashboard</a>
    </div>

    <div style="flex: 1;">
        <h3>Change Your Password</h3>

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

        <?php if ($success_message): ?>
            <div style="padding: 15px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px; margin-bottom: 20px;">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>/edit-profile.php" method="POST">
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Password</button>
        </form>
    </div>
</div>


<?php
include 'templates/footer.php';
?>
