@echo off
echo ============================================
echo MySQL Password Reset Utility
echo ============================================
echo.
echo This will reset your MySQL root password to: root123
echo.
pause

echo Stopping MySQL service...
net stop MySQL80

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo ERROR: Could not stop MySQL service.
    echo Please run this script as Administrator!
    echo Right-click this file and select "Run as administrator"
    pause
    exit
)

echo.
echo Creating temporary init file...
echo ALTER USER 'root'@'localhost' IDENTIFIED BY 'root123'; > "%TEMP%\mysql_reset.sql"
echo FLUSH PRIVILEGES; >> "%TEMP%\mysql_reset.sql"

echo.
echo Starting MySQL in safe mode...
start /B "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysqld.exe" --init-file="%TEMP%\mysql_reset.sql" --console

echo Waiting for MySQL to process password reset...
timeout /t 10 /nobreak

echo.
echo Stopping MySQL safe mode...
taskkill /F /IM mysqld.exe /T

timeout /t 3 /nobreak

echo.
echo Starting MySQL service normally...
net start MySQL80

echo.
echo Cleaning up...
del "%TEMP%\mysql_reset.sql"

echo.
echo ============================================
echo Password has been reset to: root123
echo ============================================
echo.
echo Now updating .env file...
cd /d D:\pos_billing
powershell -Command "(gc .env) -replace 'DB_PASSWORD=.*', 'DB_PASSWORD=root123' | Out-File -encoding ASCII .env"

echo.
echo Testing connection...
"C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" -u root -proot123 -e "CREATE DATABASE IF NOT EXISTS poss_billing; SELECT 'SUCCESS - Database Connected!' as Status;"

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ============================================
    echo SUCCESS! Everything is set up!
    echo ============================================
    echo.
    echo Your MySQL password is now: root123
    echo Database: poss_billing created
    echo .env file updated
    echo.
    echo Press any key to run migrations and start server...
    pause
    php artisan migrate --force
    php artisan serve
) else (
    echo.
    echo Something went wrong. Please try running as Administrator.
    pause
)
