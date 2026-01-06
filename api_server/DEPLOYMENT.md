# Cinema API - EC2 Ubuntu + Nginx Deployment Rehberi

Bu rehber, Cinema Automation API'yi Amazon EC2 Ubuntu sunucusunda Nginx ile nasıl kuracağınızı açıklar.

## Gereksinimler

- Amazon EC2 Ubuntu 20.04 veya üzeri
- SSH erişimi
- Root veya sudo yetkisi
- Domain adı (opsiyonel, IP adresi ile de çalışır)

## Kurulum Adımları

### 1. Sunucuya Bağlanın

```bash
ssh -i your-key.pem ubuntu@your-ec2-ip
```

### 2. Kurulum Scriptini Çalıştırın

```bash
cd ~
wget https://raw.githubusercontent.com/your-repo/cinema-api/main/setup_ubuntu.sh
chmod +x setup_ubuntu.sh
./setup_ubuntu.sh
```

Script şunları yapacak:
- Sistem güncellemesi
- PHP 8.2 ve gerekli extension'lar
- Composer
- MySQL
- Nginx
- Node.js ve NPM

### 3. MySQL Veritabanı Oluşturun

```bash
chmod +x setup_mysql.sh
./setup_mysql.sh
```

Bu script:
- Veritabanı oluşturur
- Kullanıcı oluşturur
- Gerekli izinleri verir

### 4. Proje Dosyalarını Yükleyin

#### Seçenek 1: Git ile

```bash
cd /var/www
sudo git clone https://github.com/your-repo/cinema-api.git
sudo chown -R $USER:$USER cinema-api
cd cinema-api
```

#### Seçenek 2: SCP ile (Yerel bilgisayardan)

```bash
# Yerel bilgisayarınızda
scp -i your-key.pem -r api_server/* ubuntu@your-ec2-ip:/var/www/cinema-api/
```

### 5. .env Dosyasını Oluşturun

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
DB_PASSWORD=your_password_here
```

### 6. Composer Bağımlılıklarını Yükleyin

```bash
composer install --no-dev --optimize-autoloader
```

### 7. Laravel Ayarlarını Yapın

```bash
# Application key oluştur
php artisan key:generate

# Storage link oluştur
php artisan storage:link

# Cache ve config optimize et
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 8. Veritabanı Migration ve Seeding

```bash
# Migration çalıştır
php artisan migrate --force

# İlk verileri yükle (opsiyonel)
php artisan db:seed --class=Database\\Seeders\\Cinemas\\CitySeeder
php artisan db:seed --class=Database\\Seeders\\Cinemas\\CinemaSeeder
# ... diğer seeder'lar
```

Veya tüm seeder'ları çalıştırmak için:

```bash
chmod +x first_setup.sh
./first_setup.sh
```

### 9. Dosya İzinlerini Ayarlayın

```bash
# Storage ve cache dizinlerine yazma izni ver
sudo chown -R www-data:www-data /var/www/cinema-api/storage
sudo chown -R www-data:www-data /var/www/cinema-api/bootstrap/cache
sudo chmod -R 775 /var/www/cinema-api/storage
sudo chmod -R 775 /var/www/cinema-api/bootstrap/cache
```

### 10. Nginx Konfigürasyonunu Kontrol Edin

```bash
# Nginx config test
sudo nginx -t

# Nginx'i yeniden başlat
sudo systemctl reload nginx
```

### 11. PHP-FPM'i Yeniden Başlatın

```bash
sudo systemctl restart php8.2-fpm
```

### 12. Firewall Ayarları (UFW)

```bash
# HTTP ve HTTPS portlarını aç
sudo ufw allow 'Nginx Full'
sudo ufw allow OpenSSH
sudo ufw enable
```

## SSL Sertifikası (Let's Encrypt)

HTTPS için Let's Encrypt kullanın:

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
```

## Güncelleme İşlemleri

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

### Veritabanı Güncellemesi

```bash
cd /var/www/cinema-api
php artisan migrate
```

## Sorun Giderme

### Nginx Logları

```bash
# Access log
sudo tail -f /var/log/nginx/cinema-api-access.log

# Error log
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

### Servis Durumları

```bash
sudo systemctl status nginx
sudo systemctl status php8.2-fpm
sudo systemctl status mysql
```

## Güvenlik Önerileri

1. **.env dosyasını koruyun**: `.env` dosyası asla Git'e commit edilmemeli
2. **Firewall**: Sadece gerekli portları açın
3. **SSH Keys**: Şifre yerine SSH key kullanın
4. **Düzenli güncellemeler**: Sistem ve paketleri düzenli güncelleyin
5. **Backup**: Veritabanını düzenli yedekleyin

## Backup

### Veritabanı Yedekleme

```bash
mysqldump -u cinema_user -p cinema_fresh > backup_$(date +%Y%m%d).sql
```

### Tüm Proje Yedekleme

```bash
tar -czf cinema-api-backup-$(date +%Y%m%d).tar.gz /var/www/cinema-api
```

## Performans Optimizasyonu

1. **OPcache**: PHP OPcache'i aktif edin
2. **Redis**: Cache için Redis kullanın
3. **CDN**: Statik dosyalar için CDN kullanın
4. **Database Indexing**: Veritabanı indexlerini optimize edin

## Test Hesapları

Kurulum sonrası oluşturulan test hesapları:

- **Admin**: admin@cinema.com / password
- **Manager**: manager@cinema.com / password
- **Cashier**: cashier@cinema.com / password
- **Customer**: customer@cinema.com / password

**ÖNEMLİ**: Production ortamında bu hesapları değiştirin veya silin!

## API Endpoints

Kurulum sonrası API'ye şu adresten erişebilirsiniz:

- API Base URL: `http://your-domain.com/api`
- Örnek: `http://your-domain.com/api/movies`
- Örnek: `http://your-domain.com/api/cinemas`

## Destek

Sorun yaşarsanız:
1. Log dosyalarını kontrol edin
2. Servis durumlarını kontrol edin
3. Nginx ve PHP-FPM konfigürasyonlarını kontrol edin

