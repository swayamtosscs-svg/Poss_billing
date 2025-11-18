@echo off
REM POS Billing System - Production Server Script (Windows)
REM For testing on Windows before deploying to server

cls
echo ============================================
echo   POS BILLING SYSTEM - PRODUCTION SERVER
echo ============================================
echo.
echo Server IP: 103.14.120.163
echo Port: 8080
echo Access URL: http://103.14.120.163:8080
echo.
echo Starting Laravel server on 0.0.0.0:8080...
echo Press Ctrl+C to stop the server
echo.
echo ============================================
echo.

REM Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

REM Start server on all interfaces (0.0.0.0) on port 8080
php artisan serve --host=0.0.0.0 --port=8080

