<?php
// Define a base URL constant for easier asset linking.
// Assumes the project is in a subdirectory named 'pharmacy-project'.
// Adjust if the project is at the web root.
if (!defined('BASE_URL')) {
    define('BASE_URL', '/pharmacy-project/public');
}

// Start a session on each page load if not already started.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// You might include other global configs or helpers here in the future
// require_once __DIR__ . '/config.php';
// require_once __DIR__ . '/helpers.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jules Pharmacy</title>

    <!-- Using Tailwind CSS via CDN for simplicity -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Link to a custom stylesheet for any additional styles -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/app.css">

    <!-- You can add more head elements here, like favicons -->
</head>
<body class="bg-gray-50 font-sans leading-normal tracking-normal">

    <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <nav class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="<?php echo BASE_URL; ?>/index.php" class="text-xl font-bold text-blue-600">
                        Jules Pharmacy
                    </a>
                </div>
                <div class="hidden md:flex items-center space-x-4">
                    <a href="<?php echo BASE_URL; ?>/index.php" class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Home</a>
                    <a href="<?php echo BASE_URL; ?>/cart.php" class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Cart</a>
                    <a href="<?php echo BASE_URL; ?>/wishlist.php" class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Wishlist</a>
                    <a href="<?php echo BASE_URL; ?>/advice.php" class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Advice</a>
                </div>
                <div class="hidden md:flex items-center space-x-4">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="<?php echo BASE_URL; ?>/profile.php" class="bg-blue-500 text-white px-3 py-2 rounded-md text-sm font-medium hover:bg-blue-600">Profile</a>
                        <a href="<?php echo BASE_URL; ?>/logout.php" class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Logout</a>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>/login.php" class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Login</a>
                        <a href="<?php echo BASE_URL; ?>/register.php" class="bg-green-500 text-white px-3 py-2 rounded-md text-sm font-medium hover:bg-green-600">Register</a>
                    <?php endif; ?>
                </div>
                <!-- Mobile menu button (placeholder) -->
                <div class="md:hidden flex items-center">
                    <button class="outline-none mobile-menu-button">
                        <svg class="w-6 h-6 text-gray-500" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M4 6h16M4 12h16m-7 6h7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </nav>
    </header>

    <!-- Start of the main content area -->
    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
