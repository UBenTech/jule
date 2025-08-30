<?php
/**
 * Main API Endpoint
 *
 * This file handles all AJAX requests from the frontend application.
 * It uses a single entry point and an 'action' parameter to route requests.
 * All responses are in JSON format.
 *
 * To edit brand color or DB settings, see config.php.
 */

// Set header to return JSON
header('Content-Type: application/json');

// Include core files
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Get the requested action
$action = $_GET['action'] ?? '';

// Get the request body
$input = json_decode(file_get_contents('php://input'), true);

// Initialize the database connection
$pdo = db_connect();

// --- Action Router ---
switch ($action) {
    case 'wishlist_add':
        handle_wishlist_add($pdo, $input);
        break;

    case 'book':
        handle_booking($pdo, $input);
        break;

    case 'med_delete':
        handle_medicine_delete($pdo, $input);
        break;

    case 'stock_check':
        // This action was mentioned in the original plan but not fully implemented.
        // Adding a basic placeholder for it.
        handle_stock_check($pdo, $_GET['id'] ?? null);
        break;

    default:
        // Return an error if the action is not recognized
        echo json_encode(['success' => false, 'message' => 'Invalid API action.']);
        break;
}

// --- Action Handler Functions ---

/**
 * Handles adding an item to the wishlist.
 */
function handle_wishlist_add($pdo, $input) {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'You must be logged in to add items to your wishlist.']);
        return;
    }

    $medicine_id = $input['medicine_id'] ?? null;

    if (!$medicine_id) {
        echo json_encode(['success' => false, 'message' => 'Medicine ID is required.']);
        return;
    }

    $user_id = $_SESSION['user_id'];
    $session_id = null; // We no longer use session_id for logged-in users' wishlists

    if (add_to_wishlist($pdo, $user_id, $session_id, $medicine_id)) {
        echo json_encode(['success' => true, 'message' => 'Item added to wishlist successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Item is already in the wishlist or an error occurred.']);
    }
}

/**
 * Handles a new medicine booking.
 */
function handle_booking($pdo, $input) {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'You must be logged in to book an item.']);
        return;
    }

    // Basic validation
    $required_fields = ['name', 'phone', 'medicine_id', 'quantity', 'booking_type'];
    foreach ($required_fields as $field) {
        if (empty($input[$field])) {
            echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
            return;
        }
    }

    try {
        $sql = "INSERT INTO bookings (user_id, name, phone, email, medicine_id, quantity, booking_type, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_SESSION['user_id'],
            $input['name'],
            $input['phone'],
            $input['email'] ?? null,
            $input['medicine_id'],
            $input['quantity'],
            $input['booking_type'],
            $input['notes'] ?? null
        ]);

        // You could also decrease stock here, but that might be better handled when the booking is 'confirmed'.

        echo json_encode(['success' => true, 'message' => 'Booking submitted successfully.']);

    } catch (PDOException $e) {
        error_log("Booking API Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred while processing your booking.']);
    }
}

/**
 * Handles deleting a medicine (Admin only).
 */
function handle_medicine_delete($pdo, $input) {
    if (!is_admin()) {
        echo json_encode(['success' => false, 'message' => 'Authentication required.']);
        return;
    }

    $medicine_id = $input['medicine_id'] ?? null;

    if (!$medicine_id) {
        echo json_encode(['success' => false, 'message' => 'Medicine ID is required.']);
        return;
    }

    try {
        $sql = "DELETE FROM medicines WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$medicine_id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Medicine deleted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Medicine not found or could not be deleted.']);
        }
    } catch (PDOException $e) {
        error_log("Delete Medicine API Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred while deleting the medicine.']);
    }
}

/**
 * Handles a stock check for a medicine.
 */
function handle_stock_check($pdo, $medicine_id) {
    if (!$medicine_id) {
        echo json_encode(['success' => false, 'message' => 'Medicine ID is required.']);
        return;
    }

    $stmt = $pdo->prepare("SELECT quantity_in_stock, min_stock FROM medicines WHERE id = ?");
    $stmt->execute([$medicine_id]);
    $stock_info = $stmt->fetch();

    if ($stock_info) {
        echo json_encode([
            'success' => true,
            'available' => $stock_info['quantity_in_stock'] > 0,
            'quantity' => (int)$stock_info['quantity_in_stock'],
            'is_low' => $stock_info['quantity_in_stock'] < $stock_info['min_stock']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Medicine not found.']);
    }
}
?>
