@echo off
echo ============================================
echo Setting up XAMPP MySQL for PHPMyAdmin
echo ============================================
echo.

echo Step 1: Stopping MySQL80 service...
echo (This requires Administrator privileges)
net stop MySQL80
if %ERRORLEVEL% NEQ 0 (
    echo.
    echo WARNING: Could not stop MySQL80
    echo Please stop it manually from Services
    echo Or run this script as Administrator
    echo.
    pause
)

echo.
echo Step 2: Please start XAMPP Control Panel and start MySQL
echo After MySQL is running in XAMPP, press any key...
pause

echo.
echo Step 3: Testing XAMPP MySQL connection...
"C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS poss_billing; SHOW DATABASES LIKE 'poss_billing';"

if %ERRORLEVEL% EQU 0 (
    echo.
    echo SUCCESS! XAMPP MySQL is working!
    echo Database 'poss_billing' created
    echo.
    
    echo Step 4: Updating .env file...
    powershell -Command "(gc .env) -replace 'DB_CONNECTION=sqlite', 'DB_CONNECTION=mysql' | Out-File -encoding ASCII .env.temp"
    powershell -Command "(gc .env.temp) -replace '# DB_HOST=127.0.0.1', 'DB_HOST=127.0.0.1' | Out-File -encoding ASCII .env.temp2"
    powershell -Command "(gc .env.temp2) -replace '# DB_PORT=3306', 'DB_PORT=3306' | Out-File -encoding ASCII .env.temp3"
    powershell -Command "(gc .env.temp3) -replace '# DB_DATABASE=poss_billing', 'DB_DATABASE=poss_billing' | Out-File -encoding ASCII .env.temp4"
    powershell -Command "(gc .env.temp4) -replace '# DB_USERNAME=root', 'DB_USERNAME=root' | Out-File -encoding ASCII .env.temp5"
    powershell -Command "(gc .env.temp5) -replace '# DB_PASSWORD=', 'DB_PASSWORD=' | Out-File -encoding ASCII .env"
    del .env.temp .env.temp2 .env.temp3 .env.temp4 .env.temp5
    
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
    echo.
    echo Application: http://127.0.0.1:8000
    echo PHPMyAdmin: http://localhost/phpmyadmin
    echo Database: poss_billing
    echo.
    php artisan serve
) else (
    echo.
    echo ERROR: Could not connect to XAMPP MySQL
    echo.
    echo Please make sure:
    echo 1. XAMPP Control Panel is open
    echo 2. MySQL is started in XAMPP (green "Running" status)
    echo 3. MySQL80 service is stopped
    echo.
    echo Then run this script again
    pause
)
