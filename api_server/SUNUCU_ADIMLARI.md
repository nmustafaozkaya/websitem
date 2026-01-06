# Sunucuda Yapılacaklar (SSH ile Bağlandıktan Sonra)

## 1. Dosyaları Doğru Yere Taşıyın

```bash
# API server dosyalarını taşı
sudo mv /var/www/temp/web/api_server /var/www/cinema-api
sudo chown -R $USER:$USER /var/www/cinema-api
cd /var/www/cinema-api

# Ana web sitesi dosyalarını taşı (opsiyonel)
sudo mv /var/www/temp/web/* /var/www/html/
sudo chown -R www-data:www-data /var/www/html
```

## 2. Gerekli Paketleri Kurun (Eğer Kurulu Değilse)

```bash
# Sistem güncellemesi
sudo apt update && sudo apt upgrade -y

# PHP 8.2 ve extension'lar
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2 php8.2-fpm php8.2-cli php8.2-common php8.2-mysql php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml php8.2-bcmath

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# MySQL (eğer kurulu değilse)
sudo apt install -y mysql-server
sudo systemctl start mysql
sudo systemctl enable mysql

# Nginx (eğer kurulu değilse)
sudo apt install -y nginx
sudo systemctl start nginx
sudo systemctl enable nginx
```

## 3. MySQL Veritabanı Oluşturun

```bash
sudo mysql
```

MySQL komut satırında:

```sql
CREATE DATABASE cinema_fresh CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'cinema_user'@'localhost' IDENTIFIED BY 'güçlü_şifre_buraya';
GRANT ALL PRIVILEGES ON cinema_fresh.* TO 'cinema_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

**ÖNEMLİ**: `güçlü_şifre_buraya` kısmını güçlü bir şifre ile değiştirin!

## 4. .env Dosyasını Yapılandırın

```bash
cd /var/www/cinema-api

# .env dosyası zaten var, düzenleyin
nano .env
```

`.env` dosyasında şunları kontrol edin/güncelleyin:

```env
APP_NAME="Cinema Automation API"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://52.59.192.113

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cinema_fresh
DB_USERNAME=cinema_user
DB_PASSWORD=güçlü_şifre_buraya
```

Kaydedin: `Ctrl+X`, `Y`, `Enter`

## 5. Laravel Kurulumu

```bash
cd /var/www/cinema-api

# Bağımlılıkları yükle
composer install --no-dev --optimize-autoloader

# Application key oluştur (eğer yoksa)
php artisan key:generate

# Veritabanı migration
php artisan migrate --force

# Storage link
php artisan storage:link

# Cache optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# İzinler
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

## 6. Nginx Konfigürasyonu

```bash
sudo nano /etc/nginx/sites-available/cinema-api
```

Aşağıdaki içeriği yapıştırın:

```nginx
server {
    listen 80;
    server_name 52.59.192.113;
    root /var/www/cinema-api/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    access_log /var/log/nginx/cinema-api-access.log;
    error_log /var/log/nginx/cinema-api-error.log;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~ ^/(storage|bootstrap/cache) {
        deny all;
    }

    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css text/xml text/javascript application/json application/javascript;
}
```

Site'ı aktif edin:

```bash
sudo ln -s /etc/nginx/sites-available/cinema-api /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl reload nginx
```

## 7. PHP-FPM Ayarları

```bash
sudo sed -i 's/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/' /etc/php/8.2/fpm/php.ini
sudo systemctl restart php8.2-fpm
```

## 8. Firewall Ayarları

```bash
sudo ufw allow 'Nginx Full'
sudo ufw allow OpenSSH
sudo ufw enable
```

## 9. Test Edin

Tarayıcınızda şu adresi açın:
- `http://52.59.192.113/api/movies`

Başarılı ise JSON yanıt görmelisiniz!

## 10. İlk Verileri Yükleyin (Opsiyonel)

```bash
cd /var/www/cinema-api
chmod +x first_setup.sh
./first_setup.sh
```

---

## Sorun Giderme

### Nginx Hatası
```bash
sudo nginx -t
sudo tail -f /var/log/nginx/cinema-api-error.log
```

### PHP-FPM Hatası
```bash
sudo systemctl status php8.2-fpm
sudo tail -f /var/log/php8.2-fpm.log
```

### Laravel Hatası
```bash
tail -f /var/www/cinema-api/storage/logs/laravel.log
```

### İzin Hatası
```bash
sudo chown -R www-data:www-data /var/www/cinema-api/storage
sudo chown -R www-data:www-data /var/www/cinema-api/bootstrap/cache
sudo chmod -R 775 /var/www/cinema-api/storage
sudo chmod -R 775 /var/www/cinema-api/bootstrap/cache
```

