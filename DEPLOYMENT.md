# LocalSaver - Deployment Guide (Hostinger VPS)

## Prerequisites
- PHP 8.2+
- Composer
- MySQL/PostgreSQL
- Node.js 18+ (for building assets)
- Nginx or Apache

## 1. Server Setup (Hostinger VPS)

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2, Composer, MySQL, Nginx
sudo apt install -y php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-gd
sudo apt install -y composer mysql-server nginx
```

## 2. Clone & Install

```bash
cd /var/www
git clone <your-repo> localsaver
cd localsaver

# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Copy environment
cp .env.example .env
php artisan key:generate

# Configure .env (see Environment Variables below)
nano .env

# Run migrations
php artisan migrate --force

# Seed admin user (optional)
php artisan db:seed --class=DatabaseSeeder

# Build frontend assets
npm ci
npm run build

# Create storage link
php artisan storage:link

# Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

## 3. Environment Variables (.env)

```env
APP_NAME=LocalSaver
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=localsaver
DB_USERNAME=localsaver
DB_PASSWORD=your_secure_password

RAZORPAY_KEY=your_razorpay_key
RAZORPAY_SECRET=your_razorpay_secret

GOOGLE_MAPS_API_KEY=your_google_maps_key

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

## 4. Nginx Configuration

```nginx
server {
    listen 80;
    server_name yourdomain.com;
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
    location = /sw.js { add_header Cache-Control "no-cache"; }
    location = /manifest.json { add_header Cache-Control "no-cache"; }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }
}
```

## 5. Cron & Queue (Scheduler)

Add to crontab: `crontab -e`

```
* * * * * cd /var/www/localsaver && php artisan schedule:run >> /dev/null 2>&1
```

For queue worker (optional, for notifications):
```bash
# Use supervisor or screen to run:
php artisan queue:work --tries=3
```

## 6. SSL (Let's Encrypt)

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com
```

## 7. Post-Deploy Checklist

- [ ] Configure Razorpay webhook: `https://yourdomain.com/razorpay/webhook` (create route if needed)
- [ ] Add Google Maps API key (enable Maps JavaScript API)
- [ ] Test PWA install on mobile
- [ ] Verify coupon expiry cron runs daily
- [ ] Set up backup for database

## 8. OTP Integration (Production)

For production phone OTP, integrate an SMS provider:
- **MSG91** (India)
- **Twilio**
- Update `app/Services/OtpService.php` → `sendOtp()` method

## 9. Performance Tips

- Enable OPcache in PHP
- Use Redis for cache/sessions (optional)
- Run `php artisan config:cache` and `php artisan route:cache`
- Enable gzip in Nginx
