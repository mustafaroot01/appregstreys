# Deployment Guide — iT-Diyala Registration System

## Prerequisites (Ubuntu/Debian server)

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install -y nginx php8.3-fpm php8.3-cli php8.3-mbstring php8.3-xml php8.3-bcmath php8.3-curl php8.3-sqlite3 php8.3-zip unzip git curl nodejs npm composer

# Verify installations
php -v
composer -v
node -v
npm -v
nginx -v
```

---

## 1. Clone from GitHub

```bash
# Go to web root
cd /var/www

# Clone the repository
sudo git clone https://github.com/mustafaroot01/appregstreys.git itdiyala

# Enter project
cd itdiyala
```

---

## 2. Install Dependencies

```bash
# PHP dependencies
composer install --no-dev --optimize-autoloader

# Node.js dependencies
npm ci

# Build frontend assets
npm run build
```

---

## 3. Environment Setup

```bash
# Copy env file
cp .env.example .env

# Generate app key
php artisan key:generate

# Create SQLite database
touch database/database.sqlite
```

### Edit `.env`:

```bash
sudo nano .env
```

Update these values:

```env
APP_NAME="iT-Diyala"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=sqlite

# Facebook Pixel (optional)
FACEBOOK_PIXEL_ID=2286332702216250

# OTP API Key (get from OTPIQ)
OTP_API_KEY=your_otpiq_key_here
```

Save: `Ctrl+O` → `Enter` → `Ctrl+X`

---

## 4. Database & Storage

```bash
# Run migrations
php artisan migrate --force

# Seed admin user
php artisan db:seed

# Create storage link
php artisan storage:link

# Cache config for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 5. Permissions

```bash
# Set ownership to web server user
sudo chown -R www-data:www-data /var/www/itdiyala

# Set permissions
sudo find /var/www/itdiyala -type f -exec chmod 644 {} \;
sudo find /var/www/itdiyala -type d -exec chmod 755 {} \;

# Make storage and bootstrap/cache writable
sudo chmod -R 775 /var/www/itdiyala/storage /var/www/itdiyala/bootstrap/cache
```

---

## 6. Nginx Configuration

```bash
sudo nano /etc/nginx/sites-available/itdiyala
```

Paste this:

```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/itdiyala/public;
    index index.php index.html;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Replace `your-domain.com` with your actual domain.

Enable the site:

```bash
sudo ln -s /etc/nginx/sites-available/itdiyala /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

---

## 7. SSL Certificate (Let's Encrypt)

```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
```

---

## 8. Admin Account

Default admin login:
- Email: `admin@it-diyala.com`
- Password: `admin123`

Change password immediately after first login!

---

## Quick Update (after code changes)

```bash
cd /var/www/itdiyala
sudo git pull origin main
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan config:cache
php artisan view:cache
sudo chown -R www-data:www-data /var/www/itdiyala
```

---

## Troubleshooting

| Problem | Fix |
|---------|-----|
| 500 Error | `sudo tail -f /var/www/itdiyala/storage/logs/laravel.log` |
| Permission denied | `sudo chmod -R 775 storage bootstrap/cache` |
| 404 on routes | `sudo nginx -t` then restart |
| CSS/JS not loading | Check `APP_URL` in `.env` matches your domain |
