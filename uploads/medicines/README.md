# Writable Directory

This directory is used to store uploaded medicine thumbnail images.

**IMPORTANT:** For the file upload functionality to work, the web server (e.g., Apache, Nginx) must have **write permissions** on this directory.

You can typically set this on a Linux server with the following command:
`chmod -R 755 /path/to/your/project/root`
`chown -R www-data:www-data /path/to/your/project/root/uploads`

(Replace `www-data` with your server's user, e.g., `apache`)
