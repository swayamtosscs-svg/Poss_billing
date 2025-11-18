@echo off
setlocal enabledelayedexpansion
echo ============================================
echo Finding MySQL Password...
echo ============================================
echo.

set MYSQL="C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe"
set FOUND=0

echo Testing empty password...
%MYSQL% -u root -e "SELECT 'SUCCESS';" >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    set PASSWORD=
    set FOUND=1
    goto :found
)

echo Testing password: root
%MYSQL% -u root -proot -e "SELECT 'SUCCESS';" >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    set PASSWORD=root
    set FOUND=1
    goto :found
)

echo Testing password: password
%MYSQL% -u root -ppassword -e "SELECT 'SUCCESS';" >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    set PASSWORD=password
    set FOUND=1
    goto :found
)

echo Testing password: admin
%MYSQL% -u root -padmin -e "SELECT 'SUCCESS';" >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    set PASSWORD=admin
    set FOUND=1
    goto :found
)

echo Testing password: mysql
%MYSQL% -u root -pmysql -e "SELECT 'SUCCESS';" >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    set PASSWORD=mysql
    set FOUND=1
    goto :found
)

echo Testing password: root123
%MYSQL% -u root -proot123 -e "SELECT 'SUCCESS';" >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    set PASSWORD=root123
    set FOUND=1
    goto :found
)

echo Testing password: 12345678
%MYSQL% -u root -p12345678 -e "SELECT 'SUCCESS';" >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    set PASSWORD=12345678
    set FOUND=1
    goto :found
)

:notfound
echo.
echo ============================================
echo Password NOT found with common defaults!
echo ============================================
echo.
echo You need to RESET the password.
echo Run this file as ADMINISTRATOR:
echo   reset_mysql_password.bat
echo.
pause
exit /b 1

:found
echo.
echo ============================================
echo SUCCESS! Password found: !PASSWORD!
echo ============================================
echo.
echo Updating .env file...
powershell -Command "(gc .env) -replace 'DB_PASSWORD=.*', 'DB_PASSWORD=!PASSWORD!' | Out-File -encoding ASCII .env"

echo Creating database...
if "!PASSWORD!"=="" (
    %MYSQL% -u root -e "CREATE DATABASE IF NOT EXISTS poss_billing;"
) else (
    %MYSQL% -u root -p!PASSWORD! -e "CREATE DATABASE IF NOT EXISTS poss_billing;"
)

echo.
echo Clearing cache...
php artisan config:clear

echo.
echo Running migrations...
php artisan migrate --force

echo.
echo ============================================
echo ALL DONE! Starting server...
echo ============================================
echo.
echo Visit: http://127.0.0.1:8000
echo.

php artisan serve
