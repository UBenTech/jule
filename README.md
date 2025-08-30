# Intelligent Pharmacy Management System

A complete, ready-to-deploy web project for managing a small pharmacy. This project is built with PHP, MySQL, and vanilla JavaScript, following a simple and flat file structure for easy manual editing and deployment.

## Features

- **Public-Facing Catalog:** Browse medicines by category, group, or search.
- **Single Medicine View:** Detailed information for each medicine, including price, description, stock status, and expiry date.
- **Stock Transparency:** A public page to view current stock levels, with low-stock items highlighted.
- **User Accounts:** Public user registration and login system.
- **Wishlist:** Registered users can add medicines to a personal wishlist.
- **Booking System:** Registered users can book medicines for pickup or consultation.
- **Advice Form:** A simple form for users to submit questions to the pharmacy staff.
- **Comprehensive Admin Panel:**
    - Secure login for administrators.
    - Dashboard with key statistics.
    - Full CRUD (Create, Read, Update, Delete) management for medicines, including thumbnail uploads.
    - CRUD management for categories.
    - View and manage customer bookings.
    - View and manage advice requests.
    - Log and manage inventory changes.
- **Friendly URLs:** SEO-friendly URLs like `/medicine/ibuprofen-200mg` are handled by `.htaccess`.

---

## Requirements

- Web Server: Apache with `mod_rewrite` enabled.
- PHP: Version 7.4 or higher with `pdo_mysql` extension.
- Database: MySQL or MariaDB.

---

## Setup Instructions

Follow these steps carefully to get the project running on your server.

### 1. Get the Code
Download the project files and upload them to your web server's root directory (e.g., `public_html`).

### 2. Create the Database
Using a tool like phpMyAdmin or your hosting control panel, create a new, empty database for the project.

### 3. Import the Database Schema & Data
You now need to import three SQL files into your new database in the following order:

1.  **`sql/schema.sql`**: This creates the structure for all the tables and inserts a default admin user.
2.  **`sql/migration_01.sql`**: This updates the users table to allow for public registration.
3.  **`sql/demo_data.sql`**: This (optional but recommended) file populates the database with a large amount of sample data so you can see the site in action immediately.

### 4. Configure the Application
Open the `config.php` file in the project root and edit the following constants:

- **`BASE_URL`**: This is very important. If your site is in a subdirectory (e.g., `https://yourdomain.com/pharmacy/`), set this to `'/pharmacy'`. If it's at the root of a domain, you can set it to `''` (an empty string).
- **`DB_HOST`**: Your database host (usually `localhost`).
- **`DB_NAME`**: The name of the database you created in step 2.
- **`DB_USER`**: Your database username.
- **`DB_PASS`**: Your database password.

### 5. Configure Apache
Ensure your Apache server is configured to read the `.htaccess` file. In your site's virtual host configuration, the directory block should have `AllowOverride All`.

```apache
<Directory /path/to/your/project/root>
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```
Remember to restart Apache after making changes.

### 6. Set Directory Permissions
The web server needs to be able to write to the `uploads/` directory to save medicine thumbnail images. Connect to your server via SSH or use a file manager and set the permissions for this directory.

`chmod -R 755 /path/to/your/project/root/uploads`

You may also need to change the owner of the directory to the web server user (e.g., `www-data` or `apache`).

`chown -R www-data:www-data /path/to/your/project/root/uploads`

---

## Usage

- **Public Site:** Navigate to your domain (e.g., `https://yourdomain.com`).
- **Admin Panel:** Navigate to `https://yourdomain.com/admin`.

**Default Admin Credentials:**
- **Username:** `admin`
- **Password:** `admin123`

---

## Security Notes

This project includes basic security measures, but for a real-world deployment, you **must** take the following steps:

1.  **Change the Default Admin Password:** The first thing you should do after logging in is change the default admin password. This can be done by manually updating the `password_hash` in the `users` table in your database. You can generate a new hash using an online tool or a simple PHP script with `password_hash('yourNewPassword', PASSWORD_DEFAULT)`.

2.  **Protect the Admin Directory:** The `/admin` directory should have an extra layer of protection. The most common method is using HTTP Basic Authentication with a `.htpasswd` file. This forces a server-level username/password prompt before the PHP login page is even shown.

3.  **Use HTTPS:** Always deploy a real-world application with a valid SSL/TLS certificate to encrypt all traffic between the user and the server.

4.  **Disable Error Display:** In `config.php`, for a production environment, it's recommended to turn off public error display by changing `ini_set('display_errors', 1);` to `ini_set('display_errors', 0);` and instead log errors to a private file.
