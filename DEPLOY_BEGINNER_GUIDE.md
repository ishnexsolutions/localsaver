# LocalSaver on DigitalOcean — Complete Beginner Guide

You've never used DigitalOcean. That's fine. This guide explains every step in plain English and tells you exactly what to click and type.

---

## Part 1: What You Need to Know

**DigitalOcean** = A company that rents you a tiny computer (server) in the cloud.

**Droplet** = That rented computer. You pay about **$6/month** for the smallest one.

**SSH** = A secure way to connect to your Droplet and run commands, like a remote control.

**Terminal** = On Mac: open **Terminal** (search in Spotlight). On Windows: use **PowerShell** or **Command Prompt**.

---

## Part 2: Create Your DigitalOcean Account

### Step 2.1: Sign up

1. Open your browser and go to: **https://www.digitalocean.com**
2. Click **Sign Up** (top right).
3. Enter your **email** and create a **password** (or sign up with Google).
4. Verify your email if asked.
5. Add a **payment method** (card or PayPal). DigitalOcean charges monthly. The $6 plan costs about $6/month.

---

## Part 3: Create Your First Droplet

### Step 3.1: Start creating a Droplet

1. After logging in, you'll see the DigitalOcean dashboard.
2. Click the green **Create** button (top right).
3. Choose **Droplets**.

### Step 3.2: Choose the image (operating system)

1. Under **Choose an image**, select **Ubuntu**.
2. Make sure the version is **24.04 (LTS)**. If you see a dropdown, pick 24.04.

### Step 3.3: Choose the plan (how powerful)

1. Under **Choose a plan**, select **Basic**.
2. Select the **$6/month** plan (1 GB RAM, 1 vCPU, 25 GB SSD).
   - Cheapest option to test.
   - If you want more power later, you can pick **$12/month** (2 GB RAM).

### Step 3.4: Choose the datacenter location

1. Under **Choose a datacenter region**, pick one **near you**.
   - Example: if you're in India, choose **Bangalore** or **Singapore**.

### Step 3.5: Authentication (how you’ll log in)

1. Under **Authentication**, you have two options:

   **Option A: Password (easiest for beginners)**

   - Select **Password**.
   - Enter a **strong password** (mix of letters, numbers, symbols).
   - **Write it down** — you’ll need it to connect.

   **Option B: SSH key (more secure)**

   - Select **SSH key**.
   - If you’ve added an SSH key to DigitalOcean before, choose it.
   - If not: see **“How to add an SSH key”** at the end of this guide.

### Step 3.6: Final settings

1. Under **Select additional options**: leave defaults (you can enable Backups later).
2. Under **Create Droplet**: you can change **Droplet hostname** to `localsaver` (optional).
3. Click **Create Droplet**.

### Step 3.7: Get your IP address

1. After 1–2 minutes, your Droplet appears in the list.
2. You’ll see an **IP address** (e.g. `123.45.67.89`). **Copy this** — you’ll need it for SSH.

---

## Part 4: Connect to Your Droplet (SSH)

### Step 4.1: Open Terminal (Mac) or PowerShell (Windows)

- **Mac**: Open **Terminal** (Applications → Utilities, or search “Terminal” in Spotlight).
- **Windows**: Open **PowerShell** (search “PowerShell” in Start menu).

### Step 4.2: Connect via SSH

Type (replace `YOUR_IP` with your Droplet’s IP):

```bash
ssh root@YOUR_IP
```

Example: `ssh root@123.45.67.89`

Press **Enter**.

- If asked **“Are you sure you want to continue connecting?”** type **yes** and press Enter.
- If you used a **password**: type your Droplet password (you won’t see it while typing). Press Enter.
- If you see `root@localsaver:~#` or similar, you’re in. You’re now controlling your server.

---

## Part 5: Install All Required Software on the Server

Run each block below **one at a time**. Copy the whole block, paste into Terminal, press Enter. Wait for it to finish before running the next.

### Step 5.1: Update the system

```bash
apt update && apt upgrade -y
```

(Takes 2–5 minutes.)

### Step 5.2: Install PHP

```bash
add-apt-repository -y ppa:ondrej/php
apt update
apt install -y php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-bcmath unzip
```

### Step 5.3: Install MySQL (database)

```bash
apt install -y mysql-server
```

Now secure MySQL:

```bash
mysql_secure_installation
```

When prompted:

- **Validate password plugin**: type `N` and Enter.
- **New password**: type a **strong password** and remember it. (Example: `MyDbPass2024!`)
- **Re-enter password**: type it again.
- **Remove anonymous users?**: type `Y`.
- **Disallow root login remotely?**: type `Y`.
- **Remove test database?**: type `Y`.
- **Reload privilege tables?**: type `Y`.

### Step 5.4: Install Nginx (web server)

```bash
apt install -y nginx
```

### Step 5.5: Install Node.js (for building frontend)

```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs
```

### Step 5.6: Install Composer (PHP package manager)

```bash
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
```

---

## Part 6: Create the Database

### Step 6.1: Log into MySQL

```bash
mysql -u root -p
```

Type the **MySQL root password** you set in Step 5.3. (Nothing will appear while typing.) Press Enter.

### Step 6.2: Create database and user

Copy and paste this **entire block** (change `YourSecurePass123` to your own password):

```sql
CREATE DATABASE localsaver CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'localsaver'@'localhost' IDENTIFIED BY 'YourSecurePass123';
GRANT ALL PRIVILEGES ON localsaver.* TO 'localsaver'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

Press Enter. You should be back at the normal command line (`root@...`).

**Write down** the password you used — you’ll put it in `.env` later.

---

## Part 7: Upload Your LocalSaver Code to the Server

### Option A: You have GitHub (recommended)

1. Push your LocalSaver project to GitHub.
2. On the server, run:

```bash
mkdir -p /var/www
cd /var/www
apt install -y git
git clone https://github.com/YOUR_USERNAME/YOUR_REPO_NAME.git localsaver
cd localsaver
```

(Replace `YOUR_USERNAME` and `YOUR_REPO_NAME` with your actual GitHub username and repo name.)

### Option B: You don’t use Git — upload from your Mac

**On your Mac** (in a **new** Terminal window, not the SSH one):

```bash
cd /Users/yishaan/Desktop/localsaver
scp -r . root@YOUR_DROPLET_IP:/var/www/localsaver
```

(Replace `YOUR_DROPLET_IP` with your Droplet’s IP.)

Enter your Droplet password when asked. This may take a few minutes.

Then **back on the server** (in your SSH session):

```bash
cd /var/www/localsaver
```

---

## Part 8: Install Dependencies and Build the App

**All commands below are run on the server** (in your SSH session).

### Step 8.1: Create `.env` file

```bash
cd /var/www/localsaver
cp .env.example .env
```

### Step 8.2: Install PHP packages

```bash
composer install --no-dev --optimize-autoloader
```

### Step 8.3: Generate app key

```bash
php artisan key:generate
```

### Step 8.4: Install Node packages and build frontend

```bash
npm ci
npm run build
```

(May take 2–3 minutes.)

### Step 8.5: Link storage and run migrations

```bash
php artisan storage:link
php artisan migrate --force
```

If migrations fail, check the database password in the next step first.

---

## Part 9: Configure Environment (.env)

### Step 9.1: Open `.env` for editing

```bash
nano /var/www/localsaver/.env
```

### Step 9.2: Edit the important lines

Use arrow keys to move. Edit these lines (change values as needed):

```
APP_ENV=production
APP_DEBUG=false
APP_URL=http://YOUR_DROPLET_IP
```

Replace `YOUR_DROPLET_IP` with your actual Droplet IP (e.g. `http://123.45.67.89`).

```
DB_DATABASE=localsaver
DB_USERNAME=localsaver
DB_PASSWORD=YourSecurePass123
```

Use the **same password** you set in Part 6.

If you have Razorpay keys, add:

```
RAZORPAY_KEY=your_key_here
RAZORPAY_SECRET=your_secret_here
```

### Step 9.3: Save and exit nano

- Press **Ctrl + O** (to save).
- Press **Enter**.
- Press **Ctrl + X** (to exit).

### Step 9.4: Clear config cache

```bash
php artisan config:cache
```

---

## Part 10: Configure Nginx (Web Server)

### Step 10.1: Create Nginx config file

```bash
nano /etc/nginx/sites-available/localsaver
```

### Step 10.2: Paste this config

(Replace `123.45.67.89` with your Droplet IP if you want to use IP only, or use `_` to accept any host.)

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name _;
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

### Step 10.3: Save and exit

**Ctrl + O**, Enter, **Ctrl + X**.

### Step 10.4: Enable the site

```bash
ln -sf /etc/nginx/sites-available/localsaver /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
nginx -t
```

You should see `syntax is ok` and `test is successful`.

```bash
systemctl reload nginx
```

---

## Part 11: Fix Permissions

```bash
chown -R www-data:www-data /var/www/localsaver
chmod -R 755 /var/www/localsaver
chmod -R 775 /var/www/localsaver/storage
chmod -R 775 /var/www/localsaver/bootstrap/cache
```

---

## Part 12: Start the Queue Worker (for notifications)

### Step 12.1: Create service file

```bash
nano /etc/systemd/system/localsaver-queue.service
```

### Step 12.2: Paste this content

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

### Step 12.3: Save and exit

**Ctrl + O**, Enter, **Ctrl + X**.

### Step 12.4: Start the service

```bash
systemctl daemon-reload
systemctl enable localsaver-queue
systemctl start localsaver-queue
```

---

## Part 13: Set Up the Scheduler (Cron)

```bash
crontab -u www-data -e
```

If asked to choose an editor, type `1` (nano) and Enter.

Add this line at the bottom:

```
* * * * * cd /var/www/localsaver && php artisan schedule:run >> /dev/null 2>&1
```

Save: **Ctrl + O**, Enter. Exit: **Ctrl + X**.

---

## Part 14: Test Your Site

Open your browser and go to:

```
http://YOUR_DROPLET_IP
```

(Use your real Droplet IP.)

You should see LocalSaver. If not, see **Troubleshooting** below.

---

## Part 15: Optional — Add a Domain and SSL (HTTPS)

Do this only if you have a domain (e.g. `localsaver.com`).

1. In your domain registrar, add an **A record** pointing to your Droplet IP.
2. Wait 5–10 minutes for DNS to update.
3. On the server:

```bash
apt install -y certbot python3-certbot-nginx
certbot --nginx -d yourdomain.com
```

Follow the prompts (enter email, agree to terms).

4. Update `.env`:

```bash
nano /var/www/localsaver/.env
```

Change `APP_URL` to `https://yourdomain.com`. Save and exit.

5. Clear config cache:

```bash
php artisan config:cache
```

---

## Troubleshooting

### "Site can’t be reached" or page won’t load

- Confirm you’re using **http://** not https.
- Check DigitalOcean: Project → Droplets → your Droplet. Firewall should allow port 80.

### 502 Bad Gateway

```bash
systemctl status php8.2-fpm
```

If it’s inactive, run:

```bash
systemctl start php8.2-fpm
```

### 500 Internal Server Error

Check the log:

```bash
tail -50 /var/www/localsaver/storage/logs/laravel.log
```

Common causes: wrong `.env` (DB password, `APP_KEY`), or wrong file permissions.

### "No application encryption key"

```bash
cd /var/www/localsaver
php artisan key:generate
php artisan config:cache
```

### Migrations fail

Check DB credentials in `.env`. Ensure the database and user from Part 6 exist.

---

## Quick Reference: Your Droplet IP

Your Droplet IP: ________________

MySQL root password: ________________

localsaver DB password: ________________

---

## (Optional) How to Add an SSH Key on Mac

1. Open Terminal.
2. Run: `cat ~/.ssh/id_rsa.pub`
3. If you see a long line starting with `ssh-rsa`, copy it.
4. If you get "No such file", create a key:

```bash
ssh-keygen -t rsa -b 4096 -C "your_email@example.com"
```

Press Enter for all prompts. Then run `cat ~/.ssh/id_rsa.pub` again and copy the output.

5. In DigitalOcean: **Account** → **Security** → **SSH Keys** → **Add SSH Key**.
6. Paste the key, give it a name, click **Add SSH Key**.

When creating a Droplet, select this key under Authentication instead of password.
