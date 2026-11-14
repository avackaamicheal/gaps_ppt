# Production Deployment & Operations Guide
## Victor Akande Real Estate Platform

This document outlines the standard operating procedures for deploying, hardening, configuring, and backing up the **Victor Akande Real Estate Platform** on a production Linux server (Ubuntu 22.04 / 24.04 LTS).

---

## 1. Server Environment Prerequisites

Verify that the target production environment satisfies the following baseline dependencies:

| Component | Minimum Version | Recommended / Production |
|---|---|---|
| **Operating System** | Ubuntu 22.04 LTS | Ubuntu 24.04 LTS |
| **Web Server** | Nginx 1.18+ | Nginx 1.24+ |
| **PHP Runtime** | PHP 8.2+ | PHP 8.3 or PHP 8.4 (FPM) |
| **Database** | MySQL 8.0+ / MariaDB 10.11+ | MySQL 8.0 with InnoDB |
| **Process Manager**| Systemd / Supervisor | Supervisor 4.2+ |
| **TLS/SSL** | OpenSSL 1.1.1+ | Let's Encrypt Certbot |
| **Composer** | Composer 2.7+ | Composer 2.8+ |

### Required PHP Extensions
```bash
sudo apt install -y php8.3-fpm php8.3-mysql php8.3-mbstring php8.3-xml \
                    php8.3-bcmath php8.3-curl php8.3-zip php8.3-gd php8.3-intl
```

---

## 2. Initial Application Deployment

### Step 2.1: Clone Repository
```bash
cd /var/www
sudo git clone git@github.com:your-account/gaps_ppt.git victorakande-realestate
cd victorakande-realestate
```

### Step 2.2: Install Composer Dependencies
Run without development packages and generate optimized classmaps:
```bash
composer install --no-dev --optimize-autoloader --no-interaction
```

### Step 2.3: Environment Configuration (`.env`)
Copy `.env.example` to `.env` and configure production variables:
```bash
cp .env.example .env
php artisan key:generate
```

Ensure the following critical values are set in `.env`:
```ini
APP_NAME="Victor Akande Real Estate"
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://victorakande.com

# Database Credentials
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gaps_ppt_prod
DB_USERNAME=gaps_user
DB_PASSWORD="SECURE_STRONG_PASSWORD_HERE"

# Session & Cache
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true

CACHE_STORE=database
QUEUE_CONNECTION=database

# Mail Configuration (Enquiry alerts)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=postmaster@victorakande.com
MAIL_PASSWORD=YOUR_SMTP_PASSWORD
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="victor@victorakande.com"
MAIL_FROM_NAME="Victor Akande Real Estate"
```

### Step 2.4: Storage Symlink & Directory Permissions
```bash
# Generate the public storage symlink
php artisan storage:link

# Set strict ownership and permissions
sudo chown -R www-data:www-data /var/www/victorakande-realestate/storage /var/www/victorakande-realestate/bootstrap/cache
sudo chmod -R 775 /var/www/victorakande-realestate/storage /var/www/victorakande-realestate/bootstrap/cache
```

### Step 2.5: Compile Frontend Assets
Build production JavaScript and Tailwind CSS 4 assets:
```bash
npm ci
npm run build
```

### Step 2.6: Database Migrations & Initial Seeding
```bash
php artisan migrate --force
php artisan db:seed --class=SettingSeeder --force
php artisan db:seed --class=SampleTestimonialSeeder --force
```

---

## 3. Production Performance Optimizations

Execute Laravel caching commands after every code deployment:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

> [!TIP]
> When updating settings or configuration in production, run `php artisan config:clear` and re-run `php artisan config:cache`.

---

## 4. Nginx Server Block Configuration Template

Create `/etc/nginx/sites-available/victorakande.com`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name victorakande.com www.victorakande.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name victorakande.com www.victorakande.com;

    root /var/www/victorakande-realestate/public;
    index index.php index.html;

    # SSL Certificates (managed via Certbot)
    ssl_certificate /etc/letsencrypt/live/victorakande.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/victorakande.com/privkey.pem;
    include /etc/letsencrypt/options-ssl-nginx.conf;
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Content-Security-Policy "default-src 'self' 'unsafe-inline' https://fonts.bunny.net https://wa.me; img-src 'self' data: https: blob:;" always;

    charset utf-8;
    client_max_body_size 64M;

    # Gzip Compression
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css text/xml application/json application/javascript application/rss+xml application/atom+xml image/svg+xml;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Static Assets Caching
    location ~* \.(ico|css|js|gif|jpe?g|png|woff2?|eot|ttf|svg)$ {
        expires 365d;
        access_log off;
        add_header Cache-Control "public, max-age=31536000, immutable";
    }

    # Protect Private Document Storage from Direct Web Crawlers
    location ^~ /storage/app/private/ {
        deny all;
        return 404;
    }

    # Health Check Endpoint
    location = /up {
        access_log off;
        try_files $uri /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    # PHP-FPM FastCGI Handler
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
        fastcgi_buffer_size 16k;
        fastcgi_buffers 4 16k;
    }

    # Deny Hidden Files (.git, .env)
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable site and test configuration:
```bash
sudo ln -s /etc/nginx/sites-available/victorakande.com /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## 5. Automated Daily Database Backup Strategy

Create backup directory and shell script `/opt/scripts/backup_gaps_ppt.sh`:

```bash
sudo mkdir -p /var/backups/mysql /opt/scripts
sudo nano /opt/scripts/backup_gaps_ppt.sh
```

Paste the following backup routine:

```bash
#!/bin/bash
set -euo pipefail

# Configuration
BACKUP_DIR="/var/backups/mysql"
DATABASE="gaps_ppt_prod"
DB_USER="gaps_user"
DB_PASS="SECURE_STRONG_PASSWORD_HERE"
RETENTION_DAYS=30
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="${BACKUP_DIR}/${DATABASE}_${TIMESTAMP}.sql.gz"

# Create backup directory if not exists
mkdir -p "${BACKUP_DIR}"

# Execute compressed dump
echo "[$(date)] Starting backup of ${DATABASE}..."
mysqldump -u"${DB_USER}" -p"${DB_PASS}" \
    --single-transaction \
    --quick \
    --routines \
    --triggers \
    "${DATABASE}" | gzip -9 > "${BACKUP_FILE}"

echo "[$(date)] Backup completed: ${BACKUP_FILE} ($(du -h "${BACKUP_FILE}" | cut -f1))"

# Prune backups older than 30 days
find "${BACKUP_DIR}" -type f -name "${DATABASE}_*.sql.gz" -mtime +${RETENTION_DAYS} -delete
echo "[$(date)] Purged backups older than ${RETENTION_DAYS} days."
```

Make executable and register in crontab for **02:00 AM daily**:
```bash
sudo chmod +x /opt/scripts/backup_gaps_ppt.sh
sudo crontab -e
```
Add:
```cron
0 2 * * * /opt/scripts/backup_gaps_ppt.sh >> /var/log/mysql_backup.log 2>&1
```

---

## 6. Disaster Recovery & Restoration Procedures

In the event of hardware failure, database corruption, or server migration:

### 1. Identify Latest Good Backup
```bash
ls -lh /var/backups/mysql/
```

### 2. Restore Database from Compressed Dump
```bash
# Decompress and stream dump directly into MySQL
gunzip < /var/backups/mysql/gaps_ppt_prod_YYYYMMDD_HHMMSS.sql.gz | mysql -u gaps_user -p gaps_ppt_prod
```

### 3. Clear and Warm Laravel Caches
```bash
cd /var/www/victorakande-realestate
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 7. Zero-Downtime Deployment Script (`deploy.sh`)

Place in project root for continuous deployment:

```bash
#!/bin/bash
set -e

echo "Deploying Victor Akande Real Estate..."

# Activate Maintenance Mode
php artisan down --render="errors::503" --secret="deployment-bypass-key" || true

# Pull latest commits
git pull origin main

# Install PHP dependencies
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Run database migrations
php artisan migrate --force

# Compile frontend assets
npm ci
npm run build

# Clear and warm up caches
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart PHP-FPM and Queue Workers
sudo systemctl reload php8.3-fpm
php artisan queue:restart

# Disable Maintenance Mode
php artisan up

echo "Deployment successful!"
```

