<?php
// Set the response header to indicate JSON content.
header("Content-Type: application/json; charset=UTF-8");
// Allow cross-origin requests, useful for development or if the API is consumed by a different frontend.
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

// Include the database connection script.
require_once __DIR__ . '/../includes/db.php';

// Define a standard response structure.
$response = [
    'status' => 'error',
    'message' => 'An unknown error occurred.',
    'data' => null
];

// This endpoint only supports the GET method.
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Prepare and execute the SQL query to fetch all categories.
        $stmt = $pdo->query("SELECT id, name, description FROM categories ORDER BY name ASC");
        $categories = $stmt->fetchAll();

        // If the query is successful, update the response.
        $response['status'] = 'success';
        $response['message'] = 'Categories fetched successfully.';
        $response['data'] = $categories;
        http_response_code(200); // OK

    } catch (PDOException $e) {
        // If a database error occurs, set an appropriate error message and response code.
        $response['message'] = 'Database error: Could not fetch categories.';
        // In a real production environment, you would log the detailed error ($e->getMessage()) instead of sending it to the client.
        http_response_code(500); // Internal Server Error
    }
} else {
    // If any other HTTP method is used, return a "Method Not Allowed" error.
    $response['message'] = 'Invalid request method. Only GET is supported.';
    http_response_code(405); // Method Not Allowed
}

// Encode the response array into a JSON string and output it.
// JSON_PRETTY_PRINT makes the output readable during development.
echo json_encode($response, JSON_PRETTY_PRINT);
?>
