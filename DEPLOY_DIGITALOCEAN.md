# Deploy LocalSaver to DigitalOcean (Fresh Server)

Complete guide to deploy from scratch. Use a **clean Droplet** or wipe the existing one first.

---

## Prerequisites

- DigitalOcean account
- Domain (or use Droplet IP for testing)
- GitHub/GitLab repo with your code (or deploy via SCP/SFTP)

---

## Step 1: Create a Fresh Droplet

1. **DigitalOcean** → **Droplets** → **Create Droplet**
2. **Image**: Ubuntu 24.04 LTS
3. **Plan**: Basic, $12/mo (2 GB RAM) or $6/mo (1 GB) for testing
4. **Datacenter**: Choose nearest region
5. **Authentication**: SSH key (recommended) or password
6. **Hostname**: `localsaver` (optional)
7. Click **Create Droplet**

---

## Step 2: Connect & Wipe (if Reusing Old Server)

```bash
ssh root@YOUR_DROPLET_IP
```

If you want to **remove everything** and start fresh:

```bash
# Stop services
systemctl stop nginx php8.2-fpm mysql 2>/dev/null

# Remove old app (adjust path if different)
rm -rf /var/www/localsaver

# Optional: Reset MySQL
# mysql -e "DROP DATABASE IF EXISTS localsaver; CREATE DATABASE localsaver;"
```

---

## Step 3: Install Required Software

Run these on the server:

```bash
# Update system
apt update && apt upgrade -y

# PHP 8.2 + extensions
add-apt-repository -y ppa:ondrej/php
apt update
apt install -y php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-bcmath php8.2-redis unzip

# MySQL
apt install -y mysql-server
mysql_secure_installation
# Set root password when prompted; answer Y to remove test DB, anonymous users

# Nginx
apt install -y nginx

# Node.js 20 (for Vite build)
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs

# Composer
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
```

---

## Step 4: Create Database & User

```bash
mysql -u root -p
```

```sql
CREATE DATABASE localsaver CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'localsaver'@'localhost' IDENTIFIED BY 'YOUR_STRONG_PASSWORD';
GRANT ALL PRIVILEGES ON localsaver.* TO 'localsaver'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

Replace `YOUR_STRONG_PASSWORD` with a secure password.

---

## Step 5: Deploy Your Code

**Option A – Git (recommended)**

```bash
mkdir -p /var/www
cd /var/www
git clone https://github.com/YOUR_USERNAME/localsaver.git
cd localsaver
```

**Option B – Upload via SCP (if no Git)**

```bash
# On your local machine:
scp -r /Users/yishaan/Desktop/localsaver/* root@YOUR_DROPLET_IP:/var/www/localsaver/
```

Then on server:

```bash
cd /var/www/localsaver
```

---

## Step 6: Install Dependencies & Build

```bash
cd /var/www/localsaver

# Copy env
cp .env.example .env

# Composer (no dev for production)
composer install --no-dev --optimize-autoloader

# Generate key
php artisan key:generate

# Build assets
npm ci
npm run build

# Link storage
php artisan storage:link

# Migrate
php artisan migrate --force
```

---

## Step 7: Configure Environment (.env)

```bash
nano /var/www/localsaver/.env
```

Set at least:

```env
APP_NAME=LocalSaver
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=localsaver
DB_USERNAME=localsaver
DB_PASSWORD=YOUR_STRONG_PASSWORD

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

RAZORPAY_KEY=your_key
RAZORPAY_SECRET=your_secret
RAZORPAY_WEBHOOK_SECRET=your_webhook_secret

GOOGLE_MAPS_API_KEY=optional
FIREBASE_SERVER_KEY=optional
```

Run after editing:

```bash
php artisan config:cache
```

---

## Step 8: Nginx Configuration

```bash
nano /etc/nginx/sites-available/localsaver
```

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/localsaver/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
        fastcgi_read_timeout 300;
    }
}
```

Enable and test:

```bash
ln -sf /etc/nginx/sites-available/localsaver /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
nginx -t && systemctl reload nginx
```

If using **droplet IP only** (no domain), replace `server_name yourdomain.com` with `_` or `default_server`.

---

## Step 9: File Permissions

```bash
chown -R www-data:www-data /var/www/localsaver
chmod -R 755 /var/www/localsaver
chmod -R 775 /var/www/localsaver/storage
chmod -R 775 /var/www/localsaver/bootstrap/cache
```

---

## Step 10: Queue Worker (Required for Notifications)

```bash
nano /etc/systemd/system/localsaver-queue.service
```

```ini
[Unit]
Description=LocalSaver Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/localsaver/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
WorkingDirectory=/var/www/localsaver

[Install]
WantedBy=multi-user.target
```

```bash
systemctl daemon-reload
systemctl enable localsaver-queue
systemctl start localsaver-queue
```

---

## Step 11: Scheduler (Cron)

```bash
crontab -u www-data -e
```

Add:

```
* * * * * cd /var/www/localsaver && php artisan schedule:run >> /dev/null 2>&1
```

---

## Step 12: SSL with Let's Encrypt (if you have a domain)

```bash
apt install -y certbot python3-certbot-nginx
certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

Follow prompts. Certbot will update Nginx and set up auto-renewal.

Update `.env`:

```env
APP_URL=https://yourdomain.com
```

Then:

```bash
php artisan config:cache
```

---

## Step 13: Razorpay Webhook

1. In Razorpay Dashboard → Webhooks → Add endpoint
2. URL: `https://yourdomain.com/webhooks/razorpay`
3. Event: `payment.captured`
4. Copy the **Signing Secret** → set as `RAZORPAY_WEBHOOK_SECRET` in `.env`
5. Run `php artisan config:cache`

---

## Quick Check Commands

```bash
# App responds
curl -I http://localhost

# Queue running
systemctl status localsaver-queue

# Scheduler
grep schedule /var/log/syslog  # or check crontab -l -u www-data
```

---

## Common Fixes

| Problem | Fix |
|--------|-----|
| 502 Bad Gateway | `systemctl status php8.2-fpm` and check socket path in Nginx |
| 500 Error | `tail -f /var/www/localsaver/storage/logs/laravel.log` |
| Assets 404 | Run `npm run build` again, ensure `public/build` exists |
| CSRF on webhook | Webhook is exempt; verify `webhooks/razorpay` in `bootstrap/app.php` |
| Migrations fail | Check DB credentials in `.env`, ensure database exists |

---

## One-Line Fresh Start (after Step 2)

If you've done setup once and only need to redeploy:

```bash
cd /var/www/localsaver && git pull && composer install --no-dev --optimize-autoloader && npm ci && npm run build && php artisan migrate --force && php artisan config:cache && systemctl restart localsaver-queue
```
