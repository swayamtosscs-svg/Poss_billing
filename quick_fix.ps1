# Quick Fix Script - Update this with YOUR MySQL password
$MYSQL_PASSWORD = "YOUR_PASSWORD_HERE"  # CHANGE THIS LINE!

Write-Host "Updating .env file..." -ForegroundColor Yellow
(Get-Content .env) -replace 'DB_PASSWORD=.*', "DB_PASSWORD=$MYSQL_PASSWORD" | Set-Content .env

Write-Host "`nTesting database connection..." -ForegroundColor Yellow
& "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" -u root "-p$MYSQL_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS poss_billing; SELECT 'Connected!' as Status;"

if ($LASTEXITCODE -eq 0) {
    Write-Host "`nSUCCESS! Running migrations..." -ForegroundColor Green
    php artisan migrate --force
    
    Write-Host "`nStarting server..." -ForegroundColor Green
    php artisan serve
} else {
    Write-Host "`nERROR: Wrong password! Edit quick_fix.ps1 and update line 2 with correct password" -ForegroundColor Red
}
