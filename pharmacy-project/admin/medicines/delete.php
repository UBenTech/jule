<?php
require_once __DIR__ . '/../header.php'; // Admin header

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id) {
    // Before deleting, get the image path to delete the file from the server.
    $img_stmt = $pdo->prepare("SELECT image_url FROM medicine_images WHERE medicine_id = ? AND is_primary = 1");
    $img_stmt->execute([$id]);
    $image_record = $img_stmt->fetch();

    // Check if the medicine exists.
    $med_stmt = $pdo->prepare("SELECT id FROM medicines WHERE id = ?");
    $med_stmt->execute([$id]);
    $medicine = $med_stmt->fetch();

    if ($medicine) {
        try {
            $pdo->beginTransaction();

            // The schema is set with ON DELETE CASCADE for stock and medicine_images,
            // so deleting the medicine will automatically delete related records.
            $delete_stmt = $pdo->prepare("DELETE FROM medicines WHERE id = ?");
            $delete_stmt->execute([$id]);

            // Delete the physical image file if it exists.
            if ($image_record && !empty($image_record['image_url'])) {
                $file_path = __DIR__ . '/../../public/' . $image_record['image_url'];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }

            $pdo->commit();

            $_SESSION['message'] = 'Medicine and all associated data deleted successfully.';
            $_SESSION['message_type'] = 'success';
        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['message'] = 'Error: Could not delete the medicine.';
            $_SESSION['message_type'] = 'danger';
        }
    } else {
        $_SESSION['message'] = 'Delete failed: Medicine not found.';
        $_SESSION['message_type'] = 'danger';
    }
} else {
    $_SESSION['message'] = 'Invalid request: No medicine ID provided.';
    $_SESSION['message_type'] = 'danger';
}

header('Location: list.php');
exit;
?>
