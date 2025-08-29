<?php
// This script is intended to be run as a cron job, perhaps hourly or nightly.
// Its purpose is to clean up any temporary files that might be left over from
// incomplete processes, such as abandoned file uploads or generated reports
// that were stored temporarily.

date_default_timezone_set('UTC');
echo "--- Cleanup Worker Started: " . date('Y-m-d H:i:s') . " ---\n";

// In a real application, you might have one or more dedicated temporary directories.
// For this example, we'll imagine a temp directory for uploads.
$temp_dir = __DIR__ . '/../public/uploads/temp/';

if (!is_dir($temp_dir)) {
    // If the directory doesn't exist, we can create it or just exit.
    // For this example, we'll just report that there's nothing to do.
    echo "Temporary directory ('" . basename($temp_dir) . "') not found. Nothing to clean.\n";
    echo "--- Cleanup Worker Finished ---\n";
    exit(0);
}

$files_deleted = 0;
$total_files_checked = 0;
// Define the maximum age for a temporary file (e.g., 2 hours = 7200 seconds)
$max_file_age_seconds = 7200;

echo "Scanning directory: $temp_dir\n";
echo "Deleting files older than " . ($max_file_age_seconds / 3600) . " hours.\n\n";

// Use a DirectoryIterator for a more robust way to loop through files.
try {
    $iterator = new DirectoryIterator($temp_dir);
    foreach ($iterator as $fileinfo) {
        // Skip dots and directories.
        if ($fileinfo->isDot() || $fileinfo->isDir()) {
            continue;
        }

        $total_files_checked++;
        $file_path = $fileinfo->getRealPath();

        // Check if the file is older than the threshold.
        if ((time() - $fileinfo->getMTime()) > $max_file_age_seconds) {
            if (unlink($file_path)) {
                echo "  - DELETED: " . $fileinfo->getFilename() . "\n";
                $files_deleted++;
            } else {
                // This might happen due to permissions issues.
                echo "  - ERROR: Could not delete file: " . $fileinfo->getFilename() . "\n";
            }
        }
    }
} catch (Exception $e) {
    echo "ERROR: Could not scan the temporary directory. " . $e->getMessage() . "\n";
    exit(1);
}

echo "\nCleanup summary:\n";
echo " - Files checked: $total_files_checked\n";
echo " - Old files deleted: $files_deleted\n";
echo "--- Cleanup Worker Finished ---\n";
exit(0);
?>
