<?php
define('BASE_URL', '/pharmacy-project/public');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

require_login(); // Users must be logged in to submit a booking.

$user_id = get_user_id();
$errors = [];
$notes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $notes = trim($_POST['notes'] ?? '');
    $prescription_image = $_FILES['prescription_image'] ?? null;

    // --- Validation ---
    if (!$prescription_image || $prescription_image['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'A prescription image is required.';
    } else {
        // More robust validation for file type and size
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
        $file_type = mime_content_type($prescription_image['tmp_name']);

        if (!in_array($file_type, $allowed_types)) {
            $errors[] = 'Invalid file type. Please upload a JPG, PNG, GIF, or PDF file.';
        }
        if ($prescription_image['size'] > 5 * 1024 * 1024) { // 5 MB limit
            $errors[] = 'File is too large. Please upload a file smaller than 5MB.';
        }
    }

    if (empty($errors)) {
        try {
            // Ensure the upload directory exists
            $upload_dir = __DIR__ . '/uploads/prescriptions/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            // Create a unique filename to avoid conflicts
            $file_extension = pathinfo($prescription_image['name'], PATHINFO_EXTENSION);
            $image_name = 'rx_' . $user_id . '_' . time() . '.' . $file_extension;
            $upload_file = $upload_dir . $image_name;

            if (move_uploaded_file($prescription_image['tmp_name'], $upload_file)) {
                $image_path_for_db = 'uploads/prescriptions/' . $image_name;

                // Insert booking into the database
                $sql = "INSERT INTO bookings (user_id, prescription_image_path, notes, status) VALUES (?, ?, ?, 'pending')";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$user_id, $image_path_for_db, $notes]);

                $_SESSION['message'] = 'Your prescription has been submitted successfully! We will review it shortly and it will appear on your profile page.';
                $_SESSION['message_type'] = 'success';
                header('Location: profile.php');
                exit;

            } else {
                throw new Exception('Server error: Failed to move uploaded file.');
            }

        } catch (Exception $e) {
            $errors[] = 'An error occurred while submitting your booking. Please try again.';
            // For debugging: error_log('Booking submission failed: ' . $e->getMessage());
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="max-w-2xl mx-auto bg-white p-6 sm:p-8 rounded-lg shadow-md">
    <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">Upload Your Prescription</h1>

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

    <form action="booking.php" method="POST" enctype="multipart/form-data">
        <div class="mb-6">
            <label for="prescription_image" class="block text-gray-700 text-sm font-bold mb-2">Prescription File</label>
            <p class="text-xs text-gray-600 mb-2">Please upload a clear photo or PDF of your prescription. (JPG, PNG, PDF, max 5MB)</p>
            <input type="file" name="prescription_image" id="prescription_image" required
                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
        </div>

        <div class="mb-6">
            <label for="notes" class="block text-gray-700 text-sm font-bold mb-2">Notes for the Pharmacist (Optional)</label>
            <textarea name="notes" id="notes" rows="4"
                      class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                      placeholder="e.g., Please check for a generic brand, quantity required, etc."><?php echo htmlspecialchars($notes); ?></textarea>
        </div>

        <div class="text-center">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg focus:outline-none focus:shadow-outline transition-colors">
                Submit Prescription for Review
            </button>
        </div>
    </form>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
