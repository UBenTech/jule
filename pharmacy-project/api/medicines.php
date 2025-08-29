<?php
// Set headers for JSON response and CORS.
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

require_once __DIR__ . '/../includes/db.php';

$response = [
    'status' => 'error',
    'message' => 'An unknown error occurred.',
    'data' => null
];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Sanitize and validate input parameters.
    $medicine_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $search_term = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING);
    $category_id = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);

    try {
        // --- Scenario 1: Fetch a single medicine by its ID ---
        if ($medicine_id) {
            $stmt = $pdo->prepare("
                SELECT m.*, c.name AS category_name, mf.name AS manufacturer_name, mi.image_url, s.quantity AS stock_quantity
                FROM medicines m
                LEFT JOIN categories c ON m.category_id = c.id
                LEFT JOIN manufacturers mf ON m.manufacturer_id = mf.id
                LEFT JOIN medicine_images mi ON m.id = mi.medicine_id AND mi.is_primary = 1
                LEFT JOIN stock s ON m.id = s.medicine_id
                WHERE m.id = ?
            ");
            $stmt->execute([$medicine_id]);
            $medicine = $stmt->fetch();

            if ($medicine) {
                $response['status'] = 'success';
                $response['message'] = 'Medicine details fetched successfully.';
                $response['data'] = $medicine;
                http_response_code(200);
            } else {
                $response['message'] = 'Medicine not found.';
                http_response_code(404); // Not Found
            }

        // --- Scenario 2: Fetch a list of medicines (with optional filters) ---
        } else {
            $sql = "
                SELECT m.id, m.name, m.price, m.description, c.name AS category_name, mi.image_url
                FROM medicines m
                LEFT JOIN categories c ON m.category_id = c.id
                LEFT JOIN medicine_images mi ON m.id = mi.medicine_id AND mi.is_primary = 1
            ";
            $conditions = [];
            $params = [];

            if ($search_term) {
                $conditions[] = "(m.name LIKE ? OR m.description LIKE ?)";
                $params[] = '%' . $search_term . '%';
                $params[] = '%' . $search_term . '%';
            }
            if ($category_id) {
                $conditions[] = "m.category_id = ?";
                $params[] = $category_id;
            }

            if (count($conditions) > 0) {
                $sql .= " WHERE " . implode(' AND ', $conditions);
            }

            $sql .= " ORDER BY m.name ASC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $medicines = $stmt->fetchAll();

            $response['status'] = 'success';
            $response['message'] = 'Medicines list fetched successfully.';
            $response['data'] = $medicines;
            http_response_code(200);
        }

    } catch (PDOException $e) {
        $response['message'] = 'Database error: Could not process request.';
        http_response_code(500);
    }

} else {
    $response['message'] = 'Invalid request method. Only GET is supported.';
    http_response_code(405); // Method Not Allowed
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>
