@echo off
echo ============================================
echo Final Setup with XAMPP MySQL (Port 3307)
echo ============================================
echo.

echo Clearing all caches...
del /f /q bootstrap\cache\*.php 2>nul

echo.
echo Running migrations...
php artisan migrate --force

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ============================================
    echo SUCCESS! Database is set up!
    echo ============================================
    echo.
    echo Starting server...
    echo.
    echo Application: http://127.0.0.1:8000
    echo PHPMyAdmin: http://localhost/phpmyadmin
    echo Database: poss_billing (port 3307)
    echo.
    php artisan serve
) else (
    echo.
    echo ============================================
    echo ERROR: Migration failed
    echo ============================================
    echo.
    echo Current .env settings:
    type .env | findstr DB_
    echo.
    pause
)
