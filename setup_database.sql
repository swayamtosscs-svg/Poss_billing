-- Run this SQL script in phpMyAdmin or MySQL command line
-- to create the user and database

-- Create user if it doesn't exist
CREATE USER IF NOT EXISTS 'pos_user'@'localhost' IDENTIFIED BY 'Pos@12345';

-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS pos_billing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Grant all privileges on the database to the user
GRANT ALL PRIVILEGES ON pos_billing.* TO 'pos_user'@'localhost';

-- Also allow connection from 127.0.0.1 (sometimes needed)
CREATE USER IF NOT EXISTS 'pos_user'@'127.0.0.1' IDENTIFIED BY 'Pos@12345';
GRANT ALL PRIVILEGES ON pos_billing.* TO 'pos_user'@'127.0.0.1';

-- Refresh privileges
FLUSH PRIVILEGES;

-- Verify
SELECT User, Host FROM mysql.user WHERE User='pos_user';
SHOW DATABASES LIKE 'pos_billing';

