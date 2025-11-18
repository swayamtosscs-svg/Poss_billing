@echo off
echo ============================================
echo Switching to XAMPP MySQL
echo ============================================
echo.

echo Step 1: Stopping MySQL80 service...
net stop MySQL80
if %ERRORLEVEL% NEQ 0 (
    echo Warning: Could not stop MySQL80. It might already be stopped.
)

echo.
echo Step 2: Starting XAMPP MySQL...
echo Please start XAMPP Control Panel and start MySQL manually
echo.
echo After MySQL is started in XAMPP, press any key to continue...
pause

echo.
echo Step 3: Testing XAMPP MySQL with empty password...
"C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS poss_billing; SHOW DATABASES LIKE 'poss_billing';"

if %ERRORLEVEL% EQU 0 (
    echo.
    echo SUCCESS! XAMPP MySQL is working!
    echo.
    echo Step 4: Updating .env file for XAMPP...
    powershell -Command "(gc .env) -replace 'DB_PASSWORD=.*', 'DB_PASSWORD=' | Out-File -encoding ASCII .env"
    
    echo.
    echo Step 5: Clearing cache...
    del /f /q bootstrap\cache\config.php 2>nul
    php artisan config:clear
    
    echo.
    echo Step 6: Running migrations...
    php artisan migrate --force
    
    echo.
    echo ============================================
    echo ALL SET! Starting server...
    echo ============================================
    echo Visit: http://127.0.0.1:8000
    echo.
    php artisan serve
) else (
    echo.
    echo ERROR: XAMPP MySQL is not accessible
    echo.
    echo Please:
    echo 1. Open XAMPP Control Panel
    echo 2. Start MySQL
    echo 3. Run this script again
    pause
)
