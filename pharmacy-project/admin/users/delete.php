<?php
require_once __DIR__ . '/../header.php'; // Admin header

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$current_user_id = get_user_id();

if ($id) {
    // Critical check: prevent an admin from deleting their own account.
    if ($id === $current_user_id) {
        $_SESSION['message'] = 'Error: You cannot delete your own account.';
        $_SESSION['message_type'] = 'danger';
        header('Location: list.php');
        exit;
    }

    // Check if the user exists.
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    if ($user) {
        try {
            // Note: The schema for bookings, wishlist, etc., is set to ON DELETE CASCADE,
            // so deleting a user will automatically remove their associated data.
            $delete_stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $delete_stmt->execute([$id]);

            $_SESSION['message'] = 'User deleted successfully.';
            $_SESSION['message_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['message'] = 'Error: Could not delete the user.';
            $_SESSION['message_type'] = 'danger';
        }
    } else {
        $_SESSION['message'] = 'Delete failed: User not found.';
        $_SESSION['message_type'] = 'danger';
    }
} else {
    $_SESSION['message'] = 'Invalid request: No user ID provided.';
    $_SESSION['message_type'] = 'danger';
}

header('Location: list.php');
exit;
?>
