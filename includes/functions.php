<?php
/**
 * Core Functions Library
 *
 * Contains helper functions used throughout the application, such as
 * data fetching, string manipulation, and security checks.
 *
 * It depends on db.php for database operations.
 * To edit DB settings, see config.php.
 */

// require_once __DIR__ . '/../config.php'; // Should be included in the entry script
// require_once __DIR__ . '/db.php';       // Should be included in the entry script

/**
 * Escapes HTML to prevent XSS attacks.
 * A simple wrapper around htmlspecialchars.
 *
 * @param string|null $string The string to escape.
 * @return string The escaped string.
 */
function esc($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Converts a string into a URL-friendly slug.
 *
 * @param string $string The string to slugify.
 * @return string The slugified string.
 */
function slugify($string) {
    $string = preg_replace('~[^\pL\d]+~u', '-', $string);
    $string = iconv('utf-8', 'us-ascii//TRANSLIT', $string);
    $string = preg_replace('~[^-\w]+~', '', $string);
    $string = trim($string, '-');
    $string = preg_replace('~-+~', '-', $string);
    $string = strtolower($string);
    return empty($string) ? 'n-a' : $string;
}

/**
 * Formats a date string into a more readable format.
 *
 * @param string $date_string The date string to format.
 * @return string Formatted date.
 */
function format_date($date_string) {
    if (!$date_string) return 'N/A';
    return date("d M, Y", strtotime($date_string));
}

/**
 * Checks if a medicine's expiry date has passed.
 *
 * @param string $expiry_date The expiry date string (e.g., 'YYYY-MM-DD').
 * @return bool True if expired, false otherwise.
 */
function check_expired($expiry_date) {
    if (!$expiry_date) return false;
    return strtotime($expiry_date) < time();
}

/**
 * Checks if stock is available for a given medicine.
 *
 * @param int $quantity_in_stock The current stock level.
 * @return bool True if stock > 0, false otherwise.
 */
function stock_available($quantity_in_stock) {
    return $quantity_in_stock > 0;
}

/**
 * Formats a price into a currency string.
 *
 * @param float $price The price to format.
 * @param string $currencySymbol The currency symbol.
 * @return string The formatted price string.
 */
function format_price($price, $currencySymbol = '$') {
    return $currencySymbol . number_format($price, 2);
}


/**
 * Fetches multiple medicines from the database.
 * Can filter by category, group, search term, and limit.
 *
 * @param PDO $pdo The database connection object.
 * @param array $options An array of filter options (limit, category_slug, group_name, search).
 * @return array An array of medicine records.
 */
function get_medicines($pdo, $options = []) {
    $sql = "SELECT m.*, c.name as category_name FROM medicines m JOIN categories c ON m.category_id = c.id WHERE 1=1";
    $params = [];

    if (!empty($options['category_slug'])) {
        $sql .= " AND c.slug = ?";
        $params[] = $options['category_slug'];
    }

    if (!empty($options['group_name'])) {
        $sql .= " AND m.group_name = ?";
        $params[] = $options['group_name'];
    }

    if (!empty($options['search'])) {
        $sql .= " AND (m.name LIKE ? OR m.description LIKE ? OR m.group_name LIKE ?)";
        $searchTerm = '%' . $options['search'] . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }

    $sql .= " ORDER BY m.name ASC";

    if (!empty($options['limit'])) {
        $sql .= " LIMIT ?";
        $params[] = (int)$options['limit'];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}


/**
 * Fetches a single medicine by its ID.
 *
 * @param PDO $pdo The database connection object.
 * @param int $id The medicine ID.
 * @return array|false The medicine record or false if not found.
 */
function get_medicine_by_id($pdo, $id) {
    $stmt = $pdo->prepare("SELECT m.*, c.name as category_name, man.name as manufacturer_name FROM medicines m LEFT JOIN categories c ON m.category_id = c.id LEFT JOIN manufacturers man ON m.manufacturer_id = man.id WHERE m.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Fetches a single medicine by its slug.
 *
 * @param PDO $pdo The database connection object.
 * @param string $slug The medicine slug.
 * @return array|false The medicine record or false if not found.
 */
function get_medicine_by_slug($pdo, $slug) {
    $stmt = $pdo->prepare("SELECT m.*, c.name as category_name, man.name as manufacturer_name FROM medicines m LEFT JOIN categories c ON m.category_id = c.id LEFT JOIN manufacturers man ON m.manufacturer_id = man.id WHERE m.slug = ?");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}


/**
 * Adds a medicine to the wishlist for a logged-in user or a guest.
 *
 * @param PDO $pdo The database connection object.
 * @param int|null $user_id The user's ID, or null for guests.
 * @param string $session_id The guest's session ID.
 * @param int $medicine_id The medicine's ID.
 * @return bool True on success, false on failure (e.g., duplicate).
 */
function add_to_wishlist($pdo, $user_id, $session_id, $medicine_id) {
    if ($user_id) { // Logged-in user
        $sql = "INSERT INTO wishlists (user_id, medicine_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE medicine_id=medicine_id";
        $params = [$user_id, $medicine_id];
    } else { // Guest user
        $sql = "INSERT INTO wishlists (session_id, medicine_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE medicine_id=medicine_id";
        $params = [$session_id, $medicine_id];
    }

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        // Could be a duplicate entry, which we can ignore.
        return false;
    }
}

/**
 * Sanitizes a filename to prevent directory traversal and other attacks.
 *
 * @param string $filename The original filename.
 * @return string The sanitized filename.
 */
function sanitize_filename($filename) {
    // Remove anything which isn't a word, whitespace, number, or any of the following characters -_~,;[]().
    $filename = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $filename);
    // Remove any runs of periods (thanks falstro!)
    $filename = mb_ereg_replace("([\.]{2,})", '', $filename);
    // Replace whitespace with underscores
    $filename = str_replace(' ', '_', $filename);
    // Prepend a timestamp to avoid name collisions
    return time() . '_' . $filename;
}

/**
 * Checks if the current user is logged in as an administrator.
 *
 * @return bool True if the user is an admin, false otherwise.
 */
function is_admin() {
    return isset($_SESSION['user_id']) && isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
}

/**
 * Redirects to a given URL and exits the script.
 *
 * @param string $url The URL to redirect to. Should be relative to the BASE_URL.
 */
function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit();
}
?>
