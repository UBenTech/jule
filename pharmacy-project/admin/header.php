<?php
// This is the header for the admin panel.
// It includes session start, auth checks, and navigation.

// Define a base URL for the entire project if not already defined.
// This helps with linking between public and admin areas.
if (!defined('BASE_URL')) {
    define('BASE_URL', '/pharmacy-project/public');
}
// Define a base URL for the admin area specifically.
if (!defined('ADMIN_BASE_URL')) {
    define('ADMIN_BASE_URL', '/pharmacy-project/admin');
}

// We are in the /admin/ directory, so includes are one level up.
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

// This is the primary security check for the entire admin section.
// The is_admin() function (from auth.php) checks if the user is logged in
// and has the 'admin' role.
if (!is_admin()) {
    // If the user is not an admin, set a message and redirect them to the main login page.
    $_SESSION['message'] = 'You do not have permission to access the admin area.';
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Jules Pharmacy</title>
    <!-- Using Tailwind CSS via CDN for simplicity -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full">
<div class="min-h-full">
    <!-- Sidebar Navigation (for Desktop) -->
    <div class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0">
        <div class="flex flex-col flex-grow pt-5 bg-gray-800 overflow-y-auto">
            <div class="flex items-center flex-shrink-0 px-4">
                <h2 class="text-2xl font-semibold text-white">Admin Panel</h2>
            </div>
            <div class="mt-5 flex-1 flex flex-col">
                <nav class="flex-1 px-2 pb-4 space-y-1">
                    <a href="<?php echo ADMIN_BASE_URL; ?>/index.php" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">Dashboard</a>
                    <a href="<?php echo ADMIN_BASE_URL; ?>/medicines/list.php" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">Medicines</a>
                    <a href="<?php echo ADMIN_BASE_URL; ?>/categories/list.php" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">Categories</a>
                    <a href="<?php echo ADMIN_BASE_URL; ?>/manufacturers/list.php" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">Manufacturers</a>
                    <a href="<?php echo ADMIN_BASE_URL; ?>/users/list.php" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">Users</a>
                    <a href="<?php echo ADMIN_BASE_URL; ?>/bookings/list.php" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">Bookings</a>
                    <a href="<?php echo ADMIN_BASE_URL; ?>/stock/list.php" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">Stock</a>
                </nav>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="md:pl-64 flex flex-col flex-1">
        <header class="sticky top-0 z-10 flex-shrink-0 flex h-16 bg-white shadow">
            <!-- Header content for mobile nav toggle, search, user menu -->
            <div class="flex-1 px-4 flex justify-between">
                <div class="flex-1 flex">
                    <!-- Can add a search bar here later -->
                </div>
                <div class="ml-4 flex items-center md:ml-6">
                    <span class="text-gray-600 mr-4">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                    <a href="<?php echo ADMIN_BASE_URL; ?>/logout.php" class="text-sm font-medium text-red-600 hover:text-red-800">Logout</a>
                </div>
            </div>
        </header>
        <main class="flex-1">
            <div class="py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                    <!-- Start of page-specific content -->
