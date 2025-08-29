<?php
// This script handles the deletion of a category.
require_once __DIR__ . '/../header.php'; // Includes DB connection and admin auth check.

// Get the category ID from the URL and validate it.
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id) {
    // Check if the category exists before attempting to delete.
    $stmt = $pdo->prepare("SELECT id FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    $category = $stmt->fetch();

    if ($category) {
        try {
            // Note: In a real application, you might want to consider what happens to medicines
            // in this category. The schema sets `category_id` to NULL on delete (`ON DELETE SET NULL`).
            // This is a safe default.
            $delete_stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
            $delete_stmt->execute([$id]);

            // Set success message.
            $_SESSION['message'] = 'Category deleted successfully.';
            $_SESSION['message_type'] = 'success';
        } catch (PDOException $e) {
            // If the deletion fails due to a foreign key constraint or other DB error.
            $_SESSION['message'] = 'Error: Could not delete the category. It might be in use.';
            $_SESSION['message_type'] = 'danger';
            // For debugging: error_log('Delete failed: ' . $e->getMessage());
        }
    } else {
        // The category ID does not exist.
        $_SESSION['message'] = 'Delete failed: Category not found.';
        $_SESSION['message_type'] = 'danger';
    }
} else {
    // The ID was not provided or was invalid.
    $_SESSION['message'] = 'Invalid request: No category ID provided.';
    $_SESSION['message_type'] = 'danger';
}

// Redirect back to the list page to show the result.
header('Location: list.php');
exit;
?>
