<?php
echo "Testing MySQL connection...\n\n";

// Try empty password (XAMPP default)
echo "Trying empty password (XAMPP)... ";
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
    echo "SUCCESS!\n";
    $pdo->exec('CREATE DATABASE IF NOT EXISTS poss_billing');
    echo "Database 'poss_billing' created!\n";
    echo "\n✓ XAMPP MySQL is working!\n";
    echo "Now run: php artisan migrate\n";
    exit(0);
} catch (Exception $e) {
    echo "Failed.\n";
}

// Try root123
echo "Trying password 'root123'... ";
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', 'root123');
    echo "SUCCESS!\n";
    $pdo->exec('CREATE DATABASE IF NOT EXISTS poss_billing');
    echo "Database 'poss_billing' created!\n";
    echo "\n✓ MySQL with root123 is working!\n";
    echo "Update .env: DB_PASSWORD=root123\n";
    echo "Then run: php artisan migrate\n";
    exit(0);
} catch (Exception $e) {
    echo "Failed.\n";
}

echo "\n✗ Could not connect with empty password or root123\n";
echo "Please check:\n";
echo "1. Is MySQL/XAMPP running?\n";
echo "2. What is the actual password?\n";
