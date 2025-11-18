@echo off
echo ============================================
echo MySQL Password Setup Helper
echo ============================================
echo.
echo Current .env settings:
type .env | findstr DB_
echo.
echo ============================================
echo.
set /p MYSQL_PASS="Enter your MySQL root password (press Enter if empty): "
echo.
echo Updating .env file...

powershell -Command "(gc .env) -replace 'DB_PASSWORD=.*', 'DB_PASSWORD=%MYSQL_PASS%' | Out-File -encoding ASCII .env"

echo.
echo Testing database connection...
"C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" -u root -p%MYSQL_PASS% -e "CREATE DATABASE IF NOT EXISTS poss_billing; USE poss_billing; SELECT 'Database connected successfully!' as Status;"

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ============================================
    echo SUCCESS! Database connected!
    echo ============================================
    echo.
    echo Now running migrations...
    php artisan migrate --force
    echo.
    echo Starting server...
    php artisan serve
) else (
    echo.
    echo ============================================
    echo ERROR: Could not connect to database
    echo Please check your password and try again
    echo ============================================
    pause
)
