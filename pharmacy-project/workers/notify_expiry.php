<?php
// This script is designed to be run from the command line (CLI) or as a cron job.
// It checks for medicines that are nearing their expiry date and prints an alert.

// Set a default timezone to avoid warnings
date_default_timezone_set('UTC');

// Since this is a CLI script, the path to includes needs to be absolute.
require_once __DIR__ . '/../includes/db.php';

echo "--- Expiry Notification Worker Started: " . date('Y-m-d H:i:s') . " ---\n";

// --- Configuration ---
// Define the notification period. We'll flag medicines expiring in the next 60 days.
$expiry_threshold_days = 60;
$threshold_date = date('Y-m-d', strtotime("+$expiry_threshold_days days"));

try {
    // The query finds medicines that are:
    // 1. In stock (quantity > 0)
    // 2. Not already expired (expiry_date >= today)
    // 3. Expiring on or before the threshold date
    $stmt = $pdo->prepare("
        SELECT m.name, m.expiry_date, s.quantity
        FROM medicines m
        JOIN stock s ON m.id = s.medicine_id
        WHERE m.expiry_date <= ? AND m.expiry_date >= CURDATE() AND s.quantity > 0
        ORDER BY m.expiry_date ASC
    ");
    $stmt->execute([$threshold_date]);
    $expiring_medicines = $stmt->fetchAll();

    if (count($expiring_medicines) > 0) {
        echo "Found " . count($expiring_medicines) . " medicine(s) nearing their expiry date (within $expiry_threshold_days days):\n\n";

        foreach ($expiring_medicines as $med) {
            $days_left = (new DateTime($med['expiry_date']))->diff(new DateTime())->days;
            $message = sprintf(
                "  - ALERT: Medicine '%s' (Stock: %d) will expire on %s (%d days left).\n",
                $med['name'],
                $med['quantity'],
                $med['expiry_date'],
                $days_left
            );
            echo $message;
            // In a real application, this is where you would trigger an email or SMS notification.
            // Example: send_expiry_alert_email('admin@pharmacy.com', $message);
        }
    } else {
        echo "No medicines are nearing their expiry date within the next $expiry_threshold_days days.\n";
    }

} catch (PDOException $e) {
    // If there's a database error, print it to the console (or a log file).
    echo "DATABASE ERROR: " . $e->getMessage() . "\n";
    exit(1); // Exit with a non-zero status code to indicate failure.
}

echo "\n--- Expiry Notification Worker Finished ---\n";
exit(0); // Exit with a success status code.
?>
