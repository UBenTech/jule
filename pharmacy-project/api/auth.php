<?php
// Set headers for JSON response, CORS, and allowed methods/headers.
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include necessary helpers.
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php'; // For the log_in_user() function

$response = [
    'status' => 'error',
    'message' => 'An unknown error occurred.',
    'data' => null
];

// This endpoint only supports POST for security.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the posted data. It's common for APIs to receive JSON.
    $input = json_decode(file_get_contents('php://input'), true);

    // Fallback to regular POST data if JSON is not provided.
    $username = $input['username'] ?? $_POST['username'] ?? null;
    $password = $input['password'] ?? $_POST['password'] ?? null;

    if (empty($username) || empty($password)) {
        $response['message'] = 'Username and password are required.';
        http_response_code(400); // Bad Request
    } else {
        try {
            // Fetch user by username or email for flexibility.
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :username");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch();

            // Verify that the user exists and the password is correct.
            if ($user && password_verify($password, $user['password_hash'])) {
                // Credentials are valid. Use our helper to log the user in server-side.
                log_in_user($user);

                $response['status'] = 'success';
                $response['message'] = 'Login successful.';
                // Return some basic, non-sensitive user information to the client.
                $response['data'] = [
                    'userId' => $user['id'],
                    'username' => $user['username'],
                    'isAdmin' => ($user['role_id'] == 1) // Check if the user is an admin
                ];
                http_response_code(200); // OK
            } else {
                // If credentials do not match, return an "Unauthorized" error.
                $response['message'] = 'Invalid username or password.';
                http_response_code(401); // Unauthorized
            }
        } catch (PDOException $e) {
            $response['message'] = 'A database error occurred during authentication.';
            http_response_code(500); // Internal Server Error
        }
    }
} else {
    $response['message'] = 'Invalid request method. Only POST is supported.';
    http_response_code(405); // Method Not Allowed
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>
