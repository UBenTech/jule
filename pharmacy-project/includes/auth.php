<?php
// This file contains helper functions for user authentication and session management.

// Ensure a session is started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Checks if a user is currently logged in by looking for a user_id in the session.
 *
 * @return bool True if the user is logged in, false otherwise.
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Enforces that a user must be logged in to view a page.
 * If the user is not logged in, it saves their intended destination
 * and redirects them to the login page.
 */
function require_login() {
    if (!is_logged_in()) {
        $_SESSION['message'] = 'You must be logged in to view that page.';
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header('Location: login.php');
        exit;
    }
}

/**
 * Logs a user in by setting key information into the session.
 * Regenerates the session ID to protect against session fixation attacks.
 *
 * @param array $user An associative array containing the user's data from the database.
 */
function log_in_user($user) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role_id'] = $user['role_id']; // Store role_id for quick checks
}

/**
 * Logs the current user out.
 * It unsets all session data, deletes the session cookie, and destroys the session.
 */
function log_out_user() {
    // Unset all session variables
    $_SESSION = [];

    // Delete the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Destroy the session
    session_destroy();
}

/**
 * Checks if the currently logged-in user is an administrator.
 * This is a quick check using the role_id stored in the session.
 * Assumes 'admin' role has an ID of 1 as per demo_data.sql.
 *
 * @return bool True if the user is an admin, false otherwise.
 */
function is_admin() {
    return is_logged_in() && isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1;
}

/**
 * Returns the current user's ID.
 *
 * @return int|null The user's ID or null if not logged in.
 */
function get_user_id() {
    return $_SESSION['user_id'] ?? null;
}
