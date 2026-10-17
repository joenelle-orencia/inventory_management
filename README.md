# Inventory Management System

A responsive full-stack PHP + MySQL inventory management system built to align with the Web Application Development project specification.

## Features
- Registration and login; passwords stored with PHP `password_hash()` and checked with `password_verify()`.
- PHP sessions with session ID regeneration on successful login.
- Role-based access: standard users can add/view/edit products; admins can also delete products and manage roles.
- Product CRUD (create, read, update, delete), unique SKU, categories, descriptions, quantities, prices, and low-stock threshold.
- Dashboard totals, low-stock count, recent product updates, and searchable inventory.
- Bootstrap 5 responsive layout, HTML5 validation, JavaScript confirmation/password-match checks.
- Server-side validation, PDO prepared statements, HTML output escaping, and CSRF tokens on POST forms.

## Requirements
- PHP 8.0+ recommended, with PDO MySQL and mbstring extensions.
- MySQL or MariaDB.
- Internet access for the Bootstrap CDN (or download Bootstrap locally for fully offline use).

## Local setup (XAMPP)
1. Copy the `inventory_management` folder into `C:\xampp\htdocs\`.
2. Start **Apache** and **MySQL** in XAMPP.
3. Open `http://localhost/phpmyadmin`.
4. Import `database.sql` (it creates the `inventory_management` database and tables).
5. Open `config.php`. Default XAMPP settings are `localhost`, database `inventory_management`, user `root`, blank password. Change these if your MySQL credentials differ.
6. Visit `http://localhost/inventory_management/`.
7. Register your account. New accounts are standard users.
8. To make your own account an administrator for grading, open phpMyAdmin, select `inventory_management` > SQL, and run the following after replacing the email:
   ```sql
   UPDATE users SET role = 'admin' WHERE email = 'your-example@example.com';
   ```
9. Log out and log back in so the admin role is refreshed in your session.

## Deploy to InfinityFree (or another PHP/MySQL host)
1. Create a hosting account and a MySQL database in the host control panel.
2. Import `database.sql` into the database using the host's phpMyAdmin. Some free hosts do not allow `CREATE DATABASE`; if import fails, remove the first two lines (`CREATE DATABASE...` and `USE inventory_management;`) and import into the database already created by the host.
3. Upload the **contents** of this project folder to `htdocs` (or the host's documented public web directory). Avoid uploading unrelated XAMPP files.
4. Update `$host`, `$db`, `$user`, and `$pass` in `config.php` to the exact database credentials supplied by your host. Do not use the local XAMPP `root` credentials online.
5. Open the live URL and register your account. Promote it to admin through phpMyAdmin using the SQL command above.
6. Test registration, login, product create/edit/delete (admin), search, low-stock display, and mobile layout.
7. Use HTTPS if provided by your host. Do not publish database credentials in GitHub.

## GitHub repository checklist
- Upload the project source and `database.sql`.
- Keep real production database credentials out of the repository; consider using environment variables for a real production deployment.
- README should include setup instructions and admin test account instructions. Do not commit real user passwords or production data.
- The live URL must follow your instructor's required student-name subdomain/domain naming convention.

## Important grading notes
- This source package is ready for local setup but is **not yet a live deployed website**. You must import the database, test it in your environment, upload it to hosting, and submit the actual live URL and GitHub repository URL.
- `database.sql` intentionally does not contain a default admin password. Register normally, then promote your own account to admin in phpMyAdmin.
- Standard users are permitted to create and edit products; deletion and user-role management are admin-only.
