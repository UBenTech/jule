<?php
// A very simple, "vanilla" PHP unit testing script.
// In a real project, a dedicated testing framework like PHPUnit would be used.

// This script is meant to be run from the command line: `php tests/unit_tests.php`

echo "--- Running Basic Unit Tests ---\n\n";

// Set up a mock session for testing purposes.
$_SESSION = [];

// Include the file with the functions to be tested.
require_once __DIR__ . '/../includes/auth.php';

// --- Test Helper Functions ---
$tests_passed = 0;
$tests_failed = 0;
$current_test_suite = '';

function describe($suite_name) {
    global $current_test_suite;
    $current_test_suite = $suite_name;
    echo "Testing: $suite_name\n";
}

function it($message, $test_function) {
    global $tests_passed, $tests_failed;
    try {
        $result = $test_function();
        if ($result === true) {
            echo "  \033[32m✓\033[0m [PASS] $message\n";
            $tests_passed++;
        } else {
            echo "  \033[31m✗\033[0m [FAIL] $message\n";
            $tests_failed++;
        }
    } catch (Exception $e) {
        echo "  \033[31m✗\033[0m [ERROR] $message (Exception: " . $e->getMessage() . ")\n";
        $tests_failed++;
    }
}

// --- Test Suites ---

describe('Authentication Helper Functions');

it('is_logged_in() should return false when session is empty', function() {
    $_SESSION = [];
    return is_logged_in() === false;
});

it('is_logged_in() should return true when user_id is in session', function() {
    $_SESSION['user_id'] = 1;
    return is_logged_in() === true;
});

it('is_admin() should return false when not logged in', function() {
    $_SESSION = [];
    return is_admin() === false;
});

it('is_admin() should return false when user is not an admin (role_id != 1)', function() {
    $_SESSION['user_id'] = 2;
    $_SESSION['role_id'] = 2; // 'client' role
    return is_admin() === false;
});

it('is_admin() should return true when user is an admin (role_id == 1)', function() {
    $_SESSION['user_id'] = 1;
    $_SESSION['role_id'] = 1; // 'admin' role
    return is_admin() === true;
});

it('get_user_id() should return null when not logged in', function() {
    $_SESSION = [];
    return get_user_id() === null;
});

it('get_user_id() should return the user ID when logged in', function() {
    $_SESSION['user_id'] = 123;
    return get_user_id() === 123;
});


// --- Test Summary ---
echo "\n--- Test Summary ---\n";
echo "Total Tests: " . ($tests_passed + $tests_failed) . "\n";
echo "\033[32mPassed: $tests_passed\033[0m\n";
echo "\033[31mFailed: $tests_failed\033[0m\n\n";

// Exit with a status code indicating success or failure, for CI/CD pipelines.
if ($tests_failed > 0) {
    exit(1);
}
exit(0);
?>
