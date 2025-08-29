<?php

// A simple function to parse the .env file
function parseEnv($path) {
    if (!file_exists($path)) {
        // Fallback for when the file might be in the parent directory
        $path = dirname($path) . '/../.env';
        if (!file_exists($path)) {
            throw new Exception("The .env file was not found at the expected locations.");
        }
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Split into name and value
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        // Set as environment variables
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Load environment variables from .env file
try {
    // __DIR__ is the 'includes' directory, so ../ points to the project root.
    parseEnv(__DIR__ . '/../.env');
} catch (Exception $e) {
    // In a real production environment, you would log this and show a generic error.
    die("Configuration Error: " . $e->getMessage());
}

// Database credentials from environment variables
$db_host = getenv('DB_HOST');
$db_name = getenv('DB_NAME');
$db_user = getenv('DB_USER');
$db_pass = getenv('DB_PASS');
$db_port = getenv('DB_PORT') ?: '3306'; // Default port if not specified
$charset = getenv('CHARSET') ?: 'utf8mb4';

// Data Source Name (DSN)
$dsn = "mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=$charset";

// PDO options
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Trigger exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Use native prepared statements
];

// Global PDO connection variable
$pdo = null;

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (\PDOException $e) {
    // For development, showing the error is fine. In production, log it.
    // die() is used here for simplicity to halt execution if the DB connection fails.
    die("Database Connection Error: " . $e->getMessage());
}

// The $pdo object is now ready to be used by any script that includes this file.
