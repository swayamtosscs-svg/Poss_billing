# Production Deployment Instructions

## Server Information
- **Server IP:** 103.14.120.163
- **Port:** 8080
- **Access URL:** http://103.14.120.163:8080

## Steps to Deploy on Server (via PuTTY/SSH)

### 1. Connect to Server
```bash
ssh root@103.14.120.163
# or use PuTTY with IP: 103.14.120.163
```

### 2. Navigate to Project Directory
```bash
cd /path/to/pos_billing
# Replace with your actual project path
```

### 3. Update Environment Configuration
Edit `.env` file and set:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://103.14.120.163:8080

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_billing
DB_USERNAME=pos_user
DB_PASSWORD=Pos@12345
```

### 4. Install Dependencies (if not already done)
```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

### 5. Run Migrations
```bash
php artisan migrate --force
php artisan storage:link
```

### 6. Set Permissions
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### 7. Start Server
```bash
# Make script executable
chmod +x start_production_server.sh

# Run the server
./start_production_server.sh
```

### 8. Run in Background (Recommended)
To keep server running after closing SSH session:
```bash
# Using nohup
nohup php artisan serve --host=0.0.0.0 --port=8080 > server.log 2>&1 &

# Or using screen (install first: apt-get install screen)
screen -S pos_billing
php artisan serve --host=0.0.0.0 --port=8080
# Press Ctrl+A then D to detach
```

### 9. Check if Server is Running
```bash
# Check if port 8080 is listening
netstat -tulpn | grep 8080

# Or
ss -tulpn | grep 8080
```

### 10. Firewall Configuration
Make sure port 8080 is open:
```bash
# For UFW (Ubuntu)
sudo ufw allow 8080/tcp

# For firewalld (CentOS/RHEL)
sudo firewall-cmd --permanent --add-port=8080/tcp
sudo firewall-cmd --reload

# For iptables
sudo iptables -A INPUT -p tcp --dport 8080 -j ACCEPT
```

## Using PM2 (Recommended for Production)

For better process management, use PM2:

```bash
# Install PM2 globally
npm install -g pm2

# Start application
pm2 start "php artisan serve --host=0.0.0.0 --port=8080" --name pos_billing

# Save PM2 configuration
pm2 save

# Setup PM2 to start on boot
pm2 startup
```

## Using Systemd Service (Best for Production)

Create a systemd service file:

```bash
sudo nano /etc/systemd/system/pos-billing.service
```

Add this content:
```ini
[Unit]
Description=POS Billing System
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/path/to/pos_billing
ExecStart=/usr/bin/php artisan serve --host=0.0.0.0 --port=8080
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
```

Then:
```bash
sudo systemctl daemon-reload
sudo systemctl enable pos-billing
sudo systemctl start pos-billing
sudo systemctl status pos-billing
```

## Access the Application

Once server is running, access it at:
- **URL:** http://103.14.120.163:8080
- **Login:** Use your existing credentials

## Troubleshooting

### Server not accessible from outside
1. Check firewall: `sudo ufw status` or `sudo firewall-cmd --list-all`
2. Check if server is listening: `netstat -tulpn | grep 8080`
3. Verify APP_URL in .env matches server IP

### Permission errors
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Database connection errors
- Verify MySQL is running: `sudo systemctl status mysql`
- Check database credentials in .env
- Test connection: `php artisan tinker` then `DB::connection()->getPdo();`

