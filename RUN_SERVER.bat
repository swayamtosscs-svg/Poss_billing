@echo off
cls
echo ============================================
echo   POS BILLING SYSTEM - XAMPP MYSQL
echo ============================================
echo.
echo Database Info:
echo   Host: 127.0.0.1
echo   Port: 3307 (XAMPP MySQL)
echo   Database: poss_billing
echo   Username: root
echo   Password: (empty)
echo.
echo Login Credentials:
echo   Email: admin@example.com
echo   Password: password
echo.
echo PHPMyAdmin: http://localhost/phpmyadmin
echo Application: http://127.0.0.1:8000
echo.
echo ============================================
echo.
echo Starting Laravel server...
echo.

REM Set environment variable for this session only
set DB_PORT=3307

php artisan serve --host=127.0.0.1 --port=8000
