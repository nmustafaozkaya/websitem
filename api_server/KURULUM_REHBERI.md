# Cinema API - Hızlı Kurulum Rehberi (EC2 Ubuntu + Nginx)

Bu rehber, Cinema Automation API'yi Amazon EC2 Ubuntu sunucusunda hızlıca kurmanız için adım adım talimatlar içerir.

## 📋 Ön Gereksinimler

- Amazon EC2 Ubuntu 20.04 veya üzeri instance
- SSH erişimi (key pair)
- Root veya sudo yetkisi
- Domain adı (opsiyonel - IP ile de çalışır)

---

## 🚀 Hızlı Kurulum (5 Adım)

### Adım 1: Sunucuya Bağlanın

```bash
ssh -i your-key.pem ubuntu@your-ec2-ip
```

### Adım 2: Otomatik Kurulum Scriptini Çalıştırın

```bash
# Scripti indirin veya oluşturun
cd ~
nano setup_ubuntu.sh
```

Aşağıdaki içeriği yapıştırın ve kaydedin (Ctrl+X, Y, Enter):

```bash
#!/bin/bash
set -e

echo "========================================"
echo "  Cinema API - Ubuntu Kurulum Başlıyor"
echo "========================================"

# Sistem güncellemesi
echo "[1/8] Sistem güncelleniyor..."
sudo apt update && sudo apt upgrade -y

# Gerekli paketler
echo "[2/8] Gerekli paketler kuruluyor..."
sudo apt install -y software-properties-common curl wget git unzip build-essential

# PHP 8.2
echo "[3/8] PHP 8.2 kuruluyor..."
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2 php8.2-fpm php8.2-cli php8.2-common php8.2-mysql php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml php8.2-bcmath

# Composer
echo "[4/8] Composer kuruluyor..."
if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
    sudo chmod +x /usr/local/bin/composer
fi

# MySQL
echo "[5/8] MySQL kuruluyor..."
if ! command -v mysql &> /dev/null; then
    sudo apt install -y mysql-server
    sudo systemctl start mysql
    sudo systemctl enable mysql
fi

# Nginx
echo "[6/8] Nginx kuruluyor..."
if ! command -v nginx &> /dev/null; then
    sudo apt install -y nginx
    sudo systemctl start nginx
    sudo systemctl enable nginx
fi

# Node.js
echo "[7/8] Node.js kuruluyor..."
if ! command -v node &> /dev/null; then
    curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
    sudo apt install -y nodejs
fi

# Proje dizini
echo "[8/8] Proje dizini hazırlanıyor..."
sudo mkdir -p /var/www/cinema-api
sudo chown -R $USER:$USER /var/www/cinema-api

echo ""
echo "✅ Kurulum tamamlandı!"
echo ""
echo "Sonraki adımlar:"
echo "1. Proje dosyalarını /var/www/cinema-api dizinine yükleyin"
echo "2. MySQL veritabanı oluşturun"
echo "3. .env dosyasını yapılandırın"
echo "4. Nginx konfigürasyonunu yapın"
```

Scripti çalıştırılabilir yapın ve çalıştırın:

```bash
chmod +x setup_ubuntu.sh
./setup_ubuntu.sh
```

### Adım 3: Proje Dosyalarını Yükleyin

#### Seçenek A: Git ile (Önerilen)

```bash
cd /var/www
sudo git clone https://github.com/your-repo/cinema-api.git cinema-api
sudo chown -R $USER:$USER cinema-api
cd cinema-api
```

#### Seçenek B: SCP ile (Yerel bilgisayardan)

Yerel bilgisayarınızda (Windows PowerShell veya Linux terminal):

```bash
# api_server klasörünün içindeyken
scp -i your-key.pem -r * ubuntu@your-ec2-ip:/var/www/cinema-api/
```

#### Seçenek C: ZIP ile

```bash
# Yerel bilgisayarda ZIP oluşturun, sonra:
scp -i your-key.pem api_server.zip ubuntu@your-ec2-ip:~/
# Sunucuda:
cd /var/www
sudo unzip ~/api_server.zip -d cinema-api
sudo chown -R $USER:$USER cinema-api
cd cinema-api
```

### Adım 4: MySQL Veritabanı Oluşturun

```bash
# MySQL'e root olarak giriş yapın
sudo mysql

# MySQL komut satırında:
CREATE DATABASE cinema_fresh CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'cinema_user'@'localhost' IDENTIFIED BY 'güçlü_şifre_buraya';
GRANT ALL PRIVILEGES ON cinema_fresh.* TO 'cinema_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

**ÖNEMLİ**: `güçlü_şifre_buraya` kısmını güçlü bir şifre ile değiştirin!

### Adım 5: .env Dosyasını Yapılandırın

```bash
cd /var/www/cinema-api
cp .env.example .env
nano .env
```

`.env` dosyasında şunları güncelleyin:

```env
APP_NAME="Cinema Automation API"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cinema_fresh
DB_USERNAME=cinema_user
DB_PASSWORD=güçlü_şifre_buraya
```

Kaydedin (Ctrl+X, Y, Enter).

### Adım 6: Laravel Kurulumu

```bash
cd /var/www/cinema-api

# Bağımlılıkları yükle
composer install --no-dev --optimize-autoloader

# Application key oluştur
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

### Adım 7: Nginx Konfigürasyonu

```bash
sudo nano /etc/nginx/sites-available/cinema-api
```

Aşağıdaki içeriği yapıştırın (domain adınızı değiştirin):

```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
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

### Adım 8: Firewall Ayarları

```bash
sudo ufw allow 'Nginx Full'
sudo ufw allow OpenSSH
sudo ufw enable
```

### Adım 9: İlk Verileri Yükleyin (Opsiyonel)

```bash
cd /var/www/cinema-api
chmod +x first_setup.sh
./first_setup.sh
```

---

## ✅ Kurulum Kontrolü

### Servisleri Kontrol Edin

```bash
sudo systemctl status nginx
sudo systemctl status php8.2-fpm
sudo systemctl status mysql
```

### API'yi Test Edin

Tarayıcınızda şu adresi açın:
- `http://your-ec2-ip/api/movies`
- `http://your-domain.com/api/movies`

Başarılı ise JSON yanıt görmelisiniz.

---

## 🔒 SSL Sertifikası (HTTPS) - Opsiyonel

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
```

---

## 🔄 Güncelleme İşlemleri

### Kod Güncellemesi

```bash
cd /var/www/cinema-api
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo systemctl reload nginx
```

---

## 🐛 Sorun Giderme

### Nginx Logları

```bash
sudo tail -f /var/log/nginx/cinema-api-error.log
```

### Laravel Logları

```bash
tail -f /var/www/cinema-api/storage/logs/laravel.log
```

### PHP-FPM Logları

```bash
sudo tail -f /var/log/php8.2-fpm.log
```

### Veritabanı Bağlantı Hatası

```bash
# MySQL servisini kontrol edin
sudo systemctl status mysql

# MySQL'e bağlanmayı test edin
mysql -u cinema_user -p cinema_fresh
```

### İzin Hataları

```bash
sudo chown -R www-data:www-data /var/www/cinema-api/storage
sudo chown -R www-data:www-data /var/www/cinema-api/bootstrap/cache
sudo chmod -R 775 /var/www/cinema-api/storage
sudo chmod -R 775 /var/www/cinema-api/bootstrap/cache
```

---

## 📝 Önemli Notlar

1. **Güvenlik**: Production'da `APP_DEBUG=false` olmalı
2. **Şifreler**: MySQL şifrelerini güçlü tutun
3. **Backup**: Düzenli veritabanı yedeği alın
4. **Güncellemeler**: Sistem paketlerini düzenli güncelleyin

---

## 🎯 Hızlı Komutlar

```bash
# Servisleri yeniden başlat
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm

# Cache temizle
cd /var/www/cinema-api
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Logları görüntüle
tail -f storage/logs/laravel.log
```

---

## 📞 Yardım

Sorun yaşarsanız:
1. Log dosyalarını kontrol edin
2. Servis durumlarını kontrol edin
3. Nginx konfigürasyonunu test edin: `sudo nginx -t`

