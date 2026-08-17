# ClinicFlow - Deployment & MySQL Configuration Guide

This document explains how to set up, configure, and install the MySQL database for ClinicFlow on **A2 Hosting** or any PHP 8.1+ web server.

---

## 1. Quick Web Installer (Recommended)

You can install and configure ClinicFlow in under 1 minute using the built-in **Web Installation Wizard**:

1. Upload the project files to your A2 Hosting server (e.g., `public_html/` or a subdomain folder).
2. Open your browser and navigate to:
   `https://yourdomain.com/install.php`
3. Fill in your MySQL credentials:
   - **Database Host**: `localhost` or your A2 Hosting MySQL server address.
   - **Database Name**: Your MySQL database name (e.g., `cpaneluser_clinicflow`).
   - **Port**: `3306`
   - **Database Username**: Your MySQL user name (e.g., `cpaneluser_clinicuser`).
   - **Database Password**: Your MySQL password.
   - **Administrator Doctor Details**: Specify the chief physician's name and specialty.
4. Click **Save Config & Install Database**.

The wizard will:
- Test the database connection.
- Create the database if it doesn't exist.
- Automatically execute `database/schema.sql` to build all required tables.
- Seed initial mock data and administrator profiles.
- Write your settings to `config/config.php`.

---

## 2. Manual Configuration File Setup

If you prefer to configure MySQL manually without using the web installer:

1. Copy `config/config.example.php` to `config/config.php`:
   ```bash
   cp config/config.example.php config/config.php
   ```

2. Open `config/config.php` in a text editor and enter your MySQL details:

   ```php
   <?php
   return [
       'db_driver' => 'mysql',
       'db_host'   => 'localhost',
       'db_port'   => '3306',
       'db_name'   => 'cpaneluser_clinicflow',
       'db_user'   => 'cpaneluser_clinicuser',
       'db_pass'   => 'YourStrongPassword123!',

       'admin_doctor' => [
           'name'      => 'Dr. Sarah Jenkins',
           'specialty' => 'Internal Medicine',
       ]
   ];
   ```

3. Import the database schema into MySQL via phpMyAdmin or command line:
   ```bash
   mysql -u cpaneluser_clinicuser -p cpaneluser_clinicflow < database/schema.sql
   ```

4. Run the seed script to populate initial mock data:
   ```bash
   php database/seed.php
   ```

---

## 3. Directory Structure

- `config/config.php` - Stores active database credentials and server settings.
- `config/database.php` - Standard PDO Database connection layer.
- `database/schema.sql` - Full MySQL schema definition.
- `database/seed.php` - CLI database seeding script.
- `install.php` - Web installation wizard.
