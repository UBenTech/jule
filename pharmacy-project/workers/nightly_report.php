<?php
// This script generates a simple nightly stock report and prints it to the console.
// This is intended to be run as a cron job, with the output piped to a log file or an email client.

date_default_timezone_set('UTC');
require_once __DIR__ . '/../includes/db.php';

echo "--- Nightly Report Worker Started: " . date('Y-m-d H:i:s') . " ---\n\n";

try {
    // Query to get all stock data along with medicine details.
    $stmt = $pdo->query("
        SELECT m.name, s.quantity, m.price, (s.quantity * m.price) as total_value
        FROM medicines m
        JOIN stock s ON m.id = s.medicine_id
        ORDER BY m.name ASC
    ");
    $stock_data = $stmt->fetchAll();

    // --- Build the Report String ---
    $report = "Nightly Stock & Value Report - " . date('Y-m-d') . "\n";
    $report .= str_repeat("=", 60) . "\n";
    $report .= str_pad("Medicine Name", 35) . str_pad("Quantity", 15) . "Total Value\n";
    $report .= str_repeat("-", 60) . "\n";

    $total_stock_value = 0;
    $total_items = 0;
    foreach ($stock_data as $item) {
        $report .= str_pad(substr($item['name'], 0, 33), 35);
        $report .= str_pad($item['quantity'], 15);
        $report .= "$" . number_format($item['total_value'], 2) . "\n";
        $total_stock_value += $item['total_value'];
        $total_items += $item['quantity'];
    }

    $report .= str_repeat("=", 60) . "\n";
    $report .= "Summary:\n";
    $report .= " - Total Unique Medicines in Stock: " . count($stock_data) . "\n";
    $report .= " - Total Number of Items: " . $total_items . "\n";
    $report .= " - Total Stock Value: $" . number_format($total_stock_value, 2) . "\n";
    $report .= str_repeat("=", 60) . "\n";

    // --- Output the Report ---
    echo "Report generated successfully.\n\n";
    echo $report;

    // In a real-world application, you would save this report to a file or email it.
    // Example:
    // $report_path = __DIR__ . '/../reports/stock_report_' . date('Y-m-d') . '.txt';
    // file_put_contents($report_path, $report);
    // send_report_email('admin@pharmacy.com', 'Nightly Stock Report', $report);

} catch (PDOException $e) {
    echo "DATABASE ERROR: " . $e->getMessage() . "\n";
    exit(1); // Exit with a failure code.
}

echo "\n--- Nightly Report Worker Finished ---\n";
exit(0); // Exit with a success code.
?>
