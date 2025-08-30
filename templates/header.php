<?php
/**
 * Global Page Header
 *
 * This template contains the opening HTML document structure, including the <head>
 * section with the site's title, metadata, and CSS stylesheet link.
 *
 * To edit brand color or DB settings, see config.php.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? esc($page_title) . ' - ' : ''; ?>Intelligent Pharmacy</title>
    <meta name="description" content="A web project for an Intelligent Pharmacy Management system.">

    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">

    <!-- Brand Color Style -->
    <style>
        :root {
            --brand-green: <?php echo defined('BRAND_GREEN') ? BRAND_GREEN : '#0f9d58'; ?>;
        }
    </style>
</head>
<body>

<?php
// Include the navigation bar
include __DIR__ . '/navbar.php';
?>

<main>
    <div class="container">
