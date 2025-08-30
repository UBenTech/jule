<?php
/**
 * Admin Area Header
 *
 * Sets up the HTML document and the top part of the admin layout.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? esc($page_title) . ' - ' : ''; ?>Admin Panel</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <style>
        .admin-wrapper {
            display: flex;
        }
        .admin-sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: #fff;
            padding: 20px;
            min-height: calc(100vh - 55px); /* Full height minus footer/header */
        }
        .admin-sidebar h3 {
            color: #fff;
            margin-top: 0;
        }
        .admin-sidebar ul {
            list-style: none;
            padding: 0;
        }
        .admin-sidebar ul li a {
            color: #ecf0f1;
            text-decoration: none;
            display: block;
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 5px;
        }
        .admin-sidebar ul li a:hover, .admin-sidebar ul li a.active {
            background-color: #34495e;
        }
        .admin-content {
            flex: 1;
            padding: 20px;
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/navbar.php'; // We can still use the main navbar ?>

<div class="admin-wrapper">
    <?php include __DIR__ . '/admin_sidebar.php'; ?>
    <div class="admin-content">
