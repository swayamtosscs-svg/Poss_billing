# This script tries common MySQL passwords
$mysqlPath = "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe"
$commonPasswords = @("", "root", "password", "admin", "mysql", "root123", "12345678", "Password", "Root", "Admin")

Write-Host "Trying to find your MySQL password..." -ForegroundColor Cyan
Write-Host ""

$foundPassword = $null

foreach ($pwd in $commonPasswords) {
    $displayPwd = if ($pwd -eq "") { "(empty)" } else { $pwd }
    Write-Host "Testing password: $displayPwd" -NoNewline
    
    try {
        if ($pwd -eq "") {
            $result = & $mysqlPath -u root -e "SELECT 'SUCCESS' as test;" 2>&1
        } else {
            $passArg = "-p" + $pwd
            $result = & $mysqlPath -u root $passArg -e "SELECT 'SUCCESS' as test;" 2>&1
        }
        
        if ($result -like "*SUCCESS*") {
            Write-Host " ✓ FOUND!" -ForegroundColor Green
            $foundPassword = $pwd
            break
        } else {
            Write-Host " ✗" -ForegroundColor Red
        }
    } catch {
        Write-Host " ✗" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Yellow

if ($foundPassword -ne $null) {
    Write-Host "SUCCESS! Your MySQL password is: $foundPassword" -ForegroundColor Green
    Write-Host "========================================" -ForegroundColor Yellow
    Write-Host ""
    
    # Update .env file
    Write-Host "Updating .env file..." -ForegroundColor Cyan
    $envContent = Get-Content .env
    $envContent = $envContent -replace 'DB_PASSWORD=.*', "DB_PASSWORD=$foundPassword"
    $envContent | Set-Content .env
    
    # Create database
    Write-Host "Creating database..." -ForegroundColor Cyan
    if ($foundPassword -eq "") {
        & $mysqlPath -u root -e "CREATE DATABASE IF NOT EXISTS poss_billing;"
    } else {
        $passArg = "-p" + $foundPassword
        & $mysqlPath -u root $passArg -e "CREATE DATABASE IF NOT EXISTS poss_billing;"
    }
    
    # Clear config
    Write-Host "Clearing Laravel cache..." -ForegroundColor Cyan
    php artisan config:clear 2>$null
    
    # Run migrations
    Write-Host "Running migrations..." -ForegroundColor Cyan
    php artisan migrate --force
    
    Write-Host ""
    Write-Host "========================================" -ForegroundColor Green
    Write-Host "✓ ALL SET! Starting server..." -ForegroundColor Green
    Write-Host "========================================" -ForegroundColor Green
    Write-Host ""
    Write-Host "Visit: http://127.0.0.1:8000" -ForegroundColor Yellow
    Write-Host ""
    
    # Start server
    php artisan serve
    
} else {
    Write-Host "Could not find password with common defaults." -ForegroundColor Red
    Write-Host "========================================" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "You need to reset MySQL password manually:" -ForegroundColor Yellow
    Write-Host "1. Right-click 'reset_mysql_password.bat'" -ForegroundColor White
    Write-Host "2. Select 'Run as administrator'" -ForegroundColor White
    Write-Host ""
    Write-Host "Or use MySQL Workbench to reset password." -ForegroundColor White
}
