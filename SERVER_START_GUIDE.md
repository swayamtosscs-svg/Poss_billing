# Server Start Guide - Quick Reference

## आपके Server पर Project चलाने के लिए

### Server Details:
- **IP Address:** 103.14.120.163
- **Port:** 8080
- **URL:** http://103.14.120.163:8080

## PuTTY से Server पर कैसे चलाएं:

### Step 1: PuTTY से Connect करें
```
Host: 103.14.120.163
Port: 22 (default SSH port)
```

### Step 2: Project Directory में जाएं
```bash
cd /path/to/pos_billing
```

### Step 3: Server Start करें
```bash
# Option 1: Simple start (foreground)
php artisan serve --host=0.0.0.0 --port=8080

# Option 2: Background में चलाने के लिए
nohup php artisan serve --host=0.0.0.0 --port=8080 > server.log 2>&1 &

# Option 3: Script use करें
chmod +x start_production_server.sh
./start_production_server.sh
```

### Step 4: Firewall Check करें
```bash
# Port 8080 को allow करें
sudo ufw allow 8080/tcp
# या
sudo firewall-cmd --permanent --add-port=8080/tcp
sudo firewall-cmd --reload
```

### Step 5: Browser में Access करें
```
http://103.14.120.163:8080
```

## Important Notes:

1. **0.0.0.0** use करें (127.0.0.1 नहीं) - यह external connections allow करता है
2. **Port 8080** firewall में open होना चाहिए
3. Server को background में चलाने के लिए `nohup` या `screen` use करें
4. `.env` file में `APP_URL=http://103.14.120.163:8080` set होना चाहिए

## Production के लिए Best Practice:

PM2 या Systemd service use करें (details के लिए `DEPLOYMENT_INSTRUCTIONS.md` देखें)

