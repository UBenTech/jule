<?php
/**
 * Database Connection Factory
 *
 * This file provides a function to connect to the database using PDO.
 * It depends on the credentials defined in config.php.
 *
 * To edit DB settings, see config.php
 */

// The config file is expected to be included before this file.
// require_once __DIR__ . '/../config.php'; // This line is not strictly needed if always included in order.

/**
 * Creates and returns a PDO database connection object.
 *
 * @return PDO|null Returns a PDO object on success or null on failure.
 */
function db_connect() {
    // DSN (Data Source Name) for the connection
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";

    // PDO options
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch associative arrays
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Use native prepared statements
    ];

    try {
        // Create a new PDO instance
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        // In a real application, you would log this error and show a generic error message.
        // For development, it's okay to die and show the error.
        error_log("Database Connection Error: " . $e->getMessage());
        die("Database connection failed. Please check your configuration and ensure the database server is running.");
    }
}

// Example of how to use it:
// $pdo = db_connect();
// $stmt = $pdo->query("SELECT * FROM medicines");
// $medicines = $stmt->fetchAll();
?>
